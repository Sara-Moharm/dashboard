<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Validation\ValidationException;


class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

       
    }
     public function render($request, Throwable $exception)
    {

        if ($request->is('api/*')) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        }

    //     // Force API routes to return JSON on validation error
    //     if ($exception instanceof ValidationException) {
    //         if ($request->is('api/*')) {
    //             return response()->json([
    //                 'message' => 'Validation Failed',
    //                 'errors' => $exception->errors(),
    //             ], 422);
    //         }
    //     }

        
    //     if ($exception instanceof ModelNotFoundException) {
    //         return response()->json([
    //             'message' => 'The requested resource was not found.'
    //         ], 404);
    //     }
    //     return parent::render($request, $exception);

    }

}

 

