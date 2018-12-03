<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: liyi
 * Date: 2018/7/18
 * Time: 下午5:22
 */

namespace Zhiyi\Plus\Models;

use Illuminate\Database\Eloquent\Model;

class GroupPost extends Model
{
    /**
     * The guarded attributes on the model.
     *
     * @var array
     */
    protected $table = 'group_posts';
   // protected $guarded = ['created_at', 'updated_at'];
    /**
     * Has likeable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'target_id');
    }
    public function grouppostable()
    {
        return $this->morphTo();
    }
}
