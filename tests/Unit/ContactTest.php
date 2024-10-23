<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Spatie\SchemalessAttributes\SchemalessAttributes;
use App\Models\{Campaign, Contact};

uses(RefreshDatabase::class, WithFaker::class);

test('agent has attributes', function () {
    if ($contact = Contact::factory()->create()) {
        expect($contact->id)->toBeInt();
        expect($contact->name)->toBeString();
        expect($contact->meta)->toBeInstanceOf(SchemalessAttributes::class);
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
