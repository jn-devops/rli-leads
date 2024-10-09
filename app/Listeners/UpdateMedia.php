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
        if ($lead = $event->lead) {
            Imagekit::uploadFilev2([
                'imageUrl' => $lead->id_image_url,
                'folderPath' => '/test',
                'fileName' => $lead->code . '-idImage'
            ]);
            Imagekit::uploadFilev2([
                'imageUrl' => $lead->selfie_image_url,
                'folderPath' => '/test',
                'fileName' => $lead->code . '-selfieImage'
            ]);
        }
    }
}
