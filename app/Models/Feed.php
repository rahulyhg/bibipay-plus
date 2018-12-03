<?php
/**
 * Created by PhpStorm.
 * User: liyi
 * Date: 2018/7/18
 * Time: 下午2:44
 */

declare(strict_types=1);


namespace Zhiyi\Plus\Models;

use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    /**feed
     * The guarded attributes on the model.
     *
     * @var array
     */
    protected $guarded = ['created_at', 'updated_at'];

    /**
     * Has likeable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function feedable()
    {
        return $this->morphTo();
    }

    /**
     * Has user of the likeable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
