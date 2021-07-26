<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Input;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
        NotFoundHttpException::class,
        MethodNotAllowedHttpException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if($exception instanceOf NotFoundHttpException && @$request->segment(1) == ADMIN_URL) {
            return redirect('404');
        }
        elseif($exception instanceOf MethodNotAllowedHttpException) {
            $src_url = $request->src_url;
            if($src_url != '')
                return redirect($src_url);
            return redirect('404');
        }
        else if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
            return redirect()->back()->withInput($request->except('password'))->with(['message' => __('messages.warning.token_expired'),'alert-class' => 'alert-danger']);
        }
        else {
            // if(request()->wantsJson()) {
            //     logger($exception->getMessage());
            //     return response()->json([
            //         'status' => 'failed','error_message'   =>  __('messages.booking.something_went_wrong'),
            //     ]);
            // }
            // // Handle all 500 exceptions
            // if(method_exists('getStatusCode', $exception) && $exception->getStatusCode() == 500 && url()->previous() && url()->previous() != url()->current()) {
            //     return redirect()->back()->with(['message' => __('messages.booking.something_went_wrong'),'alert-class' => 'alert-danger']);
            // }

            return parent::render($request, $exception);
        }
    }

    /**
     * Get the Whoops handler for the application.
     *
     * @return \Whoops\Handler\Handler
     */
    protected function whoopsHandler()
    {
        try {
            return app(\Whoops\Handler\HandlerInterface::class);
        } catch (\Illuminate\Contracts\Container\BindingResolutionException $e) {
            return parent::whoopsHandler();
        }
    }
}
