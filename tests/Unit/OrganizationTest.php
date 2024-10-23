<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Spatie\SchemalessAttributes\SchemalessAttributes;
use App\Models\{Campaign, Organization};

uses(RefreshDatabase::class, WithFaker::class);

test('organization has attributes', function () {
    if ($organization = Organization::factory()->create()) {
        expect($organization->id)->toBeInt();
        expect($organization->name)->toBeString();
        expect($organization->meta)->toBeInstanceOf(SchemalessAttributes::class);
    }
});

test('organization has campaigns', function () {
    [$campaign1, $campaign2] = Campaign::factory(2)->forOrganization()->create();
    expect($campaign1->organization)->toBeInstanceOf(Organization::class);
    expect($campaign1->organization->is($campaign2->organization))->toBeTrue();
    expect($campaign1->organization->campaigns)->toHaveCount(2);
});
