<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Homeful\KwYCCheck\Models\Lead as BaseLead;
use App\Models\Lead;

uses(RefreshDatabase::class, WithFaker::class);

test('lead has attributes', function () {
    if ($lead = Lead::from(BaseLead::factory()->create())) {
        expect($lead->campaign)->toBeString();
        expect($lead->agent)->toBeString();
        expect($lead->organization)->toBeString();
    }
});
