<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\UploadedFile;

trait StoreImage
{
    /**
     * @param UploadedFile $image
     * @return false|string
     */
    protected function storeImage(UploadedFile $image)
    {
        return $image->storePublicly('recipes');
    }
}