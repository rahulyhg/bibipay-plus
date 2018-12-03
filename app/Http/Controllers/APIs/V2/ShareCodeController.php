<?php
/**
 * Created by PhpStorm.
 * User: liyi
 * Date: 2018/8/31
 * Time: 下午2:30
 */
namespace Zhiyi\Plus\Http\Controllers\APIs\V2;
use Log;
use Zhiyi\Plus\Models\ShareCode as ShareCode;
use Illuminate\Http\Request as Request;
use Illuminate\Http\User as User;
use SimpleSoftwareIO\QrCode\BaconQrCodeGenerator;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseContract;


class ShareCodeController extends Controller
{
    //生成邀请二维码
    public function index(Request $request , ResponseContract $response , ShareCode $shareCode){
        $qrcode = new BaconQrCodeGenerator;
        $user_id = $request->uid;
        $user_id = hashid_encode($user_id);
        $num = substr(time() ,6, 12);
        //字符串前五位为id转码
        $pic_add = $user_id . hashid_encode($num);
        $pic_name = $pic_add.'code'.'.png';
        $url = 'http://192.168.1.54:8080/#/profile/inviteFriends';
        $res = $qrcode->format('png')->size(500)->color(255,0,255)->
        backgroundColor(255,255,0)->merge('/public/qrcodes/logo.jpg',.15)->
        generate($url,public_path('qrcodes/'.$pic_name));

//        $pic_address = env('APP_URL').'/qrcodes/'.$pic_name;
        $pic_address = 'http://192.168.1.137:8080/'.'qrcodes/'.$pic_name;
        $fid = $request->uid;
        $data = [
            'fid' => $fid,
            'code' => $pic_add
        ];
        $shareCode->insertGetId($data);
        Log::info(['二维码地址: '.$pic_address , '转码后的邀请id:'.$user_id , '邀请id:'.$request->uid]);
        return $response->json(['code_address' => $pic_address , 'code_name' => $pic_add]);
    }

    //验证手机
    public function getPay(Request $request , ResponseContract $response , ShareCode $shareCode , User $userModel){
        //获取相关信息
        $code_id = $request->uid;
        $user_phone = $request->phone;
        $code = $request->code;
        $code_phone = $request->phone;
        $data = [
            'phone' => $code_phone,
        ];
        $isset_phone = $shareCode->where('phone' , $code_phone)->where('fid' , $code_id)->first();
        if(!empty($isset_phone)){
            return $response->json(['message' => '手机号已被注册']);
        }
        $get_id = $shareCode->update($data)->where('code' , $code)->where('fid' , $code_id);
        Log::info(['二维码id：'.$get_id , '用户提交手机号码：'.$code_phone , '用户对应邀请码：'.$code]);
        if (!$get_id){
            return $response->json(['status' => false]);
        }
        return $response->json(['status' => true]);
    }
}





