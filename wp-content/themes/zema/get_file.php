<?php
/** 
 * This file is for security purposes, to protect against direct access to m4r and other downloadable product files, but STILL allow the files to be previewed by an embeddable media player.  It is not perfect, but it should deter casual visitors from attempting to access files directly, unauthorized.
 *
 * @date 2012-01-15
 * @author Adam Friedman
 *
 */
 

$required = array('f','k','t');

$base_path = '/home/zematone/public_html/wp-content/';

$file_path = $base_path.$_GET['f'];

 

function is_authorized() {
	global $required;
	
	foreach($required as $gk) { if(empty($_GET[$gk])) {   return false; } }
	
	$key = md5(substr(urldecode($_GET['f']),-10));
	
	if($_GET['k'] != $key) {  return false; }
	
	// one hour since the page load
	if( !is_numeric($_GET['t']) || ((time() - $_GET['t']) > 3600) ) {   return false; }
	 
	 
	return true;
}



if (is_authorized()) {
  #apache_setenv('PHP_ALLOW', '1');
  #echo "You are allowed! Redirecting...";  
  
  readfile($file_path);
  exit();
  
  #header('Location: '.$_GET['f']);
  #exit();

} else {
  echo "Sorry buddy, you're not allowed in here.\n";
}  


?>