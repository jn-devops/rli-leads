<?php

namespace App\Models;

use Homeful\KwYCCheck\Models\Lead as BaseLead;
use Illuminate\Support\Arr;

/**
 * Class Lead
 *
 * @property string $campaign
 *
 */
class Lead extends BaseLead
{

    public function getCampaignAttribute(): string
    {
        return Arr::get($this->checkin, 'body.campaign.name');
    }
    //TODO: save images to local
    //TODO: set filename to mobile number for comparison to face_id
}
