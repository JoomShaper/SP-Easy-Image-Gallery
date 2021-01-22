/**
* @package com_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2017 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

;(function($){

  $.fn.speasyimagegallery = function(options) {

    var settings = {
      'showCounter': true,
      'showTitle': true,
      'showDescription': true,
      'parent': '.speasyimagegallery-gallery'
    };

    this.each(function() {

      if (options) {
        $.extend(settings, options);
      }

      var item = this;
      var speasyimagegallery = function(){
        this.items = $(item).closest(settings.parent).find(item.nodeName);
        this.count = (this.items.length) - 1;
        this.index = this.items.index(item);
        this.navPrev = '';
        this.navNext = '';
        this.loaded = false;
        this.naturalWidth = 0;
        this.naturalHeight = 0;

        this.init = function() {
          this.modal();
          this.goto(this.index);
          var that = this;

          this.navNext.on('click', function(event) {
            event.preventDefault();
            that.next();
          });

          $(document).on('click', '.speasyimagegallery-image', function(event){
            event.preventDefault();
            that.next();
          });

          $(document).on('click', '.speasyimagegallery-modal-wrapper, .speasyimagegallery-close', function(event){
            if (event.target !== this) {
              return;
            }
            event.preventDefault();
            that.close();
          });

          $(document).on('keyup', function(event) {
            if(event.keyCode == 39) {
              event.preventDefault();
              that.next();
            }

            if(event.keyCode == 37) {
              event.preventDefault();
              that.prev();
            }

            if(event.keyCode == 27) {
              event.preventDefault();
              that.close();
            }
          })

          this.navPrev.on('click', function(event) {
            event.preventDefault();
            that.prev();
          });

          $(window).on('resize', function() {
            var dimension = that.resize();
            $('.speasyimagegallery-modal').css({
              width: dimension.width,
              height: dimension.height
            });
          });
        }

        this.modal = function() {
          $('<div id="speasyimagegallery-modal" class="speasyimagegallery-modal-wrapper"><a href="#" class="speasyimagegallery-prev"><span></span></a><a href="#" class="speasyimagegallery-next"><span></span></a><div class="speasyimagegallery-modal"><a href="#" class="speasyimagegallery-close speasyimagegallery-hidden">&times;</a><div class="speasyimagegallery-modal-body"></div></div></div>').appendTo($('body').addClass('speasyimagegallery-modal-open'));
          this.modal = $('#speasyimagegallery-modal');
          this.navNext = this.modal.find('.speasyimagegallery-next');
          this.navPrev = this.modal.find('.speasyimagegallery-prev');
        }

        this.close = function() {
          this.index = 0;
          this.loaded = true;
          this.naturalWidth = 0;
          this.naturalHeight = 0;

          $('#speasyimagegallery-modal').fadeOut(function() {
            $(this).remove();
          });

          $('.speasyimagegallery-modal').animate({
            width: 100,
            height: 100
          }, 300, function() {
            $(this).remove();
            $('body').removeClass('speasyimagegallery-modal-open')
          });
        }

        // Resize modal window
        this.resize = function() {
          var maxWidth = ($(window).width()) - 80;
          var maxHeight = ($(window).height()) - 80;
          var ratio = 0;
          var width = this.naturalWidth;
          var height = this.naturalHeight;

          if(width > maxWidth){
            ratio = maxWidth / width;
            height = height * ratio;
            width = width * ratio;
          }

          if(height > maxHeight){
            ratio = maxHeight / height;
            width = width * ratio;
            height = height * ratio;
          }

          return {
            'width': width,
            'height': height
          }
        }

        // Go to next
        this.next = function() {
          if (this.index < this.count) {
            this.index = this.index + 1;
          } else {
            this.index = 0;
          }
          this.goto(this.index);
        }

        // Go to Prev
        this.prev = function() {
          if (this.index > 0) {
            this.index = this.index -1;
          } else {
            this.index = this.count;
          }
          this.goto(this.index);
        }

        // Go to index
        this.goto = function(index) {

          if(this.loaded === false) {
            var that = this;
            var $item = $(this.items[index]);
            that.loaded = true;

            $('.speasyimagegallery-modal-body').html('<div class="speasyimagegallery-gallery-loading"></div>');

            var img = $("<img />").attr('src', $item.attr('href')).on('load', function() {
              if (!this.complete || typeof this.naturalWidth == "undefined" || this.naturalWidth == 0) {

              } else {

                that.naturalWidth = this.naturalWidth;
                that.naturalHeight = this.naturalHeight;

                var dimension = that.resize();

                $('.speasyimagegallery-modal').animate({
                  width: dimension.width,
                  height: dimension.height
                }, 300, function() {
                  var galleryHtml = '<div class="speasyimagegallery-image-wrapper">';
                  galleryHtml += '<img class="speasyimagegallery-image" src="'+ img[0].src +'" alt="'+ $item.attr('data-alt') +'">';

                  if((settings.showCounter) || (settings.showTitle && $item.attr('data-title')) || (settings.showDescription && $item.attr('data-desc'))) {
                    if($item.attr('data-title') || $item.attr('data-description')) {
                      galleryHtml += '<div class="speasyimagegallery-image-content">';

                      if(settings.showCounter){
                        galleryHtml += '<span class="speasyimagegallery-gallery-stat">'+ (that.index + 1) + ' of ' + (that.count + 1) +'</span>';
                      }

                      if(settings.showTitle && $item.attr('data-title')) {
                        galleryHtml += '<span class="speasyimagegallery-image-title">'+ $item.attr('data-title') +'</span>';
                      }

                      if(settings.showDescription && $item.attr('data-desc')) {
                        galleryHtml += '<div class="speasyimagegallery-image-description">'+ $item.attr('data-desc') +'</div>';
                      }

                      galleryHtml += '</div>';
                    }
                  }

                  galleryHtml += '</div>'

                  $('.speasyimagegallery-modal-body').html(galleryHtml);
                  that.modal.find('.speasyimagegallery-hidden').removeClass('speasyimagegallery-hidden');
                  that.loaded = false;
                });

              }
            });
          }
        }

      }

      new speasyimagegallery().init();
    });
  }

})(jQuery);
