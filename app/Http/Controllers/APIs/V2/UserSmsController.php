<?php

namespace Zhiyi\Plus\Http\Controllers\APIs\V2;

use Illuminate\Http\Request;
use Zhiyi\Plus\Models\User;
use Zhiyi\Plus\Http\Controllers\Controller;
use Qcloud\Sms\SmsSingleSender;
use Zhiyi\Plus\Models\VerificationCode;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseContract;

class UserSmsController extends Controller
{
    //验证码数字串
    protected $num = '1234567890';
    //短信入库模型
    protected $smsdata;
    public function send(Request $request , VerificationCode $verif,ResponseContract $response)
    {
      	if($request->status == 1){
        	$user = User::where('phone' , $request->phone)->first();
              if(!$user){
                  return $response->json(['message' => ['手机号还没有注册，请先注册']], 422);
              }
        }
        $templateId = config('qzsms.templateid');
        $smsSign =config('qzsms.smssign');
        $appid = config('qzsms.app_id');
        $appkey =config('qzsms.app_key');
        $params=array(substr(str_shuffle($this->num) , 0 , 6));
        $phoneNumbers = $request->phone;
        $ssender = new SmsSingleSender($appid, $appkey);
        $result = $ssender->sendWithParam("86", $phoneNumbers, $templateId,
            $params, $smsSign, "", "");  // 签名参数未提供或者为空时，会使用默认签名发送短信
        $rsp = json_decode($result);
        if($rsp->result == 0){
            $verif->account = $phoneNumbers;
            $verif->channel = 'sms';
            $verif->code = $params[0];
            $verif->state = 1;
            $verif->save();
            return $response->json(['message' => '验证码发送成功' , 'code' => '200']);
        } else {
            return $response->json(['message' => '验证码发送失败' , 'code' => '501']);
        }
    }
}