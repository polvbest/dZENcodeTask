$(document).ready(function () {

  $('#open-form.btn-warning').click(function () {
    if ($(this).hasClass('btn-warning')) {
      $('.new-comment-container').show();
      $(this).text('Hide form').removeClass('btn-warning').addClass('btn-secondary');
    } else {
      $('.new-comment-container').hide();
      $(this).text('Add comment').removeClass('btn-secondary').addClass('btn-warning');
    }
  });

  $('#postComment').click( postComment );

  $('[data-add-comment]').click(function () {
    var commentId = $(this).closest('[data-comment-id]').data('comment-id');
    console.log(commentId, $('form#comment').find('[data-field][name="parent"]'));
    $('form#comment').find('[data-field][name="parent"]').val(commentId);
    console.log($('form#comment').find('[data-field][name="parent"]').val())
  });

  $('#newCommentForm')
    .on('show.bs.modal', captchaRefresh())
    .on('hide.bs.modal', function () {
      $('form#comment').find('[data-field][name="parent"]').val('');
      $('form#comment').find('[data-field][name="text"]').val('');
      $('form#comment #files').html('');
      tinymce.activeEditor.setContent('');

    });

  $('.iSorter').find('.sorter-reset').click(function () {
    $('#btnGroupSorterFields')
      .text($('.iSorter [data-value="date"]').text())
      .attr('data-sort-field', 'date')
      .data('sort-field', -1);
  });
  $('.iSorter').find('.dropdown-menu > .dropdown-item[data-value]').click(function () {
    $('#btnGroupSorterFields').text($(this).text())
                              .attr('data-sort-field', $(this).data('value'))
                              .data('sort-field', $(this).data('value'));
  });

  $('.iSorter').find('.action-sort[data-direction]').click(function () {
    var direction = $(this).data('direction');
    $('#btnGroupSorterFields').data('direction', direction)
                              .attr('data-direction', direction);
    sorter($('#btnGroupSorterFields').data("sort-field"), direction);
  });

});

function isEmpty(data) {
  return (data === undefined || data === null || data === '');
}

function pagerSetHref($this) {
  var sort = [$(event.target).attr('href').split('?')[1]];
  $.each(getSorterValue(), function(attr, val) {
    sort.push(attr + "=" + val);
  });
  location.href = document.location.origin + document.location.pathname + "?" + sort.join("&");
}

function getSorterValue() {
  return {
    field: $("#btnGroupSorterFields").data('sort-field'),
    direct: $("#btnGroupSorterFields").data('direction')
  };
}