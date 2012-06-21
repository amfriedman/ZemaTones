<?php  if ( ! defined('AVIA_FW')) exit('No direct script access allowed');
/**
 * This file holds various functions that modify the wordpress media uploader
 *
 * It utilizes custom posts to create a gallerie for each upload field. 
 * Kudos to woothemes for this great idea :)
 *
 * @author		Christian "Kriesi" Budschedl
 * @copyright	Copyright (c) Christian Budschedl
 * @link		http://kriesi.at
 * @link		http://aviathemes.com
 * @since		Version 1.0
 * @package 	AviaFramework
 */

/**
 *
 */
 
add_action( 'init', array('avia_media', 'generate_post_type' ));
add_filter( 'media_upload_tabs', array('avia_media','add_media_label_header'));

if( !class_exists( 'avia_media' ) )
{	

	/**
	 * The avia media class is a set of static class methods that help to create the hidden image containing posts
	 * @package 	AviaFramework
	 */
	class avia_media
	{
	
		/**
		 * The avia media generate_post_type function builds the hidden posts necessary for image saving on options pages
		 */
		public static function generate_post_type()
		{
			register_post_type( 'avia_framework_post', array(
			'labels' => array('name' => 'Avia Framework' ),
			'show_ui' => false,
			'query_var' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'rewrite' => false,
			'supports' => array( 'editor', 'title' ), 
			'can_export' => true,
			'public' => true,
			'show_in_nav_menus' => false
		) );
		}
		
		
		/**
		 * The avia media get_custom_post function gets a custom post based on a post title. if no post cold be found it creates one
		 * @param string $post_title the title of the post
		 * @package 	AviaFramework
		 */
		public static function get_custom_post( $post_title )
		{
			$save_title = avia_backend_safe_string( $post_title );
			
			$args = array( 	'post_type' => 'avia_framework_post', 
							'post_title' => 'avia_' . $save_title,
							'post_status' => 'draft', 
							'comment_status' => 'closed', 
							'ping_status' => 'closed');
							
			$avia_post = avia_media::get_post_by_title( $args['post_title'] );

			if(!isset($avia_post['ID']) ) 
			{ 
				$avia_post_id = wp_insert_post( $args );
			}
			else
			{
				$avia_post_id = $avia_post['ID'];
			}

			return $avia_post_id;
		}
		
		/**
		 * The avia media get_post_by_title function gets a custom post based on a post title. 
		 * @param string $post_title the title of the post
		 * @package 	AviaFramework
		 */
		public static function get_post_by_title($post_title) {
		    global $wpdb;
		        $post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='avia_framework_post'", $post_title ));
		        if ( $post )
		            return get_post($post, 'ARRAY_A');
		
		    return null;
		}
		
		
		/**
		 * The avia add_media_label_header function hooks into wordpress galery tabs and injects a new button label
		 * this label can be found by the frameworks javascript and it then overwrites the default "insert into post" text
		 * @param array $_default_tabs the default tabs
		 * @package 	AviaFramework
		 */
		public static function add_media_label_header($_default_tabs)
		{	
		
			//change the label of the insert button
			if(isset($_GET['avia_label']))
			{	
				echo "<input class='avia_insert_button_label' type='hidden' value='".html_entity_decode($_GET['avia_label'])."' />";
			}
			
			//activate the gallery mode
			if(isset($_GET['avia_gallery_mode']))
			{	
				echo "<input class='avia_gallery_mode_active' type='hidden' value='".$_GET['avia_gallery_mode']."' />";
				if(isset($_default_tabs['library'])) unset($_default_tabs['library']);
				if(isset($_default_tabs['type_url'])) unset($_default_tabs['type_url']);
			}
			
			//remove the default insert method and replace it with the better image id based method
			if(isset($_GET['avia_idbased']))
			{	
				echo "<input class='avia_idbased' type='hidden' value='".$_GET['avia_idbased']."' />";
			}
			
			return $_default_tabs;
		}
	}
}


