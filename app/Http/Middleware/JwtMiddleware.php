<?php
namespace App\Http\Middleware;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Closure;
use JWTAuth;
use Exception;
use App;

class JwtMiddleware extends BaseMiddleware
{
	/**
	* Handle an incoming request.
	*
	* @param  \Illuminate\Http\Request  $request
	* @param  \Closure  $next
	* @return mixed
	*/
	public function handle($request, Closure $next)
	{
		$token = $request->token;
		if($token) {
			$validate_token = $this->validateToken($token);
			if($validate_token) {
				return $validate_token;
			}
			$user_details = JWTAuth::parseToken()->authenticate();
			if ($user_details && @$user_details->email_language !== null) {
				session(['language' => $user_details->email_language]);
				App::setLocale($user_details->email_language);
			}
			else if(isset($request->language)) {
				session(['language' => $request->language]);
				App::setLocale($request->language);
			}
		}

		return $next($request);
	}

	protected function validateToken($token)
	{
		try {
			$user_details = JWTAuth::parseToken()->authenticate();
			if(!$user_details) {
				return response()->json([
                        'success_message' => "User not found",
                        'status_code' => "0",
                        ],401);
			}
		}
		catch (Exception $e) {
			if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
				return response()->json(['status' => 'Token is Invalid']);
			}
			else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
				return $this->getRefreshToken();
			}
			else {
				return response()->json(['status' => 'Authorization Token not found']);
			}
		}
		return false;
	}

	protected function getRefreshToken()
	{
		$refreshed = JWTAuth::refresh(JWTAuth::getToken());
		return response()->json([
			'success_message' 	=> "Token Expired",
			'status_code' 		=> "0",
			'refresh_token' 	=> $refreshed,
		]);
	}
}