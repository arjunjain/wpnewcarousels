<?php
/**
 * @author  Arjun Jain  < http://www.arjunjain.info >
 * @version 1.6 
 * @license GNU GENERAL PUBLIC LICENSE Version 3
 */
class ManageCarousel{
	private $_DataObject;
	private $_carouselTable;
	private $_carouselData;
	
	function __construct(){
		global $wpdb;
		$this->_DataObject=$wpdb;
		$this->_carouselTable=$this->_DataObject->prefix."wpnewcarousel";
		$this->_carouselData=$this->_DataObject->prefix."wpnewcarouseldata";
	}

	
/**************************************************************************************************	
******************************* Manage Carousel Slides ********************************************
**************************************************************************************************/	
	/**
	 * Display all slides of carousel
	 * @param string $msg
	 */
	public function DisplayCarouselSlides($msg){
		$query="SELECT * FROM {$this->_carouselTable}";
		$html ='<script type="text/javascript" src="'.plugins_url('js/form.js',dirname(__FILE__)).'"></script>';
		$html .='<div class="wrap">
				<div style="width:32px; float:left;height:32px; margin:7px 8px 0 0;"><img src="'.plugins_url('images/32_carousel.png',dirname(__FILE__)).'"></div>	  
				<h2>WPNewCarousels</h2>'.$msg.'
				<div class="align-left actions" style="margin-top:15px;margin-bottom:5px;">
				<form action="" method="POST" name="selectcarousel">
					Select Carousel :<select name="carouselid">';
		$html .=$this->GetOptionsString($query,"Id","CarouselName",@$_POST['carouselid']);	
	 	$html .='</select>
				<input type="submit" class="button" name="addupdatesubmit" value="Add/Update Slides"></form>
				</div>';
		if(isset($_POST['addupdatesubmit'])){
				$html .='<form name="addcarouseldata" method="POST" action="" >
						<table class="wp-list-table widefat fixed posts draggable-listings" cellspacing="0">
						<thead>
							<tr>
								<th class="manage-column column-title" scope="col" >Background Image URL</th>
								<th class="manage-column column-title" scope="col">Background Image href link</th>
								<th class="manage-column column-title" scope="col">Background Image Alt Text</th>
								<th class="manage-column column-title" scope="col">Background Image Title</th>
							</tr>
						</thead>
						<tbody id="ajaxslide">';
			$carouselid=$_POST['carouselid'];
			$carouseldata=$this->_DataObject->get_results("SELECT * FROM {$this->_carouselData} WHERE CarouselId={$carouselid}  order by weight asc",ARRAY_A);
			if(sizeof($carouseldata)>0){
				foreach ($carouseldata as $carousel){
					$html .= $this->getSlide($carousel);
				}
			}
			else{
				for($i=0;$i<3;$i++){
//					$html .=$this->getSlide();
					$html .=$this->getInitialSLides($i);
				}
			}	
			$html .='</tbody>
					 <tfoot>
						<tr>
							<th colspan="4" class="manage-column column-title"  scope="col">
								<input type="hidden" name="carouselid" value="'.$carouselid.'"><input type="submit" class="button" style="padding:3px 8px;" name="saveCarousel" value="Save" id="slides-save-button"/></form>
								or 
								<form style="display:inline;" name="addmoreslideform" method="POST" onsubmit="return addSlides(this,\''.plugins_url('includes/DisplaySlides.php',dirname(__FILE__)).'\');">
					 				<input type="text" value="1" maxlength="1" size="1" name="numberofslideadd"  style="height:24px; width:30px">
									<input type="submit" class="button" style="padding:3px 8px;" name="addmoreslides" value="Add" />
								</form>
							</th>
						</tr>
						<tr><td colspan="4"><p><img title="" src="'.plugins_url('images/hand.jpeg',dirname(__FILE__)).'" /><small>drag the rows to update the slider order (Supported in IE9+, FF, Crome, Safari)</small></p></td></tr>
					</tfoot>
				</table>';
		}
		return $html;
	}
	
