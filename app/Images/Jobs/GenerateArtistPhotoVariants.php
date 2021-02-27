<?php

namespace App\Images\Jobs;

use App\Images\Photo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class GenerateArtistPhotoVariants implements ShouldQueue, ShouldBeUnique
{
    use CropsArtistPhoto;
    use ProcessesImages;

    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected Photo $image,
    ) { }

    public function uniqueId(): string
    {
        return $this->image->filename;
    }

    public function handle()
    {
        $temporaryDirectory = (new TemporaryDirectory())->create();

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
                $croppedFacePath, $size, 'square', $temporaryDirectory,
            );

            $file = fopen($responsiveImagePath, 'r');

            Photo::disk()
                ->put("photos/{$size}/{$this->image->filename()}", $file, 'public');
        }

        foreach (Photo::imageSizes() as $size) {
            $responsiveImagePath = $this->generateResponsiveImage(
                $croppedImagePath, $size, 'height', $temporaryDirectory,
            );

            $file = fopen($responsiveImagePath, 'r');

            Photo::disk()
                ->put("photos/{$size}/{$this->image->filename()}", $file, 'public');
        }

        $temporaryDirectory->delete();
    }
}
