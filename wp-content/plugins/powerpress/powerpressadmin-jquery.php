<?php

	// jQuery specific functions and code go here..

	

	// Credits:

	/*

	FOLDER ICON provided by Silk icon set 1.3 by Mark James link: http://www.famfamfam.com/lab/icons/silk/

	*/

	

function powerpress_add_blubrry_redirect($program_keyword)

{

	if( !strstr(POWERPRESS_BLUBRRY_API_URL, 'api.blubrry.com' ) )

		return;

	

	$Settings = powerpress_get_settings('powerpress_general');

	$RedirectURL = 'http://media.blubrry.com/'.$program_keyword;

	$NewSettings = array();

	

	// redirect1

	// redirect2

	// redirect3

	for( $x = 1; $x <= 3; $x++ )

	{

		$field = sprintf('redirect%d', $x);

		if( $Settings[$field] == '' )

		{

			$NewSettings[$field] = $RedirectURL.'/';

			break;

		}

		else if( stristr($Settings[$field], $RedirectURL ) )

		{

			return; // Redirect already implemented

		}

	}

	if( count($NewSettings) > 0 )

		powerpress_save_settings($NewSettings);

}



function powerpress_strip_redirect_urls($url)

{

	$Settings = powerpress_get_settings('powerpress_general');

	for( $x = 1; $x <= 3; $x++ )

	{

		$field = sprintf('redirect%d', $x);

		if( !empty($Settings[$field]) )

		{

			$redirect_no_http = str_replace('http://', '', $Settings[$field]);

			if( substr($redirect_no_http, -1, 1) != '/' )

				$redirect_no_http .= '/';

			$url = str_replace($redirect_no_http, '', $url);

		}

	}

	

	return $url;

}



function powerpress_admin_jquery_init()

