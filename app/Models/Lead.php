<?php

namespace App\Models;

use Homeful\KwYCCheck\Models\Lead as BaseLead;
use Illuminate\Support\Arr;

/**
 * Class Lead
 *
 * @property string $campaign
 * @property string $agent
 * @property string $organization
 *
 */
class Lead extends BaseLead
{

    static public function from(BaseLead $lead): self
    {
        $model = new Lead();
        $model->setRawAttributes($lead->getAttributes(), true);
        $model->exists = true;
        $model->setConnection($lead->getConnectionName());
        $model->fireModelEvent('retrieved', false);

        return $model;
    }

    public function getCampaignAttribute(): string
    {
        return Arr::get($this->checkin, 'body.campaign.name');
    }

    public function getAgentAttribute(): string
    {
        return Arr::get($this->checkin, 'body.campaign.agent.name');
    }

    public function getOrganizationAttribute(): string
    {
        return Arr::get($this->checkin, 'body.campaign.organization.name');
    }
    //TODO: save images to local
    //TODO: set filename to mobile number for comparison to face_id


}
