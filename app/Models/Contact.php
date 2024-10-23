<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\SchemalessAttributes\SchemalessAttributes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Homeful\Common\Traits\HasMeta;

/**
 * Class Contact
 *
 * @property int $id
 * @property string $name
 * @property SchemalessAttributes $meta
 * @property Collection $campaigns
 * @property Organization $organization
 * @property Agent $agent
 * @property Lead $lead
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

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class);
    }
}
