<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use App\Models\{Agent, Campaign, Contact, Lead, Organization};
use Spatie\SchemalessAttributes\SchemalessAttributes;
use Homeful\KwYCCheck\Models\Lead as BaseLead;
use Illuminate\Support\Str;

uses(RefreshDatabase::class, WithFaker::class);

test('campaign has attributes', function () {
    if ($campaign = Campaign::factory()->create()) {
        expect($campaign->id)->toBeInt();
        expect($campaign->name)->toBeString();
        expect($campaign->meta)->toBeInstanceOf(SchemalessAttributes::class);
    }
});

test('campaign has an organization', function () {
    if ($campaign = Campaign::factory()->forOrganization()->create()) {
        if ($campaign instanceof Campaign) {
            expect($campaign->organization)->toBeInstanceOf(Organization::class);
        }
    }
    $campaign = Campaign::factory()->create();
    $organization = Organization::factory()->create();
    if ($campaign instanceof Campaign and $organization instanceof Organization) {
        $campaign->organization()->associate($organization);
        expect($campaign->organization)->toBeInstanceOf(Organization::class);
    }
});

test('campaign has an agent', function () {
    if ($campaign = Campaign::factory()->forAgent()->create()) {
        if ($campaign instanceof Campaign) {
            expect($campaign->agent)->toBeInstanceOf(Agent::class);
        }
    }
    $campaign = Campaign::factory()->create();
    $agent = Agent::factory()->create();
    if ($campaign instanceof Campaign and $agent instanceof Agent) {
        $campaign->agent()->associate($agent);
        expect($campaign->agent)->toBeInstanceOf(Agent::class);
    }
});

test('campaign belongs to many contacts', function () {
    [$contact1, $contact2, $contact3] = Contact::factory(3)->create();
    $campaign = Campaign::factory()->create();
    expect($campaign->contacts)->toHaveCount(0);
    $campaign->contacts()->attach($contact1);
    $campaign->refresh();
    expect($campaign->contacts)->toHaveCount(1);
    $campaign->contacts()->saveMany([$contact2, $contact3]);
    $campaign->refresh();
    expect($campaign->contacts)->toHaveCount(3);
    $campaign->contacts()->detach($contact2);
    $campaign->refresh();
    expect($campaign->contacts)->toHaveCount(2);
});

test('campaign belongs to many leads', function () {
    $lead1 = Lead::from(BaseLead::factory()->create(['id' => Str::uuid()->toString()]));
//    $lead2 = Lead::from(BaseLead::factory()->create(['id' => Str::uuid()->toString()]));
//    $lead3 = Lead::from(BaseLead::factory()->create(['id' => Str::uuid()->toString()]));
    $campaign = Campaign::factory()->create();
    expect($campaign->leads)->toHaveCount(0);
    $campaign->leads()->attach($lead1);
    $campaign->refresh();
    expect($campaign->leads)->toHaveCount(1);
//    $campaign->contacts()->saveMany([$contact2, $contact3]);
//    $campaign->refresh();
//    expect($campaign->contacts)->toHaveCount(3);
    $campaign->contacts()->detach($lead1);
    $campaign->refresh();
    expect($campaign->contacts)->toHaveCount(0);
});


