<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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
//    $fileName = array_pop($filePath);
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
//      $pathText = str_replace(url('/').'/', '', config('filesystems.disks.'.$type[0].'.url').'/');
      $path = Storage::disk($type[0])->put($this->prefix, $file);
    }
    return $path ?? '';
  }

  public function resizeImage($path) {
    $image = Image::make($path);
    if ($image->width() > 320) {
      $image->resize(320, null, function ($constraint) {
        $constraint->aspectRatio();
      });
    }
    if ($image->height() > 240) {
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

//  public function imageToolsProxy(Request $request) {
//    // We recommend to extend this script with authentication logic
//    // so it can be used only by an authorized user
//    $validMimeTypes = array("image/gif", "image/jpeg", "image/png");
//
//    if (!isset($_GET["url"]) || !trim($_GET["url"])) {
//      header("HTTP/1.0 500 Url parameter missing or empty.");
//      return;
//    }
//
//    $scheme = parse_url($_GET["url"], PHP_URL_SCHEME);
//    if ($scheme === false || in_array($scheme, array("http", "https")) === false) {
//      header("HTTP/1.0 500 Invalid protocol.");
//      return;
//    }
//
//    $content = file_get_contents($_GET["url"]);
//    $info = getimagesizefromstring($content);
//
//    if ($info === false || in_array($info["mime"], $validMimeTypes) === false) {
//      header("HTTP/1.0 500 Url doesn't seem to be a valid image.");
//      return;
//    }
//
//    header('Content-Type:' . $info["mime"]);
//    echo $content;
//  }
//
//  public function uploadFile(Request $request)
//  {
//    /*******************************************************
//     * Only these origins will be allowed to upload images *
//     ******************************************************/
//    $accepted_origins = array("http://localhost", "http://192.168.1.1", "http://example.com");
//
//    /*********************************************
//     * Change this line to set the upload folder *
//     *********************************************/
//    $driver = "file";
//
//    reset($_FILES);
//    $temp = current($_FILES);
//    if (is_uploaded_file($temp['tmp_name'])) {
//      if (isset($_SERVER['HTTP_ORIGIN'])) {
//        // same-origin requests won't set an origin. If the origin is set, it must be valid.
//        if (in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)) {
//          header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
//        } else {
//          header("HTTP/1.1 403 Origin Denied");
//          return;
//        }
//      }
//
//      /*
//        If your script needs to receive cookies, set images_upload_credentials : true in
//        the configuration and enable the following two headers.
//      */
//      // header('Access-Control-Allow-Credentials: true');
//      // header('P3P: CP="There is no P3P policy."');
//
//      // Sanitize input
//      if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
//        header("HTTP/1.1 400 Invalid file name.");
//        return;
//      }
//
//      // Verify extension
//      if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("txt"))) {
//        header("HTTP/1.1 400 Invalid extension.");
//        return;
//      }
//
//      // Accept upload if there was no origin, or if it is an accepted origin
//      $newFile = $driver . $temp['name'];
//      move_uploaded_file($temp['tmp_name'], $newFile);
//
//      // Respond to the successful upload with JSON.
//      // Use a location key to specify the path to the saved image resource.
//      // { location : '/your/uploaded/image/file'}
//      echo json_encode(array('location' => $newFile));
//    } else {
//      // Notify editor that the upload failed
//      header("HTTP/1.1 500 Server Error");
//    }
//  }

}
