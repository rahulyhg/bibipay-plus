<?php

namespace Zhiyi\Plus\Models;

use Illuminate\Database\Eloquent\Model;
class TokenWallet extends Model
{
    protected $table = 'token_wallet';
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'target_id');
    }
//    public function Wallet()
//    {
//        return $this->morphTo();
//    }
//    public function chargelog()
//    {
//        return $this->hasOne(User::class, 'id', 'target_id');
//    }

}
