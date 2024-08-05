(function ($, Drupal) {
    Drupal.behaviors.contentTree = {
      attach: function (context, settings) {
        $('.content-tree-item', context).once('contentTree').click(function () {
          var nid = $(this).data('nid');
          $.ajax({
            url: '/content-tree/preview/' + nid,
            success: function (data) {
              $('#content-preview').html(data);
            }
          });
        });
      }
    };
  })(jQuery, Drupal);
  