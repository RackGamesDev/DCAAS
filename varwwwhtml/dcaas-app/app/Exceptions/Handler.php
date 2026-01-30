<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should be reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the exception types that are not to be logged.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'phone',
    ];

    /**
     * Handle an HTTP exception.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function render($request, $e) // Changed parameter name to 'e' for clarity
    {
        if ($request->expectsJson() || $request->is('api/*')) {  // Check if JSON is expected or API route
            if (method_exists($e, 'getStatusCode') && $e->getStatusCode() >= 400) {
                $data = [
                    'status' => 'error',
                    'code' => $e->getStatusCode(),
                    'message' => $this->formatErrorMessage($e), // Use helper function for message formatting
                ];

                return new JsonResponse($data, $e->getStatusCode());
            }
        }

        return parent::render($request, $e);  // Default handling for other cases.
    }


    /**
     * Formats the error message based on the exception type.
     *
     * @param \Exception $exception
     * @return string
     */
    protected function formatErrorMessage(\Exception $exception)
    {
        if (method_exists($exception, 'getMessage')) {
            return $exception->getMessage();
        }

        return 'An unexpected error occurred.'; // Generic message if no specific message available.
    }
}