	/**
	 * Create default slides
	 * @param array $post_data
	 */
	public function getInitialSLides($number)
	{
		$html = '<tr valign="top" class="slide-carousal carousal-id-" id="slide-'.$number.'">
		<td class="title column-title"><input style="width:100%" type="text" name="BackgroundImageURL[]" value="'.@addslashes($postdata['BackgroundImageURL']).'" class="uploadurl" />
		<input class="button upload_image_button" type="button" value="Select Image" /></td>
		<td class="title column-title"><input style="width:100%" type="text" name="BackgroundImageLink[]" value="'.@addslashes($postdata['BackgroundImageLink']).'" /></td>
		<td class="title column-title"><input style="width:100%" type="text" name="BackgroundImageAltText[]" value="'.@addslashes($postdata['BackgroundImageAltText']).'" /></td>
		<td class="title column-title"><input style="width:100%" type="text" name="TitleText[]" value="'.@addslashes($postdata['TitleText']).'" />
		<input type="hidden" name="Id[]" value="" class="row-id" />
		<input type="hidden" name="position[]" value="'.$number.'" class="position-fixer" /></td>
		</tr>';
		return $html;
	}
	
	/**
	 * Create default slides 
	 * @param array $post_data
	 */
	public function getSlide($postdata=array()){
		$html = '<tr valign="top" class="slide-carousal carousal-id-'.@$postdata['CarouselId'].'" id="slide-'.@$postdata['Id'].'">
					<td class="title column-title"><input style="width:100%" type="text" name="BackgroundImageURL[]" value="'.@addslashes($postdata['BackgroundImageURL']).'" class="uploadurl" />
					<input class="button upload_image_button" type="button" value="Select Image" /></td>
			  		<td class="title column-title"><input style="width:100%" type="text" name="BackgroundImageLink[]" value="'.@addslashes($postdata['BackgroundImageLink']).'" /></td>
			  		<td class="title column-title"><input style="width:100%" type="text" name="BackgroundImageAltText[]" value="'.@addslashes($postdata['BackgroundImageAltText']).'" /></td>
			  		<td class="title column-title"><input style="width:100%" type="text" name="TitleText[]" value="'.@addslashes($postdata['TitleText']).'" />
			  		<input type="hidden" name="Id[]" value="'.@$postdata['Id'].'" />
					<input type="hidden" name="position[]" value="'.@$postdata['weight'].'" class="position-fixer" /></td>
			  	</tr>';
		return $html;
	}
	
	/**
	 * Insert slides into database
	 * @param int $carouselId
	 * @param string $BackgroundImageURL
	 * @param string $BackgroundImageLink
	 * @param string $BackgroudImageAltText
	 * @param string $TitleText
	 * @param int    $slideDisplayOrder
	 */
	public function InsertCarouselSlides($carouselId,$BackgroundImageURL,$BackgroundImageLink,$BackgroudImageAltText,$TitleText,$slideDisplayOrder){
		try{
			$this->_DataObject->insert($this->_carouselData,array('CarouselId'=>$carouselId,
																  'BackgroundImageURL'=>$BackgroundImageURL,
							                                      'BackgroundImageLink'=>$BackgroundImageLink,
							                                      'BackgroundImageAltText'=>$BackgroudImageAltText,
							                                      'TitleText'=>$TitleText,
																  'weight'=>$slideDisplayOrder),
					                                        array('%d','%s','%s','%s','%s','%d'));
		}catch (Exception $e){
			echo "Error: ".$e->getMessage(); 
		}
	}
	
	/**
	 * Update carousel data into database
	 * @param int $Id
	 * @param int $carouselId
	 * @param string $BackgroundImageURL
	 * @param string $BackgroundImageLink
	 * @param string $BackgroudImageAltText
	 * @param string $TitleText
	 * @param int 	 $slideDisplayOrder
	 */
	public function UpdateCarouselSlides($Id,$carouselId,$BackgroundImageURL,$BackgroundImageLink,$BackgroudImageAltText,$TitleText,$slideDisplayOrder){
		try{
			$this->_DataObject->update($this->_carouselData,array('BackgroundImageURL'=>stripslashes($BackgroundImageURL),
																  'BackgroundImageLink'=>stripslashes($BackgroundImageLink),
																  'BackgroundImageAltText'=>stripslashes($BackgroudImageAltText),
																  'TitleText'=>stripslashes($TitleText),
																  'weight'=>$slideDisplayOrder ),
															array('CarouselId'=>$carouselId,
																  'Id'=>$Id),
															array('%s','%s','%s','%s','%d'),
															array('%d','%d'));
		}catch (Exception $e){
			echo "Error: ".$e->getMessage(); 
		}
	}
	
