<?php
/**
 * Created by PhpStorm.
 * User: liyi
 * Date: 2018/8/6
 * Time: 下午12:06
 */
namespace Zhiyi\Plus\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $table = 'user_address';
    /**
     * Has user of the likeable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}