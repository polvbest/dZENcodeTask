<!-- Modal -->
<div class="modal fade" id="newCommentForm" tabindex="-1" role="dialog" aria-labelledby="newCommentForm"
     aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form id="comment" type="multipart/form-data" role="form"
          method="POST" action="{{ route('comment.store') }}">
      {{ csrf_field() }}
      <input data-field type="hidden" name="parent">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="newCommentForm">Comment</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container">
            <div class="alert alert-danger warning" role="alert" style="display:none"></div>
          </div>
          <div class="new-comment col-md-12">
            <div class="user-name">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text required">Name</span>
                </div>
                <input type="text" class="form-control required" placeholder="Example Username"
                       name="user[name]" data-field tabindex="1">
              </div>
            </div>
            <div class="user-email">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text required">E-mail</span>
                </div>
                <input type="email" class="form-control required" placeholder="email@example.com"
                       name="user[email]"  data-field tabindex="2">
              </div>
            </div>
            <div class="user-home-page">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Home page</span>
                </div>
                <input type="text" class="form-control" placeholder="https://example.com/users/"
                       name="user[home_page]" data-field tabindex="3">
              </div>
            </div>

            <div class="form-group"><!-- CAPTCHA -->
              @include('template.captcha')
            </div><!-- END CAPTCHA -->

            <div class="text-comment">
              <label for="text" class="required">Comment text</label>
              <div class="input-group">
                <textarea class="form-control required" aria-label="With textarea"
                          name="text" id="text" data-field ></textarea>
              </div>
            </div>
          </div>
          <div id="files" class="form-group" hidden></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="postComment">Post comment</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>