<?php
/**
 * Created by PhpStorm.
 * User: liyi
 * Date: 2018/8/7
 * Time: 上午10:51
 */

namespace Zhiyi\Plus\Http\Controllers\APIs\V2;

use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\ResponseFactory;
use Symfony\Component\HttpFoundation\Session\Session;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseContract;
class PhoneidController extends Controller{
    public function index(Request $request, ResponseContract $response)
    {
        $session = new Session;
        $phoneid['phoneid'] = $session->get("phioneid");
        return $response->json($phoneid)
            ->setStatusCode(200);
    }
}