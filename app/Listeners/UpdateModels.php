<?php

namespace App\Listeners;

use App\Models\{Agent, Campaign, Contact, Lead, Organization};
use Homeful\KwYCCheck\Events\LeadProcessed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\{Arr, Str};
use phpDocumentor\Reflection\Exception;


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

        $lead = Lead::from($event->lead) ;
        if ($lead instanceof Lead) {
            try {
                $organization = app(Organization::class)->firstOrCreate(['name' => $lead->organization]);
                $agent = app(Agent::class)->firstOrCreate(['name' => $lead->agent]);
                $campaign = app(Campaign::class)->firstOrCreate(['name' => $lead->campaign]);

                if ($campaign instanceof Campaign) {
                    $campaign->organization()->associate($organization);
                    $campaign->agent()->associate($agent);
                    $campaign->leads()->attach($lead);
                    $campaign->save();
                }
                $contact = app(Contact::class)->firstOrCreate(['name' => $lead->name]);

                if ($contact instanceof  Contact) {
                    $contact->organization()->associate($organization);
                    $contact->agent()->associate($agent);
                    $contact->lead()->associate($lead);
                    $contact->campaigns()->attach($campaign);
                    $contact->save();
                }
            }catch (Exception $e){
                dd($e);
            }

        }
    }
}
