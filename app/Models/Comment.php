<?php

namespace App\Models;

use App\Http\Controllers\FileController;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{

  protected $table = 'comment';
  protected $visible = ['*'];
  protected $protected = [];
  protected $perPage = 25;
//  protected static $allowedTags = ['strong','a','i','code'];
  protected static $allowedTags = ['strong','a','i','b','code','em','img','p','br','span'];

  public function author()
  {
    return $this->belongsTo(BlogUser::class);
  }

  public function commentaries()
  {
    $this->attributes['commentaries'] = $this->where('parent_id', $this->id)->get();
    $this->visible[] = 'commentaries';
    return $this;
  }

  public static function stripTagsExcept($text, $needed = null)
  {
    $needed = $needed ?: static::$allowedTags;
    return preg_replace_callback("#<\s*\/?(\w*)\s*[^>]*?>#im",
      function ($matches) use ($needed) {
        if (isset($matches[1])) {
          if ($matches[1] == 'img'){
            $matches[0] = self::checkImageSizes($matches[0]);
          }
          if (!in_array($matches[1], $needed)) {
            return "&lt;" . trim($matches[0], "<>") . "&gt;";
//        return str_replace(['<','>'],['&lt;','&gt;'], $matches[0]); /* for replacing all sign in container */
          }
        }
        return $matches[0];
      }, $text);
  }

  public static function checkImageSizes($matches)
  {
    $maxHeight = 240;
    $maxWidth  = 320;

    return preg_replace_callback('#<\s*\/?(img)\s*[^>]*?>#im',
      function ($match) use($maxHeight,$maxWidth) {
        preg_match('/src\s*=\s*"([^"\']*)"/im', $match[0], $matches);
        if (isset($matches[1])) {
          $image = (new FileController())->resizeImage($matches[1]);
          $imageHeight = $image->height();
          $imageWidth  = $image->width();
          $match = preg_replace('/src\s*=\s*"([^"\']*)"/im', 'src="' . $matches[1] . '"', $match);
        } else {
          preg_match('/height\s*=\s*"([^"\']*)"/im', $match, $matchHeight);
          preg_match('/width\s*=\s*"([^"\']*)"/im', $match, $matchWidth);

          $imageHeight = isset($matchHeight[1]) ? $matchHeight[1] : $maxHeight;
          $imageWidth  = isset($matchHeight[1]) ? $matchHeight[1] : $maxWidth;

          if ($imageHeight > $maxHeight && $imageHeight > $imageWidth) {
            $imageWidth  = $maxHeight / $imageHeight * $imageWidth;
            $imageHeight = $maxHeight;
          }
          if ($imageHeight > $maxWidth && $imageWidth > $imageHeight) {
            $imageHeight = $maxWidth / $imageWidth * $imageHeight;
            $imageWidth  = $maxWidth;
          }
        }
        $match = preg_replace('/height\s*=\s*"([^"\']*)"/im', 'height="' . $imageHeight . '"', $match);
        $match = preg_replace('/width\s*=\s*"([^"\']*)"/im', 'width="' . $imageWidth . '"', $match);

        return isset($match[0]) ? $match[0] : $match;
      }, $matches);
  }

  public function sortByAuthorName($direction)
  {
    return $this->with('author')
      ->select($this->table . '.*')
      ->join('blog_user', 'blog_user.id', '=', 'comment.author_id')
      ->whereNull('parent_id')
      ->orderBy('blog_user.name', (($direction > 0) ? 'ASC' : 'DESC'))
      ->paginate();
  }

  public function sortByAuthorEmail($direction)
  {
    return $this->with('author')
      ->select($this->table . '.*')
      ->join('blog_user', 'blog_user.id', '=', 'comment.author_id')
      ->whereNull('parent_id')
      ->orderBy('blog_user.email', (($direction > 0) ? 'ASC' : 'DESC'))
      ->paginate();
  }

  public function sortByCreationDate($direction)
  {
    return $this->with('author')
      ->select($this->table . '.*')
      ->join('blog_user', 'blog_user.id', '=', 'comment.author_id')
      ->whereNull('parent_id')
      ->orderBy('created_at', (($direction > 0) ? 'ASC' : 'DESC'))
      ->paginate();
  }

  public static function __callStatic($method, $parameters)
  {
    if ($method === 'table') {
      return (new static())->getTable();
    }
    return parent::__callStatic($method, $parameters);
  }

}
