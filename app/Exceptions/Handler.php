<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request                                $request
     * @param  \Exception                                              $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception)
    {
        $response = [
            'success' => false,
            'message' => $exception->getMessage(),
        ];
        if (is_object($exception) && method_exists($exception, 'errors')) {
            $response['errors'] = $exception->errors();
        }
        if (config('app.debug')) {
            $response['detail'] = $exception;
        }
        // Set an appropriate status code
        $status = $exception->status ?? 400;
        if (method_exists($exception, 'getStatusCode')) {
            $status = $exception->getStatusCode();
        }
        if ($exception instanceof ModelNotFoundException) {
            $response['message'] = sprintf("%s not found", class_basename($exception->getModel()));
            $status = 404;
        } elseif ($exception instanceof AuthorizationException) {
            $status = 403;
        } elseif ($exception instanceof TokenMismatchException) {
            $status = 419;
        }

        return response()->json($response, $status);
    }
}
