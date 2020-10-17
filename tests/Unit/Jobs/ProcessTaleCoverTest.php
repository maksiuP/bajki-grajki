<?php

use App\Jobs\ProcessTaleCover;
use App\Models\Tale;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use function Tests\fixture;

it('works', function () {
    $filename = Str::random('10').'.jpg';

    // Photo by David Grandmougin on Unsplash
    $file = fopen(fixture('Images/cover.jpg'), 'r');

    Storage::cloud()->put("covers/original/$filename", $file, 'public');

    $tale = Tale::factory()
        ->create(['cover' => null]);

    ProcessTaleCover::dispatchSync($tale, $filename);

    $tale->refresh();

    expect($tale->cover)->toBe($filename);

    expect($tale->cover_placeholder)
        ->toBe(file_get_contents(fixture('Images/cover_placeholder.b64')));

    expect(file_get_contents(storage_path("testing/covers/128/$filename")))
        ->toBe(file_get_contents(fixture('Images/cover_128.jpg')));

    Storage::cloud()->delete("covers/original/$filename");
    rmdir(storage_path('testing/covers/original'));

    foreach (ProcessTaleCover::$sizes as $size) {
        expect(Storage::cloud()->delete("covers/$size/$filename"))->toBeTrue();
        rmdir(storage_path("testing/covers/$size"));
    }
});
