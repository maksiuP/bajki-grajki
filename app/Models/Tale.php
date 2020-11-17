<?php

namespace App\Models;

use App\Values\CreditType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Tale extends Model
{
    use HasSlug, HasFactory;

    public $fillable = [
        'title', 'year', 'nr',
    ];

    protected $casts = [
        'year' => 'int',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(100);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function cover($size = null): ?string
    {
        if (optional($this->attributes)['cover'] === null) {
            return null;
        }

        if ($size === null) {
            return $this->cover;
        }

        if ($size === 'original') {
            return Storage::cloud()->temporaryUrl(
                "covers/original/$this->cover",
                now()->addMinutes(15)
            );
        }

        return Storage::cloud()->url("covers/$size/$this->cover");
    }

    public function removeCover(): bool
    {
        $this->forceFill([
            'cover' => null,
            'cover_placeholder' => null,
        ])->save();
    }

    public function credits(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, 'credits')
            ->using(Credit::class)->as('credit')
            ->withPivot('type', 'nr')->withTimestamps()
            ->orderBy('credits.nr');
    }

    public function creditsFor(CreditType $type): Collection
    {
        return $this->credits
                    ->filter(fn ($credit) => $credit->credit->ofType($type))
                    ->values();
    }

    public function actors(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Artist', 'tales_actors')
            ->withPivot('credit_nr', 'characters')->withTimestamps()
            ->orderBy('tales_actors.credit_nr');
    }
}
