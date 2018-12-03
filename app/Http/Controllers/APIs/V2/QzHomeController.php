<?php
/**
 * Created by PhpStorm.
 * User: liyi
 * Date: 2018/9/12
 * Time: 上午11:34
 */

namespace Zhiyi\Plus\Http\Controllers\APIs\V2;

use Illuminate\Http\Request;
use Zhiyi\Plus\Models\Token;
use Zhiyi\Plus\Models\QzRmb;
use Zhiyi\Plus\Models\QzOkdata;
use Zhiyi\Plus\Models\QzOrder as Qzorder;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Plus\Models\QzProduct as QzProduct;
use Zhiyi\Plus\Models\QzBanner as QzBanner;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseContract;
use Log;

class QzHomeController extends Controller
{
    protected $ch = null;
    protected $jhKey = 'e6b854cef1d9951f3c674539ef0381a8';
    protected $url = 'http://web.juhe.cn:8080/finance/exchange/rmbquot';
    protected $okurl = 'https://www.okb.com/api/v1/ticker.do?symbol=ipc_usdt';
    //权证首页接口
    public function index(Request $request, ResponseContract $response)
    {
      	$token = $request->token;
      	Log::info('首页token：'.$token);
        //获取banner图
        $banner = QzBanner::get()->toArray();
        //获取ipc实时价格
        $now_ipc_price['price'] = $this->getIpc();
        //获取期权信息
        $option = QzProduct::orderBy('pay_end_time', 'desc')->where('withdraw' , 2)->get()->toArray();
        $getOption = $this->doOption($option);
        $reutrn = array_merge($getOption,$banner,$now_ipc_price);
        $reutrn['token'] = $token;
        return $response->json($reutrn);
    }
    //用户购买产品详情
    public function productDetail(Request $request, ResponseContract $response){
        //获取产品id
        $product_id = $request->input('id');
        $option = QzProduct::where('id' , $product_id)->get();
        $token_price = $this->price($product_id);
        //获取产品单价
        $option[0]['token_price'] = $token_price;
        return $response->json($option);
    }
  	//用户订单详情
    public function orderDetail(Request $request, ResponseContract $response){
        //获取产品id
        $order_id = $request->input('id');
        $option = Qzorder::where('qz_order.id' , $order_id)
          ->select('qz_order.status','qz_product.title','qz_order.product_id','qz_order.token_amount','qz_order.buy_time','qz_product.description','qz_product.exercise_start_time','qz_product.exercise_end_time')
          ->leftJoin('qz_product' , 'qz_product.id' , '=' , 'qz_order.product_id')->get();
      	return $response->json($option);
    }
    //计算合约单价
    public function price($id){
        //获取后台提交的产品信息
        $proportion = QzProduct::where('id' , $id)->first();
        //获取后台发行价格
        $ipc = $proportion->issue_price;
        //获取每份产品基数（张）
        //$num = $proportion->min_number*1000;
      	$num = 1000;
        //获取期权比例
        $first_num = $proportion->contract_first;
        $second_num = $proportion->constarct_second;
      	//return $num;
        //得到对应一份期权对应的ipc
        $ipc_price = ($second_num/$first_num)*$ipc*$num;
        //换算成对应的usdt
        $usdt = $ipc_price/($this->exchangRate());
        return $usdt;
    }
    //处理期权信息
    protected function doOption($option){
        $now = [];
        foreach ($option as $val) {
            $now['option'][] = $val;
        }
        return $now;
    }
    //获取usdt与ipc兑换比例（即美元兑换人民币汇率）
    protected function exchangRate(){
        $price = QzRmb::select('price')->orderBy('create_time' , 'desc')->first();
        $usdtExecut = $price->price;
        return $usdtExecut;
    }
    //curl网络请求
    protected function curlGet($url,$method,$post_data = 0){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($method == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }elseif($method == 'get'){
            curl_setopt($ch, CURLOPT_HEADER, 0);
        }
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
    //获取ipc实时价格
    protected function getIpc(){
        $ok_detail = QzOkdata::orderBy('id', 'desc')->limit(1)->first();
        $ipc_price = $ok_detail->last;
        $rmb_ipc = $ipc_price*$this->exchangRate();
      	return $rmb_ipc;
    }
}