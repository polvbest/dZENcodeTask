<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogUser extends Model
{
  public $timestamps = true;

  protected $table = 'blog_user';
  protected $guarded = [];
  protected $primaryKey = 'id';
  protected $visible = ['id', 'name', 'email', 'home_page'];
  protected $fillable = ['name', 'email', 'home_page'];

  public function Comments()
  {
    return $this->hasMany('comment');
  }

}
