<?php

/*

Plugin Name: Add more files extensions

Plugin URI: http://irz.fr/add-more-files-extensions/

Description: Add more files extensions with a new field in your <a href="options-media.php">Media Settings</a>.

Version: 0.1

Author: Arthur Lacoste

Author URI: http://irz.fr

*/  





add_action('admin_init','add_media_field');



function display_ext(){

	echo '<input  type="text" name="ext" id="ext" value="'.get_option('ext').'" size="30" style="width:85%" />';

	echo '<p><small>'.__('Entrez les extensions de fichier que vous souhaitez ajouter sans le point (séparé par un espace, ex: "mp3 doc gif")').'</small></p>';

	echo '<p><strong>' . __('Liste des extensions déjà disponibles : '); 

	echo '</strong>';

	$mimes = get_allowed_mime_types();

	$type_aff = array();

	foreach ($mimes as $ext => $mime) {

		$type_aff[] = str_replace('|', ', ', $ext);

	}

	echo  implode(', ', $type_aff) . '</p>';

}



function add_media_field() {

	add_settings_field( 'ext', __('Extensions'), 'display_ext', 'media', 'uploads', array( 'label_for' => 'ext' ) );

	register_setting( 'media', 'ext' );

	if(get_option('ext')!=''){ add_filter('upload_mimes', 'custom_upload_mimes');}

}

// ajoute une a une les extensions de l'option 'ext' pour l'ajouter dans les fichiers a télécharger



function custom_upload_mimes ($existing_mimes = array()) {

	$mimetype = new mimetype();

	$file_types = get_option('ext');

	$variables = explode(' ', $file_types);

	foreach($variables as $value) {

	$value = trim($value);

	if(!strstr($value, '/')) {

		$mime = $mimetype->privFindType($value);

	} else {

			$mime = $value;

		}

		$existing_mimes[$value] = $mime;

	}

	return $existing_mimes;



}



class mimetype { 

   function privFindType($ext) { 

      // create mimetypes array 

      $mimetypes = $this->privBuildMimeArray(); 

       

      // return mime type for extension 

      if (isset($mimetypes[$ext])) { 

         return $mimetypes[$ext]; 

      // if the extension wasn't found return octet-stream          

      } else { 

         return 'application/octet-stream'; 

      } 

          

   } 



	function privBuildMimeArray() { 

		require_once('types-mimes.php');

		return $types;

	}

} 