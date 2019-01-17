<div class="row">
  <div class="main-comment col-sm-12" data-comment-id="{{ $comment->id }}">
    <div class="comment-header col-sm-12">
      <div class="author-avatar">
        <img src="{{ $comment->author->avatar }}">
      </div>
      <div class="author-name"><strong>{{ $comment->author->name }}</strong></div>
      <div class="author-email">{{ $comment->author->email }}</div>
      <div class="comment-created">{{ $comment->created_at }}</div>
      <div class="comment-add">
        <button type="button" class="btn btn-warning open-form" data-add-comment>Add Comment</button>
      </div>
    </div>
    <div class="comment-content col-sm-12">
      <div class="comment-text">{{ $comment->text }}</div>
      <div class="commentaries">
        @isset($comment->commentaries)
          @foreach($comment->commentaries as $commentary)
            <div class="comment-commentary"></div>
          @endforeach
        @endisset
        @isset($comment['commentaries'])
          @foreach($comment['commentaries'] as $commentary)
            <div class="comment-commentary">{{$commentary['text']}}</div>
          @endforeach
        @endisset
      </div>
    </div>
  </div>
</div>