jQuery(document).ready(function($){
  "use strict";
  $(document).on("click", ".upload_image_button", function() {
    
    jQuery.data(document.body, 'prevElement', $(this).prev());
    
    window.send_to_editor = function(html) {
      var imgurl = jQuery('img',html).attr('src');
      var inputText = jQuery.data(document.body, 'prevElement');
      
      if(inputText != undefined && inputText != '')
      {
        inputText.val(imgurl);
      }
      
      tb_remove();
    };
    
    tb_show('', 'media-upload.php?type=image&TB_iframe=true');
    return false;
  });
  //hideListPluginWP();
  function hideListPluginWP(){
    var elemBody    = $("body"),
        list_plugin = elemBody.find('table.plugins'),
        list_disable = [];
    list_plugin.find("tr").each(function () {
      var self        = $(this);
      var slug = self.attr("data-slug");
      var plugin_hide = [
        "advanced-custom-fields",
        "custom-post-type-ui",
        "menu-image",
        "wp-simple-galleries",
        "wp-multiple-taxonomy-images",
      ];
      if(plugin_hide.indexOf(slug) !== -1){
        self.hide();
      }
    });
    
  }
});
