<?php
/**
 * Created by PhpStorm.
 * User: liyi
 * Date: 2018/8/20
 * Time: 下午3:36
 */
/**
 * Created by PhpStorm.
 * User: liyi
 * Date: 2018/8/9
 * Time: 上午10:47
 */
namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models;

use Zhiyi\Plus\Models\User;
use Illuminate\Database\Eloquent\Model;
class certifications extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}