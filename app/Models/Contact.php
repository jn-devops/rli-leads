<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\SchemalessAttributes\SchemalessAttributes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Homeful\Common\Traits\HasMeta;

/**
 * @deprecated
 * Class Contact
 *
 * @property int $id
 * @property string $name
 * @property SchemalessAttributes $meta
 * @property Collection $campaigns
 *
 * @method int getKey()
 */
class Contact extends Model
{
    /** @use HasFactory<\Database\Factories\ContactFactory> */
    use HasFactory;
    use HasMeta;

    protected $fillable = [
        'name'
    ];

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class);
    }
}
