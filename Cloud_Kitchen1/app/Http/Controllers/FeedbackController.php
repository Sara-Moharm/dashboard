<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Ramsey\Uuid\FeatureSet;
use App\Services\FeedbackService;
use App\Http\Requests\Feedback\StoreFeedbackRequest;
use App\Http\Requests\Feedback\ProcessFeedbackRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    use AuthorizesRequests;

      protected $feedbackService;
    
    public function __construct(FeedbackService $feedbackService)
    {
        $this->feedbackService = $feedbackService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Feedback::class);
        return response()->json(Feedback::all());

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFeedbackRequest $request)
    {
        $this->authorize('create', Feedback::class);
        $validatedData = $request->validated();
        $user = User::with('customer')->find(Auth::id());

       // Analyze sentiment - could be -1 if API fails
       $validatedData['analyzed_feedback'] = $this->feedbackService->analyzeSentiment($validatedData['feedback']);
        
       // Create feedback regardless of sentiment status
       $result = $this->feedbackService->createFeedback($validatedData , $user);
      
       // Return appropriate response
       $responseData = [
           'message' => 'Feedback saved successfully',
           'feedback_id' => $result->id,
           'sentiment' => $result->sentiment,
           'aspects' => $result->aspects->map(function ($aspect) {
               return [
                   'aspect' => $aspect->aspect,
                   'sentence' => $aspect->sentence,
                   'sentiment' => $aspect->sentiment
               ];
           })
       ];
       $status = 201;
       
       // Add warning message if sentiment couldn't be analyzed
       if ($result->sentiment == FeedbackService::SENTIMENT_UNPROCESSED) {
           $responseData['message'] = 'Feedback saved, but sentiment analysis is pending.';
           $responseData['sentiment_status'] = 'unprocessed';
       }
       
       return response()->json($responseData, $status);
    }

    /**
     * Process unprocessed feedback.
     */
    public function processFeedback()
    {
        $this->authorize('process', Feedback::class);
        $processedCount = $this->feedbackService->processFeedback();        
        return response()->json([
            'message' => 'Feedback processed successfully',
            'processed_count' => $processedCount
        ]);
    }
}
