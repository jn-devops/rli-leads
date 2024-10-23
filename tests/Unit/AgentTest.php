<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Spatie\SchemalessAttributes\SchemalessAttributes;
use App\Models\{Agent, Campaign};

uses(RefreshDatabase::class, WithFaker::class);

test('agent has attributes', function () {
    if ($agent = Agent::factory()->create()) {
        expect($agent->id)->toBeInt();
        expect($agent->name)->toBeString();
        expect($agent->meta)->toBeInstanceOf(SchemalessAttributes::class);
    }
});

test('agent has many campaigns', function () {
    [$campaign1, $campaign2, $campaign3] = Campaign::factory(3)->create();
    $agent = Agent::factory()->create();
    expect($agent->campaigns)->toHaveCount(0);
    $agent->campaigns()->save($campaign1);
    $agent->refresh();
    expect($agent->campaigns)->toHaveCount(1);
    $agent->campaigns()->saveMany([$campaign2, $campaign3]);
    $agent->refresh();
    expect($agent->campaigns)->toHaveCount(3);
    $campaign2->delete();
    $agent->refresh();
    expect($agent->campaigns)->toHaveCount(2);
});
