<?php

namespace App\Listeners;

use Homeful\KwYCCheck\Actions\AttachLeadMediaAction;
use Homeful\KwYCCheck\Events\LeadProcessed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Homeful\Imagekit\Imagekit;

class UpdateMedia
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LeadProcessed $event): void
    {   
        $imageKit = new Imagekit;
        if ($lead = $event->lead) {
            $response = $imageKit->uploadFilev2([
                'imageUrl' => $lead->id_image_url,
                'folderPath' => '/test',
                'fileName' => $lead->code . '-idImage'
            ]);
            $lead->idImageUrl = json_decode($response)->url;
            $response = $imageKit->uploadFilev2([
                'imageUrl' => $lead->selfie_image_url,
                'folderPath' => '/test',
                'fileName' => $lead->code . '-selfieImage'
            ]);
            $lead->selfieImageUrl = json_decode($response)->url;
            $lead->save();
        }
    }
}