	/**
	 * delete carousel data
	 * @param int $Id
	 * @since 1.5
	 */
	public function DeleteCarouselSlides($Id){
		try{		
			$this->_DataObject->query($this->_DataObject->prepare("DELETE FROM {$this->_carouselData} WHERE Id = %d",$Id));
		}
		catch(Exception $e){
			echo "Error: ".$e->getMessage();
		}
	}
	
	/**
	 * Get all the carousel data by carousel id
	 * @param int $carouselId
	 * @return array
	 */
	public function GetCarouselDataById($carouselId){
		try{
			$result=$this->_DataObject->get_results($this->_DataObject->prepare('SELECT * FROM '.$this->_carouselData.' WHERE CarouselId=%d order by weight asc',$carouselId));
			return $result;
		}catch(Exception $e){
			echo "Error: ".$e->getMessage();
		}
	}
	
/******************************************************************************************	
****************************** Manage Carousel ********************************************
******************************************************************************************/	
	
	/**
	 * Check any carousel exists or not
	 * @return boolean
	 */
	public function CheckCarouselExist(){
		try{
			$results=$this->_DataObject->get_var('SELECT Id FROM '.$this->_carouselTable.' LIMIT 0,1');
			if(sizeof($results)==0)
				return false;
			else
				return true;
		}catch(Exception $e){
			echo "Error: ".$e->getMessage();
		}
	}
	
	/**
	 * Display all carousel
	 */
	public function DisplayCarouselList(){
		$query="SELECT * FROM {$this->_carouselTable}";
		$allcarousel=$this->_DataObject->get_results($query);
		$html='<div class="wrap">
				<div style="width:32px; float:left;height:32px; margin:7px 8px 0 0;"><img src="'.plugins_url('images/32_carousel.png',dirname(__FILE__)).'"></div>	  
				<h2>WPNewCarousels <a class="add-new-h2" href="admin.php?page=add-new-wpnewcarousel">Add New Carousel</a></h2>
				<table class="wp-list-table widefat fixed posts" cellspacing="0">
					<thead>
						<tr>
							<th class="manage-column column-author" scope="col">Carousel Name</th>
							<th class="manage-column column-comments" scope="col"></th>
							<th class="manage-column column-author" scope="col">Carousel Width</th>
							<th class="manage-column column-author" scope="col">Carousel Height</th>
							<th class="manage-column column-author" scope="col">Carousel Effect</th>
							<th class="manage-column column-author" scope="col">Animation speed</th>
							<th class="manage-column column-author" scope="col">Pause time</th>
							<th class="manage-column column-author" scope="col">Navigation icons</th>
							<th class="manage-column column-author" scope="col">Hover Pause</th>
						</tr>
					</thead>
					<tbody id="the-list">';
		foreach ($allcarousel as $carousel){
			$html .=	'<tr class="" valign="top">
							<td colspan="2" class="author column-author">
								<strong><a href="?page=add-new-wpnewcarousel&edit=1&rid='.$carousel->Id.'">'.$carousel->CarouselName.'</a></strong>
								<div class="row-actions">
									<span class="edit"><a href="?page=add-new-wpnewcarousel&edit=1&rid='.$carousel->Id.'">Edit</a> | </span>';
									if($carousel->IsActive)
			$html .=					'<span class="activate"><a href="?page=list-all-wpnewcarousel&edit=1&action=deactivate&rid='.$carousel->Id.'">Deactivate</a> | </span>';
									else 
			$html .=					'<span class="activate"><a href="?page=list-all-wpnewcarousel&edit=1&action=activate&rid='.$carousel->Id.'">Activate</a> | </span>';
			$html .=				'<span class="trash"><a href="?page=list-all-wpnewcarousel&edit=1&action=delete&rid='.$carousel->Id.'">Delete</a></span>
								</div>
							</td>
							<td class="author column-author"><a href="?page=add-new-wpnewcarousel&edit=1&rid='.$carousel->Id.'">'.$carousel->CarouselWidth.'</a></td>
							<td class="author column-author"><a href="?page=add-new-wpnewcarousel&edit=1&rid='.$carousel->Id.'">'.$carousel->CarouselHeight.'</a></td>
							<td class="author column-author"><a href="?page=add-new-wpnewcarousel&edit=1&rid='.$carousel->Id.'">'.$carousel->CarouselEffect.'</a></td>
							<td class="author column-author"><a href="?page=add-new-wpnewcarousel&edit=1&rid='.$carousel->Id.'">'.$carousel->AnimationSpeed.'</a></td>
							<td class="author column-author"><a href="?page=add-new-wpnewcarousel&edit=1&rid='.$carousel->Id.'">'.$carousel->PauseTime.'</a></td>						
							<td class="author column-author"><a href="?page=add-new-wpnewcarousel&edit=1&rid='.$carousel->Id.'">'.$carousel->ShowNav.'</a></td>	
							<td class="author column-author"><a href="?page=add-new-wpnewcarousel&edit=1&rid='.$carousel->Id.'">'.$carousel->HoverPause.'</a></td>
						</tr>';					
		}	
		$html .='	</tbody>
					<tfoot>
						<tr>
							<th colspan="9"class="manage-column column-title" style="text-align:right !important;" scope="col">Find Bug or suggest new feature please <a target="_blank" href="http://www.arjunjain.info/contact">click here</a></th>
						</tr>
					</tfoot>
				</table>
			</div>';
		return $html;
	}
	
