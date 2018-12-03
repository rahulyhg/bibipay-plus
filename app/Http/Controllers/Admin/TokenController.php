<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/19 0019
 * Time: 15:02
 */

namespace Zhiyi\Plus\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Plus\Models\Token as TokenModel;

class TokenController extends Controller
{
    //返回权证币种
    public function return_token(TokenModel $TokenModel)
    {
        $data = $TokenModel->select('token_name', 'id', 'poundage', 'status')->get();
//        $data['need'] = $TokenModel->where('status', 0)->select('token_name', 'id')->get();
//        $data['no_need'] = $TokenModel->where('status', 1)->select('token_name', 'id')->get();
        return response()->json($data);
    }

    //设置币种是否开启提现审核
    public function setting(Request $request, TokenModel $TokenModel)
    {
        $data = $request->post('TokenModel');
//        return $data;
//        $result = $TokenModel->updateBatch($data);
        foreach ($data as $value) {
            $TokenModel->where('id', $value['id'])->update(['status' => $value['status']]);
        }

//        if ($result) {
            return response()->json(['message' => '成功', 'code' => 200, 'data' => $data]);
//        }
//        return response()->json(['message' => '操作失败，稍后重试', 'code' => 201]);
    }
    
    //设置提现手续费
    public function poundage(Request $request, TokenModel $TokenModel)
    {
        $data = $request->post();
        $result = $TokenModel
            ->where(['id' => $data['id'], 'token_name' => $data['token_name']])
            ->update(['poundage' => $data['poundage']]);
        if ($result) {
            return response()->json(['id' => $data['id'], 'code' => 200, 'message' => '成功']);
        }
    }
}