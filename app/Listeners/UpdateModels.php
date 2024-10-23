<?php

namespace App\Listeners;

use App\Models\{Agent, Campaign, Contact, Lead, Organization};
use Homeful\KwYCCheck\Events\LeadProcessed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\{Arr, Str};


class UpdateModels implements ShouldQueue
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
        $lead = $event->lead;
        if ($lead instanceof Lead) {
            $organization = app(Organization::class)->create(['name' => $lead->organization]);
            $agent = app(Agent::class)->create(['name' => $lead->agent]);
            $campaign = app(Campaign::class)->create(['name' => $lead->campaign]);
            if ($campaign instanceof Campaign) {
                $campaign->organization()->associate($organization);
                $campaign->agent()->associate($agent);
                $campaign->leads()->attach($lead);
                $campaign->save();
            }
            $contact = app(Contact::class)->create(['name' => $lead->name]);
            if ($contact instanceof  Contact) {
                $contact->organization()->associate($organization);
                $contact->agent()->associate($agent);
                $contact->lead()->associate($lead);
                $contact->save();
            }
        }
    }
}