{

	$Settings = false; // Important, never remove this

	$Settings = get_option('powerpress_general');

	

	$Error = false;



	$Programs = false;

	$Step = 1;

	

	$action = (isset($_GET['action'])?$_GET['action']: (isset($_POST['action'])?$_POST['action']:false) );

	if( !$action )

		return;

	

	$DeleteFile = false;

	switch($action)

	{

		case 'powerpress-jquery-stats': {

		

			// Make sure users have permission to access this

			if( @$Settings['use_caps'] && !current_user_can('view_podcast_stats') )

			{

				powerpress_admin_jquery_header( __('Blubrry Media Statistics', 'powerpress') );

?>

<h2><?php echo __('Blubrry Media Statistics', 'powerpress'); ?></h2>

<p><?php echo __('You do not have sufficient permission to manage options.', 'powerpress'); ?></p>

<p style="text-align: center;"><a href="#" onclick="self.parent.tb_remove();"><?php echo __('Close', 'powerpress'); ?></a></p>

<?php

				powerpress_admin_jquery_footer();

				exit;

			}

			else if( !current_user_can('edit_posts') )

			{

				powerpress_admin_jquery_header( __('Blubrry Media Statistics', 'powerpress') );

				powerpress_page_message_add_notice( __('You do not have sufficient permission to view media statistics.', 'powerpress') );

				powerpress_page_message_print();

				powerpress_admin_jquery_footer();

				exit;

			}

				

			$StatsCached = get_option('powerpress_stats');

			

			powerpress_admin_jquery_header( __('Blubrry Media Statistics', 'powerpress') );

?>

<h2><?php echo __('Blubrry Media Statistics', 'powerpress'); ?></h2>

<?php

			echo $StatsCached['content'];

			powerpress_admin_jquery_footer();

			exit;

		}; break;

		case 'powerpress-jquery-media-delete': {

			

			if( !current_user_can('edit_posts') )

			{

				powerpress_admin_jquery_header('Uploader');

				powerpress_page_message_add_notice( __('You do not have sufficient permission to upload media.', 'powerpress') );

				powerpress_page_message_print();

				powerpress_admin_jquery_footer();

				exit;

			}

			

			check_admin_referer('powerpress-jquery-media-delete');

			$DeleteFile = $_GET['delete'];

			

		}; // No break here, let this fall thru..

		case 'powerpress-jquery-media': {

			

			if( !current_user_can('edit_posts') )

			{

				powerpress_admin_jquery_header( __('Select Media', 'powerpress') );

?>

<h2><?php echo __('Select Media', 'powerpress'); ?></h2>

<p><?php echo __('You do not have sufficient permission to manage options.', 'powerpress'); ?></p>

<p style="text-align: center;"><a href="#" onclick="self.parent.tb_remove();"><?php echo __('Close', 'powerpress'); ?></a></p>

<?php

				powerpress_admin_jquery_footer();

				exit;

			}

			

			if( !isset($Settings['blubrry_auth']) || $Settings['blubrry_auth'] == '' || !isset($Settings['blubrry_hosting']) || $Settings['blubrry_hosting'] == 0 )

			{

				powerpress_admin_jquery_header( __('Select Media', 'powerpress') );

?>

<h2><?php echo __('Select Media', 'powerpress'); ?></h2>

<p><?php echo __('Wait a sec! This feature is only available to Blubrry Podcast paid hosting members.', 'powerpress');

if( !isset($Settings['blubrry_auth']) || $Settings['blubrry_auth'] == '' )

	echo ' '. sprintf( __('Join our community to get free podcast statistics and access to other valuable %s.', 'powerpress'),

	'<a href="http://www.blubrry.com/powerpress_services/" target="_blank">'. __('services', 'powerpress') .'</a>');

?>

</p>

<p><?php 

	echo sprintf( __('Our %s PowerPress makes podcast publishing simple. Check out the %s on our exciting three-step publishing system!', 'powerpress'),

		'<a href="http://www.blubrry.com/powerpress_services/" target="_blank">'. __('podcast-hosting integrated', 'powerpress') .'</a>',

		'<a href="http://www.blubrry.com/powerpress_services/" target="_blank">'. __('video', 'powerpress') .'</a>' );

	?>

   </p>

<p style="text-align: center;"><a href="#" onclick="self.parent.tb_remove();"><?php echo __('Close', 'powerpress'); ?></a></p>

<?php

				powerpress_admin_jquery_footer();

				exit;

			}

			

			$Msg = false;

			if( $DeleteFile )

			{

				$api_url = sprintf('%s/media/%s/%s?format=json', rtrim(POWERPRESS_BLUBRRY_API_URL, '/'), $Settings['blubrry_program_keyword'], $DeleteFile );

				$api_url .= (defined('POWERPRESS_BLUBRRY_API_QSA')?'&'. POWERPRESS_BLUBRRY_API_QSA:'');

				$json_data = powerpress_remote_fopen($api_url, $Settings['blubrry_auth'], array(), 10, 'DELETE');

				$results =  powerpress_json_decode($json_data);

				

				if( isset($results['text']) )

					$Msg = $results['text'];

				else if( isset($results['error']) )

					$Msg = $results['error'];

				else

					$Msg = __('An unknown error occurred deleting media file.', 'powerpress');

			}



			$api_url = sprintf('%s/media/%s/index.json?quota=true&published=true', rtrim(POWERPRESS_BLUBRRY_API_URL, '/'), $Settings['blubrry_program_keyword'] );

			$api_url .= (defined('POWERPRESS_BLUBRRY_API_QSA')?'&'. POWERPRESS_BLUBRRY_API_QSA:'');

			$json_data = powerpress_remote_fopen($api_url, $Settings['blubrry_auth']);

			$results =  powerpress_json_decode($json_data);

				

			$FeedSlug = $_GET['podcast-feed'];

			powerpress_admin_jquery_header( __('Select Media', 'powerpress'), true );

?>

<script language="JavaScript" type="text/javascript"><!--



function SelectMedia(File)

{

	self.parent.document.getElementById('powerpress_url_<?php echo $FeedSlug; ?>').value=File;

	self.parent.document.getElementById('powerpress_hosting_<?php echo $FeedSlug; ?>').value='1';

	self.parent.document.getElementById('powerpress_url_<?php echo $FeedSlug; ?>').readOnly=true;

	self.parent.document.getElementById('powerpress_hosting_note_<?php echo $FeedSlug; ?>').style.display='block';

	if( self.parent.powerpress_update_for_video )

		self.parent.powerpress_update_for_video(File, '<?php echo $FeedSlug; ?>');

	self.parent.tb_remove();

}

function SelectURL(url)

{

	self.parent.document.getElementById('powerpress_url_<?php echo $FeedSlug; ?>').value=url;

	self.parent.document.getElementById('powerpress_hosting_<?php echo $FeedSlug; ?>').value='0';

	self.parent.document.getElementById('powerpress_url_<?php echo $FeedSlug; ?>').readOnly=false;

	self.parent.document.getElementById('powerpress_hosting_note_<?php echo $FeedSlug; ?>').style.display='none';

	if( self.parent.powerpress_update_for_video )

		self.parent.powerpress_update_for_video(File, '<?php echo $FeedSlug; ?>');

	self.parent.tb_remove();

}

function DeleteMedia(File)

{

	return confirm('<?php echo __('Delete', 'powerpress'); ?>: '+File+'\n\n<?php echo __('Are you sure you want to delete this media file?', 'powerpress'); ?>');

}

//-->

</script>

		<div id="media-header">

			<h2><?php echo __('Select Media', 'powerpress'); ?></h2>

			<?php

				if( $Msg )

				echo '<p>'. $Msg . '</p>';

			?>

			<div class="media-upload-link"><a href="<?php echo admin_url() . wp_nonce_url("admin.php?action=powerpress-jquery-upload", 'powerpress-jquery-upload'); ?>&podcast-feed=<?php echo $FeedSlug; ?>&keepThis=true&TB_iframe=true&height=350&width=530&modal=true" class="thickbox"><?php echo __('Upload Media File', 'powerpress'); ?></a></div>

			<p><?php echo __('Select from media files uploaded to blubrry.com', 'powerpress'); ?>:</p>

		</div>

	<div id="media-items-container">

		<div id="media-items">

<?php

		$QuotaData = false;

		if( isset($results['error']) )

		{

			echo $results['error'];

		}

		else if( is_array($results) )

		{

			$PublishedList = false;

			while( list($index,$data) = each($results) )

			{

				if( $index === 'quota' )

				{

					$QuotaData = $data;

					continue;

				}

				

				if( $PublishedList == false && !empty($data['published']) )

				{

?>

<div id="media-published-title">

	<?php echo __('Media Published within the past 30 days', 'powerpress'); ?>:

</div>

<?php

					$PublishedList = true;

				}



?>

<div class="media-item <?php echo (empty($data['published'])?'media-unpublished':'media-published'); ?>">

	<strong class="media-name"><?php echo htmlspecialchars($data['name']); ?></strong>

	<cite><?php echo powerpress_byte_size($data['length']); ?></cite>

	<?php if( !empty($data['published']) ) { ?>

	<div class="media-published-date">&middot; <?php echo __('Published on', 'powerpress'); ?> <?php echo date(get_option('date_format'), $data['last_modified']); ?></div>

	<?php } ?>

	<div class="media-item-links">

		<?php if( !empty($data['published']) && !empty($data['url']) ) { ?>

			<a href="#" onclick="SelectURL('<?php echo $data['url']; ?>'); return false;"><?php echo __('Select', 'powerpress'); ?></a>

		<?php } else { ?>

			<?php if (function_exists('curl_init')) { ?>

				<a href="<?php echo admin_url() . wp_nonce_url("admin.php?action=powerpress-jquery-media-delete", 'powerpress-jquery-media-delete'); ?>&amp;podcast-feed=<?php echo $FeedSlug; ?>&amp;delete=<?php echo urlencode($data['name']); ?>" onclick="return DeleteMedia('<?php echo $data['name']; ?>');"><?php echo __('Delete', 'powerpress'); ?></a> | 

			<?php } ?>

			<a href="#" onclick="SelectMedia('<?php echo $data['name']; ?>'); return false;"><?php echo __('Select', 'powerpress'); ?></a>

		<?php } ?>

	</div> 

</div>

<?php				

			}

		}

?>

		</div>

	</div>

	<div id="media-footer">

		<div class="media-upload-link"><a href="<?php echo admin_url() . wp_nonce_url("admin.php?action=powerpress-jquery-upload", 'powerpress-jquery-upload'); ?>&podcast-feed=<?php echo $FeedSlug; ?>&keepThis=true&TB_iframe=true&height=350&width=530&modal=true" class="thickbox"><?php echo __('Upload Media File', 'powerpress'); ?></a></div>

		<?php

		if( $QuotaData ) { 

			$NextDate = strtotime( $QuotaData['published']['next_date']);

		?>

			<p><?php

			echo sprintf( __('You have uploaded %s (%s available) of your %s limit.', 'powerpress'),

				'<em>'. powerpress_byte_size($QuotaData['unpublished']['used']) .'</em>',

				'<em>'. powerpress_byte_size($QuotaData['unpublished']['available']) .'</em>',

				'<em>'. powerpress_byte_size($QuotaData['unpublished']['total']) .'</em>' );

			?>

			</p>

			<p><?php

			echo sprintf( __('You are hosting %s (%s available) of your %s/30 day limit.', 'powerpress'),

				'<em>'. powerpress_byte_size($QuotaData['published']['total']-$QuotaData['published']['available']) .'</em>',

				'<em>'. powerpress_byte_size($QuotaData['published']['available']) .'</em>',

				'<em>'. powerpress_byte_size($QuotaData['published']['total']) .'</em>' );

			?>

			</p>

			<p><?php

			echo sprintf( __('Your limit will adjust on %s to %s (%s available).', 'powerpress'),

				date('m/d/Y', $NextDate),

				'<em>'. powerpress_byte_size($QuotaData['published']['total']-$QuotaData['published']['next_available']) .'</em>',

				'<em>'. powerpress_byte_size($QuotaData['published']['next_available']) .'</em>' );

			?>

			</p>

		<?php } ?>

		<p style="text-align: center;"><a href="#" onclick="self.parent.tb_remove();"><?php echo __('Close', 'powerpress'); ?></a></p>

	</div>

	

<?php	

			powerpress_admin_jquery_footer(true);

			exit;

		}; break;

		case 'powerpress-jquery-account-save': {

		

			if( !current_user_can('manage_options') )

			{

				powerpress_admin_jquery_header('Blubrry Services Integration', 'powerpress');

				powerpress_page_message_add_notice( __('You do not have sufficient permission to manage options.', 'powerpress') );

				powerpress_page_message_print();

				powerpress_admin_jquery_footer();

				exit;

			}

			

			check_admin_referer('powerpress-jquery-account');

			

			$Password = $_POST['Password'];

			$SaveSettings = $_POST['Settings'];

			$Password = powerpress_stripslashes($Password);

			$General = powerpress_stripslashes($SaveSettings);

			

			$Save = false;

			$Close = false;

		

			

			if( !empty($_POST['Remove']) )

			{

				$SaveSettings['blubrry_username'] = '';

				$SaveSettings['blubrry_auth'] = '';

				$SaveSettings['blubrry_program_keyword'] = '';

				$SaveSettings['blubrry_hosting'] = 0;

				$Close = true;

				$Save = true;

			}

			else

			{

				$Programs = array();

				//if( isset($_POST['ChangePassword']) )

				//{

				//	$Settings['blubrry_program_keyword'] = ''; // Reset the program keyword stored

					

					// Anytime we change the password we need to test it...

				$auth = base64_encode( $SaveSettings['blubrry_username'] . ':' . $Password );

				if( $SaveSettings['blubrry_hosting'] == 0 )

					$api_url = sprintf('%s/stats/index.json', rtrim(POWERPRESS_BLUBRRY_API_URL, '/') );

				else

					$api_url = sprintf('%s/media/index.json', rtrim(POWERPRESS_BLUBRRY_API_URL, '/') );

				$api_url .= (defined('POWERPRESS_BLUBRRY_API_QSA')?'?'. POWERPRESS_BLUBRRY_API_QSA:'');

				$json_data = powerpress_remote_fopen($api_url, $auth);

				if( $json_data )

				{

					$results =  powerpress_json_decode($json_data);

					

					if( isset($results['error']) )

					{

						$Error = $results['error'];

						if( strstr($Error, __('currently not available', 'powerpress') ) )

						{

							$Error = __('Unable to find podcasts for this account.', 'powerpress');

							$Error .= '<br /><span style="font-weight: normal; font-size: 12px;">';

							if( $SaveSettings['blubrry_hosting'] == 0 )

								$Error .= 'Verify that the email address you enter here matches the email address you used when you listed your podcast on blubrry.com.</span>';

							else

								$Error .= 'Media hosting customers are encouraged to <a href="http://www.blubrry.com/contact.php" target="_blank">contact blubrry</a> for support.</span>';

						}

						else if( preg_match('/No programs found.*media hosting/i', $results['error']) )

						{

							$Error .= '<br/><span style="font-weight: normal; font-size: 12px;">';

							$Error .= 'Service may take up to 48 hours to activate.</span>';

						}

					}

					else if( !is_array($results) )

					{

						$Error = $json_data;

					}

					else

					{

						// Get all the programs for this user...

						while( list($null,$row) = each($results) )

							$Programs[ $row['program_keyword'] ] = $row['program_title'];

						

						if( count($Programs) > 0 )

						{

							$SaveSettings['blubrry_auth'] = $auth;

							

							if( !empty($SaveSettings['blubrry_program_keyword']) )

							{

								powerpress_add_blubrry_redirect($SaveSettings['blubrry_program_keyword']);

								$Save = true;

								$Close = true;

							}

							else if( isset($SaveSettings['blubrry_program_keyword']) )

							{

								$Error = __('You must select a program to continue.', 'powerpress');

							}

							else if( count($Programs) == 1 )

							{

								list($keyword, $title) = each($Programs);

								$SaveSettings['blubrry_program_keyword'] = $keyword;

								powerpress_add_blubrry_redirect($keyword);

								$Close = true;

								$Save = true;

							}

							else

							{

								$Error = __('Please select your podcast program to continue.', 'powerpress');

								$Step = 2;

								$Settings['blubrry_username'] = $SaveSettings['blubrry_username'];

								$Settings['blubrry_hosting'] = $SaveSettings['blubrry_hosting'];

							}

						}

						else

						{

							$Error = __('No podcasts for this account are listed on blubrry.com.', 'powerpress');

						}

					}

				}

				else

				{

					global $g_powerpress_remote_error, $g_powerpress_remote_errorno;

					if( !empty($g_powerpress_remote_errorno) && $g_powerpress_remote_errorno == 401 )

						$Error = 'Incorrect user email address or password.  <br /><span style="font-weight: normal; font-size: 12px;">Verify your account settings and try again.</span>';

					else if( !empty($g_powerpress_remote_error) )

						$Error = __('Error:', 'powerpress') .' '.$g_powerpress_remote_error;

					else

						$Error = __('Authentication failed.', 'powerpress');

				}

				

				if( $Error )

				{

					$Error .= '<p style="text-align: center;"><a href="http://help.blubrry.com/blubrry-powerpress/blubrry-services-integration/authentication-help/" target="_blank">'. __('Click Here For Help','powerpress') .'</a></p>';

				}

			}

			

			if( $Save )

				powerpress_save_settings($SaveSettings);

			

			// Clear cached statistics

			delete_option('powerpress_stats');

			

			if( $Error )

				powerpress_page_message_add_notice( $Error );

				

			if( $Close )

			{

				powerpress_admin_jquery_header( __('Blubrry Services Integration', 'powerpress') );

				powerpress_page_message_print();

?>

<p style="text-align: right; position: absolute; top: 5px; right: 5px; margin: 0; padding:0;"><a href="#" onclick="self.parent.tb_remove(); return false;" title="<?php echo __('Close', 'powerpress'); ?>"><img src="<?php echo admin_url(); ?>/images/no.png" alt="<?php echo __('Close', 'powerpress'); ?>" /></a></p>

<h2><?php echo __('Blubrry Services Integration', 'powerpress'); ?></h2>

<p style="text-align: center;"><strong><?php echo __('Settings Saved Successfully!', 'powerpress'); ?></strong></p>

<p style="text-align: center;">

	<a href="<?php echo admin_url("admin.php?page=powerpress/powerpressadmin_basic.php"); ?>" target="_top"><?php echo __('Close', 'powerpress'); ?></a>

</p>

<?php

				powerpress_admin_jquery_footer();

				exit;

			}

			

			

		} // no break here, let the next case catch it...

		case 'powerpress-jquery-account':

		{

			if( !current_user_can('manage_options') )

			{

				powerpress_admin_jquery_header( __('Blubrry Services Integration', 'powerpress') );

				powerpress_page_message_add_notice( __('You do not have sufficient permission to manage options.', 'powerpress') );

				powerpress_page_message_print();

				powerpress_admin_jquery_footer();

				exit;

			}

			

			if( !ini_get( 'allow_url_fopen' ) && !function_exists( 'curl_init' ) )

			{

				powerpress_admin_jquery_header( __('Blubrry Services Integration', 'powerpress') );

				powerpress_page_message_add_notice( __('Your server must either have the php.ini setting \'allow_url_fopen\' enabled or have the PHP cURL library installed in order to continue.', 'powerpress') );

				powerpress_page_message_print();

				powerpress_admin_jquery_footer();

				exit;

			}

			

			check_admin_referer('powerpress-jquery-account');

			

			if( !$Settings )

				$Settings = get_option('powerpress_general');

			

			if( $Programs == false )

				$Programs = array();

			

			// If we have programs to select from, then we're at step 2

			//if( count($Programs) )

			//	$Step = 2;

			

			powerpress_admin_jquery_header( __('Blubrry Services Integration', 'powerpress') );

			powerpress_page_message_print();	

?>

<form action="<?php echo admin_url(); ?>" enctype="multipart/form-data" method="post">

<?php wp_nonce_field('powerpress-jquery-account'); ?>

<input type="hidden" name="action" value="powerpress-jquery-account-save" />

<div id="accountinfo">

	<h2><?php echo __('Blubrry Services Integration', 'powerpress'); ?></h2>

<?php if( $Step == 1 ) { ?>

	<p>

		<label for="blubrry_username"><?php echo __('Blubrry User Name (Email)', 'powerpress'); ?></label>

		<input type="text" id="blubrry_username" name="Settings[blubrry_username]" value="<?php echo $Settings['blubrry_username']; ?>" />

	</p>

	<p id="password_row">

		<label for="password_password"><?php echo __('Blubrry Password', 'powerpress'); ?></label>

		<input type="password" id="password_password" name="Password" value="" />

	</p>

	<p><strong><?php echo __('Select Blubrry Services', 'powerpress'); ?></strong></p>

	<p style="margin-left: 20px; margin-bottom: 0px;margin-top: 0px;">

		<input type="radio" name="Settings[blubrry_hosting]" value="0" <?php echo ($Settings['blubrry_hosting']==0?'checked':''); ?> /> <?php echo __('Statistics Integration only', 'powerpress'); ?>

	</p>

	<p style="margin-left: 20px; margin-top: 0px;">

		<input type="radio" name="Settings[blubrry_hosting]" value="1" <?php echo ($Settings['blubrry_hosting']==1?'checked':''); ?> /> <?php echo __('Statistics and Hosting Integration (Requires Blubrry Hosting Account)', 'powerpress'); ?>

	</p>

<?php } else { ?>

	<input type="hidden" name="Settings[blubrry_username]" value="<?php echo htmlspecialchars($Settings['blubrry_username']); ?>" />

	<input type="hidden" name="Password" value="<?php echo htmlspecialchars($Password); ?>" />

	<input type="hidden" name="Settings[blubrry_hosting]" value="<?php echo $Settings['blubrry_hosting']; ?>" />

	<p>

		<label><?php echo __('Blubrry Program Keyword', 'powerpress'); ?></label>

<select name="Settings[blubrry_program_keyword]">

<option value=""><?php echo __('Select Program', 'powerpress'); ?></option>

<?php

while( list($value,$desc) = each($Programs) )

	echo "\t<option value=\"$value\"". ($Settings['blubrry_program_keyword']==$value?' selected':''). ">$desc</option>\n";

?>

</select>

	</p>

<?php } ?>

	<p>

		<input type="submit" name="Remove" value="Remove" style="float: right;" onclick="return confirm('<?php echo __('Remove Blubrry Services Integration, are you sure?', 'powerpress'); ?>');" />

		<input type="submit" name="Save" value="<?php echo __('Save', 'powerpress'); ?>" />

		<input type="button" name="Cancel" value="<?php echo __('Cancel', 'powerpress'); ?>" onclick="self.parent.tb_remove();" />

	</p>

</div>

</form>

<?php

			powerpress_admin_jquery_footer();

			exit;

		}; break;

		case 'powerpress-jquery-upload': {

			

			if( !current_user_can('edit_posts') )

			{

				powerpress_admin_jquery_header( __('Uploader', 'powerpress') );

				powerpress_page_message_add_notice( __('You do not have sufficient permission to upload media.','powerpress') );

				powerpress_page_message_print();

				powerpress_admin_jquery_footer();

				exit;

			}

			

			check_admin_referer('powerpress-jquery-upload');

			

			$RedirectURL = false;

			$Error = false;

			if( $Settings['blubrry_hosting'] == 0 )

			{

				$Error = __('This feature is available to Blubrry Hosting users only.','powerpress');

			}

			

			if( $Error == false )

			{

				$api_url = sprintf('%s/media/%s/upload_session.json', rtrim(POWERPRESS_BLUBRRY_API_URL, '/'), $Settings['blubrry_program_keyword'] );

				$api_url .= (defined('POWERPRESS_BLUBRRY_API_QSA')?'?'. POWERPRESS_BLUBRRY_API_QSA:'');

				$json_data = powerpress_remote_fopen($api_url, $Settings['blubrry_auth']);

				

				$results =  powerpress_json_decode($json_data);

				

				// We need to obtain an upload session for this user...

				if( isset($results['error']) && strlen($results['error']) > 1 )

				{

					$Error = $results['error'];

					if( strstr($Error, 'currently not available') )

						$Error = __('Unable to find podcasts for this account.','powerpress');

				}

				else if( $results === $json_data )

				{

					$Error = $json_data;

				}

				else if( !is_array($results) || $results == false )

				{

					$Error = $json_data;

				}

				else

				{

					if( isset($results['url']) && !empty($results['url']) )

						$RedirectURL = $results['url'];

				}

			}

			

			if( $Error == false && $RedirectURL )

			{

				$RedirectURL .= '&ReturnURL=';

				$RedirectURL .= urlencode( admin_url("admin.php?action=powerpress-jquery-upload-complete") );

				header("Location: $RedirectURL");

				exit;

			}

			else if( $Error == false )

			{

				$Error = __('Unable to obtain upload session.','powerpress');

			}

			

			powerpress_admin_jquery_header( __('Uploader','powerpress') );

			echo '<h2>'. __('Uploader','powerpress') .'</h2>';

			echo '<p>';

			echo $Error;

			echo '</p>';

			?>

			<p style="text-align: center;"><a href="#" onclick="self.parent.tb_remove();"><?php echo __('Close', 'powerpress'); ?></a></p>

			<?php

			powerpress_admin_jquery_footer();

			exit;

		}; break;

		case 'powerpress-jquery-upload-complete': {

		

			if( !current_user_can('edit_posts') )

			{

				powerpress_admin_jquery_header('Uploader');

				powerpress_page_message_add_notice( __('You do not have sufficient permission to upload media.', 'powerpress') );

				powerpress_page_message_print();

				powerpress_admin_jquery_footer();

				exit;

			}

			

			$File = (isset($_GET['File'])?$_GET['File']:false);

			$Message = (isset($_GET['Message'])?$_GET['Message']:false);

			

			powerpress_admin_jquery_header( __('Upload Complete', 'powerpress') );

			echo '<h2>'. __('Uploader', 'powerpress') .'</h2>';

			echo '<p>';

			if( $File )

			{

				echo __('File', 'powerpress')  .': ';

				echo $File;

				echo ' - ';

			}

			echo $Message;

			echo '</p>';

			?>

			<p style="text-align: center;"><a href="#" onclick="self.parent.tb_remove();"><?php echo __('Close', 'powerpress'); ?></a></p>

			<?php

			

			if( $Message == '' )

			{

?>

<script language="JavaScript" type="text/javascript"><!--

<?php if( $File != '' ) { ?>

self.parent.SelectMedia('<?php echo $File ; ?>'); <?php } ?>

self.parent.tb_remove();

//-->

</script>

<?php

			}

			powerpress_admin_jquery_footer();

			exit;

		}; break;

	}

	

}



