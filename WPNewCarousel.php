<?php
/*
Plugin Name: WPNewCarousels
Plugin URI: http://wordpress.org/extend/plugins/wpnewcarousels/
Description: Provide functionality to create carousel that can be inserted to any wordpress page.
Author: Arjun Jain
Author URI: http://www.arjunjain.info
Version: 1.7
*/

global $wpnewcarousel_db_version;
$wpnewcarousel_db_version="1.2"; 
$olderversion=get_option('wpnewcarousel_db_version');   // find current version stored in database

/**
 * Add WPNewCarousel to wordpress Admin Section
 * @since 1.0
 */
require_once 'includes/ManageCarousel.php';
add_action('admin_menu', 'WPNewCarousels');
function WPNewCarousels() {
	add_menu_page('WPNewCarousels - Add new carousel','Add carousel', 'administrator', 'add-new-wpnewcarousel', 'AddWPNewCarousel',plugins_url('images/16_carousel.png',__FILE__));
	add_submenu_page('add-new-wpnewcarousel','Manage Carousels','All Carousels','administrator','list-all-wpnewcarousel','AdminWPNewCarousels');
	add_submenu_page('add-new-wpnewcarousel','Manage Slides','Add Slides','administrator','wpnewcarousel-add-image','WPNewCarouselAddImages');
}

/**
 * Add New Carousel 
 * @since 1.5
 */
function AddWPNewCarousel(){
	$mcObject=new ManageCarousel();
	$html="";
	$postdata='';
	$oldname='';
	if(isset($_GET['edit']) && isset($_GET['rid'])){
		$carouseldata=$mcObject->GetCarouselById($_GET['rid']);
		if(sizeof($carouseldata)>0){
			$postdata=array(
					'carouselid'=>$carouseldata->Id,
					'carouselname'=>$carouseldata->CarouselName,
					'oldcarouselname'=>$carouseldata->CarouselName,
					'carouselwidth'=>$carouseldata->CarouselWidth,
					'carouselheight'=>$carouseldata->CarouselHeight,
					'carouseleffect'=>$carouseldata->CarouselEffect,
					'startslide'=>$carouseldata->StartSlide,
					'animationspeed'=>$carouseldata->AnimationSpeed,
					'pausetime'=>$carouseldata->PauseTime,
					'shownav'=>$carouseldata->ShowNav,
					'hoverpause'=>$carouseldata->HoverPause,
			);
			$oldname=$postdata['carouselname'];
		}
		else{
			echo '<div class="error"><p>Carousel Id not exist</p></div>';
		}
	}
	if(isset($_POST['isSubmit'])){
		$postdata=array(
				'carouselid'=>trim($_POST['carouselid']),
				'carouselname'=>trim($_POST['carouselname']),
				'oldcarouselname'=>$_POST['oldcarouselname'],
				'carouselwidth'=>trim($_POST['carouselwidth']),
				'carouselheight'=>trim($_POST['carouselheight']),
				'carouseleffect'=>trim($_POST['carouseleffect']),
				'startslide'=>trim($_POST['startslide']),
				'animationspeed'=>trim($_POST['animationspeed']),
				'pausetime'=>trim($_POST['pausetime']),
				'shownav'=>trim($_POST['shownav']),
				'hoverpause'=>trim($_POST['hoverpause']),
		);
		if($postdata['startslide']=='')
			$postdata['startslide']=0;
		if($postdata['animationspeed']=='')
			$postdata['animationspeed']=500;
		if($postdata['pausetime']=='')
			$postdata['pausetime']=3000;
		$errormsg=$mcObject->validatecarousel($postdata);
		if($errormsg=="valid"){
			$mcObject->InsertNewCarousel($postdata);
			if($postdata['carouselid']=='')
				$errormsg='<div class="updated"><p>Carousel Added Successfully.</p></div>';
			else
				$errormsg='<div class="updated"><p>Carousel Updated Successfully.</p></div>';
			$html .= $mcObject->DisplayAddNewCarousel(array(),$errormsg);
		}
		else{
			$errormsg='<div class="error"><p>'.$errormsg.'</p></div>';
			$html .=$mcObject->DisplayAddNewCarousel($postdata, $errormsg);
		}
	}
	if(!isset($_POST['isSubmit']))
		$html .=$mcObject->DisplayAddNewCarousel($postdata);
	echo $html;
}


/** 
 * Display Manage Carousel Console
 * @since 1.5
 */