	/**
	 * Display Carousel form
	 * @param array $postdata
	 * @param string $errormsg
	 */
	public function DisplayAddNewCarousel($postdata=array(),$errormsg=''){
		if(isset($postdata['carouselid']) && ($postdata['carouselid']==0))
			$buttontext="Add new carousel";
		else 
			$buttontext="Update carousel";
		
		$anims = array(	'random'=>'Random',
						'sliceDown'=>'SliceDown',
						'sliceDownRight'=>'Slicedownright',
						'sliceDownLeft'=>'Slicedownleft',
						'sliceUp'=>'SlideUp',
						'sliceUpRight'=>'Sliceupright',
						'sliceUpLeft'=>'Sliceupleft',
						'sliceUpDown'=>'Sliceupdown',
						'sliceUpDownLeft'=>'Sliceupdownleft',
						'slideInRight'=>'SlideInRight',
						'slideInLeft'=>'SlideInLeft',
						'fold'=>'Fold',
						'fade'=>'Fade',
						'boxRandom'=>'BoxRandom',
						'boxRain'=>'BoxRain',
						'boxRainReverse'=>'Boxrainreverse',
						'boxRainGrow'=>'Boxraingrow',
						'boxRainGrowReverse'=>'Boxraingrowreverse');

		$truefalse=array("true"=>"Yes","false"=>"No");
		$html='<div class="wrap">
					<div style="width:32px; float:left;height:32px; margin:7px 8px 0 0;"><img src="'.plugins_url('images/32_carousel.png',dirname(__FILE__)).'" /></div>
			   		<h2>Add New Carousel</h2>'.$errormsg.'
			   		<div  style="margin-top:10px;" class="sidebar-name no-movecursor"><h3>Carousel settings</h3></div>
						<div class="popover-holder">			   		
		   				<form action="'.esc_attr($_SERVER['REQUEST_URI']).'" method="POST" name="carouselform">
							<input type="hidden" name="carouselid" value="'.@$postdata['carouselid'].'" />
							<input type="hidden" name="oldcarouselname" value="'.@$postdata['oldcarouselname'].'" />
							<tbody>
							<table class="form-table" style="margin-top:0px;">
								<tr>
			  						<th scope="row"><label for="carouselname" >Carousel Name*</label></th>
			  						<th><input type="text" id="carouselname" name="carouselname" value="'.@$postdata['carouselname'].'" class="regular-text" /></th>
			  					</tr>
			  					<tr>
			  						<th scope="row"><label for="carouselwidth" >Carousel Width*</label></th>
			  						<th><input type="text" id="carouselwidth" name="carouselwidth" value="'.@$postdata['carouselwidth'].'" class="small-text" />px</th>
			  					</tr>
								<tr>
			  						<th scope="row"><label for="carouselheight" >Carousel Height*</label></th>
			  						<th><input type="text" id="carouselheight" name="carouselheight" value="'.@$postdata['carouselheight'].'" class="small-text" />px</th>
			  					</tr>			
			  					<tr>
			  						<th scope="row"><label for="carouseleffect" >Carousel Effect</th>
			  						<th>
			  							<select name="carouseleffect">';
			  	
			  	foreach ($anims as $key=>$value){
			  		$html .= '<option value="'.$key.'" ';
			  		if(isset($postdata['carouseleffect']) && ($key== $postdata['carouseleffect']))
			  			$html .= 'selected="selected"';
			  		$html .='>'.$value.'</option>';								
			  	}
			  	$html .=				'</select><p class="description">Default value Random</p>
			  						</th>
			  					</tr>
			  					<tr>
			  						<th scope="row"><label for="startslide" >Starting Slide</th>
			  						<th><input type="text" id="startslide" name="startslide" value="'.@$postdata['startslide'].'" class="small-text" />
			  							<p class="description">Default value 0</p>
			  						</th>
			  					</tr>
			  					<tr>
			  						<th scope="row"><label for="animationspeed" >Carousel Animation Speed</th>
			  						<th><input type="text" id="animationspeed" name="animationspeed" value="'.@$postdata['animationspeed'].'" class="small-text" />
			  							<p class="description">Default value 500 [1000 = 1 sec]</p>
			  						</th>
			  					</tr>
			  					<tr>
			  						<th scope="row"><label for="pausetime" >Pause time</th>
			  						<th><input type="text" id="pausetime" name="pausetime" value="'.@$postdata['pausetime'].'" class="small-text" />
			  							<p class="description">Default value 3000 [1000 = 1 sec]</p>
			  						</th>
			  					</tr>
								<tr>
									<th scope="row"><label for="shownav">Show Navigation Icon</th>
									<th>
										<select name="shownav">';
				foreach ($truefalse as $key=>$value){
					$html .= "<option value='$key' ";
					if( isset($postdata['shownav']) && ($key==$postdata['shownav']))
						$html .= 'selected="selected"';
					$html .= ">$value</option>";
				}
				$html .='				</select><p class="description">Default Yes</p>
									</th>
								</tr>
								<tr>
									<th scope="row"><label for="hoverpause">Carouse Pause on mouse over</label></th>
									<th>
										<select name="hoverpause">';
				foreach ($truefalse as $key=>$value){
					$html .= "<option value='$key' ";
					if(isset($postdata['hoverpause']) && ($key==$postdata['hoverpause']))
						$html .= 'selected="selected"';
					$html .= ">$value</option>";
				}
		 		$html .=			   '</select><p class="description">Default Yes</p>
									</th>
								</tr>
							</tbody>
							</table>
							<p class="submit">&nbsp;&nbsp;<input id="submit" class="button-primary" type="submit" value="'.$buttontext.'" name="isSubmit"></p>
						</form>
						</div>
			   </div>';
	 	$css='<link href="'.plugins_url('css/style.css',dirname(__FILE__)).'" rel="stylesheet" />';
		return $css.$html;
	}
	
