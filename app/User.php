<?php

namespace App;

use Carbon\Carbon;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

/**
 * Class User
 * @package App
 * @property-read int $id
 * @property string $name
 * @property string $email
 * @property string $ip
 * @property string $api_token
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class User extends Model implements AuthorizableContract
{
    use Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'id'
    ];

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }
}
