<?php

use App\Images\Photo;
use App\Images\Values\ArtistPhotoCrop;
use App\Models\Artist;
use App\Models\Tale;
use App\Values\CreditType;
use App\Values\Discogs\Photo as DiscogsPhoto;
use Facades\App\Services\Discogs;
use Facades\App\Services\FilmPolski;
use Facades\App\Services\Wikipedia;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

it('casts discogs id to integer')
    ->expect((new Artist(['discogs' => '1023394']))->discogs)
    ->toBe(1023394);

it('casts filmpolski id to integer')
    ->expect((new Artist(['filmpolski' => '116251']))->filmpolski)
    ->toBe(116251);

it('generates slug when created', function () {
    $artist = Artist::create(['name' => 'Tadeusz Włudarski']);

    expect($artist->slug)->toBe('tadeusz-wludarski');

    $artist = new Artist();
    $artist->name = 'Zofia Rysiówna';

    expect($artist->slug)->toBeNull();

    $artist->save();

    expect($artist->slug)->toBe('zofia-rysiowna');
});

it('regenerates slug when updated', function () {
    $artist = Artist::create(['name' => 'Tadeusz Włudarski']);

    expect($artist->slug)->toBe('tadeusz-wludarski');

    $artist->name = 'Zofia Rysiówna';

    $artist->save();

    expect($artist->slug)->toBe('zofia-rysiowna');

    $artist->fill(['name' => 'Andrzej Stockinger'])->save();

    expect($artist->slug)->toBe('andrzej-stockinger');
});

it('can get its photo', function () {
    $photo = Photo::create([
        'filename' => 'tXySLaaEbhfyzLXm6QggZY5VSFulyN2xLp4OgYSy.png',
        'crop' => ArtistPhotoCrop::fake(),
    ]);

    $artist = Artist::factory()->photo($photo)->create();

    expect($artist->photo)->toBeInstanceOf(Photo::class)
        ->and($artist->photo->filename())
        ->toBe('tXySLaaEbhfyzLXm6QggZY5VSFulyN2xLp4OgYSy.png');
});

it('can generate discogs url', function () {
    $artist = Artist::factory()
        ->make(['discogs' => 518243]);

    Discogs::shouldReceive('url')
        ->andReturn('https://www.discogs.com/artist/518243');

    expect($artist->discogs_url)
        ->toBe('https://www.discogs.com/artist/518243');
});

it('can generate filmpolski url', function () {
    $artist = Artist::factory()
        ->make(['filmpolski' => 112891]);

    FilmPolski::shouldReceive('url')
        ->andReturn('http://www.filmpolski.pl/fp/index.php?osoba=112891');

    expect($artist->filmpolski_url)
        ->toBe('http://www.filmpolski.pl/fp/index.php?osoba=112891');
});

it('can generate wikipedia url', function () {
    $artist = Artist::factory()
        ->make(['wikipedia' => 'Joanna_Sobieska']);

    Wikipedia::shouldReceive('url')
        ->andReturn('https://pl.wikipedia.org/wiki/Joanna_Sobieska');

    expect($artist->wikipedia_url)
        ->toBe('https://pl.wikipedia.org/wiki/Joanna_Sobieska');
});

it('does not generate nonexistent urls', function () {
    $artist = Artist::factory()->make([
        'discogs' => null,
        'filmpolski' => null,
        'wikipedia' => null,
    ]);

    expect($artist->discogs_url)->toBeNull()
        ->and($artist->filmpolski_url)->toBeNull()
        ->and($artist->wikipedia_url)->toBeNull();
});

it('can get extract from wikipedia', function () {
    $extract = "<p><b>Piotr Fronczewski</b> (ur. 8 czerwca 1946 w Łodzi) – polski aktor.\n</p>";

    $artist = Artist::factory()
        ->create(['wikipedia' => 'Piotr_Fronczewski']);

    Wikipedia::shouldReceive('extract')
        ->with('Piotr_Fronczewski')
        ->andReturn($extract);

    expect($artist->wikipedia_extract)->toBe($extract);
});

it('does not query wikipedia when no id is set', function () {
    $artist = Artist::factory()
        ->create(['wikipedia' => null]);

    Wikipedia::spy()
        ->shouldNotReceive('extract');

    expect($artist->wikipedia_extract)->toBeNull();
});

