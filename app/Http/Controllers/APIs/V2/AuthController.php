<?php

declare(strict_types=1);

/*
 * +----------------------------------------------------------------------+
 * |                          ThinkSNS Plus                               |
 * +----------------------------------------------------------------------+
 * | Copyright (c) 2018 Chengdu ZhiYiChuangXiang Technology Co., Ltd.     |
 * +----------------------------------------------------------------------+
 * | This source file is subject to version 2.0 of the Apache license,    |
 * | that is bundled with this package in the file LICENSE, and is        |
 * | available through the world-wide-web at the following url:           |
 * | http://www.apache.org/licenses/LICENSE-2.0.html                      |
 * +----------------------------------------------------------------------+
 * | Author: Slim Kit Group <master@zhiyicx.com>                          |
 * | Homepage: www.thinksns.com                                           |
 * +----------------------------------------------------------------------+
 */

namespace Zhiyi\Plus\Http\Controllers\APIs\V2;

use Illuminate\Http\Request;
use function Zhiyi\Plus\username;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Zhiyi\Plus\Models\VerificationCode;
use Zhiyi\Plus\Models\User;
use Zhiyi\Plus\Models\BaseUser;
use Zhiyi\Plus\Models\AuthCodeKey;
use \Zhiyi\Plus\Http\Middleware\Ucenter;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;
use Log;


class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function __construct(Request $request)
    {
        if(! empty(($request->basetoken || $request->header('Authorization')) && ! empty($request->platekey))){
            $this->middleware(Ucenter::class);
        } else {
            $this->middleware('auth:api', ['except' => ['login', 'refresh','phoneLogin','base_token']]);
        }
    }
    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard(): Guard
    {
        return Auth::guard('api');
    }

    /**
     * Get a JWT token via given credentials.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function login(Request $request): JsonResponse
    {
        $login = (string) $request->input('login', '');
      	session(['login' => $login]);
        $credentials = [
            username($login) => $login,
            'password' => $request->input('password', ''),
        ];

        if ($token = $this->guard()->attempt($credentials)) {
            return $this->respondWithToken($token);
        }
        return $this->response()->json(['message' => '账号或密码不正确'], 422);
    }

    public function me()
    {
        return response()->json(auth('api')->user());
    }
    /**
     * 用户手机登录
     *
     */
    public function phoneLogin(Request $request , ResponseFactoryContract $response): JsonResponse
    {
        $login = $request->input('phone');
      	$user = User::where('phone' , $login)->first();
      	if(!$user){
            return $response->json(['message' => ['手机号还没有注册，请先注册']], 422);
        }
      	session(['login' => $login]);
        $code = $request->input('verifiable_code');
        $verify = VerificationCode::where('account', $login)
            ->where('channel', 'sms')
            ->where('code', $code)
            ->orderby('id', 'desc')
            ->first();
        if (! $verify) {
            return $response->json(['message' => ['验证码过期或错误']], 422);
        }
        $credentials = [
            'phone' => $login,
        ];
		Auth::attempt($credentials);
        $token = $this->guard()->attempt($credentials);
        return $this->respondWithToken($token);
    }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function logout(): JsonResponse
    {
        $this->guard()->logout();

        return $this->response()->json(['message' => '退出成功']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(
            $this->guard()->refresh()
        );
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
  	protected function respondWithToken(string $token): JsonResponse
    {
      	$baseUser = BaseUser::where('tel' , session('login'))->orWhere('name' , session('login'))->first();
      	$base_user_id = $baseUser->id;
        return $this->response()->json([
            'base_token' =>$this->base_token($base_user_id),
            'access_token' => $token,
            //'token_type' => 'Bearer',
            'expires_in' => $this->guard()->factory()->getTTL(),
            'refresh_ttl' => config('jwt.refresh_ttl'),
        ]);
    }
  /**  protected function respondWithToken(string $token): JsonResponse
    {
        return $this->response()->json([
            'access_token' => $token,
            //'token_type' => 'Bearer',
            'expires_in' => $this->guard()->factory()->getTTL(),
            'refresh_ttl' => config('jwt.refresh_ttl'),
        ]);
    } **/
    //获取base_token
    public function base_token($base_user_id){
        //获取用户key
        $user_key = BaseUser::where('id' , $base_user_id)->first();
        //密钥
        $key = "bibipay1039";
        $time = time();
        //加密的key
        $user_key = $user_key->key;
        //设置的加密参数
        $data = "$user_key.$time";
        $hmac = hash_hmac("sha256", $data, $key, TRUE);
        //加密后字符串
        $signature = base64_encode($hmac);
        //最终生成的base_token
        $base_token = $user_key.'.'.$time.'.'.$signature;
        return $base_token;
    }
}
