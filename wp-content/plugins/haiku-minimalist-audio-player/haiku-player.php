<?php
/*
Plugin Name: Haiku - minimalist audio player
Plugin URI: http://madebyraygun.com/lab/haiku
Description: A simple HTML5-based audio player that inserts a text link or graphical player for audio playback.
Author: Dalton Rooney
Version: 0.4.3
Author URI: http://madebyraygun.com
*/ 

define("HAIKU_VERSION", "0.4.3");

register_activation_hook( __FILE__, 'haiku_install' );

function haiku_install() { // add and update our default options upon activation    
	update_option('haiku_player_version', HAIKU_VERSION);
	add_option("haiku_player_show_support", 'true'); 
	add_option("haiku_player_show_graphical", 'false'); 
	add_option("haiku_player_analytics", 'false'); 
	add_option("haiku_player_default_location", ''); 
	add_option("haiku_player_replace_audio_player", ''); 
	add_option("haiku_player_replace_mp3_links", ''); 
}
   
// now let's grab the options table data
$haiku_player_version = get_option('haiku_player_version');
$haiku_player_show_support = get_option('haiku_player_show_support');
$haiku_player_show_graphical = get_option('haiku_player_show_graphical');
$haiku_player_analytics = get_option('haiku_player_analytics');
$haiku_player_default_location = get_option('haiku_player_default_location');
$haiku_player_replace_audio_player = get_option('haiku_player_replace_audio_player');
$haiku_player_replace_mp3_links = get_option('haiku_player_replace_mp3_links');

//set up defaults if these fields are empty
if (empty($haiku_player_show_graphical)) {$haiku_player_show_graphical = "false";}
if (empty($haiku_player_analytics)) {$haiku_player_analytics = "false";}
if (empty($haiku_player_replace_audio_player)) {$haiku_player_replace_audio_player = "false";}


//action link http://www.wpmods.com/adding-plugin-action-links

function haiku_action_links($links, $file) {
    static $this_plugin;
 
    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }
 
    // check to make sure we are on the correct plugin
    if ($file == $this_plugin) {
        // the anchor tag and href to the URL we want. For a "Settings" link, this needs to be the url of your settings page
        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=haiku-minimalist-audio-player/haiku-admin.php">Settings</a>';
        // add the link to the list
        array_unshift($links, $settings_link);
    }
 
    return $links;
}

add_filter('plugin_action_links', 'haiku_action_links', 10, 2);


function replace_audio($content) { //finds the old audio player shortcode and rewrites it
  $content = preg_replace('/\[audio:/','[haiku url=',$content,1);
  return $content;
}

if (!empty($haiku_player_replace_audio_player)) { //only run the audio tag replacement filter if the user selected it
	add_filter('the_content', 'replace_audio');
}

function replace_mp3_links($content) {
  $pattern = "/<a ([^=]+=['\"][^\"']+['\"] )*href=['\"](([^\"']+\.mp3))['\"]( [^=]+=['\"][^\"']+['\"])*>([^<]+)<\/a>/i"; //props to WordPress Audio Player for the regex
  $replacement = '[haiku url=$2 defaultpath=disabled]';
  $content = preg_replace($pattern, $replacement, $content);
  return $content;
}

if (!empty($haiku_player_replace_mp3_links)) { //only run the MP3 link replacement filter if the user selected it
	add_filter('the_content', 'replace_mp3_links');
}

add_shortcode('haiku', 'haiku_player_shortcode');
// define the shortcode function

