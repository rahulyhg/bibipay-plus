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

use Carbon\Carbon;
use Illuminate\Http\Request;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedReward;
use Zhiyi\Plus\Models\UserCount;
use Illuminate\Support\Facades\DB;
use Zhiyi\Plus\Models\Like as LikeModel;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Plus\Models\FileWith as FileWithModel;
use Zhiyi\Plus\Models\PaidNode as PaidNodeModel;
use Zhiyi\Plus\Models\WalletCharge as WalletChargeModel;
use Zhiyi\Plus\Packages\Currency\Processes\User as UserProcess;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedVideo;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedPinned;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed as FeedModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedReward as FeedRewardModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\TokenWallet as TokenWalletModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Token as TokenModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\ChargeLog as ChargeLogModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Repository\Feed as FeedRepository;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\FormRequest\API2\StoreFeedPost as StoreFeedPostRequest;
use Symfony\Component\HttpFoundation\Session\Session;

class FeedController extends Controller
{
    /**
     * 分享列表.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function index(Request $request, ApplicationContract $app, ResponseContract $response , TokenModel $token)
    {
        $type = $request->query('type', 'new');
        if (! in_array($type, ['new', 'hot', 'follow', 'users'])) {
            $type = 'new';
        }
        if (!empty($request->phoneid)){
            $phoneid = $request->phoneid;
            $session = new Session;
            $session->set('phioneid',$phoneid);
        }
        $token_type = $token->select('token_name')->get()->toArray();
        return $response->json([
            'token_type' => $token_type,
            // 'ad' => $app->call([$this, 'getAd']),
            'pinned' => $app->call([$this, 'getPinnedFeeds']),
            'feeds' => $app->call([$this, $type]),
        ])
            ->setStatusCode(200);
    }

    public function getAd()
    {
        // todo.
    }
    public function getPinnedFeeds(Request $request, FeedModel $feedModel, FeedRepository $repository, Carbon $datetime)
    {
        $user = $request->user('api')->id ?? 0;
        if ($request->query('after') || $request->query('type') === 'follow' || $request->query('offset')) {
            return collect([]);
        }

        $feeds = $feedModel->select('feeds.*')
            ->join('feed_pinneds', function ($join) use ($datetime) {
                return $join->on('feeds.id', '=', 'feed_pinneds.target')->where('channel', 'feed')->where('expires_at', '>', $datetime);
            })
            ->whereDoesntHave('blacks', function ($query) use ($user) {
                $query->where('user_id', $user);
            })
            ->with([
                'pinnedComments' => function ($query) use ($datetime) {
                    return $query->where('expires_at', '>', $datetime)->limit(5);
                },
                'user' => function ($query) {
                    return $query->withTrashed();
                },
            ])
            ->orderBy('feed_pinneds.amount', 'desc')
            ->orderBy('feed_pinneds.created_at', 'desc')
            ->get();

        $user = $request->user('api')->id ?? 0;
        $ids = $feeds->pluck('id');
        $feedModel->whereIn('id', $ids)->increment('feed_view_count');

        return $feedModel->getConnection()->transaction(function () use ($feeds, $repository, $user) {
            return $feeds->map(function (FeedModel $feed) use ($repository, $user) {
                $repository->setModel($feed);
                $repository->images();
                $repository->format($user);
                $repository->previewComments();

                $feed->has_collect = $feed->collected($user);
                $feed->has_like = $feed->liked($user);

                return $feed;
            });
        });
    }

    /**
     * Get new feeds.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $feedModel
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Repository\Feed $repository
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function new(Request $request, FeedModel $feedModel, FeedRepository $repository, Carbon $datetime)
    {

        $limit = $request->query('limit', 15);
        $after = $request->query('after');
        $user = $request->user('api')->id ?? 0;
        $search = $request->query('search');
        $feeds = $feedModel->when($after, function ($query) use ($after) {
            return $query->where('id', '<', $after);
        })
            ->when(isset($search), function ($query) use ($search) {
                return $query->where('feed_content', 'LIKE', '%'.$search.'%');
            })
            ->whereDoesntHave('blacks', function ($query) use ($user) {
                $query->where('user_id', $user);
            })
            ->orderBy('id', 'desc')
            ->with([
                'pinnedComments' => function ($query) use ($datetime) {
                    return $query->with('user')->where('expires_at', '>', $datetime)->limit(5);
                },
                'user' => function ($query) {
                    return $query->withTrashed();
                },
            ])
            ->limit($limit)
            ->get();

        $ids = $feeds->pluck('id');
        $feedModel->whereIn('id', $ids)->increment('feed_view_count');

        return $feedModel->getConnection()->transaction(function () use ($feeds, $repository, $user) {
            return $feeds->map(function (FeedModel $feed) use ($repository, $user) {
                $repository->setModel($feed);
                $repository->images();
                $repository->format($user);
                $repository->previewComments();

                $feed->has_collect = $feed->collected($user);
                $feed->has_like = $feed->liked($user);

                return $feed;
            });
        });
    }

    /**
     * Get hot feeds.
     *
     * @param \Illuminate\Http\Request                                     $request
     * @param LikeModel                                                    $model
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Repository\Feed $repository
     * @param \Carbon\Carbon                                               $dateTime
     * @return mixed
     * @throws \Throwable
     * @author Seven Du <shiweidu@outlook.com>
     */

