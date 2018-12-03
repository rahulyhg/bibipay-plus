<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/13 0013
 * Time: 17:07
 */

namespace Zhiyi\Plus\Models;

use Illuminate\Database\Eloquent\Model;

class BaseUser extends Model
{
    protected $table = 'base_user';
    public $timestamps = false;
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }


}