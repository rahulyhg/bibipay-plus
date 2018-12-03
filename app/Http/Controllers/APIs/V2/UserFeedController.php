<?php
/**
 * Created by PhpStorm.
 * User: liyi
 * Date: 2018/7/18
 * Time: 下午2:30
 */
declare(strict_types=1);

namespace Zhiyi\Plus\Http\Controllers\APIs\V2;

use Illuminate\Http\Request;
use Zhiyi\Plus\Models\Feed as FeedModel;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseContract;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Repository\Feed as FeedRepository;

class UserFeedController extends Controller
{
    //获取喜欢的人列表
    public function getClickList(Request $request, ResponseContract $response, FeedModel $model)
    {
        $limit = $request->query('limit', 15);
        $after = $request->query('after', false);
        $user = $request->user();
        $feeds = $model->with(['feedable', 'user'])
            ->where('feed_from', $user->id)
            ->when($after, function ($query) use ($after) {
                return $query->where('id', '<', $after);
            })
            ->where('user_id', '!=', $user->id)
            ->limit($limit)
            ->orderBy('id', 'desc')
            ->get();

        if ($user->unreadCount !== null) {
            $user->unreadCount()->decrement('unread_likes_count', $user->unreadCount->unread_likes_count);
        }

        return $response->json($feeds)->setStatusCode(200);
    }
    //获取我的动态列表
    public function getMyCards(Request $request, ResponseContract $response, FeedModel $model,FeedRepository $repository)
    {

     	$limit = $request->query('limit', 15);
        $after = $request->query('after', false);
        $type = $request->query('type', 0);//获取帖子类型[普通/奖励帖]
        $user = $request->user();
        $feeds = $model->with(['feedable', 'user'])
            ->when($after, function ($query) use ($after) {
                return $query->where('id', '<', $after);
            })
            ->where('user_id', '=', $user->id)
            ->where('feed_money', $type)
            ->limit($limit)
            ->orderBy('id', 'desc')
            ->get();
        if ($user->unreadCount !== null) {
            $user->unreadCount()->decrement('unread_likes_count', $user->unreadCount->unread_likes_count);
        }
        //$feeds = [$image , $feeds];
        return $response->json($feeds)->setStatusCode(200);
    }
  	//帖子领取明细
    public function detailed(Request $request, ResponseContract $response, FeedModel $FeedModel, FeedReward $FeedReward)
    {
        $feed_id = $request->input('feed_id');
        $feed = $FeedModel->where('id', $feed_id)->first();//帖子详情
        $feed_reward = $FeedReward->where('tid', $feed_id)->get();
        if (!empty($feed_reward)) {
            $return['count'] = $feed->pay_count;//发帖子时充值总额

            //帖子余额计算
            $balance = 0;
            foreach ($feed_reward as $key => $value) {
                if ($value->action_type == 1) {//浏览
                    $return['pay_look'] = $value->price;//单价
                    $return['feed_view_count'] = $feed->feed_view_count;//浏览数
                    $balance = $balance+$value->count;
                }
                if ($value->action_type == 2) {//点赞
                    $return['pay_praise'] = $value->price;//单价
                    $return['like_count'] = $feed->like_count;//点赞数
                    $balance = $balance+$value->count;
                }
                if ($value->action_type == 3) {//评论
                    $return['pay_comment'] = $value->price;//单价
                    $return['feed_comment_count'] = $feed->feed_comment_count;//评论数
                    $balance = $balance+$value->count;
                }
            }
            $return['balance'] = $balance;

            return $response->json($return)->setStatusCode(200);
        }

    }
}