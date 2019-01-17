@extends('layouts.app')

@section('styles')
  @parent
  <link href="./css/comment.css" rel="stylesheet">
@endsection

@section('content')
  <div class="wrapper mt-3" id="comment">
    <div class="blog-comments container">
      <div class="comment-table">
        <div class="table-header inlined mb-2">
          <div class="add-comment float-left">
            <div class="container">
              <button type="button" class="btn btn-warning"
                      data-toggle="modal" data-target="#newCommentForm">New Comment
              </button>
            </div>
          </div>
          <div class="iSorter ">
            @include('template.iSorter')
          </div>
          <div class="iPager float-right">
            @include('template.iPager')
          </div>
        </div>
        <div class="table-content">
          @isset($comments)
            @foreach($comments as $comment)
              @include('comment_item', ['comment' => $comment])
              <hr>
            @endforeach
          @endisset
        </div>
      </div>
    </div>
  </div>
  @include('comment_form')
@endsection

@section('scripts-footer')
  @parent
  <script type="text/javascript" src="js/comment.js"></script>
@endsection