function AdminWPNewCarousels(){
	$mcObject=new ManageCarousel();
	if($mcObject->CheckCarouselExist()){
		if(isset($_GET['edit']) && isset($_GET['action']) && isset($_GET['rid']))
			$mcObject->CarouselAction($_GET['action'],$_GET['rid']);	
		echo $mcObject->DisplayCarouselList();
	}
	else{
		echo '<div class="wrap">
				<div style="width:32px; float:left;height:32px; margin:7px 8px 0 0;"><img src="'.plugins_url('images/32_carousel.png',__FILE__).'"></div>	  
				<h2>WPNewCarousels</h2><br />
				<div class="updated"><p>Please Add <a href="?page=add-new-wpnewcarousel">New Carousel</a></p></div>
			  </div>';
	}
}

/**
 * Add Images to carousel
 * @since 1.5
 */
function WPNewCarouselAddImages(){
	$mcObject=new ManageCarousel();
	if($mcObject->CheckCarouselExist()){
		$msg="";
		if(isset($_POST['saveCarousel'])){
			$Id=$_POST['Id'];
			$carouselId=$_POST['carouselid'];
			$BackgroundImageURL=$_POST['BackgroundImageURL'];
			$BackgroundImageLink=$_POST['BackgroundImageLink'];
			$BackgroudImageAltText=$_POST['BackgroundImageAltText'];
			$TitleText=$_POST['TitleText'];
			$slideDisplayOrder = $_POST['position'];
			$allids=array();
			$previous_slides=$mcObject->GetCarouselDataById($carouselId);
			if(sizeof($previous_slides) > 0){
				foreach ($previous_slides as $slides){
					array_push($allids,$slides->Id);	
				}
			}
			for($i=0;$i<sizeof($Id);$i++){
				if($Id[$i] !=""){
					if(in_array($Id[$i],$allids)){
						if(($key = array_search($Id[$i],$allids )) !== false) {
 					   		unset($allids[$key]);
						}
					}
					$mcObject->UpdateCarouselSlides($Id[$i], $carouselId, trim($BackgroundImageURL[$i]), trim($BackgroundImageLink[$i]), trim($BackgroudImageAltText[$i]), trim($TitleText[$i]), $slideDisplayOrder[$i]);
				}
				else{
					//add
					if(trim($BackgroundImageURL[$i])!="")
						$mcObject->InsertCarouselSlides($carouselId, trim($BackgroundImageURL[$i]),trim($BackgroundImageLink[$i]),trim($BackgroudImageAltText[$i]), trim($TitleText[$i]), $slideDisplayOrder[$i]);		
				}				
			}	
			
			// remove deleted ids
			if(sizeof($allids) > 0){
				$mcObject->DeleteCarouselSlides($allids);
			}
			$msg='<div class="updated"><p>Carousel Updated Successfully</a></p></div>';
		}
		echo $mcObject->DisplayCarouselSlides($msg);
	}
	else{
		echo '<div class="wrap">
				<div style="width:32px; float:left;height:32px; margin:7px 8px 0 0;"><img src="'.plugins_url('images/32_carousel.png',__FILE__).'"></div>	  
				<h2>WPNewCarousels</h2><br />
				<div class="updated"><p>Please Add <a href="?page=add-new-wpnewcarousel">New Carousel</a></p></div>
			  </div>';
	}
	
}


/**************************************************************************************************
*************************************  WPNEWCAROUSEL SHORTCODE ************************************
**************************************************************************************************/

/**
 * WPNewCarousel Shortcode 
 * Accept three parametes Name, Width, Height . Width and Height will replace default width and height set for carousel
 * Name is required parameter in carousel
 * Height and Width are the optional parameters in carousel
 * Startslide is the starting slide number, default value is 0
 * Animationspeed is the speed of carousel animation, default value is 500 [ where 1000 = 1sec ]
 * Imagepause is the time between image change, default value is 3000
 * Shownav is the flag to show navigation with carousel or not, default value is true
 * Hoverpause is the flag to stop carousel on mouse over, default value is true
 * Effect is the type of effect in image transition
 *
 * === FINAL SHORTCODE ===
 * [wpnewcarousel name="YOUR_CAROUSEL_NAME" height="" width=""  effect="" startslide="" animationspeed="" imagepausetime="" shownav="" hoverpause=""]
 * 
 * @since: 1.0
 */
