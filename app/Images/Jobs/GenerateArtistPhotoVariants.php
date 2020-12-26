<?php

namespace App\Images\Jobs;

use App\Images\Photo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class GenerateArtistPhotoVariants implements ShouldQueue
{
    use CropsArtistPhoto, ProcessesImages;
    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct(
        protected Photo $image,
    ) { }

    public function handle()
    {
        $temporaryDirectory = (new TemporaryDirectory)->create();

        $sourceStream = Photo::disk()->readStream(
            $this->image->originalPath(),
        );

        $baseImagePath = $this->copyToTemporaryDirectory(
            $sourceStream,
            $temporaryDirectory,
            $this->image->filename(),
        );

        $croppedImagePath = $this->cropImage(
            $baseImagePath,
            $this->image->crop(),
            $temporaryDirectory,
        );

        $croppedFacePath = $this->cropFace(
            $baseImagePath,
            $this->image->crop(),
            $temporaryDirectory,
        );

        foreach (Photo::faceSizes() as $size) {
            $responsiveImagePath = $this->generateResponsiveImage(
                $croppedFacePath,
                $size, 'square',
                $temporaryDirectory,
            );

            $file = fopen($responsiveImagePath, 'r');

            Photo::disk()
                ->put("photos/{$size}/{$this->image->filename()}", $file, 'public');
        }

        foreach (Photo::imageSizes() as $size) {
            $responsiveImagePath = $this->generateResponsiveImage(
                $croppedImagePath,
                $size, 'height',
                $temporaryDirectory,
            );

            $file = fopen($responsiveImagePath, 'r');

            Photo::disk()
                ->put("photos/{$size}/{$this->image->filename()}", $file, 'public');
        }

        $temporaryDirectory->delete();
    }
}