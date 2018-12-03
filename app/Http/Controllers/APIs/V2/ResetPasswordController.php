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
use Zhiyi\Plus\Models\BaseUser;
use Zhiyi\Plus\Models\User as UserModel;
use Zhiyi\Plus\EaseMobIm\EaseMobController;
use Zhiyi\Plus\Models\VerificationCode as VerificationCodeModel;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;
use Log;
class ResetPasswordController extends Controller
{
    /**
     * Reset password.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
  	//权证平台修改用户密码
    public function setBasePassword(Request $request, UserModel $user ,ResponseFactoryContract $response){
      	$user = $request->user();
        if (! $user->verifyPassword($request->old_password)) {
            return $response->json('账户密码错误', 422);
        }
        if($request->password != $request->password_confirmation){
            return $response->json('输入密码不一致，从新输入', 422);
        }
        if (!empty($request->password)){
            $password = $user->createPassword($request->password);
            $res = $user->where('id', $user->id) ->update(['password' => $password->password]);
            BaseUser::where('sns_uid' , $user->id)->update(['password' => $password->password]);
            Log::info($res.'--------'.$password);
            return $response->json('修改成功', 200);
        }
    }
    public function reset(Request $request, ResponseFactoryContract $response)
    {

        $user = $request->user();
        // 用户未设置密码时，只需设置新密码
        if ($user->password === null) {
            return $this->setPassword($request, $user);
        }

        $this->validate($request, $this->resetRules(), $this->resetValidationErrorMessages());


        if (! $user->verifyPassword($request->input('old_password'))) {
            return $response->json(['old_password' => ['账户密码错误']], 422);
        }
		
        $oldPwdHash = $user->getImPwdHash();
        $user->createPaypass($request->input('password'));
        $user->save();
      	Log::info($user->password);
      	//BaseUser::where('sns_uid' , $user->id)->update(['password' => $user->password]);
		   
		
        // 环信重置密码
        $easeMob = new EaseMobController();
        $request->user_id = $user->id;
        $request->old_pwd_hash = $oldPwdHash;
        $easeMob->resetPassword($request);

        return $response->make('', 204);
    }
    //设置用户交易密码
    public function payreset(Request $request , ResponseFactoryContract $response) {
        $user = $request->user();
        //用户未设置交易密码时，直接设置新密码
        if(empty($user->paypass)){
            return  $this->setPaypass($request , $user);
        } else {
            //用户已经设置密码则校队密码
            if (! $user->verifyPaypass($request->post('paypass'))) {
                return $response->json(['paypass' => ['账户密码错误']], 422);
            }
            //重新设置交易密码
            return  $this->setNewPaypass($request , $user);
        }
    }

    /**
     * Get reset validateion rules.
     *
     * @return array
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function resetRules(): array
    {
        return [
            'old_password' => 'required|string',
            'password' => 'required|string|different:old_password|confirmed',
        ];
    }


    /**
     * Get reset validation error messages.
     *
     * @return array
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function resetValidationErrorMessages(): array
    {
        return [
            'old_password.required' => '请输入账户密码',
            'old_password.string' => '密码必须是字符串',
            'password.required' => '请输入新密码',
            'password.string' => '密码必须是字符串',
            'password.different' => '新密码和旧密码相同',
            'password.confirmed' => '确认输入的新密码不一致',
        ];
    }

    /**
     * Set new password.
     *
     * @author bs<414606094@qq.com>
     * @param  Request $request
     * @param  User  $user
     */
    public function setPassword(Request $request, UserModel $user)
    {
        $this->validate($request, [
            'password' => 'required|string|confirmed',
        ], $this->resetValidationErrorMessages());

        $user->createPassword($request->input('password'));
        $user->save();

        return response()->make('', 204);
    }
    //交易密码入库
    public function setPaypass(Request $request, UserModel $user){
        if (!empty($request->post())){
            $pass = $user->createPaypass($request->post('paypass'));
            if (!empty($pass)){
                $paypass = $pass->paypass;
                $res = $user->where('id', $user->id) ->update(['paypass' => $paypass]);
                return $res;
            }
        }
    }
    //修改交易密码
    public function setNewPaypass(Request $request, UserModel $user){
        if (!empty($request->post())){
            $repass = $user->createRepass($request->post('repass'));
            if (!empty($repass)){
                $paypass = $repass->repass;
                $res = $user->where('id', $user->id) ->update(['paypass' => $paypass]);
                return $res;
            }
        }
    }

    /**
     * Retrueve user password.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     * @param \Zhiyi\Plus\Models\VerificationCode $verificationCodeModel
     * @param \Zhiyi\Plus\Models\User $userModel
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function retrieve(Request $request,
                             ResponseFactoryContract $response,
                             VerificationCodeModel $verificationCodeModel,
                             UserModel $userModel)
    {
        if ($request->input('phone') && $request->input('email')) {
            return $response->json(['message' => ['非法请求']], 400);
        }

        $this->validate($request, [
            'verifiable_type' => 'required|in:mail,sms',
            'verifiable_code' => 'required',
            'phone' => 'required_unless:verifiable_type,mail|cn_phone|exists:users,phone',
            'email' => 'required_unless:verifiable_type,sms|email|exists:users,email',
            'password' => 'required|string',
        ]);

        $field = $request->input('phone') ? 'phone' : 'email';
        $user = $userModel->where($field, $account = $request->input($field, 'password'))->first();
        $verificationCode = $verificationCodeModel->where('channel', $request->input('verifiable_type'))
            ->where('code', $request->input('verifiable_code'))
            ->where('account', $account)
            ->first();

        if (! $verificationCode) {
            return $response->json(['message' => ['验证码错误或者已失效']], 422);
        }
        $oldPwdHash = $user->getImPwdHash();

        $user->createPassword($request->input('password'));
        $user->save();

        // 环信重置密码
        $easeMob = new EaseMobController();
        $request->user_id = $user->id;
        $request->old_pwd_hash = $oldPwdHash;
        $easeMob->resetPassword($request);

        $verificationCode->delete();

        return $response->make('', 204);
    }
}
