jQuery(function($) {

  // Get CSRF token
  var csrfToken = (typeof Joomla !== 'undefined' && Joomla.getOptions) ? Joomla.getOptions('csrf.token') : null;
  if (!csrfToken) {
    $('form#adminForm input[type="hidden"]').each(function() {
      var name = $(this).attr('name');
      if ($(this).val() === '1' && name && /^[a-f0-9]{32}$/i.test(name)) {
        csrfToken = name;
        return false;
      }
    });
  }

  if (csrfToken) {
    $.ajaxSetup({
      headers: {
        'X-CSRF-Token': csrfToken
      }
    });
  }

  // Ordering
  $(document).ready(function () {
    $('.button-delete').attr('disabled', 'disabled');

    var imagesList = document.getElementById('imagesList');
    if (!imagesList) {
      return;
    }

    function saveOrderings() {
      var orders = [];
      $('#imagesList .speasyimagegallery-singe-image').each(function () {
        var id = $(this).attr('id');
        if (id) {
          orders.push(id);
        }
      });

      var data = {
        orders: orders.join(','),
        csrf_token: csrfToken
      };
      if (csrfToken) {
        data[csrfToken] = 1;
      }

      $.ajax({
        type: 'POST',
        url: 'index.php?option=com_speasyimagegallery&task=albums.sort_images',
        data: data
      });
    }

    if (typeof window.dragula === 'function') {
      window.dragula([imagesList], {
        direction: 'vertical',
        mirrorContainer: imagesList,
        moves: function (el, source, handle) {
          return handle.classList.contains('sortable-handler')
            || !!(handle.closest && handle.closest('.sortable-handler'))
            || handle.classList.contains('icon-menu');
        }
      }).on('drop', function () {
        saveOrderings();
      });
    } else if (typeof $.fn.sortable === 'function') {
      $(imagesList).sortable({
        axis: 'y',
        handle: '.sortable-handler',
        update: function () {
          saveOrderings();
        }
      });
    }
  });

  // Change state
  $(document).on('click', '.btn-change-state', function(event) {
    event.preventDefault();
    var $this = $(this);

    var data = {
      id: $(this).closest('.speasyimagegallery-singe-image').attr('id'),
      state: $(this).attr('data-state'),
      csrf_token: csrfToken
    };
    if (csrfToken) {
      data[csrfToken] = 1;
    }

    if(data.state == 'enabled') {
      $this.removeClass('active').removeAttr('data-state').attr('data-state', 'disabled').html('<span class="icon-unpublish"></span>');
    } else {
      $this.addClass('active').removeAttr('data-state').attr('data-state', 'enabled').html('<span class="icon-publish"></span>');
    }

    $.ajax({
      type: "POST",
      url: 'index.php?option=com_speasyimagegallery&task=albums.image_state',
      data: data
    });
  });

  // Delete single image
  $(document).on('click', '.speasyimagegallery-delete-image', function(event) {
    event.preventDefault();
    if(confirm(Joomla.JText._('COM_SPEASYIMAGEGALLERY_DELETE_IMAGE_CONFIRM'))) {
      var $this = $(this);
      var data = {
        id: $this.closest('.speasyimagegallery-singe-image').attr('id'),
        album_id: album_id,
        csrf_token: csrfToken
      };
      if (csrfToken) {
        data[csrfToken] = 1;
      }
      $.ajax({
        type: "POST",
        url: 'index.php?option=com_speasyimagegallery&task=albums.image_delete',
        data: data,
        success: function(response) {
          var data = null;
          try {
            data = typeof response === 'object' ? response : JSON.parse(response);
          } catch(e) {}

          if(data && data.status) {
            $this.closest('.speasyimagegallery-singe-image').remove();
          } else if(data && data.output) {
            alert(data.output);
          }

          if(data && !data.count) {
            $('.speasyimagegallery-images-list').addClass('speasyimagegallery-no-images');
          }
        }
      });
    }
  });

  // Edit Image
  $(document).on('click', '.speasyimagegallery-edit-image', function(event) {
    event.preventDefault();
    var $this = $(this);
    var data = {
      id: $this.attr('data-id'),
      album_id: album_id,
      csrf_token: csrfToken
    };
    if (csrfToken) {
      data[csrfToken] = 1;
    }

    $.ajax({
      type: "POST",
      url: 'index.php?option=com_speasyimagegallery&task=albums.edit_image',
      data: data,
      beforeSend: function() {
        $this.find('.fa').addClass('fa-spinner fa-spin');
      },
      success: function(response) {
        $this.find('.fa').removeClass('fa-spinner fa-spin');
        var json = null;
        try {
          json = typeof response === 'object' ? response : JSON.parse(response);
        } catch (e) {}

        if (json && json.status === false) {
          alert(json.output || 'An error occurred.');
          return;
        }

        var modal = '<div class="speasyimagegallery-edit-modal-wrapper"><div class="speasyimagegallery-edit-modal">';
        modal += '<div class="speasyimagegallery-edit-modal-header"><h3>'+ Joomla.JText._('COM_SPEASYIMAGEGALLERY_MODAL_EDIT_IMAGE') +'</h3><a href="#" class="speasyimagegallery-edit-modal-close"><i class="fa fa-times"></i></a></div>';
        modal += '<div class="speasyimagegallery-edit-modal-body">' + response + '</div>';
        modal += '</div></div>';
        $('body').addClass('speasyimagegallery-edit-modal-open').append(modal);
      },
      error: function(xhr, status, error) {
        $this.find('.fa').removeClass('fa-spinner fa-spin');
        alert(error || 'Failed to load image details.');
      }
    });
  });

  //Save image settings
  $(document).on('click', '#btn-save-image-settings', function(event) {
    event.preventDefault();
    var $this = $(this);
    var id = $this.attr('data-image'),
    title = $('#speasyimagegallery-image-field-title').val(),
    alt = $('#speasyimagegallery-image-field-alt').val(),
    desc = $('#speasyimagegallery-image-field-desc').val();

    var data = {
      id: id,
      album_id: album_id,
      title: title,
      alt: alt,
      desc: desc,
      csrf_token: csrfToken
    };
    if (csrfToken) {
      data[csrfToken] = 1;
    }

    $.ajax({
      type: "POST",
      url: 'index.php?option=com_speasyimagegallery&task=albums.save_image',
      data: data,
      beforeSend: function() {
        $this.find('.fa').addClass('fa-spinner fa-spin');
      },
      success: function(response) {
        $this.find('.fa').removeClass('fa-spinner fa-spin');
        var json = null;
        try {
          json = typeof response === 'object' ? response : JSON.parse(response);
        } catch (e) {}

        if (json && json.status === false) {
          alert(json.output || 'An error occurred.');
          return;
        }

        $('#imagesList').find('#' + id).find('.speasyimagegallery-image-title').text(title);
        $('body').removeClass('speasyimagegallery-edit-modal-open');
        $('.speasyimagegallery-edit-modal-wrapper').remove();
      },
      error: function(xhr, status, error) {
        $this.find('.fa').removeClass('fa-spinner fa-spin');
        alert(error || 'Failed to save image.');
      }
    });
  });

  // Close Modal
  $(document).on('click', '.speasyimagegallery-edit-modal-close', function(event) {
    event.preventDefault();
    $('body').removeClass('speasyimagegallery-edit-modal-open');
    $('.speasyimagegallery-edit-modal-wrapper').remove();
  });

  // Upload Image
  $.fn.uploadImages = function(options) {

    var options = $.extend({
      index: '',
      data : ''
    }, options);

    $.ajax({
      type: "POST",
      url: 'index.php?option=com_speasyimagegallery&task=albums.upload_image',
      data: options.data,
      contentType: false,
      cache: false,
      processData:false,
      beforeSend: function() {
        var placeholder = '<div id="'+ options.index +'" class="sp-tr speasyimagegallery-image-loader clearfix">';
        placeholder += '<div class="speasyimagegallery-image-loader-text">';
        placeholder += '<div><i class="fa fa-circle-o-notch fa-spin"></i> '+ Joomla.JText._('COM_SPEASYIMAGEGALLERY_IMAGE_UPLOADING') +'...</div></div>';
        placeholder += '<div class="speasyimagegallery-image-loader-progress"><div>';
        placeholder += '<div class="speasyimagegallery-progress"><div class="speasyimagegallery-progress-bar" style="width: 0%;"></div></div>';
        placeholder += '</div></div></div>';
        $('#imagesList').prepend($(placeholder));
        $('.speasyimagegallery-images-list').removeClass('speasyimagegallery-no-images');
      },
      success: function(response) {
        var data = null;
        try {
          data = typeof response === 'object' ? response : JSON.parse(response);
        } catch(e) {}

        if(data && data.status) {
          $('#imagesList').find('#' + options.index).remove();
          $('#imagesList').prepend(data.output);
        } else {
          $('#imagesList').find('#' + options.index).remove();
          alert((data && data.output) ? data.output : 'Upload failed');
        }

        if(data && data.count) {
          $('.speasyimagegallery-images-list').removeClass('speasyimagegallery-no-images');
        } else {
          $('.speasyimagegallery-images-list').addClass('speasyimagegallery-no-images');
        }

      },
      xhr: function() {
        myXhr = $.ajaxSettings.xhr();
        if(myXhr.upload){
          myXhr.upload.addEventListener('progress', function(evt) {
            $('#imagesList').find('#' + options.index).find('.speasyimagegallery-progress-bar').css('width', Math.floor(evt.loaded / evt.total *100) + '%').text(Math.floor(evt.loaded / evt.total *100) + '%');
          }, false);
        }
        return myXhr;
      }
    });
  };

  $(document).on('click', '#speasyimagegallery-upload-images-empty, #speasyimagegallery-btn-upload-images', function(event){
    event.preventDefault();
    $('#speasyimagegallery-images-input-file').click();
  });

  $('#speasyimagegallery-images-input-file').on('change', function(event){
    event.preventDefault();
    var $this = $(this);
    var files = $(this).prop('files');

    for (var i = 0; i < files.length; i++){
      var formdata = new FormData();

      formdata.append('image', files[i]);
      formdata.append('album_id', album_id);
      if (csrfToken) {
        formdata.append(csrfToken, 1);
        formdata.append('csrf_token', csrfToken);
      }

      $(this).uploadImages({
        data: formdata,
        index: 'image-id-' + Math.floor(Math.random() * (1e6 - 1 + 1) + 1)
      });
    }

    $this.val('');
  });

  /* ========================================================================
  * Drag & Drop Upload
  * ======================================================================== */
  $(document).on('dragenter', '.speasyimagegallery-images-list', function (event){
    event.preventDefault();
    event.stopPropagation();
    $(this).addClass('sp-pagebuilder-media-drop');
  });

  $(document).on('mouseleave', '.speasyimagegallery-images-list', function (event){
    event.preventDefault();
    event.stopPropagation();
    $(this).removeClass('sp-pagebuilder-media-drop');
  });

  $(document).on('dragover', '.speasyimagegallery-images-list', function (event){
    event.preventDefault();
  });

  $(document).on('drop', '.speasyimagegallery-images-list', function (event){
    event.preventDefault();
    event.stopPropagation();
    $(this).removeClass('sp-pagebuilder-media-drop');
    var files = event.originalEvent.dataTransfer.files;

    for (var i = 0; i < files.length; i++){
      var formdata = new FormData();

      formdata.append('image', files[i]);
      formdata.append('album_id', album_id);
      if (csrfToken) {
        formdata.append(csrfToken, 1);
        formdata.append('csrf_token', csrfToken);
      }

      $(this).uploadImages({
        data: formdata,
        index: 'image-id-' + Math.floor(Math.random() * (1e6 - 1 + 1) + 1)
      });
    }
  });


  /**
   * Multiple delete of images
   * 
  */
    Joomla.submitbutton = function(pressbutton) {
      console.log(pressbutton);
        if (pressbutton == 'album.deleteSelectedList') {
            let confirm = window.confirm('Do you really want to remove those images?');
            if (confirm) {
                Joomla.submitform('album.deleteSelectedList');
            } else {
                return false;
            }
        } else {
            Joomla.submitform(pressbutton);
        }
    }
    var boxData = [];


    // select all image rows
    $(document).on('change', 'input[type=checkbox].speasyimage-gallery-select-all-row', function(e){
        e.preventDefault();
        let is_select_all_checked = $(this).prop('checked');
        let all_image_id = [];
        let $image_rows = $('input[type=checkbox].select-single-image');
        let boxcheckid = '0';

        if (is_select_all_checked) {
            $image_rows.each(function(e){
                $(this).prop('checked', true);
                all_image_id.push($(this).val());
                $('.button-delete').removeAttr('disabled');
            });
        } else {
            all_image_id = [];
            $image_rows.each(function(e){
                $(this).prop('checked', false);
                $('.button-delete').attr('disabled','disabled');
            });
        }
        
        if (typeof (all_image_id) == 'object' && all_image_id instanceof Array && all_image_id.length > 0) {
            boxcheckid = all_image_id.join(',');
        }
        $('input[name=boxchecked]').val(boxcheckid);

    });

    
    $(document).on('change', 'input[type=checkbox].select-single-image', function (e) {
        e.preventDefault();

        if($(this).prop('checked'))
        {
          $('.button-delete').removeAttr('disabled');
        }
        else
        {
          $('.button-delete').attr('disabled','disabled');
        }
        

        let image_id = $(this).val();
        if (boxData.indexOf(image_id) == -1) {
            boxData.push(image_id);
        } else if (boxData.indexOf(image_id) > -1) {
            let i = boxData.indexOf(image_id);
            boxData.splice(i,1);
        }
        
        
        let boxcheckid = '0';
        if (typeof(boxData) == 'object' && boxData instanceof Array && boxData.length > 0) {
            boxcheckid = boxData.join(',');
        }

        $('input[name=boxchecked]').val(boxcheckid);
    });
});
