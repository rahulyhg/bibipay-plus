<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: liyi
 * Date: 2018/9/12
 * Time: 上午11:22
 */

namespace Zhiyi\Plus\Models;

use Zhiyi\Plus\Http\Controllers\APIs\V2\QzDetailsController;
class QzExercise
{
    public function forceExecute()
    {
        $QzDetails = new QzDetailsController();
        $doExecute = $QzDetails->forceExecute();
        return $doExecute;
    }
}