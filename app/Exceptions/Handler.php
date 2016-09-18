<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler {
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
	 * @param  \Exception $e
	 * @return void
	 */
	public function report(Exception $e) {
		parent::report($e);
	}
	
	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Exception               $e
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $e) {
		if ($e instanceof TokenMismatchException) {
			return response()->view('errors.custom', ['title' => 'Form token mismatch', 'description' => 'ดูเหมือนว่าคุณไม่ได้กดส่งฟอร์มเป็นเวลานานเกินไป กรุณาลองใหม่', 'button' => '<a href="/" class="waves-effect waves-light btn indigo darken-3 tooltipped center-align"
       style="width:80%;max-width:350px;margin-top:20px">ไปยังหน้าหลัก</a>']);
		}
		
		return parent::render($request, $e);
	}
}