add_shortcode('wpnewcarousel','WPNewCarouselShortcode');
function WPNewCarouselShortcode($atts){
	extract(shortcode_atts(array(
		'name' => '',
	    'width' =>'',
		'height' =>'',
		'startslide'=>'',
		'animationspeed'=>'',
		'imagepausetime'=>'',
		'shownav'=>'',
		'hoverpause'=>'',
		'effect'=>''
	),$atts));
	
 	if(trim($name)=="")
		return "Please specify the carousel name";
	static $i=1;
	
	$mc=new ManageCarousel();
	$carouselresults=$mc->GetCarouselByName($name);
	
	if(sizeof($carouselresults)==0)
		return "Please specify the correct carousel name";

	$validarray=array("true","false");
	$effectsarray=array("sliceDown","sliceDownLeft","sliceUp","sliceUpLeft","sliceUpDown","sliceUpDownLeft","fold","fade","random","slideInRight","slideInLeft","boxRandom","boxRain","boxRainReverse","boxRainGrow","boxRainGrowReverse");
	
	if(trim($height)=="" ||!is_numeric($height) || trim($height)=="0")
		$height=$carouselresults->CarouselHeight;
	
	if(trim($width)=="" || !is_numeric($width) || trim($width)=="0")
		$width=$carouselresults->CarouselWidth;
	
	if(trim($startslide)=="" || !is_numeric($startslide)){
		if($carouselresults->StartSlide != "")
			$startslide=$carouselresults->StartSlide;
		else
			$startslide=0;
	}
	
	if(trim($animationspeed)=="" ||!is_numeric($animationspeed)){
		if($carouselresults->AnimationSpeed != "")
			$animationspeed=$carouselresults->AnimationSpeed;
		else
			$animationspeed=500;
	}
	
	if(trim($imagepausetime)=="" || !is_numeric($imagepausetime)){
		if($carouselresults->PauseTime != "")
			$imagepausetime=$carouselresults->PauseTime;
		else
			$imagepausetime=3000;	
	}
	
	if(trim($shownav)=="" || !in_array(strtolower($shownav),$validarray)){
		if($carouselresults->ShowNav != "")
			$shownav=$carouselresults->ShowNav;
		else 
			$shownav="true";
	}		
	
	if(trim($hoverpause)=="" || !in_array(strtolower($hoverpause),$validarray)){
		if($carouselresults->HoverPause != "")
			$hoverpause=$carouselresults->HoverPause;
		else
			$hoverpause="true";
	}
	
	if(trim($effect)=="" ||!in_array($effect,$effectsarray)){
		if($carouselresults->CarouselEffect != "")
			$effect=$carouselresults->CarouselEffect;
		else
			$effect="random";
	}
		
	$carouseldata=$mc->GetCarouselDataById($carouselresults->Id);
	$output='';
	if(sizeof($carouseldata)>0){
			$output = '<script type="text/javascript">
					     jQuery(document).ready(function() {
			       			 jQuery(".nivoSlider'.$i.'").nivoSlider({
			       			 	effect:	"'.$effect.'",  
			    				startSlide:'.$startslide.', 
			        			animSpeed:'.$animationspeed.',
			            		pauseTime:'.$imagepausetime.',
			    				controlNav:'.$shownav.', 
			    				pauseOnHover:'.$hoverpause.'     	
			        		});
					    });
					</script>';
			
			$output .= '<div class="nivoSlider'.$i.'" style="width:'.$width.'px; height:'.$height.'px;" >';
			foreach ($carouseldata as $result){
				if($result->BackgroundImageLink != "")
					$output .='<a href="'.$result->BackgroundImageLink.'" >';
				$output .='<img src="'.$result->BackgroundImageURL.'"  width="'.$width.'" height="'.$height.'"  alt="'.$result->BackgroundImageAltText.'" title="'.$result->TitleText.'" >';
				if($result->BackgroundImageLink != "")
					$output .='</a>';
			}
			$output .= '</div>';
	}
	$i++;
	return $output;
}

/*************************************************************************************************
 *********************************** Add dependent scripts and styles ****************************
**************************************************************************************************/

/**
 * Include js and css for carousel
 * @since 1.0
 */
add_action( 'wp_enqueue_scripts', 'wpnewcarousel_script' );
add_action( 'wp_print_styles', 'WPNewCarousel_Styles' );

function wpnewcarousel_script() {
	wp_enqueue_script('jquery');
	wp_register_script( 'wpnewcarousel_script',path_join( WP_PLUGIN_URL, basename( dirname( __FILE__ ) ) .'/js/jquery.nivo.slider.js' ) , array('jquery') );
	wp_enqueue_script('wpnewcarousel_script');	
}
function WPNewCarousel_Styles() {
	wp_enqueue_style( 'WPNewCarousel_Styles',
			path_join( WP_PLUGIN_URL,
					basename( dirname( __FILE__ ) ) .
					'/css/carousel.css' ));
}

/**
 *  Add media upload script to plugin
 *  @since 1.4
 */
if (isset($_GET['page']) && $_GET['page'] == 'wpnewcarousel-add-image'){
	add_action('admin_print_scripts', 'wpnewcarousel_admin_scripts');
	add_action('admin_print_styles', 'wpnewcarousel_admin_styles');
}
function wpnewcarousel_admin_scripts() {
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_register_script('wc-upload',path_join( WP_PLUGIN_URL,basename( dirname( __FILE__ )).'/js/upload-script.js'),array('jquery','media-upload','thickbox'));
	wp_enqueue_script('wc-upload');
}
function wpnewcarousel_admin_styles() {
	wp_enqueue_style('thickbox');
}

