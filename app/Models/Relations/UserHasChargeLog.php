<?php
/**
 * Created by PhpStorm.
 * User: liyi
 * Date: 2018/8/6
 * Time: 下午12:06
 */
namespace Zhiyi\Plus\Models\Relations;

use Zhiyi\Plus\Models\ChargeLog;

trait UserHasChargeLog
{
    /**
     * Has likes for user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author Seven Du <shiweidu@outlook.com>
     */

    public function charges()
    {
        return $this->hasMany(ChargeLog::class , 'user_id' , 'id');
    }
//    public function likes()
//    {
//        return $this->hasMany(ChargeLog::class, 'user_id', 'id');
//    }
//
//    /**
//     * Has be likeds for user.
//     *
//     * @return \Illuminate\Database\Eloquent\Relations\HasMany
//     * @author Seven Du <shiweidu@outlook.com>
//     */
//    public function belikeds()
//    {
//        return $this->hasMany(Like::class, 'target_user', 'id');
//    }
}
