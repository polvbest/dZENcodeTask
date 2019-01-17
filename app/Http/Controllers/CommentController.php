<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\BlogUser;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class CommentController extends Controller
{
  protected $perPage = 25;

  public function index(Request $request)
  {

    $comments = $this->getSortedComments($request) ?: (new Comment())->sortByCreationDate(-1);

    if($request->get('ajax')) {
      return ['html'   => $this->prepareCommentsHtml($comments),
              'pager_html'  => view('template.iPager')->with(['pager' => $comments])->render(),
              'pager' => $comments
      ];
    }

    $newCommentaries = [];
    foreach ($comments as $commentary) {
      $newCommentaries[] = $this->prepareComment($commentary);
    }

    return view('comment', [
      'comments' => $newCommentaries,
      'pager'    => $comments,
      'sorter'   => [ 'field'  => $request->get('field')  ?: 'date'
                    , 'direct' => $request->get('direct') ?: -1],
    ]);
  }

  public function store(StoreCommentRequest $request)
  {

    if ($request->isMethod('POST')) {

      $filesPath  = $this->uploadDocuments($request, true);
      $imagesPath = $this->uploadImages($request, true);

      $blogUser = $this->updateBlogUserData($request);

      $comment            = new Comment();
      $comment->text      = $this->prepareCommentText($request->get('text'), $imagesPath);
      $comment->author_id = $blogUser->id;
      $comment->parent_id = $request->get('parent');

      try {
        $comment->save();
      } catch (\Exception $e) {
        return $e->getMessage();
      }

      return response()->json([
        'done' => true,
        'comment' => $comment->getAttributes(),
      ]);

    }

    return false;

  }

  public function updateBlogUserData(Request $request)
  {
    $author = $request->get('user');

    $user = BlogUser::where('email', '=', $author['email'])->first();

    if (!isset($user->id)) {
      $user = BlogUser::firstOrNew($author);
    }
    $user->name      = $author['name'];
    $user->home_page = $author['home_page'];
    $user->remote_id = $request->ip();
    $user->browser   = $request->header('User-Agent');

    try {
      $user->save();
    } catch (\Exception $e) {
      return $e->getMessage();
    }
    return $user;
  }

  public function prepareCommentText(string $text = '', array $imagesPath = [])
  {
    foreach ($imagesPath as $src => $path) {
      $image = (new FileController())->resizeImage($path);
      $text  = preg_replace_callback('#<\s*\/?(img)\s*[^>]*?>#im',
        function ($match) use ($path, $image) {
          $match[0] = preg_replace('/height\s*=\s*"([^"\']*)"/im', 'height="' . $image->height() . '"', $match[0]);
          $match[0] = preg_replace('/width\s*=\s*"([^"\']*)"/im', 'width="' . $image->width() . '"', $match[0]);
          $match[0] = preg_replace('/src\s*=\s*"([^"\']*)"/im', 'src="' . $path . '"', $match[0]);
          return $match[0];
        }, $text);
    }
    return $text;
  }

  public function prepareCommentTextDocuments(string $text = '', array $realPath = [], $identifier = '')
  {
    foreach ($realPath as $src => $path) {
/*      $text  = preg_replace_callback('#<\s*\/?(a)\s*[^>]*?>#im',*/
      $text  = preg_replace_callback("#<\s*\/?(a)\s*[^>]*?{$identifier}\s*[^>]*?>#im",
        function ($match) use ($path, $identifier) {
          $match[0] = preg_replace("/href\s*=\s*\"[\/\w]*\/{$identifier}([^\"\']*)\"/im", 'href="file/download/' . $path . '"', $match[0]);
          return $match[0];
        }, $text);
    }
    return $text;
  }

  public function uploadDocuments(Request $request, $inner = false)
  {
    $filesPath = [];
    $files = $request->file('docs');
    if ($files) {
      foreach ($files as $fileName => $file) {
        if ($this->validateDocument($file)) {
          $text = $this->prepareCommentTextDocuments($request->get('text'),[(new FileController)->store($file)], $fileName);
        $request->merge(['text' => $text]);
        }
      }
    }
    return $inner ? $filesPath : response()->json(['success' => ['filesPath' => $filesPath]]);

  }

  public function uploadImages(Request $request, $inner = false)
  {
    $imagesPath = [];
    $images = $request->file('images');
    if ($images) {
      foreach ($images as $image) {
        if ($this->validateImage($image)) {
          $imagesPath[$image->getClientOriginalName()] = (new FileController)->store($image);
        }
      }
    }
    return $inner ? $imagesPath : response()->json(['success' => $imagesPath]);
  }

  protected function validateDocument($file)
  {
    $validator = Validator::make(['file' => $file], ['file' => 'max:100|mimetypes:text/plain']);
    if ($validator->fails()) {
      return abort(422, \GuzzleHttp\json_encode($validator->errors()));
    }
    return true;
  }

  protected function validateImage($image)
  {
    $validator = Validator::make(['image' => $image], ['image' => 'image|mimes:jpg,png,gif']);
    if ($validator->fails()) {
      return abort(422, \GuzzleHttp\json_encode($validator->errors()));
    }
    return true;
  }

  public function getSortedComments(Request $request) {
    $comments  = [];
    $requested = $request->all();
    if (isset($requested['field']) && isset($requested['direct'])) {
      if ($requested['field'] === 'name') {
        $comments = (new Comment())->sortByAuthorName($requested['direct']);
      }
      if ($requested['field'] === 'email') {
        $comments = (new Comment())->sortByAuthorEmail($requested['direct']);
      }
      if ($requested['field'] === 'date') {
        $comments = (new Comment())->sortByCreationDate($requested['direct']);
      }
    }
    return $comments;
  }

  public function prepareCommentsHtml($comments)
  {
    $newCommentaries = [];
    foreach ($comments as $commentary) {
      $newCommentaries[] = view('comment_item', ['comment' => $this->prepareComment($commentary)])->render();
      $newCommentaries[] = "<hr>";
    }
    return implode(PHP_EOL, $newCommentaries);
  }

  public function prepareComment($comment)
  {
    $newCommentary = (object)$comment->getAttributes();
    $newCommentary->author = (object)$comment->author->getAttributes();
    $newCommentary->commentaries = $this->getCommentaries($comment);
    return $newCommentary;
  }

  public function getCommentaries($comment)
  {
    $commentaries = Comment::with('author')->where('parent_id', $comment->id)->get();
    $newCommentaries = [];
    foreach ($commentaries as &$commentary) {
      $newCommentary = (object)$commentary->getAttributes();
      $newCommentary->author = (object)$commentary->author->getAttributes();
      $newCommentary->commentaries = $this->getCommentaries($commentary);
      $newCommentaries[] = $newCommentary;
    }
    return $newCommentaries;
  }

}
