<?php

namespace App\MoonShine\CustomFields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use MoonShine\Exceptions\FieldException;
use MoonShine\Fields\Image;

class ImageCustom extends Image
{
    public function store(UploadedFile $file): false|string
    {
        $extension = $file->extension();

        throw_if(
            ! $this->isAllowedExtension($extension),
            new FieldException("$extension not allowed")
        );

        $file = \Intervention\Image\Facades\Image::make($file)->fit(1080)->encode('jpg',80);
        $img_name = $this->getDir() . '/' . uniqid() . '.jpg';
        Storage::disk($this->getDisk())->put($img_name, $file);

        return $img_name;
    }
}
