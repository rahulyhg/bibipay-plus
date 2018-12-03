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
use Zhiyi\Plus\Http\Controllers\APIs\V2\QzDetailsController;

class QzExerDetail extends Model
{
    protected $table = 'qz_exer_detail';
    public $timestamps = false;
}