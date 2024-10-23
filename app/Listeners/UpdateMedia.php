<?php

namespace App\Listeners;

use Homeful\KwYCCheck\Events\LeadProcessed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Homeful\KwYCCheck\Models\Lead;
use Illuminate\Support\{Arr, Str};
use Homeful\Imagekit\Imagekit;

class UpdateMedia implements ShouldQueue
{
    /**
     * @param Imagekit $imageKit
     */
    public function __construct(protected Imagekit $imageKit){}

    /**
     * @param LeadProcessed $event
     * @return void
     */
    public function handle(LeadProcessed $event): void
    {
        $lead = $event->lead;
        if ($lead instanceof Lead) {

            $folderPath = $this->getFolderPath($lead);
            $response = $this->imageKit->uploadFilev2([
                'imageUrl' => $lead->id_image_url,
                'folderPath' => '/test',
                'fileName' => $lead->code . '-idImage'
            ]);

                $lead->id_image_url = json_decode($response)->url;
            $response = $this->imageKit->uploadFilev2([
                'imageUrl' => $lead->selfie_image_url,
                'folderPath' => '/test',
                'fileName' => $lead->code . '-selfieImage'
            ]);
                $lead->selfie_image_url = json_decode($response)->url;

            $lead->save();
        }

    }

    /**
     * @param \Homeful\KwYCCheck\Models\Lead $lead
     * @return array|\Illuminate\Contracts\Translation\Translator|\Illuminate\Foundation\Application|string|null
     */
    public function getFolderPath(\Homeful\KwYCCheck\Models\Lead $lead): \Illuminate\Contracts\Translation\Translator|string|array|null|\Illuminate\Foundation\Application
    {
        $root_folder = Str::start(config('leads.storage.root_folder'), '/');
        $organization = Arr::get($lead->checkin, 'body.campaign.organization.name');
        $campaign = Arr::get($lead->checkin, 'body.campaign.name');

//        return __(':root_folder/:organization/:campaign', compact('root_folder', 'organization', $campaign));
        return __(':root_folder/:organization/:campaign', compact('root_folder', 'organization', 'campaign'));
    }
}
