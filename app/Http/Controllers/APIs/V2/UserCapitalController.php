<?php
namespace Zhiyi\Plus\Http\Controllers\APIs\V2;

use Illuminate\Http\Request;
use Zhiyi\Plus\Http\Controllers\Controller;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseContract;
use Zhiyi\Plus\Models\TokenWallet as TokenWalletModel;
use Zhiyi\Plus\Models\ChargeLog as ChargeLogModel;
use Zhiyi\Plus\Models\UserAddress as UserAddressModel;
use Zhiyi\Plus\Models\Token as TokenModel;
use DB;
class UserCapitalController extends Controller
{
  
  	/**
    *	50001:用户没有消费记录
    *
    **/
    //用户资产列表
    public function show_all(Request $request, ResponseContract $response, TokenWalletModel $model ,
                             UserAddressModel $UserAddressModel,TokenModel $token)
    {
        $user = $request->user();
        //获取用户钱包信息
        $wallet = $model->join('token' , function($token_tab){
            $token_tab->on('token_wallet.type_name' , '=' ,  'token.token_name');
        })->where('user_id' , $user->id)->get()->toArray();
        //获取钱包类型
        $type_name = $model->select('type_name')->where('user_id' , $user->id)->get()->toArray();
        $type_now = [];
        foreach ($type_name as $val) {
            $type_arr = array_push($type_now ,$val['type_name']);
        }
        //查找未生成钱包的token类型
        $token_nowollet = $token->whereNotIn('token_name' , $type_now)->get()->toArray();
        $address = $UserAddressModel->select('path')->where('status' , 1)->where('user_id' , $user->id)->get()->toArray();
        //获取用户未生成钱包的token
        //return $response->json($user->id);
        $wallet_no = $this->doWalletNo($address , $token_nowollet);
        //生成用户信息
        $wall['make_wallet'] = $wallet;
        $wall['no_make_wallet'] = $wallet_no;
        $wall['user'] = $user;
        return $response->json($wall)->setStatusCode(200);
    }
    //用户资产详情
    public function show_detail(Request $request, ResponseContract $response,
                                TokenWalletModel $model,ChargeLogModel $ChargeLogModel,UserAddressModel $UserAddressModel)
    {
        $user = $request->user();
        //获取资产类型
        $type_name = $request->get('type');
        //查询钱包类型
        $token_name = DB::table('token')->where('token_name' , $type_name)->first();
        $type = $token_name->id;
        //获取用户id
        $user_id = $request->user()->id;
        //获取用户余额
        $wallet = $model->select('balance')
            ->where('user_id' , $user_id)
            ->where('type' , $type)->first();
        //获取用户资产消费信息
        $list = $ChargeLogModel->where(['user_id' => $user_id , 'type' => $token_name->id])->where('status' , '>' , 0)->orderBy('created_time','desc')->get();
        //分配没有钱包的token地址
        if(empty($list[0])){
            $address = $UserAddressModel->select('path')->where('user_id' , 0)->where('status' , 0)->first();
            return $response->json(['message' => '没有消费记录' , 'code' => 50001]);  
        }
        //获取用户本地地址
        $address = $UserAddressModel->select('path')->where('user_id' , $user_id)->first();
        $list['balance'] = $wallet->balance;
        $list['path'] = $address->path;
        return $response->json($list)->setStatusCode(200);
    }
    //处理未生成钱包的token
    public function doWalletNo($address , $token_nowallet){
        $no_pass_token = [];
        foreach ($token_nowallet as $key => $value) {
            $value['pass'] = $address[0]['path'];
            array_push($no_pass_token , $value);
        }
        return $no_pass_token;
    }
}

