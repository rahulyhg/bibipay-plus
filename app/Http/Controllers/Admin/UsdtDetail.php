<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/9 0009
 * Time: 16:10
 */

namespace Zhiyi\Plus\Http\Controllers\Admin;

use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Plus\Models\UsdtDetail as UsdtDetailModel;

class UsdtDetail extends Controller
{
    //公账信息
    public function account(UsdtDetailModel $UsdtDetailModel)
    {
        $data = $UsdtDetailModel->first();
        return response()->json($data)->setStatusCode(200);
    }
}