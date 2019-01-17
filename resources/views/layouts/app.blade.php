<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name') }}</title>

  <!-- Styles -->
  @section('styles')
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
          crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css"
          integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg"
          crossorigin="anonymous">
    <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.7/summernote.css" rel="stylesheet">
  @endsection
  @yield('styles')

<!-- Script header -->
  @section('scripts-head')
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>

  @endsection

</head>

<body>
<!-- Navigation panel -->
@section('header')
  @include('layouts.header')
@endsection
@yield('header')

<!-- Content -->
@yield('content')

<!-- Footer -->
@section('footer')

@endsection
@yield('footer')

<!-- Scripts -->
@section('scripts-footer')
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"
          integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
          crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
          integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
          crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
          integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
          crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote.js"></script>

  <script src="https://cloud.tinymce.com/stable/tinymce.min.js"></script>
  {{--<script src="{{ url('src/tinymce/js/tinymce/tinymce.min.js') }}"></script>--}}
  <script type="text/javascript" src="{{ url('js/tinyMCE.js') }}" ></script>

  <script type="text/javascript">

    function postComment() {
      // console.log('----------- postComment ------------');
      var requiredFields = ['user[name]','user[email]','captcha','text'];
      var data = new FormData($('form#comment')[0]);
      var alert;

      data.set('text', tinymce.activeEditor.getContent({format : 'raw'}));
      for (var key of data.keys()) {
        alert = false;
        if (key === 'text' &&  data.get(key) === tinymce.activeEditor.startContent) { //
          alert = true;
          markNotValidField($("form#comment").find('.text-comment .input-group'));
        }
        if ($.inArray(key, requiredFields) > -1 && isEmpty(data.get(key))) {
          alert = true;
          markNotValidField($("form#comment").find('[data-field][name="' + key + '"]').addClass('not-valid'));
        }
      }
      tinymce.activeEditor.editorUpload.scanForImages().then(function(value) {
        $.each(value, function(i, blobCache) {
          data.append('images[]',blobCache.blobInfo.blob(),blobCache.blobInfo.blobUri());
        });
        if (!alert) storeComment(data);
      });

    }

    function storeComment(data) {
      // console.log('------------- storeComment ----------------');
      $.ajax({
        url: "{{ route('comment.store') }}",
        type: "POST",
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        success: function (success) {
          // console.log(success, 'postComment.success');
          if (success.done) {
            $('#newCommentForm').modal('hide');
            document.location.reload();
          }
        },
        error: function (error) {
          captchaRefresh();
          // console.log(error, 'postComment.error');
          let element = $('form .warning[role="alert"]').show();
          if (error.responseJSON) {
            if (error.responseJSON.errors) {
              for (let field in error.responseJSON.errors) {
                element.append(error.responseJSON.errors[field]);
              }
            } else {
              if (typeof error.responseJSON.message === 'string') {
                var message = JSON.parse(error.responseJSON.message);
                for (let field in message) {
                  element.append(message[field]);
                }
              }
            }
          }
          setTimeout(function(){ element.hide().html(''); }, 5000);
        }
      });
    }

    function sorter(field, direction) {
      $.get({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: "{{ route('comment.index') }}?ajax=1&field=" + field + '&direct=' + direction,
        success: function (response) {
          if (response.length !== 0 && response.html) {
            $('.comment-table .table-content').html(response.html);
            $('.iPager').html(response.pager_html);
          }
        }
      });
    }

    function paginate($this) {
      var request = getFilterFields();
      request.pop();
      request.push($($this).attr('href').replace('#',''));
      $.get({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: "{{ route('comment.index') }}?ajax=1&" + request.join('&'),
        success: function (response) {
          // console.log(response, 'paginate.success');
          if (response.length !== 0 && response.html) {
            $('.comment-table .table-content').html(response.html);
            $('.iPager').html(response.pager_html);
          }
        },
      });
    }

    function getFilterFields() {
      var request = [];
      request.push("field="  + $(".iSorter #btnGroupSorterFields").data('sort-field'));
      request.push("direct=" + $(".iSorter #btnGroupSorterFields").data('direction'));
      request.push("page=" + $('.iPager .active[aria-current="page"][data-page]').data('page'));
      return request;
    }

    function markNotValidField(element) {
      element.addClass('not-valid');
      setTimeout(function(){
        element.removeClass('not-valid');
      }, 5000);
    }

    function captchaRefresh() {
      $.get({
        url: "{{ route('captcha') }}",
        success: function(data) {
          $(".captcha-container .captcha-img").html(data.captcha);
          $("#captcha").val('');
        }
      })
    }

  </script>

@endsection
@yield('scripts-footer')

</body>

</html>