	/**
	 * Validate carousle form data
	 * @param array $postdata
	 */
	public function validatecarousel($postdata){
		if(strlen($postdata['carouselname']) == 0  || strlen($postdata['carouselwidth'])==0 || strlen($postdata['carouselheight'])==0)
			return "Please enter the required field";
		else if(preg_match ("/[^0-9]/",$postdata['carouselwidth']))
			return "Please enter the carousel width";
		else if(preg_match ("/[^0-9]/",$postdata['carouselheight']))
			return "Please enter the carousel height";
		else if(preg_match ("/[^0-9]/",$postdata['startslide']))
			return "Please enter the valid start slide";
		else if(preg_match ("/[^0-9]/",$postdata['animationspeed']))
			return "Please enter the valid animation speed";
		else if(preg_match ("/[^0-9]/",$postdata['pausetime']))
			return "Please enter the valid pause time";
		else if($this->_Checkcarouselname(strtolower($postdata['carouselname']),strtolower($postdata['oldcarouselname']),$postdata['carouselid']))
			return "Please enter the different carousel name";
		else 
			return "valid";
	}
	
 	/**
 	 * Insert new carousel into database
 	 * @since 1.5
 	 */
 	public function InsertNewCarousel($postdata){
 		if($postdata['carouselid']==''){
 			try{
	 			$this->_DataObject->insert($this->_carouselTable,array('CarouselName'=>$postdata['carouselname'],
	 																   'CarouselHeight'=>$postdata['carouselheight'],
	 																   'CarouselWidth'=>$postdata['carouselwidth'],
	 																   'CarouselEffect'=>$postdata['carouseleffect'],
	 																   'AnimationSpeed'=>$postdata['animationspeed'],
	 																   'StartSlide'=>$postdata['startslide'],
	 																   'PauseTime'=>$postdata['pausetime'],
	 																   'ShowNav'=>$postdata['shownav'],
	 																   'HoverPause'=>$postdata['hoverpause'],
	 																   'IsActive'=>1,
	 																	),array('%s','%d','%d','%s','%d','%d','%d','%s','%s','%d'));
 			}catch (Exception $e){
 				echo $e->getMessage();
 			}
 		}
 		else{
 			try{
 				$this->_DataObject->update($this->_carouselTable,array('CarouselName'=>$postdata['carouselname'],
	 																   'CarouselHeight'=>$postdata['carouselheight'],
	 																   'CarouselWidth'=>$postdata['carouselwidth'],
	 																   'CarouselEffect'=>$postdata['carouseleffect'],
	 																   'AnimationSpeed'=>$postdata['animationspeed'],
	 																   'StartSlide'=>$postdata['startslide'],
	 																   'PauseTime'=>$postdata['pausetime'],
	 																   'ShowNav'=>$postdata['shownav'],
	 																   'HoverPause'=>$postdata['hoverpause']),
	 															  array('Id'=>$postdata['carouselid']),
	 															  array('%s','%d','%d','%s','%d','%d','%d','%s','%s'),
 																  array('%s'));		
 			}catch (Exception $e){
 				echo "Error: ".$e->getMessage();
 			}	
 		}
 	}
 	
