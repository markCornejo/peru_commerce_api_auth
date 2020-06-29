<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponser;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {

        if($exception instanceof HttpException) {
            $code = $exception->getStatusCode();
            $message = Response::$statusTexts[$code];

            return $this->errorResponse($message, $code);
        }

        if($exception instanceof ModelNotFoundException) {
            $model = strtolower(class_basename($exception->getModel()));

            return $this->errorResponse("Does not exist any instance of {$model} with the given id", Response::HTTP_NOT_FOUND);
        }

        if($exception instanceof AuthorizationException) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_FORBIDDEN);
        }

        if($exception instanceof AuthorizationException) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
        }

        if($exception instanceof ValidationException) {
            $errors = $exception->validator->errors()->getMessages();
            return $this->errorResponse($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if($exception instanceof ClientException) {
            $message = $exception->getResponse()->getBody();
            $code = $exception->getCode();

            return $this->errorMessage($message, $code);
        }

        if(env('APP_DEBUG', false)) {
            return parent::render($request, $exception);
        }

        // return $this->errorResponse('Unnexpected error. Try later', Response::HTTP_INTERNAL_SERVER_ERROR);
        return parent::render($request, $exception);
    }
}
