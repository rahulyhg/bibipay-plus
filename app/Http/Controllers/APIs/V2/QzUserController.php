<?php

declare(strict_types=1);

/*
 * +----------------------------------------------------------------------+
 * |                          bibipay                                     |
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

use DB;
use RuntimeException;
use Tymon\JWTAuth\JWTAuth;
use Zhiyi\Plus\Models\User;
use Zhiyi\Plus\Models\BaseUser;
use Zhiyi\Plus\Models\AuthCodeKey;
use Zhiyi\Plus\Models\CommonConfig;
use Zhiyi\Plus\Models\VerificationCode;
use Zhiyi\Plus\Http\Requests\API2\StoreUserPost;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;

class QzUserController extends Controller
{
    /**
     * 创建用户.
     *
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function register(StoreUserPost $request, ResponseFactoryContract $response, JWTAuth $auth)
    {
        $phone = $request->input('phone');
        $email = $request->input('email');
        $name = $request->input('name');
        $password = $request->input('password');
        $channel = $request->input('verifiable_type');
        $code = $request->input('verifiable_code');
      	$send_code = $request->input('send_code');
      	
//        $platfrom = $request->input('platfrom') ? $request->input('platfrom') : '';
      	//验证用户邀请码
        $isset_code = User::where('my_code' , $send_code)->first();
        if(empty($isset_code)){
            return $response->json(['message' => ['邀请码不存在']], 422);
        }
      	//生成用户邀请码
      	$my_code = str_shuffle(substr(uniqid(),3,6));
      	//验证是否已经存在
      	$isset_mycode =User::where('my_code' , $my_code)->first();
      	if(!empty($isset_mycode)){
        	$my_code = str_shuffle(substr(uniqid(),3,6));
        }
      	$address = DB::table('user_address')->where('status', 0)->first();
      	//判断是否有可用钱包地址
      	if(!$address){
        	return $response->json(['message' => '平台繁忙，请联系客服'], 422);
        }
        $role = CommonConfig::byNamespace('user')
            ->byName('default_role')
            ->firstOr(function () {
                throw new RuntimeException('Failed to get the defined user group.');
            });
        $verify = VerificationCode::where('account', $channel == 'mail' ? $email : $phone)
            ->where('channel', $channel)
            ->where('code', $code)
            ->orderby('id', 'desc')
            ->first();
        if (! $verify) {
            return $response->json(['message' => ['验证码错误或者已失效']], 422);
        }
        //用户信息插入user表 
      	$user = new User();
        $user->phone = $phone;
        $user->email = $email;
        $user->name = $name;
      	$user->my_code = $my_code;
      	$user->send_code = $send_code;
        $user->createPassword($password);
        $verify->delete();
        if (! $user->save()) {
            return $response->json(['message' => '注册失败'], 422);
        }
        //用户信息插入base_user表
      	$str = 'qwertyuiopasdfghjklzxcvbnm1234567890';
      	$key = substr(str_shuffle($str) ,0,16);
        $data = [
            'key'      => $key,
            'password' => app('hash')->make($password),
            'name'     => $name,
            'tel'      =>$phone,
            'email'     =>$email,
            'type'     =>1,
            'sns_uid'  =>$user->id,
        ];
        $base_user_id = BaseUser::insertGetId($data);
//
        /***
         *
         * 创建用户ipc钱包
         *
         **/
        //分配钱包地址
        
        $new_address['user_id'] = $base_user_id;
        $new_address['status'] = 1;
        DB::table('user_address')->where('id', $address->id)->update($new_address);
        //为新注册用户添加IPC钱包
        $new_wallet = [
            'user_id'          =>    $base_user_id,
            'type'             =>    1,
            'type_name'        =>    'IPC',
            'path'             =>    $address->path,
            'updated_at'       =>   date('Y-m-d H:i:s' , time())
        ];
        DB::table('token_wallet')->insert($new_wallet);
        /***
         *
         * 创建用户usdt钱包
         *
         **/
      	/**
        $usdt_address = DB::table('user_address')->where('status' , 0)->first();
        $new_usdt['user_id'] = $user->id;
        $new_usdt['status'] = 1;
        DB::table('user_address')->where('id', $address->id)->update($new_usdt);**/
        //为新用户添加usdt钱包
        $usdt_wallet = [
            'user_id' => $base_user_id,
            'type'    => 2,
            'type_name' => 'USDT',
            'path' => $address->usdt_path,
            'updated_at' =>date('Y-m-d H:i:s' , time()),
        ];
        DB::table('token_wallet')->insert($usdt_wallet);
        $user->roles()->sync($role->value);
        return $response->json([
            'token' => $auth->fromUser($user),
            'base_token' =>$this->base_token($base_user_id),
            'ttl' => config('jwt.ttl'),
            'refresh_ttl' => config('jwt.refresh_ttl'),
        ])->setStatusCode(201);
    }
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