/**
 * @author  Arjun Jain ( http://www.arjunjain.info ) 
 * @license GNU GENERAL PUBLIC LICENSE Version 3
 * @since   1.0
 * @version 1.5
 */

function addSlides(em,filepath){
	
	var formname=em.name;
	var count=document.forms[formname].elements["numberofslideadd"].value;
		jQuery.ajax({
			type:"POST",  
			url:filepath,  
			data:"pcount="+count,
			success: function(data){
				var curdata=document.getElementById("ajaxslide").innerHTML;
				document.getElementById("ajaxslide").innerHTML=curdata+data;
			}
		});
		return false;
}