it('can get photos from discogs', function () {
    $images = collect([new DiscogsPhoto(
        type: 'primary',
        uri: 'test',
        resource_url: 'test',
        uri150: 'test150',
        width: 561,
        height: 800,
    )]);

    $artist = Artist::factory()
        ->create(['discogs' => 602473]);

    Discogs::shouldReceive('photos')
        ->with(602473)
        ->andReturn($images);

    expect($artist->discogsPhotos())->toBe($images);
});

it('does not query discogs when no id is set', function () {
    $artist = Artist::factory()
        ->create(['discogs' => null]);

    Discogs::spy()
        ->shouldNotReceive('photos');

    expect($artist->discogsPhotos()->toArray())->toBe([]);
});

it('can get photos from filmpolski', function () {
    $images = [
        'main' => [
            'year' => null,
            'photos' => ['test'],
        ],
    ];

    $artist = Artist::factory()
        ->create(['filmpolski' => 112891]);

    FilmPolski::shouldReceive('photos')
        ->with(112891)
        ->andReturn($images);

    expect($artist->filmPolskiPhotos())->toBe($images);
});

it('does not query filmpolski when no id is set', function () {
    $artist = Artist::factory()
        ->create(['filmpolski' => null]);

    FilmPolski::spy()
        ->shouldNotReceive('photos');

    expect($artist->filmPolskiPhotos())->toBe([]);
});

it('can get photo from discogs', function () {
    $images = collect([new DiscogsPhoto(
        type: 'primary',
        uri: 'test',
        resource_url: 'test',
        uri150: 'test150',
        width: 561,
        height: 800,
    )]);

    $artist = Artist::factory()
        ->create(['discogs' => 602473]);

    Discogs::shouldReceive('photos')
        ->with(602473)
        ->andReturn($images);

    expect($artist->discogsPhoto())->toBe('test')
        ->and($artist->discogsPhoto('150'))->toBe('test150');
});

it('can get its appearances as actor', function () {
    $artist = Artist::factory()->create();

    expect($artist->asActor())->toBeInstanceOf(BelongsToMany::class);

    $artist->asActor()->attach($tales = [
        Tale::factory()->create(['year' => 1978])->id,
        Tale::factory()->create(['year' => 1969, 'title' => 'b'])->id,
        Tale::factory()->create(['year' => 1969, 'title' => 'a'])->id,
    ]);

    expect($artist->asActor)->toHaveCount(3);

    expect($artist->asActor->get(2)->id)->toBe($tales[0]);
    expect($artist->asActor->get(1)->id)->toBe($tales[1]);
    expect($artist->asActor->get(0)->id)->toBe($tales[2]);
});

it('can get credits', function () {
    $artist = Artist::factory()->create();

    expect($artist->credits())->toBeInstanceOf(BelongsToMany::class);

    $tales = Tale::factory(6)->sequence(
        ['year' => 1979],
        ['year' => 1969, 'title' => 'b'],
        ['year' => 1969, 'title' => 'a'],
        ['year' => 1978],
        ['year' => 1969, 'title' => 'c'],
        ['year' => 1969, 'title' => 'd'],
    )->create();

    $lyricist = ['type' => CreditType::text(), 'nr' => 0];
    $composer = ['type' => CreditType::music(), 'nr' => 0];

    $artist->credits()->attach([
        $tales[0]->id => $lyricist,
        $tales[1]->id => $lyricist,
        $tales[2]->id => $lyricist,
        $tales[3]->id => $composer,
        $tales[4]->id => $composer,
        $tales[5]->id => $composer,
    ]);

    expect($artist->credits)
        ->toHaveCount(6)
        ->sequence(
            fn ($e) => $e->toBeModel($tales[2]),
            fn ($e) => $e->toBeModel($tales[1]),
            fn ($e) => $e->toBeModel($tales[4]),
            fn ($e) => $e->toBeModel($tales[5]),
            fn ($e) => $e->toBeModel($tales[3]),
            fn ($e) => $e->toBeModel($tales[0]),
        );
});

