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

class QzRmb extends Model
{
    protected $table = 'qz_rmb';
    public $timestamps = false;
    public function Rmb(){
        $QzDetails = new QzDetailsController();
        $doExecute = $QzDetails->getRmb();
        return $doExecute;
    }
}