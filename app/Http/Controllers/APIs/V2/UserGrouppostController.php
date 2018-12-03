<?php
/**
 * Created by PhpStorm.
 * User: liyi
 * Date: 2018/7/18
 * Time: 下午2:30
 */
declare(strict_types=1);

namespace Zhiyi\Plus\Http\Controllers\APIs\V2;
use DB;
use Illuminate\Http\Request;
use Zhiyi\Plus\Models\GroupPost as GroupPostModel;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseContract;

class UserGrouppostController extends Controller
{
    //获取我的帖子列表
    public function myCardsList(Request $request, ResponseContract $response, GroupPostModel $model)
    {

        $limit = $request->query('limit', 15);
        $after = $request->query('after', false);
        $user = $request->user();
        //var_dump($after);die();
        $usercard = $model->with(['grouppostable' , 'user'])
            ->where('user_id', $user->id)
            ->when($after, function ($query) use ($after) {
                return $query->where('id', '<', $after);
            })
            ->where('group_id' , 1)
            ->limit($limit)
            ->orderBy('id' , 'desc')
            ->get();

//        $usercard = DB::table('users')
//            ->when($user, function ($query) use ($user) {
//                return $query->where('id', '=',$user->id );
//            })
//            ->limit($limit)
//            ->orderBy('id', 'desc')
//            ->get();
//        $groupposts = DB::table('group_posts')->when($after , function ($query) use ($after){
//            return $query->where('id' , '<' , $after);
//        })->limit($limit)->where('user_id', '=', $user->id)->get();
//

        if ($user->unreadCount !== null) {
            $user->unreadCount()->decrement('unread_likes_count', $user->unreadCount->unread_likes_count);
        }

        return $response->json($usercard)->setStatusCode(200);
    }
}