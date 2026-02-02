<?php

use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

function generateTransactionId(): string
{
    $micro = now()->format('u');
    $milliseconds = substr($micro, 0, 3);
    return 'TXN' . now()->format('YmdHis') . $milliseconds;
}


function imageUpload($file, $folder = 'uploads', $height = 800, $width = 800)
{
    try {
        if (!$file->isValid()) {
            return false;
        }
        if (!str_starts_with($file->getMimeType(), 'image/')) {
            return false;
        }

        $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $original);
        $filename = $safeName . '_' . time() . '.webp';

        // Process image with Intervention
        $image = Image::read($file);

        // Resize to specified height and width (maintains aspect ratio)
        if ($image->width() > $width || $image->height() > $height) {
            $image->scaleDown(width: $width, height: $height);
        }

        // Convert to WebP with 85% quality
        $encoded = $image->toWebp(85);

        // Save to folder
        $folder = trim($folder, '/');
        $path = public_path($folder);

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        $fullPath = $path . '/' . $filename;
        file_put_contents($fullPath, $encoded);


        // Return URL
        return asset($folder . '/' . $filename);
    } catch (\Exception $e) {
        return false;
    }
}



function CopyImage($fromPath, $toPath, $filename)
{
    try {
        $from = public_path($fromPath . '/' . $filename);
        $toDir = public_path($toPath);

        if (!File::exists($from)) {
            return false;
        }

        if (!File::exists($toDir)) {
            File::makeDirectory($toDir, 0755, true);
        }

        $to = $toDir . '/' . $filename;
        File::move($from, $to);

        return asset($toPath . '/' . $filename);
    } catch (\Exception $e) {
        Log::error($e->getMessage());
        return false;
    }
}




function moveProductImage($fromPath, $toPath, $filename)
{
    try {
        $from = public_path($fromPath . '/' . $filename);
        $toDir = public_path($toPath);

        if (!File::exists($from)) {
            return false;
        }

        if (!File::exists($toDir)) {
            File::makeDirectory($toDir, 0755, true);
        }

        $to = $toDir . '/' . $filename;
        File::move($from, $to);

        return asset($toPath . '/' . $filename);
    } catch (\Exception $e) {
        Log::error($e->getMessage());
        return false;
    }
}



function unlinkImage($path, $filename)
{
    try {
        $filePath = public_path($path . '/' . $filename);

        if (!File::exists($filePath)) {
            return false;
        }

        File::delete($filePath);
        return true;
    } catch (\Exception $e) {
        Log::error($e->getMessage());
        return false;
    }
}




// function moveProductImage($fromPath, $toPath, $filename)
// {
//     try {
//         $from = public_path($fromPath . '/' . $filename);


//         $toDir = public_path($toPath);

//         if (!File::exists($from)) {
//             return false;
//         }

//         if (!File::exists($toDir)) {
//             File::makeDirectory($toDir, 0755, true);
//         }

//         $to = $toDir . '/' . $filename;
//         File::move($from, $to);
//         return asset('product_images/' . $filename);
//     } catch (\Exception $e) {
//         Log::error($e->getMessage());
//         return false;
//     }
// }




function generateOrderNumber(): string
{
    return 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
}


function generateTransactionNumber(): string
{
    return 'TXN-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
}


function checkPermission($permission)
{
    return Auth::guard('admin')->user()->can($permission);
}
