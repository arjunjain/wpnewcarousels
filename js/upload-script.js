/**
 * @author   Arjun Jain ( http://www.arjunjain.info ) 
 * @license  GNU GENERAL PUBLIC LICENSE Version 3
 * @since    1.4
 * @version  1.6
 */

jQuery(document).ready(function() {
jQuery('.upload_image_button').live('click',function() {
formfield = jQuery(this).prev('.uploadurl');//.attr('name');
tb_show('', 'media-upload.php?post_id=1&amp;type=image&amp;TB_iframe=true');
return false;
});

window.send_to_editor = function(html) {
imgurl = jQuery('img',html).attr('src');
jQuery(formfield).val(imgurl);
tb_remove();
}});