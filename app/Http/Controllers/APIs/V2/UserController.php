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

use RuntimeException;
use Tymon\JWTAuth\JWTAuth;
use Zhiyi\Plus\Models\User;
use Illuminate\Http\Request;
use Zhiyi\Plus\Models\CommonConfig;
use Zhiyi\Plus\Models\VerificationCode;
use Zhiyi\Plus\Http\Requests\API2\StoreUserPost;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;
use DB;

class UserController extends Controller
{
    /**
     * Get all users.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     * @param \Zhiyi\Plus\Models\User $model
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function index(Request $request, ResponseFactoryContract $response, User $model)
    {
        $user = $request->user('api');
        $ids = array_filter(explode(',', $request->query('id', '')));
        $limit = max(min($request->query('limit', 15), 50), 1);
        $order = in_array($order = $request->query('order', 'desc'), ['asc', 'desc']) ? $order : 'desc';
        $since = $request->query('since', false);
//        $platform = $request->query('platform');
        $name = $request->query('name', false);
        $phone = $request->query('phone' , false);
        $users = $model->when($since, function ($query) use ($since, $order) {
            return $query->where('id', $order === 'asc' ? '>' : '<', $since);
        })->when($phone, function ($query) use ($phone) {
            return $query->where('phone', $phone);
        })->when($name, function ($query) use ($name) {
            return $query->where('name', 'like', sprintf('%%%s%%', $name));
        })->when(! empty($ids), function ($query) use ($ids) {
            return $query->whereIn('id', $ids)->withTrashed();
        })->limit($limit)
          ->orderby('id', $order)
          ->get();

        return $response->json($model->getConnection()->transaction(function () use ($users, $user) {
            return $users->map(function (User $item) use ($user) {
                $item->following = $item->hasFollwing($user->id ?? 0);
                $item->follower = $item->hasFollower($user->id ?? 0);
                $item->blacked = $user ? $user->blacked($item) : false;
                return $item;
            });
        }))->setStatusCode(200);
    }
    /**
     *  Get user.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Zhiyi\Plus\Models\User $user
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function show(Request $request, int $user)
    {
        $user = User::withTrashed()
            ->where('id', $user)
            ->firstOrFail();
        // 我关注的处理
        $this->hasFollowing($request, $user);
        // 处理关注我的
        $this->hasFollower($request, $user);
        // 处理黑名单
        $this->hasBlacked($request, $user);

        return response()->json($user, 200);
    }

    /**
     * 创建用户.
     *
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function store(StoreUserPost $request, ResponseFactoryContract $response, JWTAuth $auth)
    {
        $phone = $request->input('phone');
        $email = $request->input('email');
        $name = $request->input('name');
        $password = $request->input('password');
//        $ask_id = $request->input('ask_id');
        $channel = $request->input('verifiable_type');
        $code = $request->input('verifiable_code');
        //$platfrom = $request->input('platfrom') ? $request->input('platfrom') : '';
        $role = CommonConfig::byNamespace('user')
            ->byName('default_role')
            ->firstOr(function () {
                throw new RuntimeException('Failed to get the defined user group.');
            });
        $verify = VerificationCode::where('account', $channel == 'mail' ? $email : $phone)
            ->where('channel', $channel)
            ->where('code', $code)
            ->orderby('id', 'desc')
            ->first();

        if (! $verify) {
            return $response->json(['message' => ['验证码错误或者已失效']], 422);
        }

        $user = new User();
        $user->phone = $phone;
        $user->email = $email;
        $user->name = $name;
//        $user->askid = $ask_id;
        $user->createPassword($password);

        $verify->delete();
        if (! $user->save()) {
            return $response->json(['message' => '注册失败'], 500);
        }

        //分配钱包地址
        $address = DB::table('user_address')->where('status', 0)->first();
        $new_address['user_id'] = $user->id;
        $new_address['status'] = 1;
        DB::table('user_address')->where('id', $address->id)->update($new_address);
        //为新注册用户添加IPC钱包
        $new_wallet = [
            'user_id'          =>    $user->id,
            'type'             =>    1,
            'type_name'        =>    'IPC',
            'path'             =>    $address->path,
            'updated_at'       =>   date('Y-m-d' , time())
        ];
        DB::table('token_wallet')->insert($new_wallet);

        $user->roles()->sync($role->value);
        return $response->json([
            'token' => $auth->fromUser($user),
            'ttl' => config('jwt.ttl'),
            'refresh_ttl' => config('jwt.refresh_ttl'),
        ])->setStatusCode(201);
    }

    /**
     * Handle the state of my follow status.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Zhiyi\Plus\Models\User &$user
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function hasFollowing(Request $request, User &$user)
    {
        $currentUser = $request->user('api');
        $hasUser = (int) $request->query('following', $currentUser ? $currentUser->id : 0);
        $user['following'] = $user->hasFollwing($hasUser);
    }

    /**
     * Verify that I am followed.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Zhiyi\Plus\Models\User &$user
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function hasFollower(Request $request, User &$user)
    {
        $currentUser = $request->user('api');
        $hasUser = (int) $request->query('follower', $currentUser ? $currentUser->id : 0);
        $user['follower'] = $user->hasFollower($hasUser);
    }

    protected function hasBlacked(Request $request, User &$user)
    {
        $currentUser = $request->user('api');
        $user['blacked'] = $currentUser ? $currentUser->blacked($user) : false;
    }
}
