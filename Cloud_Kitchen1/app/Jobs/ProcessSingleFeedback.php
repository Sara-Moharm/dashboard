<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Models\Feedback;
use App\Services\FeedbackService;
use App\Models\FeedbackAspect;
use Illuminate\Support\Facades\Log;

class ProcessSingleFeedback implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $feedback;

    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
    }

    public function handle(): void
    {
        try {
            $analyzed = (new FeedbackService())->analyzeSentiment($this->feedback);
            $overallSentiment = is_array($analyzed) && isset($analyzed['prediction']) ? $analyzed['prediction'] : FeedbackService::SENTIMENT_UNPROCESSED;
            $aspectsWithSentiment = is_array($analyzed) && isset($analyzed['aspects']) ? $analyzed['aspects'] : [];

            if ($overallSentiment === FeedbackService::SENTIMENT_UNPROCESSED) {
                Log::info("Feedback {$this->feedback->id} could not be classified.");
                return;
            }

            $this->feedback->update([
                'sentiment' => $overallSentiment,
                'processed_at' => now(),
            ]);

            if (array_key_exists('uncategorized', $aspectsWithSentiment)) {
                Log::warning('Uncategorized aspects found in feedback: ' . json_encode($aspectsWithSentiment['Uncategorized']));
                return;
            }

            if (empty($aspectsWithSentiment)) {
                Log::info("Feedback {$this->feedback->id} has no aspects with sentiment.");
                $this->feedback->update(['sentiment' => FeedbackService::SENTIMENT_UNPROCESSED]);
                return;
            }

            $this->feedback->aspects()->delete();

            foreach ($aspectsWithSentiment as $aspectName => $aspectSentences) {
                foreach ($aspectSentences as $aspect) {
                    FeedbackAspect::create([
                        'feedback_id' => $this->feedback->id,
                        'aspect' => $aspectName,
                        'sentence' => $aspect['sentence'],
                        'sentiment' => $aspect['sentiment'],
                    ]);
                }
            }

            Log::info("Feedback {$this->feedback->id} processed successfully.");

        } catch (\Throwable $e) {
            Log::error("Error processing feedback ID {$this->feedback->id}: " . $e->getMessage());
        }
    }
}