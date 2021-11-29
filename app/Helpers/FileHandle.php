<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use App\Helpers\Log;

use GuzzleHttp\Client;

class FileHandle
{
    public static function getURL($file)
    {
        if (config('filesystems.default') == 'local') {
            // $storage_path = Storage::disk(config('filesystems.default'))->getDriver()->getAdapter()->getPathPrefix();
            // $full_path = $storage_path . $file;

            return $file ? url($file) : null;

        } else if (config('filesystems.default') == 's3') {
            $file=str_replace('/storage/','',$file);
            $file=str_replace('storage/','',$file);
            $s3Client = Storage::disk(config('filesystems.default'))->getDriver()->getAdapter()->getClient();

            $cmd = $s3Client->getCommand('GetObject', [
                'Bucket' => config('filesystems.disks.s3.bucket'),
                'Key'    => $file
            ]);

            //Log::debug('get url ' . $file);

            $s3Request = $s3Client->createPresignedRequest($cmd, '+120 minutes');

            // Get the actual presigned-url
            $url = (string) $s3Request->getUri();

            //Log::debug($url); bump again

            return $url;
        } else {
            throw new \Exception(100, "file system not supported: " . config('filesystems.default'));
        }
    }


}
