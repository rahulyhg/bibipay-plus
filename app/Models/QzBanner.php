<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: liyi
 * Date: 2018/9/12
 * Time: 上午11:22
 */

namespace Zhiyi\Plus\Models;

use Illuminate\Database\Eloquent\Model;

class QzBanner extends Model
{
    protected $table = 'qz_banner';
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}