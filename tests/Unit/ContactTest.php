<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use App\Models\{Agent, Campaign, Contact, Lead, Organization};
use Spatie\SchemalessAttributes\SchemalessAttributes;
use Homeful\KwYCCheck\Models\Lead as BaseLead;

uses(RefreshDatabase::class, WithFaker::class);

test('agent has attributes', function () {
    if ($contact = Contact::factory()->create()) {
        expect($contact->id)->toBeInt();
        expect($contact->name)->toBeString();
        expect($contact->meta)->toBeInstanceOf(SchemalessAttributes::class);
    }
});

test('contact has an agent', function () {
    if ($contact = Contact::factory()->forAgent()->create()) {
        if ($contact instanceof Contact) {
            expect($contact->agent)->toBeInstanceOf(Agent::class);
        }
    }
    $contact = Contact::factory()->create();
    $agent = Agent::factory()->create();
    if ($contact instanceof Contact and $agent instanceof Agent) {
        $contact->agent()->associate($agent);
        expect($contact->agent)->toBeInstanceOf(Agent::class);
    }
});

test('contact has an organization', function () {
    if ($contact = Contact::factory()->forOrganization()->create()) {
        if ($contact instanceof Contact) {
            expect($contact->organization)->toBeInstanceOf(Organization::class);
        }
    }
    $contact = Contact::factory()->create();
    $organization = Organization::factory()->create();
    if ($contact instanceof Contact and $organization instanceof Organization) {
        $contact->organization()->associate($organization);
        expect($contact->organization)->toBeInstanceOf(Organization::class);
    }
});

test('contact belongs to a lead', function () {
    if ($contact = Contact::factory()->create()) {
        if ($contact instanceof Contact) {
            $lead = Lead::from(BaseLead::factory()->create());
            $contact->lead()->associate($lead);
            expect($contact->lead)->toBeInstanceOf(Lead::class);
        }
    }
});

test('contact belongs to many campaigns', function () {
    [$campaign1, $campaign2, $campaign3] = Campaign::factory(3)->create();
    $contact = Contact::factory()->create();
    expect($contact->campaigns)->toHaveCount(0);
    $contact->campaigns()->attach($campaign1);
    $contact->refresh();
    expect($contact->campaigns)->toHaveCount(1);
    $contact->campaigns()->saveMany([$campaign2, $campaign3]);
    $contact->refresh();
    expect($contact->campaigns)->toHaveCount(3);
    $contact->campaigns()->detach($campaign2);
    $contact->refresh();
    expect($contact->campaigns)->toHaveCount(2);
});
