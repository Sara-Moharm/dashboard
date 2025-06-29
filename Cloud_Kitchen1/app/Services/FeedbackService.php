<?php

namespace App\Services;

use App\Models\Feedback;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\FeedbackAspect;
use App\Jobs\ProcessSingleFeedback;
class FeedbackService
{
    // Adding a constant to identify unprocessed sentiments
    const SENTIMENT_UNPROCESSED = "unprocessed";
    const FLASK_API_URL = 'http://127.0.0.1:5000/predict';

      /**
     * Analyze sentiment using Flask API
     *
     * @param string $text
     * @return int
     */

    public function analyzeSentiment(string $text)
    {
        
        if (empty($text)) {
            Log::warning('Empty text provided for sentiment analysis');
            return self::SENTIMENT_UNPROCESSED;
        }
        if(!filter_var(self::FLASK_API_URL, FILTER_VALIDATE_URL)) {
            Log::warning('Invalid Flask API URL: ' . self::FLASK_API_URL);
            Log::warning('Flask service unavailable during sentiment analysis');
            return self::SENTIMENT_UNPROCESSED;
        }


        try {
            // Send request to Flask API 
            $response = Http::post(self::FLASK_API_URL, ['text' => $text]);
            
            if ($response->failed()) {
                Log::error('Flask API request failed: ' . $response->body());
                return self::SENTIMENT_UNPROCESSED;
            }
            // Log the response for debugging
            Log::info('Flask API response: ' . $response->body());

            // Check if Flask API responded correctly
            $analysis = json_decode($response, true);

        if (!isset($analysis['prediction']) || !is_string($analysis['prediction'])) {
            Log::warning('Prediction missing or invalid in analysis response');
            return ['prediction' => self::SENTIMENT_UNPROCESSED, 'aspects' => []];
        }

        if (!isset($analysis['aspects']) || !is_array($analysis['aspects']) || empty($analysis['aspects'])) {
            Log::warning('Aspects missing or empty in analysis response');
            return ['prediction' => self::SENTIMENT_UNPROCESSED, 'aspects' => []];
        }

        return $analysis;
        }
          catch (\Exception $e) {
            Log::error('Sentiment analysis failed: ' . $e->getMessage());
        }

        // If we get here, something went wrong, mark as unprocessed
        return ['prediction' => self::SENTIMENT_UNPROCESSED, 'aspects' => []];
    }

    /**
     * Create a new feedback record
     *
     * @param array $data
     * @param User $user
     * @return Feedback
     * @throws \RuntimeException
     */
    public function createFeedback(array $data, User $user): Feedback
    {
        try {
            Log::info('Full request data:', $data);

            Log::info('Creating feedback for user ID: ' . $user->id);
            // Ensure sentiment_class is properly cast to integer
            $analyzedFeedback = $data['analyzed_feedback'];
            $overallSentiment = is_array($analyzedFeedback) && isset($analyzedFeedback['prediction']) ? $analyzedFeedback['prediction'] : self::SENTIMENT_UNPROCESSED;
            $aspectsWithSentiment = is_array($analyzedFeedback) && isset($analyzedFeedback['aspects']) ? $analyzedFeedback['aspects'] : [];         
            
            // Log the data for debugging
            Log::info('Creating feedback with data: ', [
                'customer_id' => $user->customer->id,
                'feedback' => $data['feedback'],
                'sentiment' => $overallSentiment
            ]);
            
            $feedback = Feedback::create([
                'customer_id' => $user->customer->id,
                'feedback' => $data['feedback'],
                'sentiment' => $overallSentiment
            ]);

            Log::info('aspectsWithSentiment: ', $aspectsWithSentiment);

            if(array_key_exists('uncategorized', $aspectsWithSentiment)) {
                Log::warning('Uncategorized aspects found in feedback: ' . json_encode($aspectsWithSentiment['uncategorized']));
                return $feedback;
            }
            if (empty($aspectsWithSentiment)) {
                Log::info('No aspects with sentiment found, returning feedback without aspects');
                $feedback->update(['sentiment' => FeedbackService::SENTIMENT_UNPROCESSED]);
                return $feedback;
            }
            $aspects = [];
            foreach ($aspectsWithSentiment as $aspectName => $aspectSentences) {
                foreach ($aspectSentences as $aspect) {
                 $feedbackAspect = FeedbackAspect::create([
                        'feedback_id' => $feedback->id,
                        'aspect' => $aspectName, 
                        'sentence' => $aspect['sentence'],
                        'sentiment' => $aspect['sentiment'],
        ]);
                $aspects[] = $feedbackAspect;
            Log::info('Aspect saved: ', [
            'aspect' => $aspectName,
            'sentence' => $aspect['sentence'],
            'sentiment' => $aspect['sentiment']
        ]);
    }

    }
            return $feedback->load('aspects');

        }

         catch (\Exception $e) {
            Log::error('Failed to create feedback: ' . $e->getMessage());
            Log::error('Exception trace: ' . $e->getTraceAsString());
            throw new \RuntimeException("Failed to create feedback record: " . $e->getMessage(), 0, $e);
        }
    }
  


    /**
     * Process unprocessed feedback items and update their sentiment class
     *
     * @param array $feedbackItems
     * @return int Number of processed feedback items
     */
    public function processFeedback()
    {
        try {
            
            $feedbacks = Feedback::where('sentiment', self::SENTIMENT_UNPROCESSED)->get();

            if ($feedbacks->isEmpty()) {
                Log::info('No feedbacks to process');
                return 0;
            }
            foreach ($feedbacks as $feedback) {
                $analyzed = $this->analyzeSentiment($feedback);
                $overallSentiment = is_array($analyzed) && isset($analyzed['prediction']) ? $analyzed['prediction'] : self::SENTIMENT_UNPROCESSED;
                $aspectsWithSentiment = is_array($analyzed) && isset($analyzed['aspects']) ? $analyzed['aspects'] : [];

                if ($overallSentiment !== self::SENTIMENT_UNPROCESSED) {
                    $feedback->update([
                        'sentiment' => $overallSentiment,
                        'processed_at' => now(),
                    ]);

                if(array_key_exists('uncategorized', $aspectsWithSentiment)) {
                    Log::warning('Uncategorized aspects found in feedback: ' . json_encode($aspectsWithSentiment['uncategorized']));
                    continue; // Skip this feedback if uncategorized aspects are found
                }

                if (empty($aspectsWithSentiment)) {
                    Log::info('No aspects with sentiment found, returning feedback without aspects');
                    $feedback->update(['sentiment' => FeedbackService::SENTIMENT_UNPROCESSED]);
                    return;
                }
                    $feedback->aspects()->delete();

                    foreach ($analyzed['aspects'] as $aspectName => $aspectSentences) {
                        foreach ($aspectSentences as $aspect) {
                            FeedbackAspect::create([
                                'feedback_id' => $feedback->id,
                                'aspect' => $aspectName,
                                'sentence' => $aspect['sentence'],
                                'sentiment' => $aspect['sentiment'],
                            ]);
                        }
                    }
                }
            }

            return $feedbacks->count();
        } catch (\Exception $e) {
            Log::error('Failed to process feedback: ' . $e->getMessage());
            Log::error('Exception trace: ' . $e->getTraceAsString());
            throw new \RuntimeException('Failed to process feedback: ' . $e->getMessage());
        }
    }
}