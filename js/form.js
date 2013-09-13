/**
 * @author  Arjun Jain ( http://www.arjunjain.info ) 
 * @license GNU GENERAL PUBLIC LICENSE Version 3
 * @since   1.0
 * @version 1.6
 */

function addSlides(em,filepath){
	var formname=em.name;
	var count=document.forms[formname].elements["numberofslideadd"].value;
		jQuery.ajax({
			type:"POST",  
			url:filepath,  
			data:"pcount="+count,
			success: function(data){
				jQuery("#ajaxslide").append(data);
				var get = jQuery(".slide-carousal").length - 1;
				for(i = count;i > 0;i--)
				{
					jQuery(jQuery(".slide-carousal")[get]).attr("id","slide-"+get);
					jQuery(jQuery(".slide-carousal")[get]).find(".position-fixer").attr("value",get);
					get--;
				}
			}
		});
		return false;
}
jQuery(function(){
    jQuery( ".draggable-listings tbody" ).sortable({
		stop: function( event, ui ) {
			jQuery("#slides-save-button").attr("disabled","true");
			for(i = 0;i < jQuery(".slide-carousal").length;i++){
				jQuery(jQuery(".slide-carousal")[i]).find(".position-fixer").attr("value",i);
			}
			jQuery("#slides-save-button").removeAttr("disabled");
		}
	});		
});
		
function saveCarousalOrder(getPath){
	var getCarosalId = jQuery(jQuery(".draggable-listings tbody tr")[0]).attr("class").split("-");
	var slidesOrder = new Object();

	slidesOrder.carosal_id = getCarosalId[2];
	slidesOrder.slides_order = new Object();
	
	for(i = 0;i < jQuery(".draggable-listings tbody tr").length;i++){
		var getSlideId = jQuery(jQuery(".draggable-listings tbody tr")[i]).attr("id").split("-");
        slidesOrder.slides_order[i] = new Object();
		slidesOrder.slides_order[i].slide_id = getSlideId[1];
		slidesOrder.slides_order[i].order = i;
	}
	//console.log(slidesOrder);
	var get = JSON.stringify(slidesOrder);
	
	jQuery.ajax({    
		type: "POST",
		url: getPath,
		data: { slidesOrder : get },
		success: function(){
			document.getElementById('carossal-inputs-form').submit();
		}
	});
}