function haiku_player_shortcode($atts) {
	global $haiku_player_show_graphical, $haiku_player_default_location, $haiku_player_analytics;
	STATIC $i = 1;
	extract(shortcode_atts(array(
		'url'	=> '',
		'title'	=> '',
		'defaultpath' => '',
		'noplayerdiv' => '',
		'graphical' => $haiku_player_show_graphical
	), $atts));
	// stuff that loads when the shortcode is called goes here
	
	if ($graphical == "false") {	//decide whether to show the text or graphical player
	
		if ( $noplayerdiv != "true" ) { //this exists mainly to hide the player controls and control it with an external application.
			$haiku_player_shortcode = '<div id="haiku-text-player'.$i.'" class="haiku-text-player"></div>';
		} else {
			$haiku_player_shortcode = "";
		}

		$haiku_player_shortcode .= '
			<div id="text-player-container'.$i.'" class="text-player-container"> 
			<ul id="player-buttons'.$i.'" class="player-buttons"> 
				<li class="play"';
				if ($haiku_player_analytics == "true") { $haiku_player_shortcode .=  ' onClick="_gaq.push([\'_trackEvent\', \'Audio\', \'Play\', \''.$title.'\']);"';}
				$haiku_player_shortcode .= '><a title="Listen to '.$title.'" class="play" href="';
				
				if (!empty($haiku_player_default_location) && $defaultpath !="disabled") {
					$haiku_player_shortcode .= site_url() . $haiku_player_default_location . "/";
				}
				
				$haiku_player_shortcode .= $url;
				
				$haiku_player_shortcode .= '">play</a></li> 
				<li class="stop"><a href="javascript: void(0);">stop</a></li>';
				
				if(!empty($title)) { $haiku_player_shortcode .= '<li class="title">'.esc_attr($title).'</li>'; }
				
			$haiku_player_shortcode .= '</ul>
	</div>';

	} elseif ($graphical == "true") {
	
		if ( $noplayerdiv != "true" ) { //this option exists mainly so we can the player controls and control it with an external application if necessary.
			$haiku_player_shortcode = '<div id="haiku-player'.$i.'" class="haiku-player"></div>';
		} else {
			$haiku_player_shortcode = "";
		}

		$haiku_player_shortcode .= '<div id="player-container'.$i.'" class="player-container"><div id="haiku-button'.$i.'" class="haiku-button"><a title="Listen to '.$title.'" class="play" href="';
				
				if (!empty($haiku_player_default_location) && $defaultpath !="disabled") {
					$haiku_player_shortcode .= site_url() . $haiku_player_default_location . "/";
				}
				
				$haiku_player_shortcode .= $url;
				
				$haiku_player_shortcode .= '"';
		if ($haiku_player_analytics == "true") 
			{$haiku_player_shortcode .=  ' onClick="_gaq.push([\'_trackEvent\', \'Audio\', \'Play\', \''.$title.'\']);"';}
		$haiku_player_shortcode .= '><img alt="Listen to '.$title.'" class="listen" src="';
		$haiku_player_shortcode .=  plugins_url( 'resources/play.png', __FILE__ );
		$haiku_player_shortcode .= '"  /></a>
		
		<ul id="controls'.$i.'" class="controls"><li class="pause"><a href="javascript: void(0);"></a></li><li class="play"><a href="javascript: void(0);"></a></li><li class="stop"><a href="javascript: void(0);"></a></li><li id="sliderPlayback'.$i.'" class="sliderplayback"></li></ul></div>
	</div><!-- player_container-->
	
';}
		
	$i++; //increment static variable for unique player IDs
	return $haiku_player_shortcode;
} //ends the haiku_player_shortcode function

// scripts to go in the header and/or footer
if( !is_admin()){
   wp_enqueue_script('jquery');
   wp_register_script('jplayer', plugins_url( '/js/jquery.jplayer.min.js', __FILE__ ), false, '1.2', true); 
   wp_enqueue_script('jplayer');
   wp_register_script('haiku-player', plugins_url( '/js/haiku-player.js', __FILE__ ), false, $haiku_player_version, true); 
   wp_enqueue_script('haiku-player');
 wp_register_script('jquery-ui-custom', plugins_url( '/js/jquery-ui-custom.min.js', __FILE__ ), false, '1.8.7', true); 
   wp_enqueue_script('jquery-ui-custom');
}


function haiku_player_head() {
global $haiku_player_version;
	echo '
<!-- loaded by Haiku audio player plugin-->
<link rel="stylesheet" type="text/css" href="' .  plugins_url( 'haiku-player.css', __FILE__ ) . '?ver='.$haiku_player_version.'" />
<script type="text/javascript">
var jplayerswf = "'. plugins_url( '/js/', __FILE__ ) . '";
</script>
<!-- end Haiku -->
';
} // ends haiku_player_head function
add_action('wp_head', 'haiku_player_head');

if ( is_admin() ) { 
	require('haiku-admin.php');
}	

?>