    public function hot(Request $request, LikeModel $model, FeedRepository $repository, Carbon $dateTime)
    {
        $limit = $request->query('limit', config('app.data_limit', 15));
        $offset = $request->query('offset', 0);
        $user = $request->user('api')->id ?? 0;

        $feeds = FeedModel::where('created_at', '>', $dateTime->subDay(config('feed.duration', 7)))
            ->whereDoesntHave('blacks', function ($query) use ($user) {
                $query->where('user_id', $user);
            })
            ->with([
                'pinnedComments' => function ($query) use ($dateTime) {
                    return $query->with('user')->where('expires_at', '>', $dateTime)->limit(5);
                },
                'user' => function ($query) {
                    return $query->withTrashed();
                },
            ])
            ->select('*', $model->getConnection()->raw('(feed_view_count + (feed_comment_count * 10) + (like_count * 5)) as popular'))
            ->limit($limit)
            ->offset($offset)
            ->orderBy('popular', 'desc')
            ->get();

        FeedModel::whereIn('id', $feeds->pluck('id'))->increment('feed_view_count');

        return $model->getConnection()->transaction(function () use ($feeds, $repository, $user) {
            return $feeds->map(function ($feed) use ($repository, $user) {
                if (! $feed) {
                    return null;
                }
                $repository->setModel($feed);
                $repository->images();
                $repository->format($user);
                $repository->previewComments();
                $feed->has_collect = $feed->collected($user);
                $feed->has_like = $feed->liked($user);

                return $feed;
            });
        });
    }
    /**
     * Get user follow user feeds.
     *
     * @param \Illuminate\Http\Request                                     $request
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed     $model
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Repository\Feed $repository
     * @param Carbon                                                       $datetime
     * @return mixed
     * @throws \Throwable
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function follow(Request $request, FeedModel $model, FeedRepository $repository, Carbon $datetime)
    {
        if (is_null($user = $request->user('api'))) {
            abort(401);
        }

        $limit = $request->query('limit', 15);
        $after = $request->query('after');
        $feeds = $model->leftJoin('user_follow', function ($join) use ($user) {
            $join->where('user_follow.user_id', $user->id);
        })
            ->whereDoesntHave('blacks', function ($query) use ($user) {
                $query->where('user_id', $user);
            })
            ->where(function ($query) use ($user) {
                $query->whereColumn('feeds.user_id', '=', 'user_follow.target')
                    ->orWhere('feeds.user_id', $user->id);
            })
            ->with([
                'pinnedComments' => function ($query) use ($datetime) {
                    return $query->with('user')->where('expires_at', '>', $datetime)->limit(5);
                },
                'user' => function ($query) {
                    return $query->withTrashed();
                },
            ])
            ->when((bool) $after, function ($query) use ($after) {
                return $query->where('feeds.id', '<', $after);
            })
            ->distinct()
            ->select('feeds.*')
            ->orderBy('feeds.id', 'desc')
            ->limit($limit)
            ->get();
        $ids = $feeds->pluck('id');
        $model->whereIn('id', $ids)->increment('feed_view_count');

        return $model->getConnection()->transaction(function () use ($repository, $user, $feeds) {

            return $feeds->map(function (FeedModel $feed) use ($repository, $user) {
                $repository->setModel($feed);
                $repository->images();
                $repository->format($user->id);
                $repository->previewComments();

                $feed->has_collect = $feed->collected($user->id);
                $feed->has_like = $feed->liked($user);

                return $feed;
            });
        });
    }

    /**
     * get single feed info.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Repository\Feed $repository
     * @param int $feed
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function show(Request $request, FeedRepository $repository, int $feed)
    {
        $user = $request->user('api')->id ?? 0;
        $feed = $repository->find($feed);
        $tid = $feed->id;
        //查看是否为设置佣金
        $feed_detail = $feed->select('feed_money','pay_look')->where('user_id' , $user)->where('id' , $tid)->get()->toArray();
        if(!empty($feed_detail[0])){
            if ($feed_detail[0]['feed_money'] == 1 && $feed_detail[0]['pay_look'] == 1) {
                $reward = new FeedRewardModel();
                $rew_look = $reward->select('price' , 'count' , 'action_type' , 'type')->
                where('user_id' , $user)->where('tid' , $tid)->where('action_type' , 1)->get()->toArray();
                //判断用户是否已经浏览
                $chargeLog = new ChargeLogModel();
                $isset = $chargeLog->where('user_id' , $user)->where('tid' , $tid)
                    ->where('type' , $rew_look[0]['type'])->where('action_type' , 1)->get()->toArray();
                if (empty($isset)){
                    if ($rew_look[0]['count'] > 0){
                        $up_count = $reward->where('user_id' , $user)->where('tid' , $tid)->where('action_type' , 1)
                            ->decrement('count' , $rew_look[0]['price']);
                        $tokenWallet = new TokenWalletModel();
                        //为用户的余额和收入充值（此版本IPC直接写死了，后面上其他token需要修改type值）
                        $tokenWallet->where('user_id' , $user)->where('type' , 1)->increment('balance' , $rew_look[0]['price']);
                        $tokenWallet->where('user_id' , $user)->where('type' , 1)->increment('total_income' , $rew_look[0]['price']);
                        //将日志信息插入log表
                        $ins = [
                            'category' => 0,
                            'add_number' => $rew_look[0]['price'],
                            'type' => $rew_look[0]['type'],
                            'created_time' => date('Y-m-d' , time()),
                            'action_type' => 1,
                            'user_id' => $user,
                            'tid' => $tid
                        ];
                        $ins_chargelog = $chargeLog->insertGetId($ins);
                    }
                }
            }
        }
        if ($feed->paidNode !== null && $feed->paidNode->paid($user) === false) {
            return response()->json([
                'message' => '请购买动态',
                'paid_node' => $feed->paidNode->id,
                'amount' => $feed->paidNode->amount,
            ])->setStatusCode(403);
        }

        // 启用获取事物，避免多次 sql 查询造成查询连接过多.
        return $feed->getConnection()->transaction(function () use ($feed, $repository, $user) {
            $feed->load('user');
            $feed->has_collect = $feed->collected($user);
            $feed->has_like = $feed->liked($user);
            $feed->reward = $feed->rewardCount();

            $repository->images();
            $repository->previewLike();

            $feed->increment('feed_view_count');

            return response()->json($repository->format($user))->setStatusCode(200);
        });
    }

    /**
     * 储存分享.
     *
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\FormRequest\API2\StoreFeedPost $request
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function store(StoreFeedPostRequest $request)
    {
        $user = $request->user();
        $return = $request->all();
        //查看余额是否充足
        if ($return['feed_money'] == 1) {
            $pay_all = $this->pay_all_fun($return, $user->id);
            if ($pay_all == false) {
                return response()->json(['message' => '余额不足，请充值']);
            }
        }
        $feed = $this->fillFeedBaseData($request, new FeedModel());
        $paidNodes = $this->makePaidNode($request);
        $fileWiths = $this->makeFileWith($request);
        $videoWith = $this->makeVideoWith($request);
        $videoCoverWith = $this->makeVideoCoverWith($request);

        try {
            $feed->saveOrFail();
            $feed->getConnection()->transaction(function () use ($request, $feed, $paidNodes, $fileWiths, $user, $videoWith, $videoCoverWith) {
                $this->saveFeedPaidNode($request, $feed);
                $this->saveFeedFilePaidNode($paidNodes, $feed);
                $this->saveFeedFileWith($fileWiths, $feed);
                $videoWith && $this->saveFeedVideoWith($videoWith, $videoCoverWith, $feed);
                $user->extra()->firstOrCreate([])->increment('feeds_count', 1);
            });
        } catch (\Exception $e) {
            $feed->delete();
            throw $e;
        }
        //佣金规则入库
        if ($return['feed_money'] == 1) {
            if (!empty($return['look'][1]['money_all'])) {
                $pay_type = 'pay_look';
                $feed_id = $feed->id;
                $feed_type = $return['look'][0]['action_type'];
                $pay_token = $this->doPayFeed($feed_type,$pay_type ,$feed_id);
                if ($pay_token){
                    $return['look']['tid'] = $feed_id;
                    $return['look']['user_id'] = $user->id;
                    $return['look']['type'] = $this->choose_type($return['type']);
                    $pull_feed['look'] = $this->pullMoney($return['look']);
                }
            }
        }
        if ($return['feed_money'] == 1) {
            if (!empty($return['praise'][1]['money_all'])) {
                $pay_type = 'pay_praise';
                $feed_id = $feed->id;
                $feed_type = $return['praise'][0]['action_type'];
                $pay_token = $this->doPayFeed($feed_type,$pay_type ,$feed_id);
                if ($pay_token){
                    $return['praise']['tid'] = $feed_id;
                    $return['praise']['user_id'] = $user->id;
                    $return['praise']['type'] = $this->choose_type($return['type']);
                    $pull_feed['praise'] = $this->pullMoney($return['praise']);
                }
            }
        }
        if ($return['feed_money'] == 1) {
            if (!empty($return['comment'][1]['money_all'])) {
                $pay_type = 'pay_comment';
                $feed_id = $feed->id;
                $feed_type = $return['comment'][0]['action_type'];
                $pay_token = $this->doPayFeed($feed_type,$pay_type ,$feed_id);
                if ($pay_token){
                    $return['comment']['tid'] = $feed_id;
                    $return['comment']['user_id'] = $user->id;
                    $return['comment']['type'] = $this->choose_type($return['type']);
                    $pull_feed['comment'] = $this->pullMoney($return['comment']);
                }
            }
        }
        return response()->json(['message' => '发布成功', 'id' => $feed->id])->setStatusCode(201);
    }
    //规则入库(点赞，浏览，评论，回复)
    protected function pullMoney($var){
        $fRewardmodel = new FeedRewardModel();
        $reward = [
            'tid' =>$var['tid'],
            'user_id' => $var['user_id'],
            'action_type' => $var[0]['action_type'],
            'count' => $var[1]['money_all'],
            'price' => $var[2]['money_one'],
            'type' => $var['type'],
            'updated_time' =>date('Y-m-d H:i:s' , time())
        ];
        return $fRewardmodel->insertGetId($reward);
    }
    //选择对应的type类型
    protected function choose_type($type){
        $token_type = DB::table('token')->where('token_name' , $type)->first();
        return $token_type->id;
    }
    //修改action_type的状态值
    protected function doPayFeed($usr , $pay_type ,$feed_id){
        $feedmodel = new FeedModel();
        $array = [
            $pay_type => $usr,
            'feed_money' => 1
        ];
        return $res = $feedmodel->where('id' , $feed_id)->update($array);
    }
    //判断用户钱包余额
    protected function pay_all_fun($return , $user_id){
        $tokenwallet = new TokenWalletModel();
        $type = $return['type'];

        //查询余额额度
        $balance = $tokenwallet->select('balance')->where('user_id' , $user_id)->where('type_name' , $type)->get()->toArray();
        //统计设置总额
        $pay_all = $return['look'][1]['money_all'] +
            $return['praise'][1]['money_all']+$return['comment'][1]['money_all'];
        //设计额度和余额比较
        if($balance){
            $compare = $balance[0]['balance']-$pay_all;
        } else {
            return false;
        }
        if ($compare > 0){
            $tokenwallet->where('user_id' , $user_id)->where('type_name' , $type)->decrement('balance', $pay_all);
            $tokenwallet->where('user_id' , $user_id)->where('type_name' , $type)->increment('total_expenses', $pay_all);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 创建文件使用模型.
     *
     * @param StoreFeedPostRequest $request
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function makeFileWith(StoreFeedPostRequest $request)
    {
        return FileWithModel::whereIn(
            'id',
            collect($request->input('images'))->filter(function (array $item) {
                return isset($item['id']);
            })->map(function (array $item) {
                return $item['id'];
            })->values()
        )->where('channel', null)
            ->where('raw', null)
            ->where('user_id', $request->user()->id)
            ->get();
    }

    /**
     * 获取动态视频.
     * @Author   Wayne
     * @DateTime 2018-04-02
     * @Email    qiaobin@zhiyicx.com
     * @param    StoreFeedPostRequest $request [description]
     * @return   [type]                        [description]
     */
    protected function makeVideoWith(StoreFeedPostRequest $request)
    {
        $video = $request->input('video');

        return FileWithModel::where(
            'id',
            $video['video_id']
        )->where('channel', null)
            ->where('raw', null)
            ->where('user_id', $request->user()->id)
            ->first();
    }

