<?php

namespace App\Models;

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
        if (isset($matches[1]) && !in_array($matches[1], $needed)) {
          return "&lt;" . trim($matches[0], "<>") . "&gt;";
//        return str_replace(['<','>'],['&lt;','&gt;'], $matches[0]); /* for replacing all sign in container */
        }
        return $matches[0];
      }, $text);
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
