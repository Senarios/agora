<?php

namespace App\Models;
use App\Helpers\FileHandle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brands extends Model
{
    use HasFactory;

    public function getImageUrlAttribute($file)
    {
        return FileHandle::getURL('/storage/attachments/'.$file);
    }
}