    /**
     * 获取段视频封面.
     * @Author   Wayne
     * @DateTime 2018-04-02
     * @Email    qiaobin@zhiyicx.com
     * @param    StoreFeedPostRequest $request [description]
     * @return   [type]                        [description]
     */
    protected function makeVideoCoverWith(StoreFeedPostRequest $request)
    {
        $video = $request->input('video');

        return FileWithModel::where(
            'id',
            $video['cover_id']
        )->where('channel', null)
            ->where('raw', null)
            ->where('user_id', $request->user()->id)
            ->first();
    }

    /**
     * 创建付费节点模型.
     *
     * @param StoreFeedPostRequest $request
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function makePaidNode(StoreFeedPostRequest $request)
    {
        return collect($request->input('images'))->filter(function (array $item) {
            return isset($item['amount']);
        })->map(function (array $item) {
            $paidNode = new PaidNodeModel();
            $paidNode->channel = 'file';
            $paidNode->raw = $item['id'];
            $paidNode->amount = $item['amount'];
            $paidNode->extra = $item['type'];

            return $paidNode;
        });
    }

    /**
     * 保存视频.
     * @Author   Wayne
     * @DateTime 2018-04-02
     * @Email    qiaobin@zhiyicx.com
     * @param    [type]              $videoWith      [description]
     * @param    [type]              $videoCoverWith [description]
     * @param    FeedModel           $feed           [description]
     * @return   [type]                              [description]
     */
    protected function saveFeedVideoWith($videoWith, $videoCoverWith, FeedModel $feed)
    {
        $video = new FeedVideo();
        DB::transaction(function () use ($feed, $video, $videoWith, $videoCoverWith) {
            $videoWith->channel = 'feed:video';
            $videoCoverWith->channel = 'feed:video:cover';
            $videoWith->raw = $feed->id;
            $videoCoverWith->raw = $feed->id;
            $videoWith->save();
            $videoCoverWith->save();
            $video->video_id = $videoWith->id;
            $video->cover_id = $videoCoverWith->id;
            $video->user_id = $feed->user_id;
            $video->feed_id = $feed->id;
            $video->width = $videoWith->file->width;
            $video->height = $videoWith->file->height;
            $video->save();
        });
    }

