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

class QzOkdata extends Model
{
    protected $table = 'qz_okdata';
    public $timestamps = false;
    public function okdata(){
        $QzDetails = new QzDetailsController();
        $doExecute = $QzDetails->getOkdata();
        return $doExecute;
    }
}