function powerpress_admin_jquery_header($title, $jquery = false)

{

	$other = false;

	if( $jquery )

		add_thickbox(); // we use the thckbox for some settings

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>

<head>

<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />

<title><?php bloginfo('name') ?> &rsaquo; <?php echo $title; ?> &#8212; <?php echo __('WordPress', 'powerpress'); ?></title>

<?php



// In case these functions haven't been included yet...

require_once(ABSPATH . 'wp-admin/includes/admin.php');



wp_admin_css( 'css/global' );

wp_admin_css();

if( $jquery )

	wp_enqueue_script('utils');



do_action('admin_print_styles');

do_action('admin_print_scripts');

do_action('admin_head');



echo '<!-- done adding extra stuff -->';



?>

<link rel="stylesheet" href="<?php echo powerpress_get_root_url(); ?>css/jquery.css" type="text/css" media="screen" />

<?php if( $other ) echo $other; ?>

</head>

<body>

<div id="container">

<p style="text-align: right; position: absolute; top: 5px; right: 5px; margin: 0; padding: 0;"><a href="#" onclick="self.parent.tb_remove();" title="<?php echo __('Cancel', 'powerpress'); ?>"><img src="<?php echo admin_url(); ?>/images/no.png" /></a></p>

<?php

}





function powerpress_admin_jquery_footer($jquery = false)

{

	if( $jquery )

		do_action('admin_print_footer_scripts');

	

?>

</div><!-- end container -->

</body>

</html>

<?php

}



?>