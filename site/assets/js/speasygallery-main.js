/**
* @package com_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2020 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

;(function($){
    $(document).on('click', '.speasyimagegallery-gallery-item', function(event) {
      event.preventDefault();
      var spdata = $(this).closest('.speasyimagegallery-gallery');
      $(this).speasyimagegallery({
        showTitle: spdata.data('showtitle'),
        showDescription: spdata.data('showdescription'),
        showCounter: spdata.data('showcounter')
      });
    });
})(jQuery);