 	/**
 	 * Manage carousel action (delete, activate, deactivate)
 	 * @param string $action
 	 * @param int $id
 	 */
 	public function CarouselAction($action,$id){
 		$query="";
 		if($action == "delete")
 			$query='DELETE FROM '.$this->_carouselTable.' WHERE Id='.$id;
 		else if($action == "activate")
 			$query='UPDATE '.$this->_carouselTable.' SET IsActive=1';
 		else if($action=="deactivate")
 			$query='UPDATE '.$this->_carouselTable.' SET IsActive=0';
		if($query != "")
 			$this->_DataObject->query($query);		
 	}
 	
 	/**
 	 * Get carousel by carousel id
 	 * @param int $id
 	 */
 	public function GetCarouselById($id){
 		$query='SELECT * FROM '.$this->_carouselTable.' WHERE Id='.$id;
 		return $this->_DataObject->get_row($query);
 	}
 	
 	/**
 	 * Get carousel by carouselname
 	 * @param string $carouselname
 	 */
 	public function GetCarouselByName($carouselname){
 		$query='SELECT * FROM '.$this->_carouselTable.' WHERE CarouselName="%s" and IsActive=1';
 		return $this->_DataObject->get_row($this->_DataObject->prepare($query,$carouselname));
 	}
 	
