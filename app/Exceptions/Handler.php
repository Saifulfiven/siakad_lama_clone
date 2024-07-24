<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // var_dump($exception);exit; 
        // Token mismatch solve
       if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
           return redirect()
                  ->back()
                  ->withInput($request->except(['password', 'password_confirmation']))
                  ->with('error', 'Token telah kadaluarsa. Ulangi lagi');
       
            return parent::render($request, $exception);

       } else {

            return parent::render($request, $exception);
            
            // if ( env('APP_DEBUG') ) {

            //     return parent::render($request, $exception);

            // } else {

            //     if ( !empty($exception->getMessage() && $exception->getMessage() != 'Please provide a valid cache path.') ) {
            //         return response()->view('errors.custom');
            //     }

            //     return parent::render($request, $exception);
            // }
       }

    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            // return response()->json(['error' => 'Unauthenticated.'], 401);
            return response()->json(['error' => 1, 'msg' => 'Anda harus login'], 401);
        }

        return redirect()->guest('login');
    }
}
