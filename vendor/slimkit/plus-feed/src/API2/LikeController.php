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

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\API2;

use Illuminate\Http\Request;
use Zhiyi\Plus\Services\Push;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Plus\Models\UserCount as UserCountModel;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseContract;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed as FeedModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedReward as FeedRewardModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\TokenWallet as TokenWalletModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\ChargeLog as ChargeLogModel;

class LikeController extends Controller
{
    /**
     * Get feed likes.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $feed
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */

    public function index(Request $request, ResponseContract $response, FeedModel $feed)
    {
        $limit = $request->query('limit', 15);
        $after = $request->query('after', false);
        $userID = $request->user('api')->id ?? 0;
        $likes = $feed->likes()
            ->whereHas('user')
            ->with(['user' => function ($query) {
                return $query->withTrashed();
            }])
            ->when($after, function ($query) use ($after) {
                return $query->where('id', '<', $after);
            })
            ->limit($limit)
            ->orderBy('id', 'desc')
            ->get();
        return $response->json(
            $feed->getConnection()->transaction(function () use ($likes, $userID) {
                return $likes->map(function ($like) use ($userID) {
                    if (! $like->relationLoaded('user')) {
                        return $like;
                    }

                    $like->user->following = false;
                    $like->user->follower = false;

                    if ($userID && $like->user_id !== $userID) {
                        $like->user->following = $like->user->hasFollwing($userID);
                        $like->user->follower = $like->user->hasFollower($userID);
                    }

                    return $like;
                });
            })
        )->setStatusCode(200);
    }

    /**
     * 用户点赞接口.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $feed
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function store(Request $request, ResponseContract $response, FeedModel $feed)
    {
        $user = $request->user();
        $tid = $feed->like($user)->likeable_id;
        //查看是否为设置佣金
        $feed_detail = $feed->select('feed_money','pay_praise')->where('user_id' , $user->id)->where('id' , $tid)->get()->toArray();
        $prise_mes = '';
      	if($feed_detail){
        	if ($feed_detail[0]['feed_money'] == 1 && $feed_detail[0]['pay_praise'] == 2) {
              $reward = new FeedRewardModel();
              $rew_praise = $reward->select('price' , 'count' , 'action_type' , 'type')->
              where('user_id' , $user->id)->where('tid' , $tid)->where('action_type' , 2)->get()->toArray();
              //判断用户是否已经点赞
              $chargeLog = new ChargeLogModel();
              $isset = $chargeLog->where('user_id' , $user->id)->where('tid' , $tid)
                  ->where('type' , $rew_praise[0]['type'])->where('action_type' , 2)->get()->toArray();
              if (empty($isset)){
                  if ($rew_praise[0]['count'] > 0){
                      $up_count = $reward->where('user_id' , $user->id)->where('tid' , $tid)->where('action_type' , 2)
                          ->decrement('count' , $rew_praise[0]['price']);
                      $tokenWallet = new TokenWalletModel();
                      //此版本IPC直接写死了，后面上其他token需要修改type值
                      $upd_Wallet = $tokenWallet->where('user_id' , $user->id)->where('type' , 1)->increment('balance' , $rew_praise[0]['price']);
                      $upd_Wallet = $tokenWallet->where('user_id' , $user->id)->where('type' , 1)->increment('total_income' , $rew_praise[0]['price']);
                      //将日志信息插入log表
                      $ins = [
                          'category' => 0,
                          'add_number' => $rew_praise[0]['price'],
                          'type' => $rew_praise[0]['type'],
                          'created_time' => date('Y-m-d' , time()),
                          'action_type' => 2,
                          'user_id' => $user->id,
                          'tid' => $tid
                      ];
                      $ins_chargelog = $chargeLog->insertGetId($ins);
                  }
              } else {
                  $prise_mes = '重复点赞不会有奖励哦';
              }
        	}
        }
        

        if ($feed->user_id !== $user->id) {
            // 添加被赞的未读数
            $feed->user->unreadCount()->firstOrCreate([])->increment('unread_likes_count', 1);
            // 新未读统计 1.8启用
            $userLikedCount = UserCountModel::firstOrNew([
                'type' => 'user-liked',
                'user_id' => $feed->user->id,
            ]);
            $userLikedCount->total += 1;
            $userLikedCount->save();
            app(Push::class)->push(sprintf('%s 点赞了你的动态', $user->name), (string) $feed->user->id, ['channel' => 'feed:digg']);
        }
        return $response->json(['message' => '操作成功' , 'prise_mes' => $prise_mes])->setStatusCode(201);
    }

    /**
     * 取消动态赞.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $feed
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function destroy(Request $request, ResponseContract $response, FeedModel $feed)
    {
        $user = $request->user();
        $feed->unlike($user);

        return $response->json(['message' => '操作成功'])->setStatusCode(204);
    }
}