 	/**
 	 * Validate unique carousel name
 	 * @param string $carouselname
 	 * @param string $oldcarouselname
 	 * @param int $id
 	 */
 	private function _Checkcarouselname($carouselname,$oldcarouselname,$id){
 		$query='SELECT CarouselName FROM '.$this->_carouselTable.' WHERE CarouselName="'.$carouselname.'"';
 		$result=$this->_DataObject->get_var($query);
 		if($id != '' && ($oldcarouselname == $carouselname))
 			return false;
 		else{
 			if($result == '')
 				return false;
 			else
 				return true;
 		}
 	}
 	
 	public function GetOptionsString($query, $keyCol, $valueCol, $selectedKey){
   		$results=$this->_DataObject->get_results($query, ARRAY_A);
		$optionsString = "";
   		$isArray = is_array($selectedKey);
   		foreach($results as $result)
   		{
      		if($isArray)
       		$selected = (array_search($result[$keyCol], $selectedKey) !== false)? " selected" : "";
      		else
       		$selected = ($result[$keyCol]==$selectedKey)? " selected" : "";
       		$optionsString .= "<option value='$result[$keyCol]' title='$result[$valueCol]'" . $selected . ">$result[$valueCol]</option>";
   		}	
   		return $optionsString;
	}
	
	/*
	 * Update existing plugin table structure
	 * @since 1.5
	 */
	public function UpdateTable(){
		$oldmaintablenamewindow=$this->_DataObject->prefix."wpnewcarousels";
		$oldmaintablenamelinux=$this->_DataObject->prefix."WPNewCarousels";
		$olddatatablenamewindow=$this->_DataObject->prefix."wpnewcaroselsdata";
		$olddatatablenamelinux=$this->_DataObject->prefix."WPNewCaroselsData";
		
		if(($this->_DataObject->get_var("SHOW TABLES LIKE '{$oldmaintablenamewindow}'") == $oldmaintablenamewindow ) || ($this->_DataObject->get_var("SHOW TABLES LIKE '{$oldmaintablenamelinux}'") == $oldmaintablenamelinux )){
			$sql ="CREATE TABLE {$this->_carouselTable} ("
				  ."Id INT NOT NULL AUTO_INCREMENT,"
				  ."CarouselName VARCHAR(100) NOT NULL,"
				  ."CarouselWidth INT NOT NULL,"
				  ."CarouselHeight INT NOT NULL,"
			      ."CarouselEffect varchar(100),"
			      ."AnimationSpeed INT,"
			      ."StartSlide TINYINT,"
			      ."PauseTime INT,"
			      ."ShowNav varchar(6),"
			      ."HoverPause varchar(6),"
			      ."SubmitDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,"
			      ."IsActive TINYINT(2),"
			      ."PRIMARY KEY (Id))ENGINE=INNODB;";
			      require_once ABSPATH.'wp-admin/includes/upgrade.php';
				  dbDelta($sql);
			if($this->_DataObject->get_var("SHOW TABLES LIKE '{$oldmaintablenamewindow}'") == $oldmaintablenamewindow){
			 	$this->_DataObject->query("INSERT INTO $this->_carouselTable(Id,CarouselName,CarouselWidth,CarouselHeight,IsActive) SELECT * FROM {$oldmaintablenamewindow}");
			}
			else if($this->_DataObject->get_var("SHOW TABLES LIKE '{$oldmaintablenamelinux}'") == $oldmaintablenamelinux){
			  	$this->_DataObject->query("INSERT INTO $this->_carouselTable(Id,CarouselName,CarouselWidth,CarouselHeight,IsActive) SELECT * FROM {$oldmaintablenamelinux}");
			}	  	  
		}
		
		if(($this->_DataObject->get_var("SHOW TABLES LIKE '{$olddatatablenamewindow}'") == $olddatatablenamewindow ) ||($this->_DataObject->get_var("SHOW TABLES LIKE '{$olddatatablenamelinux}'") == $olddatatablenamelinux ) ){
			$sql ="CREATE TABLE {$this->_carouselData} ("
				  ."Id INT NOT NULL AUTO_INCREMENT,"
				  ."CarouselId INT NOT NULL,"
				  ."BackgroundImageURL varchar(255) DEFAULT NULL,"
				  ."BackgroundImageLink varchar(255) DEFAULT NULL,"
				  ."BackgroundImageAltText varchar(255) DEFAULT NULL,"
				  ."TitleText varchar(255) DEFAULT NULL,"
				  ."FOREIGN KEY (CarouselId) REFERENCES $this->_carouselTable(Id) ON UPDATE CASCADE ON DELETE CASCADE,"
				  ."PRIMARY KEY (Id,CarouselId))ENGINE=INNODB;";
			require_once ABSPATH.'wp-admin/includes/upgrade.php';
			dbDelta($sql);
		 	if($this->_DataObject->get_var("SHOW TABLES LIKE '{$olddatatablenamewindow}'") == $olddatatablenamewindow){
				$this->_DataObject->query("INSERT INTO {$this->_carouselData} SELECT * FROM {$olddatatablenamewindow}");
			}
			else if($this->_DataObject->get_var("SHOW TABLES LIKE '{$olddatatablenamelinux}'") == $olddatatablenamelinux){
				$this->_DataObject->query("INSERT INTO {$this->_carouselData} SELECT * FROM {$olddatatablenamelinux}");
			}
		}
		if($this->_DataObject->get_var("SHOW TABLES LIKE '{$olddatatablenamewindow}'") == $olddatatablenamewindow){
			$this->_DataObject->query("DROP TABLE {$olddatatablenamewindow}");
			$this->_DataObject->query("DROP TABLE {$oldmaintablenamewindow}");
		}
		else if($this->_DataObject->get_var("SHOW TABLES LIKE '{$olddatatablenamelinux}'") == $olddatatablenamelinux){
			$this->_DataObject->query("DROP TABLE {$olddatatablenamelinux}");
			$this->_DataObject->query("DROP TABLE {$oldmaintablenamelinux}");
		}
	}
	