    /**
     * 保存分享图片使用.
     *
     * @param array $fileWiths
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $feed
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function saveFeedFileWith($fileWiths, FeedModel $feed)
    {
        foreach ($fileWiths as $fileWith) {
            $fileWith->channel = 'feed:image';
            $fileWith->raw = $feed->id;
            $fileWith->save();
        }
    }

    /**
     * 保存分享文件付费节点.
     *
     * @param array $nodes
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $feed
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function saveFeedFilePaidNode($nodes, FeedModel $feed)
    {
        foreach ($nodes as $node) {
            $node->subject = '购买动态附件';
            $node->body = sprintf('购买动态《%s》的图片', str_limit($feed->feed_content, 100, '...'));
            $node->user_id = $feed->user_id;
            $node->save();
        }
    }

    /**
     * 保存分享付费节点.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $feed
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function saveFeedPaidNode(Request $request, FeedModel $feed)
    {
        $amount = $request->input('amount');

        if (! $amount) {
            return;
        }

        $paidNode = new PaidNodeModel();
        $paidNode->amount = $amount;
        $paidNode->channel = 'feed';
        $paidNode->raw = $feed->id;
        $paidNode->subject = sprintf('购买动态《%s》', str_limit($feed->feed_content, 100, '...'));
        $paidNode->body = $paidNode->subject;
        $paidNode->user_id = $feed->user_id;
        $paidNode->save();
    }

    /**
     * Fill initial feed data.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $feed
     * @return \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function fillFeedBaseData(Request $request, FeedModel $feed): FeedModel
    {
        foreach ($request->only(['feed_content', 'feed_from', 'feed_mark', 'feed_latitude', 'feed_longtitude', 'feed_geohash']) as $key => $value) {
            $feed->$key = $value;
        }
        $feed->feed_client_id = $request->ip();
        $feed->audit_status = 1;
        $feed->feed_view_count = 1;
        $feed->user_id = $request->user()->id;

        return $feed;
    }

    /**
     * Delete comment.
     *
     * @param Request $request
     * @param ResponseContract $response
     * @param FeedRepository $repository
     * @param FeedModel $feed
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function destroy(
        Request $request,
        ResponseContract $response,
        FeedModel $feed
    ) {
        $user = $request->user();

        if ($user->id !== $feed->user_id) {
            return $response->json(['message' => '你没有权限删除动态'])->setStatusCode(403);
        }

        $feed->getConnection()->transaction(function () use ($feed, $user) {
            if ($pinned = $feed->pinned()->where('user_id', $user->id)->where('expires_at', null)->first()) { // 存在未审核的置顶申请时退款
                $charge = new WalletChargeModel();
                $charge->user_id = $user->id;
                $charge->channel = 'user';
                $charge->account = 0;
                $charge->action = 1;
                $charge->amount = $pinned->amount;
                $charge->subject = '动态申请置顶退款';
                $charge->body = sprintf('退还申请置顶动态《%s》的款项', str_limit($feed->feed_content, 100));
                $charge->status = 1;

                $user->wallet()->increment('balance', $charge->amount);
                $user->walletCharges()->save($charge);
                $pinned->delete();
            }

            $feed->delete();
            $user->extra()->decrement('feeds_count', 1);
        });

        return $response->json(null, 204);
    }

    /**
     * 新版删除动态接口，如有置顶申请讲退还相应积分.
     *
     * @param Request $request
     * @param ResponseContract $response
     * @param FeedModel $feed
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function newDestroy(
        Request $request,
        ResponseContract $response,
        FeedModel $feed
    ) {
        $user = $request->user();

        if ($user->id !== $feed->user_id) {
            return $response->json(['message' => '你没有权限删除动态'])->setStatusCode(403);
        }


        // 统计当前用户未操作的动态评论置顶
        $unReadCount = FeedPinned::where('channel', 'comment')
            ->where('target_user', $user->id)
            ->whereNull('expires_at')
            ->count();

        $feed->getConnection()->transaction(function () use ($feed, $user, $unReadCount) {
            $process = new UserProcess();
            $userCount = UserCount::firstOrNew([
                'type' => 'user-feed-comment-pinned',
                'user_id' => $user->id,
            ]);
            if ($pinned = $feed->pinned()->where('user_id', $user->id)->where('expires_at', null)->first()) { // 存在未审核的置顶申请时退款
                $process = new UserProcess();
                $process->reject(0, $pinned->amount, $user->id, '动态申请置顶退款', sprintf('退还申请置顶动态《%s》的款项', str_limit($feed->feed_content, 100)));
            }
            $pinnedComments = $feed->pinnedingComments()
                ->get();
            $pinnedComments->map(function ($comment) use ($process, $feed) {
                $process->reject(0, $comment->amount, $comment->user_id, '评论申请置顶退款', sprintf('退还在动态《%s》申请评论置顶的款项', str_limit($feed->feed_content, 100)));
                $comment->delete();
            });
            // 更新未被操作的评论置顶
            $userCount->total = $unReadCount - $pinnedComments->count();
            $userCount->save();
            $feed->delete();
            $user->extra()->decrement('feeds_count', 1);
        });

        return $response->json(null, 204);
    }

    /**
     * 获取某个用户的动态列表.
     *
     * @author bs<414606094@qq.com>
     * @param  Request        $request
     * @param  FeedModel      $feedModel
     * @param  FeedRepository $repository
     * @return mixed
     */
    public function users(Request $request, FeedModel $feedModel, FeedRepository $repository, Carbon $datetime)
    {
        $user = $request->user('api')->id ?? 0;
        $limit = $request->query('limit', 15);
        $after = $request->query('after');
        $current_user = $request->query('user', $user);
        $screen = $request->query('screen');

        $feeds = $feedModel->where('user_id', $current_user)
            ->when($screen, function ($query) use ($datetime, $screen) {
                switch ($screen) {
                    case 'pinned':
                        $query->whereHas('pinned', function ($query) use ($datetime) {
                            $query->where('expires_at', '>', $datetime);
                        });
                        break;
                    case 'paid':
                        $query->whereHas('paidNode');
                        break;
                }
            })
            ->when($after, function ($query) use ($after) {
                return $query->where('id', '<', $after);
            })
            ->with(['pinnedComments' => function ($query) use ($datetime) {
                return $query->where('expires_at', '>', $datetime)->limit(5);
            }, 'user' => function ($query) {
                return $query->withTrashed();
            }])
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

        return $feedModel->getConnection()->transaction(function () use ($feeds, $repository, $user) {
            return $feeds->map(function (FeedModel $feed) use ($repository, $user) {
                $repository->setModel($feed);
                $repository->images();
                $repository->format($user);
                $repository->previewComments();

                $feed->has_collect = $feed->collected($user);
                $feed->has_like = $feed->liked($user);

                return $feed;
            });
        });
    }
}