it('can get credits of given type', function () {
    $artist = Artist::factory()->create();

    $tales = Tale::factory(4)->sequence(
        ['year' => 1978],
        ['year' => 1969, 'title' => 'b'],
        ['year' => 1978],
        ['year' => 1969, 'title' => 'a'],
    )->create();

    $lyricist = ['type' => CreditType::text(), 'nr' => 0];
    $composer = ['type' => CreditType::music(), 'nr' => 0];

    $artist->credits()->attach([
        $tales[0]->id => $lyricist,
        $tales[1]->id => $lyricist,
        $tales[2]->id => $composer,
        $tales[3]->id => $lyricist,
    ]);

    expect($artist->creditsFor(CreditType::text()))
        ->toBeInstanceOf(EloquentCollection::class)
        ->toHaveCount(3)
        ->sequence(
            fn ($e) => $e->toBeModel($tales[3]),
            fn ($e) => $e->toBeModel($tales[1]),
            fn ($e) => $e->toBeModel($tales[0]),
        );
});

test('countAppearances scope works', function () {
    $artist = Artist::factory()->create();

    $artist->credits()->attach(
        Tale::factory(4)->create()->map->id,
        ['type' => CreditType::music(), 'nr' => 0],
    );

    $artist->asActor()->attach(
        Tale::factory(6)->create()->map->id,
    );

    $duplicate = Tale::factory()->create();

    $artist->credits()->attach(
        $duplicate,
        ['type' => CreditType::directing(), 'nr' => 0],
    );

    $artist->asActor()->attach($duplicate);

    expect(Artist::countAppearances()->find($artist->id)->appearances)->toBe(11);
});

test('appearances method works', function () {
    $artist = Artist::factory()->create();

    $artist->credits()->attach(
        Tale::factory(4)->create()->map->id,
        ['type' => CreditType::music(), 'nr' => 0],
    );

    $artist->asActor()->attach(
        Tale::factory(6)->create()->map->id,
    );

    $duplicate = Tale::factory()->create();

    $artist->credits()->attach(
        $duplicate,
        ['type' => CreditType::directing(), 'nr' => 0],
    );

    $artist->asActor()->attach($duplicate);

    expect($artist->appearances())->toBe(11);
});

it('can refresh cached data', function () {
    $artist = Artist::factory()->create([
        'discogs' => 602473,
        'filmpolski' => 112891,
        'wikipedia' => 'Piotr_Fronczewski',
    ]);

    Discogs::shouldReceive('refreshCache')
        ->with(602473)
        ->andReturn(true);

    FilmPolski::shouldReceive('refreshCache')
        ->with(112891)
        ->andReturn(true);

    Wikipedia::shouldReceive('refreshCache')
        ->with('Piotr_Fronczewski')
        ->andReturn(true);

    $artist->refreshCache();
});

it('can flush cached data', function () {
    $artist = Artist::factory()->create([
        'discogs' => 602473,
        'filmpolski' => 112891,
        'wikipedia' => 'Piotr_Fronczewski',
    ]);

    Discogs::shouldReceive('forget')
        ->with(602473)
        ->andReturn(true);

    FilmPolski::shouldReceive('forget')
        ->with(112891)
        ->andReturn(true);

    Wikipedia::shouldReceive('forget')
        ->with('Piotr_Fronczewski')
        ->andReturn(true);

    expect($artist->flushCache())->toBeTrue();
});

test('findBySlug method works', function () {
    expect(Artist::findBySlug('Jan Matyjaszkiewicz'))->toBeNull();

    $artist = Artist::factory()
        ->create(['name' => 'Jan Matyjaszkiewicz']);

    expect(Artist::findBySlug('Jan Matyjaszkiewicz'))->toBeNull();

    expect(Artist::findBySlug('jan-matyjaszkiewicz'))->toBeModel($artist);
});

test('findBySlugOrNew method works', function () {
    $artist = Artist::factory()
        ->create(['name' => 'Jan Matyjaszkiewicz']);

    expect(Artist::count())->toBe(1);

    expect(
        Artist::findBySlugOrNew(' jan matyjaSZkiewiCZ'),
    )->toBeModel($artist);

    expect(Artist::count())->toBe(1);

    $artist = Artist::findBySlugOrNew('Jan Kobuszewski');

    expect(Artist::count())->toBe(2);

    expect($artist->name)->toBe('Jan Kobuszewski')
        ->and($artist->slug)->toBe('jan-kobuszewski');
});
