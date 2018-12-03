<?php
/**
 * Created by PhpStorm.
 * User: liyi
 * Date: 2018/8/7
 * Time: 下午2:45
 */
namespace Zhiyi\Plus\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalsApply extends Model
{
    /**
     * The guarded attributes on the model.
     *
     * @var array
     */
    protected $table = 'withdrawals_apply';
    public $timestamps = false;

    /**
     * Has user of the withdrawals_apply.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

//    public function getCreatedTimeAttribute($value)
//    {
//        return date('Y-m-d H:m:s', $value);
//    }
}
