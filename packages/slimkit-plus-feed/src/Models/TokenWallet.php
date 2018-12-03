<?php
/**
 * Created by PhpStorm.
 * User: liyi
 * Date: 2018/8/9
 * Time: 上午10:47
 */
namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models;

use Zhiyi\Plus\Models\User;
use Illuminate\Database\Eloquent\Model;
class TokenWallet extends Model
{
    protected $table = 'token_wallet';
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}