/**
 * Add Settings tab with plugin admin section
 * @since 1.5
 */
/*
add_filter("plugin_action_links",'wpnewcarouselsettingslink','administrator',2);
function wpnewcarouselsettingslink($link,$file){
	static $this_plugin;
	if (!$this_plugin) {
		$this_plugin = plugin_basename(__FILE__);
	}
	if ($file == $this_plugin) {
		$settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=add-new-wpnewcarousel">Settings</a>';
		array_unshift($link, $settings_link);
	}
	return $link;
}
*/

/**
 * Add carousel button to editor
 * @since 1.3
 */
add_action('init', 'wpnewcarousel_editor_button');
function wpnewcarousel_editor_button() {
	if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
		if ( in_array(basename($_SERVER['PHP_SELF']), array('post-new.php', 'page-new.php', 'post.php', 'page.php') ) ) 
			{    
    		 add_action('admin_head','wpnewcarousel_add_simple_buttons');
   			}
	}
}
function wpnewcarousel_add_simple_buttons(){ 
    wp_print_scripts( 'quicktags' );
	$output = "<script type='text/javascript'>\n
	/* <![CDATA[ */ \n";
	
	$buttons = array();
	$buttons[] = array('name' => 'wpnewcarousel',
					'options' => array(
						'display_name' => 'wpnewcarousel',
						'open_tag' => '\n[wpnewcarousel name="" width="" height="" effect="" startslide="" animationspeed="" imagepausetime="" shownav="" hoverpause="" ]',
						'key' => ''
					));
					
					
	for ($i=0; $i <= (count($buttons)-1); $i++) {
		$output .= "edButtons[edButtons.length] = new edButton('ed_{$buttons[$i]['name']}'
			,'{$buttons[$i]['options']['display_name']}'
			,'{$buttons[$i]['options']['open_tag']}'
			,'{$buttons[$i]['options']['key']}'
		); \n";
	}
	
	$output .= "\n /* ]]> */ \n
	</script>";
	echo $output;
}



/********************************************************************************************
******************************** ON PLUGIN ACTIVATION ACTION ********************************
********************************************************************************************/

register_activation_hook( __FILE__, "WPNewCarousels_activate" );
function WPNewCarousels_activate(){
	global $wpdb,$wpnewcarousel_db_version;
	if (function_exists('is_multisite') && is_multisite()) {
		if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
			$old_blog = $wpdb->blogid;
			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			 foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				$mcObject=new ManageCarousel();
				$mcObject->CreateTable();
			}
			switch_to_blog($old_blog);
			return; 
		}
		else{
			$mcObject=new ManageCarousel();
			$mcObject->CreateTable();
		}
	}
	else{
		$mcObject=new ManageCarousel();
		$mcObject->CreateTable();
	}
	add_option("wpnewcarousel_db_version", $wpnewcarousel_db_version);
}

/**
 * Update database < major changes from version 1.4 to 1.5 >
 * @since 1.5
 * @version 1.6
 */
add_action('plugins_loaded', 'wpnewcarousel_update_db_check');
function wpnewcarousel_update_db_check() {
	global $wpdb,$wpnewcarousel_db_version,$olderversion;
	
	if($olderversion == '1.1'){
		if (function_exists('is_multisite') && is_multisite()) {
			if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
				$old_blog = $wpdb->blogid;
				$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
				foreach ($blogids as $blog_id) {
					switch_to_blog($blog_id);
					$mcObject=new ManageCarousel();
					$mcObject->UpdateTable_AddWeight();
				}
				switch_to_blog($old_blog);
				return;
			}
			else{
				$mcObject=new ManageCarousel();
				$mcObject->UpdateTable_AddWeight();
			}
		}
		else{
			$mcObject=new ManageCarousel();
			$mcObject->UpdateTable_AddWeight();
		}
		update_option('wpnewcarousel_db_version', $wpnewcarousel_db_version); // update database version from 1.1 to 1.2
	}
	else{
		if ( $olderversion != $wpnewcarousel_db_version) {
			if (function_exists('is_multisite') && is_multisite()) {
				if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
					$old_blog = $wpdb->blogid;
					$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
					foreach ($blogids as $blog_id) {
						switch_to_blog($blog_id);
						$mcObject=new ManageCarousel();
						$mcObject->UpdateTable();
					}
					switch_to_blog($old_blog);
					return;
				}
				else{
					$mcObject=new ManageCarousel();
					$mcObject->UpdateTable();
				}
			}
			else{
				$mcObject=new ManageCarousel();
				$mcObject->UpdateTable();
			}
			update_option('wpnewcarousel_db_version', $wpnewcarousel_db_version); // update database version from 1.0 to 1.1
		}
	}
}
?>
