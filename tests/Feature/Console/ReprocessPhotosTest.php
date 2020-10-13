<?php

use App\Jobs\ProcessArtistPhoto;
use App\Models\Artist;
use Illuminate\Support\Facades\Bus;
use function Pest\Laravel\artisan;
use function Tests\fixture;

it('throws error when artist doesn\'t exist')
    ->artisan('reprocess:photos --artist nonexistent-artist')
    ->expectsOutput('Artist doesn\'t exist.')
    ->assertExitCode(1);

it('throws error when artist doesn\'t have a photo', function () {
    Artist::factory()->create([
        'name' => 'Test Artist',
        'photo' => null,
    ]);

    artisan('reprocess:photos --artist test-artist')
        ->expectsOutput('Artist doesn\'t have a photo.')
        ->assertExitCode(1);
});

it('works with single artist', function () {
    Artist::factory()->create([
        'name' => 'Test Artist',
        'photo' => 'test.jpg',
    ]);

    // Photo by David Grandmougin on Unsplash
    $file = fopen(fixture('Images/photo.jpg'), 'r');

    Storage::cloud()->put('photos/original/test.jpg', $file, 'public');

    Bus::fake();

    artisan('reprocess:photos --artist test-artist')
        ->assertExitCode(0);

    Bus::assertDispatched(ProcessArtistPhoto::class);

    Storage::cloud()->delete('photos/original/test.jpg');
});

it('asks for confirmation when processing all photos')
    ->artisan('reprocess:photos')
    ->expectsConfirmation('Do you want to reprocess all photos?', 'no')
    ->assertExitCode(1);