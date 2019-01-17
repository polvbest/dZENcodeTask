
// function userItemsList($element) {
//     $.ajax({
//         headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
//         url: '{{ route("user.items.ajax") }}',
//         type: 'POST',
//         data: ( '{ "user": ' + {{Auth::id()}} +' }' ),
//         contentType: 'json',
//         processData: false,
//         success: function (response) {
//         if (response.length !== 0) {
//             $("#itemListModal .modal-body").html(response);
//         }
//         $('#itemListModal').modal('toggle');
//         console.log(response);
//         $('a[href="#sendOffer"]').bind("click", function () {
//             let $id = $(this).data('item-id');
//             if ($id) {
//                 $('form input#itemOfferId').val($id);
//                 $('#itemsExchangeForm').submit();
//             }
//             console.log($id);
//         });
//     }
//     });
// }

// function postComment(parent) {
//     parent = parent || null;
//     $.ajax({
//         headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
//         url: "{{ route('comment.store') }}",
//         type: 'POST',
//         data: ( '{ "parent": ' + parent + ' }' ),
//         success: function (response) {
//             console.log(response);
//             if (response.length !== 0) {
//                 // $("#itemModify .modal-body").html(response);
//             }
//         }
//     });
// }

// function optionShow($element) {
//     var $id = $element.getAttribute('value');
//
//     $.ajax({
//         headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
//         url: '{{ route("admin.items.ajax", ["id"=> '0'])}}' + $id,
//         type: 'POST',
//         data: ( '{ "item": ' + $id + ' }' ),
//         contentType: 'json',
//         processData: false,
//         success: function (response) {
//             $("#itemModify .modal-body").html(response);
//
//             let item = document.querySelector('#myModal input[name="id"]');
//             let title = document.querySelector('#myModal .modal-title');
//             title.innerHTML = "Редактирование итемки № <b>" + item.value + "</b>";
//         }
//     });
//
//     $("#itemModify").modal('show');
//     $("#itemModify").attr('style', 'margin-top: 5%; transition: all 600ms ease-in-out;');
// }
