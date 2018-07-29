<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Class Recipe
 * @package App
 * @property-read int $id
 * @property string $name
 * @property integer $user_id
 * @property string|null $image
 * @property array $ingredients
 * @property string $recipe
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Recipe extends Model
{
    protected $fillable = [
        'name', 'image', 'ingredients', 'recipe'
    ];

    protected $hidden = [
        'user_id', 'image'
    ];

    protected $casts = [
        'ingredients' => 'array'
    ];

    protected $appends = [
        'image_link'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getImageLinkAttribute()
    {
        return $this->image ? sprintf('%s/storage/%s', config('app.url'), $this->image) : null;
    }
}