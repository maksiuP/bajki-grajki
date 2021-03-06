<?php

use App\Models\Artist;
use function Pest\Laravel\get;
use function Pest\Laravel\put;
use function Tests\asUser;

beforeEach(function () {
    $this->oldAttributes = [
        'slug' => 'ilona-kusmierska',
        'name' => 'Ilona Kuśmierska',
        'discogs' => 602488,
        'filmpolski' => 11623,
        'wikipedia' => 'Ilona_Kuśmierska',
    ];

    $this->newAttributes = [
        'slug' => 'tadeusz-bartosik',
        'name' => 'Tadeusz Bartosik',
        'discogs' => 1023394,
        'filmpolski' => 116251,
        'wikipedia' => 'Tadeusz_Bartosik',
    ];

    $this->artist = Artist::factory()->create($this->oldAttributes);
});

test('guests are asked to log in when attempting to view edit artist form', function () {
    get("artysci/{$this->artist->slug}/edit")
        ->assertRedirect('login');
});

test('guests are asked to log in when attempting to view edit form for nonexistent artist')
    ->get('artysci/2137/edit')
    ->assertRedirect('login');

test('users can view edit artist form', function () {
    Http::fake();

    asUser()
        ->get("artysci/{$this->artist->slug}/edit")
        ->assertOk();
});

test('guests cannot edit artist', function () {
    put("artysci/{$this->artist->slug}", $this->newAttributes)
        ->assertRedirect('login');

    $artist = $this->artist->fresh();

    foreach ($this->oldAttributes as $key => $attribute) {
        expect($artist->{$key})->toBe($attribute);
    }
});

test('users with permissions can edit artist', function () {
    asUser()
        ->put("artysci/{$this->artist->slug}", $this->newAttributes)
        ->assertRedirect("artysci/{$this->newAttributes['slug']}");

    $artist = $this->artist->fresh();

    foreach ($this->newAttributes as $key => $attribute) {
        expect($artist->{$key})->toBe($attribute);
    }
});
