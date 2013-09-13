<?php
/**
 * @author  Arjun Jain  < http://www.arjunjain.info >
 * @version 1.6
 * @license GNU GENERAL PUBLIC LICENSE Version 3
 */
require_once '../../../../wp-load.php';
require_once 'ManageCarousel.php';
$mc=new ManageCarousel();
$count=$_POST['pcount'];
$data='';
for ($i=0;$i<$count;$i++)
 	$data .= $mc->getSlide();
echo $data;
?>