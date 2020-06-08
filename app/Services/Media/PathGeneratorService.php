<?php

namespace App\Services\Media;

use Spatie\MediaLibrary\PathGenerator\PathGenerator;
use Spatie\MediaLibrary\Models\Media;

class PathGeneratorService implements PathGenerator
{
    /*
     * Get the path for the given media, relative to the root storage path.
     */
    public function getPath(Media $media): string
    {
        return $media->collection_name . '/' . $media->id . '/';
    }

    /*
     * Get the path for conversions of the given media, relative to the root storage path.
     */
    public function getPathForConversions(Media $media): string
    {
        return $media->collection_name . '/' . $media->id . '/conversions/';
    }

    /*
     * Get the path for responsive images of the given media, relative to the root storage path.
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $media->collection_name . '/' . $media->id . '/responsive/';
    }
}