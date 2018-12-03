<?php

namespace Zhiyi\Plus\Http\Controllers\APIs\V2;

use Illuminate\Http\Request;
use Zhiyi\Plus\Models\ChargeLog;
use Zhiyi\Plus\Models\TokenWallet;
use Zhiyi\Plus\Models\WithdrawalsApply as WithdrawalsModel;
use Zhiyi\Plus\Models\Certification as CertificationModel;
use Zhiyi\Plus\Models\ChargeLog as ChargeLogModer;
use Zhiyi\Plus\Http\Controllers\Controller;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseContract;
use DB;
class PayWalletController extends Controller
{
  
  	/**
    *错误码
    *	40001：未设置支付密码
    *	40002：支付密码错误
    **/
    //用户提现
    function withdraw(Request $request , ResponseContract $response ,TokenWallet $tokenmodel,
                      WithdrawalsModel $withdrawmodel , ChargeLogModer $chargemodel, CertificationModel $certifications){
        //获取提现表相关信息
      	date_default_timezone_set("Asia/Shanghai");
        $user = $request->user();
        $return = $request->post();
        $created_time  = date('Y-m-d H:i:s' , time());
        $user_id = $user->id;
        $symbol= $return['type'];
        $address = $return['address'];
        //查询钱包类型
        $token_type = DB::table('token')->where('token_name' , $symbol)->first();
    	$type = $token_type->id;
      	//判断用户是否设置支付密码
		if(empty($user->paypass)){
        	return $response->json(['message' => '未设置支付密码' , 'error_code' => '40001']);
        }
        //用户密码验证
        if (! $user->verifyPaypass($return['paypass'])) {
            return $response->json(['message' => '支付密码错误' ,  'error_code' => '40002']);
        }
        //查看是否认证
        $user_certifi = $certifications->where('user_id' , $user_id)->where('status' , 1)->get();
        if (empty($user_certifi[0])){
            return response()->json(['message' => '用户未认证' , 'status' => false]);
        }
        //判断参数
        if (empty($return['balance']) || empty($type)) {
            return $response->json(['message' => '参数错误' , 'status' => false]);
        }
        //查找用户余额
        $total = $tokenmodel->select('balance')->where('user_id' , $user_id)
            ->where('type' , $type)->get();
        //判断是否创建充值类型钱包
        if(empty($total[0])){
            return $response->json(['message' => '您还未拥有钱包' , 'status' => false]);
        }
        $total_balance = $total[0]['balance'];
        //判断余额是否不足
        if ($total_balance < $return['balance']){
            return $response->json(['message' => '余额不足' , 'status' => false]);
        }
        //return $response->json($return);
        //修改提现后的余额
        $total = $tokenmodel->where('user_id' , $user_id)
            ->where('type' , $type)->decrement('balance' , $return['balance']);

        //获取记录信息入库字段数组
        $charge_array = [
            'category' => 0,
            'type' => $type,
            'created_time' => $created_time,
            'action_type' => 5,
            'user_id' =>$user_id,
            'less_number' =>$return['balance']

        ];
        //记录信息入库
        $charge_id = $chargemodel->insertGetId($charge_array);
        if (isset($charge_id)){
            //获取钱包提现入库字段数组
            $withdraw_array = [
                'type' => $type,
                'balance' =>$return['balance'],
                'user_id' =>$user_id,
                'address' =>$address,
                'status' =>0,
                'created_time' =>$created_time,
                'log_id' =>$charge_id,
                'token_symbol' =>$symbol
            ];
            //钱包提现信息入库
            $withdraw_id = $withdrawmodel->insertGetId($withdraw_array);
            if (isset($withdraw_id)){
                return $response->json(['message' => '提现成功' , 'status' => true]);
            } else {
                return $response->json(['message' => '平台繁忙，请稍后提现' , 'status' => false]);
            }
        }
    }
    //用户充值
    public function userRecharge(ResponseContract $response, Request $request)
    {
      	date_default_timezone_set("Asia/Shanghai");
        //接收数据
        $user = $request->user();
        //判断用户是否认证过
        if (!empty(CertificationModel::where([
            'user_id'   =>    $user->id,
            'status'    =>    1
        	])->get())) {
            $data = $request->input();
            $time = date('Y-m-d H:m:s', time());
            if (empty($data['money'])) return $response->json(['message' => '请输入数额' , 'status' => false]);
            //查询钱包类型名
            $token_name = DB::table('token')->where('token_name' , $data['type'])->first();
            //判断用户是否有该币种的钱包
            $isset_wallte = DB::table('token_wallet')->where([
                'user_id' => $user->id,
                'type_name' => $data['type']
            ])->first();
            //查询用户token信息
            $address = DB::table('user_address')->where('user_id', $user->id)->where('status' , 1)->first();
            if (empty($isset_wallte))
            {
                //为用户添加对应token钱包
                $new_wallet = [
                    'user_id' => $user->id,
                    'type' => $token_name->id,
                    'type_name' => $token_name->token_name,
                    'path' => $address->path,
                ];
                DB::table('token_wallet')->insert($new_wallet);
            }
            //增加记录
            $charge_log = [
                'category' => 0,
                'add_number' => $data['money'],
                'type' => $token_name->id,
                'action_type' => 6,//充值
                'user_id' => $user->id,
                'created_time' =>date('Y-m-d H:i:s' , time()),
            ];
            $log_return = DB::table('charge_log')->insert($charge_log);
            if ($log_return) {
                //极光推送
                $token = DB::table('token')
                    ->where('token_name', $token_name->token_name)
                    ->first();
                //return $response->json($token->precision);
                $jpush['alias'] = $data['device_id'];
                $jpush['thirdAddress'] = $address->path;
                $jpush['thirdAmount'] = $data['money'];
                $jpush['type'] = $token_name->token_name;
                //$jpush['type'] = 'IPC';
                $jpush['thirdTokenAccur'] = $token->precision;
                $jpush_pub = new JPushController();
                $suss_jupus = $jpush_pub->alias($jpush);
         
                return $response->json(['message' => '充值成功' , 'status' => true]);
            } else {
                return $response->json(['message' => '充值失败' , 'status' => false]);
            }
        } else {
            return $response->json(['message' => '用户未认证' , 'status' => false]);
        }
    }
}

