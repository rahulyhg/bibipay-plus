<?php
/**
 * Created by PhpStorm.
 * User: liyi
 * Date: 2018/8/8
 * Time: 下午9:24
 */
namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models;

use Zhiyi\Plus\Models\User;
use Illuminate\Database\Eloquent\Model;
class FeedReward extends Model
{
    protected $table = 'feed_reward';
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}