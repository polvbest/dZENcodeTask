<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FileController extends Controller
{
  protected $visibility  = 'public';
  protected $driver      = 'image';
  protected $prefix      = 'comments';
  protected $defaultPath = 'items-default-image.jpg';

  public function show($pathAfter, Request $request)
  {
    if ($pathAfter === 'default-avatar') {
      $path = public_path('logo/default_avatar.png');
    } elseif ($pathAfter === 'default-file') {
      $path = public_path('logo/default-filesTxt.ico');
    } else {
      $path = Storage::disk($this->driver)->path($this->prefix . '/' . $pathAfter);
    }
    if (!File::exists($path)) {
      $path = public_path('logo/placeholder.png');
    }

    $response = Response::make(File::get($path), 200);
    $response->header("Content-Type", File::mimeType($path));

    return $response;
  }

  public function download($filePath)
  {
    $filePathArr = explode('/', $filePath);
    $path = Storage::disk('text')->path($filePath);
    if (File::exists($path)) {
      return Storage::disk('text')->download($filePath, array_pop($filePathArr));
    }
    return abort(404, 'File not founded');
  }

  public function store(UploadedFile $file): string
  {
    $type = explode('/', $file->getClientMimeType());
    if($type[0] === 'image') {
      $pathImage = str_replace(url('/').'/', '', config('filesystems.disks.'.$type[0].'.url').'/');
      $path = $pathImage . Storage::disk($type[0])->put($this->prefix, $file);
      $image = $this->resizeImage($path);
      $image->save($path);
    }
    if($type[0] === 'text') {
      $path = Storage::disk($type[0])->put($this->prefix, $file);
    }
    return $path ?? '';
  }

  public function resizeImage($path) {
    $image = Image::make($path);
    if ($image->width() > 320 && $image->width() >= $image->height()) {
      $image->resize(320, null, function ($constraint) {
        $constraint->aspectRatio();
      });
    }
    if ($image->height() > 240 && $image->height() > $image->width()) {
      $image->resize(null, 240, function ($constraint) {
        $constraint->aspectRatio();
      });
    }
    return $image;
  }

  /**
   * @param array $files from Input::file()
   * @return array
   */
  public function storeInputFiles(array $files): array
  {
    $pathToFiles = [];
    foreach ($files as $fileBag) {
      foreach ($fileBag as $file) {
        $pathToFiles[] = $this->store($file);
      }
    }
    return $pathToFiles;
  }

}
