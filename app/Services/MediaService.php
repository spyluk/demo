<?php

namespace App\Services;

use Spatie\MediaLibrary\HasMedia\HasMedia;

class MediaService
{
    /**
     * if the model has one file
     * @param HasMedia $model
     * @param $file
     * @param string $collection
     * @return \Spatie\MediaLibrary\Models\Media
     */
    public function replace(HasMedia $model, $file, $collection = 'default')
    {
        $model->clearMediaCollection();
        return $model->addMedia($file)
            ->toMediaCollection($collection);
    }
}