<?php
/**
 * Created by PhpStorm.
 * User: liyi
 * Date: 2018/9/11
 * Time: 下午8:12
 */

namespace Zhiyi\Plus\Http\Controllers\APIs\V2;

use Log;
use Illuminate\Http\Request;
use Zhiyi\Plus\Models\Token;
use Zhiyi\Plus\Models\BaseUser;
use Zhiyi\Plus\Models\UserAddress;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Plus\Models\QzOrder as Qzorder;
use Zhiyi\Plus\Models\ChargeLog as logModel;
use Zhiyi\Plus\Models\QzProduct as productModel;
use Zhiyi\Plus\Models\TokenWallet as tokenModel;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseContract;

class QzBuyController extends Controller
{

    /**
     * @param Request $request
     * @param ResponseContract $response
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Http\JsonResponse|null|object|static
     *
     *
     * 回复码：
     *     40010：已到达最大订购量
     *     40020：余额不足
     *	   40030：交易已结束
     */

    //权证购买接口
    public function buyGoods(Request $request, ResponseContract $response , Qzorder $order , tokenModel $token)
    {
        $user = $request->user();
        $product_id = $request->input('id');
        $product_name = $request->input('title');
        $token_amount = $request->input('token_amount');  //数量
        $token_name = 'USDT';
        $token_price = $request->input('token_price');  //单价
        $all_money = $token_price*$token_amount;
      	$product = productModel::where('id' , $product_id)->first();
      	//查询用户
      	$base_user = BaseUser::where('sns_uid' , $user->id)->first();
      	//获取用户钱包信息
        $wallet = $token->where('user_id' , $base_user->id)->where('type_name' , $token_name)->first();
      	//判断是否超过过期时间
      	if(time() > $product->end_time){
        	return $response->json(['message' => '交易已结束','code' => '40030']);
        }
        //查看是否超过购买限制
        $buyRestrict = $this->doRestrict($user->id , $product_id , $token_amount);
      	//return $response->json($buyRestrict);
        if ($buyRestrict == false){
            return $response->json(['message' => '已到达最大订购量','code' => '40010']);
        }
      	//查找token类型
        $token_type = Token::select('id')->where('token_name' , $token_name)->first(); 
      	//判断用户入口
      	if(empty($request->input('door_type'))){
        	//判断用户余额是否充足
            if(isset($wallet->id)) {
                if($wallet->balance < $all_money){
                    //创建订单
                    $order_data = [
                        'user_id' => $user->id,
                        'product_id' => $product_id,
                        'product_name' =>$product_name,
                        'status' => 0,
                        'token_price' => $token_price,
                        'token_amount' => $token_amount,
                        'token_type' => $token_type->id,
                        'create_time' => time(),
                        'buy_time' =>time(),
                      	'exercise_time' => $product->exercise_end_time
                    ];
                    $orderId = $order->insertGetId($order_data);
                    return $response->json(['message' => '余额不足','code' => '40020']);
                }
            }
        }
      	if($wallet->balance < $all_money){
        	return $response->json(['message' => '余额不足','code' => '40020']);
        }
        //创建订单
        $order_data = [
            'user_id' => $user->id,
            'product_id' => $product_id,
            'product_name' =>$product_name,
            'status' => 1,
            'token_price' => $token_price,
            'token_amount' => $token_amount,
            'token_type' => $token_type->id,
            'create_time' => time(),
            'buy_time' =>time(),
          	'exercise_time' => $product->exercise_end_time
        ];
        $orderId = $order->insertGetId($order_data);
        //处理钱包余额
        $token_data = [
            'balance' => $wallet->balance - $all_money,
            'total_expenses' => $wallet->total_expenses + $all_money
        ];
        $token->where('user_id' , $base_user->id)->where('type' , $token_type->id)->update($token_data);
        //创建账户流水记录
        $log_data = [
            'user_id' => $base_user->id,
            'less_number' => $all_money,
            'type' => 2,
            'status' => 0,
            'created_time' => date('Y-m-d H:i:s' , time()),
            'action_type' => 7,
            'category' => 0
        ];
        logModel::insertGetId($log_data);
        $data = [
            'title' => $product->title,
            'exercise_time' => date('Y-m-d H:i:s' , $product->exercise)
        ];
        return $response->json(['message' => $data,'code' => '200']);
    }
    //获取充值token地址
    public function getAddress(Request $request , ResponseContract $response){
        $user = $request->user();
      	//查询用户
      	$base_user = BaseUser::where('sns_uid' , $user->id)->first();
        $token_address = UserAddress::where('user_id' , $base_user->id)->where('status' , 1)->first();
        return $response->json($token_address->usdt_path);
    }
    //判断用户是否超过购买限制
    protected function doRestrict($user_id , $product_id ,$token_amount){
        $product = productModel::where('id' , $product_id)->first();
        $max_num_option = $product->max_num_option;
        $allOrder = Qzorder::where('user_id' , $user_id)
          	->whereIn('status' , [1, 2, 4])
            ->where('product_id' , $product_id)->sum('token_amount');
      	$all_amount = $allOrder+$token_amount;
        if ($max_num_option >= $all_amount) {
            return true;
        }
        return false;
    }
}