	public function CreateTable(){
		$sql="";
		if($this->_DataObject->get_var("SHOW TABLES LIKE '{$this->_carouselTable}'") != $this->_carouselTable){
			$sql .="CREATE TABLE {$this->_carouselTable} ("
				 ."Id INT NOT NULL AUTO_INCREMENT,"	
				 ."CarouselName VARCHAR(100) NOT NULL,"
				 ."CarouselWidth INT NOT NULL,"
				 ."CarouselHeight INT NOT NULL,"
				 ."CarouselEffect varchar(100),"
				 ."AnimationSpeed INT,"
				 ."StartSlide TINYINT,"
			     ."PauseTime INT,"
			     ."ShowNav varchar(6),"
			     ."HoverPause varchar(6),"
			     ."SubmitDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,"
				 ."IsActive TINYINT(2),"
				 ."PRIMARY KEY (Id))ENGINE=INNODB;";
		}
		if($this->_DataObject->get_var("SHOW TABLES LIKE '{$this->_carouselData}'") != $this->_carouselData){	
			$sql .="CREATE TABLE {$this->_carouselData} ("
				 ."Id INT NOT NULL AUTO_INCREMENT,"
				 ."CarouselId INT NOT NULL,"
				 ."BackgroundImageURL varchar(255) DEFAULT NULL,"
				 ."BackgroundImageLink varchar(255) DEFAULT NULL,"
				 ."BackgroundImageAltText varchar(255) DEFAULT NULL,"
				 ."TitleText varchar(255) DEFAULT NULL,"
				 ."weight int(3) DEFAULT NULL,"
				 ."FOREIGN KEY (CarouselId) REFERENCES $this->_carouselTable(Id) ON UPDATE CASCADE ON DELETE CASCADE,"
				 ."PRIMARY KEY (Id,CarouselId))ENGINE=INNODB;";			
		}
		if ($sql != ""){
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}
	}
}