<div class="main-comment" data-comment-id="{{ $comment->id }}">
  <div class="comment-header">
    <div class="author-avatar">
      <img src="{{ route('image.show', 'default-avatar') }}" width="25" height="25">
    </div>
    <div class="author-name"><strong>{{ $comment->author->name }}</strong></div>
    <div class="author-email">{{ $comment->author->email }}</div>
    <div class="comment-created">{{ date('Y-m-d', strtotime($comment->created_at)) }} in {{ date('H:i', strtotime($comment->created_at)) }}</div>
    <div class="comment-add float-right">
      <button type="button" class="btn btn-sm btn-outline-success" data-add-comment
              data-toggle="modal" data-target="#newCommentForm">Comment
      </button>
    </div>
  </div>
  <div class="comment-content">
    <div class="comment-text">{!! App\Models\Comment::stripTagsExcept($comment->text) !!}</div>
    <div class="commentaries">
      @isset($comment->commentaries)
        @foreach($comment->commentaries as $commentary)
          <div class="comment-commentary">
            @include('comment_item', ['comment' => $commentary])
          </div>
        @endforeach
      @endisset
    </div>
  </div>
</div>

