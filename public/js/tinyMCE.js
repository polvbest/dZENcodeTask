$(document).ready(function() {

    var editorConfig = {
      path_absolute: '/',
      selector: "textarea",
      plugins: 'image codesample link linkchecker paste image preview ',
      menubar: false,
      toolbar: 'undo redo | link image | bold italic codesample | pastetext preview | myInsertFile myInsertCode',
      valid_elements: "@[id|class|title|style|data-options|data*],#p,br,-b,hr",
      extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|title|width=320|height=240|align|name],span[class|align|style]",
      custom_ui_selector: "#text",
      default_link_target: "_blank",
      link_title: false,
      target_list: false,
      link_list: false,

      file_picker_types: 'image file',
      file_browser_callback: function(fieldName, url, type, win) {
        var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
        var y = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;
        var cmsURL = editorConfig.path_absolute + "laravel-filemanager?field_name=" + fieldName;

        cmsURL += "&type=" + (type == 'image') ? 'image' : 'file';

        tinyMCE.activeEditor.windowManager.open({
          file: cmsURL,
          title: "Filemanager",
          width: x * 0.8,
          height: y * 0.8,
          resizable: "yes",
          close_previous: "no"
        });
      },

      image_title: true,
      image_description: false,
      automatic_uploads: false,

      file_picker_callback: function(cb, value, meta) {
        var input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('name', 'file[]');
        input.setAttribute('multiple', true);
        input.setAttribute('accept', 'image/x-png,image/gif,image/jpg');
        input.onchange = function() {
          var file = this.files[0];
          var reader = new FileReader();
          reader.readAsDataURL(file);
          reader.onload = function () {
            var id = 'blobid' + (new Date()).getTime();
            var blobCache = tinymce.activeEditor.editorUpload.blobCache;
            var base64 = reader.result.split(',')[1];
            var blobInfo = blobCache.create(id, file, base64);
            blobCache.add(blobInfo);
            cb(blobInfo.blobUri(), {title: file.name});
          };
        };
        input.click();

      },

      setup: function (editor) {
        editor.addButton('myInsertFile', {
          title: 'add file',
          icon: 'insert',
          onclick: function () {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('name', 'docs[]');
            input.setAttribute('accept', 'text/plain');
            input.onchange = function() {
              var file = this.files[0];
              var reader = new FileReader();
              reader.onload = function () {
                var link = document.createElement('a');
                var fileName = [file.lastModified,file.size,file.name].join('_');
                input.setAttribute('name', 'docs['+fileName+']');
                link.setAttribute('href', 'file/download/' + fileName);
                link.setAttribute('target', "_blank");
                link.setAttribute('title', file.name);
                link.innerHTML = "file: " + file.name;
                editor.insertContent('<span>' + link.outerHTML + '</span>');
                $('#files').append(input);
              };
              reader.readAsDataURL(file);
            };
            input.click();
          }
        });
      }

    };

    tinymce.init(editorConfig);

    $(document).on('focusin', function(e) {
      if ($(event.target).closest(".mce-window").length) {
        e.stopImmediatePropagation();
      }
    });

  function validateFileType(){
    var fileName = document.getElementById("fileName").value;
    var idxDot = fileName.lastIndexOf(".") + 1;
    var extFile = fileName.substr(idxDot, fileName.length).toLowerCase();
    if (extFile=="jpg" || extFile=="jpeg" || extFile=="png"){
      //TODO
    }else{
      alert("Only jpg/jpeg and png files are allowed!");
    }
  }

});