<?php

namespace Zhiyi\Plus\Http\Middleware;

use Closure;
use Zhiyi\Plus\Models\AuthCodeKey;
use Zhiyi\Plus\Models\BaseUser as BaseUser;


class Ucenter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,$massage = '')
    {
        $base_token = ($request->basetoken ? $request->basetoken : $request->header('Authorization'));
        if ($request->platekey == env('UCENTER_KEY')){
            $base_arr = explode('.' , $base_token);
            $user = BaseUser::where('key' , $base_arr[0])->first();
            if (isset($user)){
                $time = base64_decode($base_arr[1]);
                $base_sns_token = $this->base_token($user->id,$time);
                if ($base_token == $base_sns_token){
                    return $next($request);
                } else {
                    abort(403, $massage ?: '你没有权限执行该操作');
                }
            } else {
                abort(403, $massage ?: '你没有权限执行该操作');
            }
        } else {
            abort(403, $massage ?: '你没有权限执行该操作');
        }
    }
    public function base_token($base_user_id,$time){
        $authcode =new AuthCodeKey();
        //获取用户key
        $user_key = BaseUser::where('id' , $base_user_id)->first();
        $key = env('JWT_SECRET');
        $time = base64_encode((string)time());
        //加密的key
        $user_key = $user_key->key;
        //加密用户的密钥
        $signParam = array($user_key, $time, $key);
        $sessionId = implode(',', $signParam);
        // 加密
        $last_user_key = $authcode->encrypt($sessionId);
        // 解密
        //$t = $authcode->decrypt($r);
        //设置的加密参数
        $data = "$user_key.$time";

        $hmac = hash_hmac("sha256", $data, $key, TRUE);
        //加密后字符串
        $signature = base64_encode($hmac);
        //最终生成的base_token
        $base_token = $last_user_key.'.'.$time.'.'.$signature;
        return $base_token;
    }
}
