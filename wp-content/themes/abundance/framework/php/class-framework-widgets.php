<?php  if ( ! defined('AVIA_FW')) exit('No direct script access allowed');
/**
 * This file holds several widgets exclusive to the framework
 *
 * @author		Christian "Kriesi" Budschedl
 * @copyright	Copyright (c) Christian Budschedl
 * @link		http://Kriesi.at
 * @link		http://aviathemes.com
 * @since		Version 1.0
 * @package 	AviaFramework
 */


/**
 * AVIA TWEETBOX
 *
 * Widget that creates a list of latest tweets
 *  
 * @package AviaFramework
 * @todo replace the widget system with a dynamic one, based on config files for easier widget creation
 */


class avia_tweetbox extends WP_Widget {
	
	function avia_tweetbox() {
		//Constructor
		$widget_ops = array('classname' => 'tweetbox', 'description' => 'A widget to display your latest twitter messages' );
		$this->WP_Widget( 'tweetbox', THEMENAME.' Twitter Widget', $widget_ops );
	}

	function widget($args, $instance) {
		// prints the widget

		extract($args, EXTR_SKIP);
		echo $before_widget;
		
		$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
		$count = empty($instance['count']) ? '' : apply_filters('widget_title', $instance['count']);
		$username = empty($instance['username']) ? '' : apply_filters('widget_title', $instance['username']);
		$exclude_replies = empty($instance['exclude_replies']) ? '' : apply_filters('widget_title', $instance['exclude_replies']);
		$time = empty($instance['time']) ? 'no' : apply_filters('widget_title', $instance['time']);
		$display_image = empty($instance['display_image']) ? 'no' : apply_filters('widget_title', $instance['display_image']);
		
		if ( !empty( $title ) ) { echo $before_title . "<a href='http://twitter.com/$username/' title='$title'>".$title ."</a>". $after_title; };
		
		$messages = tweetbox_get_tweet($count, $username, $widget_id, $time, $exclude_replies, $display_image);
		echo $messages;
		
		echo $after_widget;
		
		
	}

	function update($new_instance, $old_instance) {
		//save the widget
		$instance = $old_instance;	
		foreach($new_instance as $key=>$value)
		{
			$instance[$key]	= strip_tags($new_instance[$key]);
		}
	
		delete_transient(THEMENAME.'_tweetcache_id_'.$instance['username'].'_'.$this->id_base."-".$this->number);
		return $instance;
	}

	function form($instance) {
		//widgetform in backend
		
		$instance = wp_parse_args( (array) $instance, array( 'title' => 'Latest Tweets', 'count' => '3', 'username' => avia_get_option('twitter') ) );
		$title = 			isset($instance['title']) ? strip_tags($instance['title']): "";
		$count = 			isset($instance['count']) ? strip_tags($instance['count']): "";
		$username = 		isset($instance['username']) ? strip_tags($instance['username']): "";
		$exclude_replies = 	isset($instance['exclude_replies']) ? strip_tags($instance['exclude_replies']): "";
		$time = 			isset($instance['time']) ? strip_tags($instance['time']): "";
		$display_image = 	isset($instance['display_image']) ? strip_tags($instance['display_image']): "";
?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>">Title: 
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
		
		<p><label for="<?php echo $this->get_field_id('username'); ?>">Enter your twitter username:
		<input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo esc_attr($username); ?>" /></label></p>
		
		<p>
			<label for="<?php echo $this->get_field_id('count'); ?>">How many entries do you want to display: </label>
			<select class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>">
				<?php 
				$list = "";
				for ($i = 1; $i <= 20; $i++ )
				{
					$selected = "";
					if($count == $i) $selected = 'selected="selected"';
				
					$list .= "<option $selected value='$i'>$i</option>";
				}
				$list .= "</select>";
				echo $list;
				?>
				
			
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('exclude_replies'); ?>">Exclude @replies: </label>
			<select class="widefat" id="<?php echo $this->get_field_id('exclude_replies'); ?>" name="<?php echo $this->get_field_name('exclude_replies'); ?>">
				<?php 
				$list = "";
				$answers = array('yes','no');
				foreach ($answers as $answer)
				{
					$selected = "";
					if($answer == $exclude_replies) $selected = 'selected="selected"';
				
					$list .= "<option $selected value='$answer'>$answer</option>";
				}
				$list .= "</select>";
				echo $list;
				?>
				
			
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('time'); ?>">Display time of tweet</label>
			<select class="widefat" id="<?php echo $this->get_field_id('time'); ?>" name="<?php echo $this->get_field_name('time'); ?>">
				<?php 
				$list = "";
				$answers = array('yes','no');
				foreach ($answers as $answer)
				{
					$selected = "";
					if($answer == $time) $selected = 'selected="selected"';
				
					$list .= "<option $selected value='$answer'>$answer</option>";
				}
				$list .= "</select>";
				echo $list;
				?>
				
			
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('display_image'); ?>">Display Twitter User Avatar</label>
			<select class="widefat" id="<?php echo $this->get_field_id('display_image'); ?>" name="<?php echo $this->get_field_name('display_image'); ?>">
				<?php 
				$list = "";
				$answers = array('yes','no');
				foreach ($answers as $answer)
				{
					$selected = "";
					if($answer == $display_image) $selected = 'selected="selected"';
				
					$list .= "<option $selected value='$answer'>$answer</option>";
				}
				$list .= "</select>";
				echo $list;
				?>
		</p>
		
		
		
	<?php	
	}
}



function tweetbox_get_tweet($count, $username, $widget_id, $time='yes', $exclude_replies='yes', $avatar = 'yes')
{		
		$filtered_message = "";
		$output = "";
		$iterations = 0;
		
		$cache = get_transient(THEMENAME.'_tweetcache_id_'.$username.'_'.$widget_id);
		
		if($cache)
		{
			$tweets = get_option(THEMENAME.'_tweetcache_'.$username.'_'.$widget_id);
		}
		else
		{
			//$response = wp_remote_get( 'http://api.twitter.com/1/statuses/user_timeline.xml?screen_name='.$username );
			$response = wp_remote_get( 'http://api.twitter.com/1/statuses/user_timeline.xml?include_rts=true&screen_name='.$username );
			if (!is_wp_error($response)) 
			{
				$xml = simplexml_load_string($response['body']);
				//follower: (int) $xml->status->user->followers_count

				if( empty( $xml->error ) ) 
			    {
			    	if ( isset($xml->status[0])) 
			    	{
			    		
			    	    $tweets = array();
			    	    foreach ($xml->status as $tweet) 
			    	    {
			    	    	if($iterations == $count) break;
			    	    	
			    	    	$text = (string) $tweet->text;
			    	    	if($exclude_replies == 'no' || ($exclude_replies == 'yes' && $text[0] != "@"))
			    	    	{
			    	    		$iterations++;
			    	    		$tweets[] = array(
			    	    			'text' => tweetbox_filter( $text ),
			    	    			'created' =>  strtotime( $tweet->created_at ),
			    	    			'user' => array(
			    	    				'name' => (string)$tweet->user->name,
			    	    				'screen_name' => (string)$tweet->user->screen_name,
			    	    				'image' => (string)$tweet->user->profile_image_url,
			    	    				'utc_offset' => (int) $tweet->user->utc_offset[0],
			    	    				'follower' => (int) $tweet->user->followers_count

			    	    			));
			    			}
			    		}
			    		
			    		set_transient(THEMENAME.'_tweetcache_id_'.$username.'_'.$widget_id, 'true', 60*30);
			    		update_option(THEMENAME.'_tweetcache_'.$username.'_'.$widget_id, $tweets);
	
			    	}
			    }
			}
		}
		

		
		if(!isset($tweets[0]))
		{
			$tweets = get_option(THEMENAME.'_tweetcache_'.$username.'_'.$widget_id);
		}
		
	    if(isset($tweets[0]))
	    {	
	    
	    	foreach ($tweets as $message)
	    	{	
	    		$output .= '<li class="tweet">';
	    		if($avatar == "yes") $output .= '<div class="tweet-thumb"><a href="http://twitter.com/'.$username.'" title=""><img src="'.$message['user']['image'].'" alt="" /></a></div>';
	    		$output .= '<div class="tweet-text avatar_'.$avatar.'">'.$message['text'];
	    		if($time == "yes") $output .= '<div class="tweet-time">'.date_i18n( get_option('date_format')." - ".get_option('time_format'), $message['created'] + $message['user']['utc_offset']).'</div>';
	    		$output .= '</div></li>';
			}
	    }
	
		
		if($output != "")
		{
			$filtered_message = "<ul class='tweets'>$output</ul>";
		}
		else
		{
			$filtered_message = "<ul class='tweets'><li>No public Tweets found</li></ul>";
		}
		
		return $filtered_message;
}


function tweetbox_filter($text) {
    // Props to Allen Shaw & webmancers.com & Michael Voigt
    $text = preg_replace('/\b([a-zA-Z]+:\/\/[\w_.\-]+\.[a-zA-Z]{2,6}[\/\w\-~.?=&%#+$*!]*)\b/i',"<a href=\"$1\" class=\"twitter-link\">$1</a>", $text);
    $text = preg_replace('/\b(?<!:\/\/)(www\.[\w_.\-]+\.[a-zA-Z]{2,6}[\/\w\-~.?=&%#+$*!]*)\b/i',"<a href=\"http://$1\" class=\"twitter-link\">$1</a>", $text);    
    $text = preg_replace("/\b([a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]*\@[a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]{2,6})\b/i","<a href=\"mailto://$1\" class=\"twitter-link\">$1</a>", $text);
    $text = preg_replace("/#(\w+)/", "<a class=\"twitter-link\" href=\"http://search.twitter.com/search?q=\\1\">#\\1</a>", $text);
    $text = preg_replace("/@(\w+)/", "<a class=\"twitter-link\" href=\"http://twitter.com/\\1\">@\\1</a>", $text);

    return $text;
}









/**
 * AVIA NEWSBOX
 *
 * Widget that creates a list of latest news entries
 *  
 * @package AviaFramework
 * @todo replace the widget system with a dynamic one, based on config files for easier widget creation
 */


class avia_newsbox extends WP_Widget {

	var $avia_term = '';
	var $avia_post_type = '';
	var $avia_new_query = '';

	function avia_newsbox() 
	{
		$widget_ops = array('classname' => 'newsbox', 'description' => 'A Sidebar widget to display latest post entries in your sidebar' );
		
		$this->WP_Widget( 'newsbox', THEMENAME.' Latest News', $widget_ops );
	}
 
	function widget($args, $instance) 
	{	
	
		extract($args, EXTR_SKIP);
		echo $before_widget;
		
		$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
		$count = empty($instance['count']) ? '' : apply_filters('widget_entry_title', $instance['count']);
		$cat = empty($instance['cat']) ? '' : apply_filters('widget_comments_title', $instance['cat']);
		$excerpt = empty($instance['excerpt']) ? '' : $instance['excerpt'];
		
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
		
		
		if(empty($this->avia_term))
		{
			$additional_loop = new WP_Query("cat=".$cat."&posts_per_page=".$count);
		}
		else
		{
			$catarray = explode(',', $cat);


			if(empty($catarray[0]))
			{
				$new_query = array("posts_per_page"=>$count,"post_type"=>$this->avia_post_type);
			}
			else
			{
				if($this->avia_new_query)
				{
					$new_query = $this->avia_new_query;
				}
				else
				{
					$new_query = array(	"posts_per_page"=>$count, 'tax_query' => array( 
													array( 'taxonomy' => $this->avia_term, 
														   'field' => 'id', 
														   'terms' => explode(',', $cat), 
														   'operator' => 'IN')
														  )
													);
				}
			}
			
			$additional_loop = new WP_Query($new_query);
		}
		
		if($additional_loop->have_posts()) : 
		
		echo '<ul class="news-wrap">';
		while ($additional_loop->have_posts()) : $additional_loop->the_post();
		
		echo '<li class="news-content">';
		
		//check for preview images:
		$image = "";
		$slides = avia_post_meta(get_the_ID(), 'slideshow', true);
		
		if( $slides != "" && !empty( $slides[0]['slideshow_image'] ) )
		{
			$image = avia_image_by_id($slides[0]['slideshow_image'], 'widget', 'image');
		}
		
		if(!$image && current_theme_supports( 'post-thumbnails' ))
		{
			$image = get_the_post_thumbnail( get_the_ID(), 'related' );
		}
		
		echo "<a class='news-link' title='".get_the_title()."' href='".get_permalink()."'>";
		echo "<span class='news-thumb'>";
		echo $image;
		echo "</span>";
		echo "<strong class='news-headline'>".get_the_title();
		echo "<span class='news-time'>".get_the_time("F j, Y, g:i a")."</span>";
		echo "</strong>";
		echo "</a>";
		
		if('display title and excerpt' == $excerpt)
		{
			echo "<div class='news-excerpt'>";
			the_excerpt();
			echo "</div>";
		}
		
		echo '</li>';
		
		
		endwhile;
		echo "</ul>";
		wp_reset_postdata();
		endif;
		
		
		echo $after_widget;
		
	}
 
 
	function update($new_instance, $old_instance) 
	{
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['count'] = strip_tags($new_instance['count']);
		$instance['excerpt'] = strip_tags($new_instance['excerpt']);
		$instance['cat'] = implode(',',$new_instance['cat']);
		return $instance;
	}

 
 
	function form($instance) 
	{
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'count' => '', 'cat' => '', 'excerpt'=>'' ) );
		$title = strip_tags($instance['title']);
		$count = strip_tags($instance['count']);
		$excerpt = strip_tags($instance['excerpt']);
		
		
		$elementCat = array("name" 	=> "Which categories should be used for the portfolio?",
							"desc" 	=> "You can select multiple categories here",
				            "id" 	=> $this->get_field_name('cat')."[]",
				            "type" 	=> "select",
				            "std"   => strip_tags($instance['cat']),
				            "class" => "",
            				"multiple"=>6,
				            "subtype" => "cat");
		//check if a different taxonomy than the default is set
		if(!empty($this->avia_term))
		{
			$elementCat['taxonomy'] = $this->avia_term;
		}
		
		
		
		
		$html = new avia_htmlhelper();
	
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: 
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
		
		<p>
			<label for="<?php echo $this->get_field_id('count'); ?>">How many entries do you want to display: </label>
			<select class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>">
				<?php 
				$list = "";
				for ($i = 1; $i <= 20; $i++ )
				{
					$selected = "";
					if($count == $i) $selected = 'selected="selected"';
				
					$list .= "<option $selected value='$i'>$i</option>";
				}
				$list .= "</select>";
				echo $list;
				?>
				
			
		</p>
		
		<p><label for="<?php echo $this->get_field_id('cat'); ?>">Choose the categories you want to display (multiple selection possible):
		<?php echo $html->select($elementCat); ?>
		</label></p>
		
		<p>
			<label for="<?php echo $this->get_field_id('excerpt'); ?>">Display title only or title &amp; excerpt</label>
			<select class="widefat" id="<?php echo $this->get_field_id('excerpt'); ?>" name="<?php echo $this->get_field_name('excerpt'); ?>">
				<?php 
				$list = "";
				$answers = array('show title only','display title and excerpt');
				foreach ($answers as $answer)
				{
					$selected = "";
					if($answer == $excerpt) $selected = 'selected="selected"';
				
					$list .= "<option $selected value='$answer'>$answer</option>";
				}
				$list .= "</select>";
				echo $list;
				?>
				
			
		</p>
		
			
<?php
	}
}



/**
 * AVIA PORTFOLIOBOX
 *
 * Widget that creates a list of latest portfolio entries. Basically the same widget as the newsbox with some minor modifications, therefore it just extends the Newsbox
 *  
 * @package AviaFramework
 * @todo replace the widget system with a dynamic one, based on config files for easier widget creation
 */
 
class avia_portfoliobox extends avia_newsbox
{
	function avia_portfoliobox() 
	{
		
		$this->avia_term = 'portfolio_entries';
		$this->avia_post_type = 'portfolio';
		$this->avia_new_query = ''; //set a custom query here
		
		
		$widget_ops = array('classname' => 'newsbox', 'description' => 'A Sidebar widget to display latest portfolio entries in your sidebar' );
		
		$this->WP_Widget( 'portfoliobox', THEMENAME.' Latest Portfolio', $widget_ops );
	}
}




/**
 * AVIA SOCIALCOUNT
 *
 * Widget that retrieves, stores and displays the number of twitter and rss followers
 *  
 * @package AviaFramework
 * @todo replace the widget system with a dynamic one, based on config files for easier widget creation
 */


class avia_socialcount extends WP_Widget {
	
	function avia_socialcount() {
		//Constructor
		$widget_ops = array('classname' => 'avia_socialcount', 'description' => 'A widget to display your twitter and rss followers' );
		$this->WP_Widget( 'avia_socialcount', THEMENAME.' Twitter and RSS count', $widget_ops );
	}

	function widget($args, $instance) {
		// prints the widget

		extract($args, EXTR_SKIP);
		$twitter = empty($instance['twitter']) ? '' : apply_filters('widget_title', $instance['twitter']);
		$rss 	 = empty($instance['rss'])     ? '' : apply_filters('widget_title', $instance['rss']);
		$rss = preg_replace('!https?:\/\/feeds.feedburner.com\/!','',$rss);
		
		$follower = $this->count_followers($twitter, $rss, $widget_id);
		
		
		if(!empty($follower) && is_array($follower))
		{
			$addClass = "asc_multi_count";
			if(!isset($follower['twitter']) || !isset($follower['rss'])) $addClass = 'asc_single_count';
			
			echo $before_widget;
			
			if(isset($follower['twitter']))
			{
				$link = 'http://twitter.com/'.$twitter.'/';
				echo "<a href='$link' class='asc_twitter $addClass'><strong class='asc_count'>".$follower['twitter']."</strong><span>".__('Follower','avia_framework')."</span></a>";
			}
			
			if(isset($follower['rss']))
			{
				$link = 'http://feeds.feedburner.com/'.$rss;
				echo "<a href='$link' class='asc_rss $addClass'><strong class='asc_count'>".$follower['rss']."</strong><span>".__('Subscribers','avia_framework')."</span></a>";
			}

			echo $after_widget;
		}
	}
	
	function count_followers($twitter, $rss, $widget_id)
	{
		$follower = array();
		$optionkey = strtolower(THEMENAME.'_follower_cache_id_'.$widget_id);
		$cache = get_transient($optionkey);
		
		
		if($cache)
		{
			$follower = get_option($optionkey);
		}
		else
		{
			
			if($twitter != "")
			{
				$twittercount = wp_remote_get( 'http://api.twitter.com/1/statuses/user_timeline.xml?screen_name='.$twitter );
				if (!is_wp_error($twittercount)) 
				{
					$xml = simplexml_load_string($twittercount['body']);
					
					if( empty( $xml->error ) && isset($xml->status->user->followers_count)) 
					{
						$follower['twitter'] = (int) $xml->status->user->followers_count;
					}
				}
			} 
			
			if($rss != "")
			{
				$requesturl = "http://feedburner.google.com/api/awareness/1.0/GetFeedData?uri=http://feeds.feedburner.com/".$rss.'&dates=' . date('Y-m-d', strtotime('-2 days', time()));
				$feedcount = wp_remote_get($requesturl);
				

				if (!is_wp_error($feedcount)) 
				{
					$xml = simplexml_load_string($feedcount['body']);
					
					if(is_object($xml->feed->entry))
					{
						$follower['rss'] = (int) $xml->feed->entry->attributes()->circulation;
					}
				}
			}
			
			$fallback = get_option($optionkey);
		
			if(!isset($follower['rss']) && isset($fallback['rss'])) $follower['rss'] = $fallback['rss'];
			if(!isset($follower['twitter']) && isset($fallback['twitter'])) $follower['twitter'] = $fallback['twitter'];

			set_transient($optionkey, 1, 60*60*12);
			update_option($optionkey, $follower);
			
		}
		
		return $follower;
	}
	
	
	

	function update($new_instance, $old_instance) {
		//save the widget
		$instance = $old_instance;	
		foreach($new_instance as $key=>$value)
		{
			$instance[$key]	= strip_tags($new_instance[$key]);
		}
	
		delete_transient(THEMENAME.'_follower_cache_id_'.$this->id_base."-".$this->number);
		return $instance;
	}

	function form($instance) {
		//widgetform in backend
		
		$instance = wp_parse_args( (array) $instance, array('rss' => avia_get_option('feedburner'), 'twitter' => avia_get_option('twitter') ) );
		$twitter = empty($instance['twitter']) ? '' :  strip_tags($instance['twitter']);
		$rss 	 = empty($instance['rss'])     ? '' :  strip_tags($instance['rss']);
?>
		<p>
		<label for="<?php echo $this->get_field_id('twitter'); ?>">Twitter Username: 
		<input class="widefat" id="<?php echo $this->get_field_id('twitter'); ?>" name="<?php echo $this->get_field_name('twitter'); ?>" type="text" value="<?php echo esc_attr($twitter); ?>" /></label></p>
		
		<p><label for="<?php echo $this->get_field_id('rss'); ?>">Enter your feedburner url:
		<input class="widefat" id="<?php echo $this->get_field_id('rss'); ?>" name="<?php echo $this->get_field_name('rss'); ?>" type="text" value="<?php echo esc_attr($rss); ?>" /></label></p>
		
	
		
	<?php	
	}
}




/**
 * AVIA ADVERTISING WIDGET
 *
 * Widget that retrieves, stores and displays the number of twitter and rss followers
 *  
 * @package AviaFramework
 * @todo replace the widget system with a dynamic one, based on config files for easier widget creation
 */
 
 
//multiple images

class avia_partner_widget extends WP_Widget {
	
	function avia_partner_widget() {
		
		$this->add_cont = 2;
		//Constructor
		$widget_ops = array('classname' => 'avia_partner_widget', 'description' => 'An advertising widget that displays 2 images with 125 x 125 px in size' );
		$this->WP_Widget( 'avia_partner_widget', THEMENAME.' Advertising Area', $widget_ops );
	}

	function widget($args, $instance) 
	{
		extract($args, EXTR_SKIP);
		echo $before_widget;
		
		global $kriesiaddwidget, $firsttitle;
		$kriesiaddwidget ++;
		
		$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
		$image_url = empty($instance['image_url']) ? '<span class="avia_parnter_empty">'.__('Advertise here','avia_framework').'</span>' : '<img class="rounded" src="'.$instance['image_url'].'" title="" alt=""/>';
		$ref_url = empty($instance['ref_url']) ? '#' : apply_filters('widget_comments_title', $instance['ref_url']);
		$image_url2 = empty($instance['image_url2']) ? '<span class="avia_parnter_empty">'.__('Advertise here','avia_framework').'</span>' : '<img class="rounded" src="'.$instance['image_url2'].'" title="" alt=""/>';
		$ref_url2 = empty($instance['ref_url2']) ? '#' : apply_filters('widget_comments_title', $instance['ref_url2']);

		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
		echo '<a href="'.$ref_url.'" class="preloading_background  avia_partner1 link_list_item'.$kriesiaddwidget.' '.$firsttitle.'" >'.$image_url.'</a>';
		if($this->add_cont == 2) echo '<a href="'.$ref_url2.'" class="preloading_background avia_partner2 link_list_item'.$kriesiaddwidget.' '.$firsttitle.'" >'.$image_url2.'</a>';
		echo $after_widget;
		
		if($title == '')
		{
			$firsttitle = 'no_top_margin';
		}
		
	}
 
 
	function update($new_instance, $old_instance) {
		//save the widget
		$instance = $old_instance;	
		foreach($new_instance as $key=>$value)
		{
			$instance[$key]	= strip_tags($new_instance[$key]);
		}
		return $instance;
	}

 
 
	function form($instance) 
	{
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'image_url' => '', 'ref_url' => '', 'image_url2' => '', 'ref_url2' => '' ) );
		$title = strip_tags($instance['title']);
		$image_url = strip_tags($instance['image_url']);
		$ref_url = strip_tags($instance['ref_url']);
		$image_url2 = strip_tags($instance['image_url2']);
		$ref_url2 = strip_tags($instance['ref_url2']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: 
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
		
		<p><label for="<?php echo $this->get_field_id('image_url'); ?>">Image URL: <?php if($this->add_cont == 2) echo "(125px * 125px):"; ?>
		<input class="widefat" id="<?php echo $this->get_field_id('image_url'); ?>" name="<?php echo $this->get_field_name('image_url'); ?>" type="text" value="<?php echo esc_attr($image_url); ?>" /></label></p>
		
		<p><label for="<?php echo $this->get_field_id('ref_url'); ?>">Referal URL: 
		<input class="widefat" id="<?php echo $this->get_field_id('ref_url'); ?>" name="<?php echo $this->get_field_name('ref_url'); ?>" type="text" value="<?php echo esc_attr($ref_url); ?>" /></label></p>
		
		<?php if($this->add_cont == 2)
		{ ?>
		
				<p><label for="<?php echo $this->get_field_id('image_url2'); ?>">Image URL 2: (125px * 125px):
		<input class="widefat" id="<?php echo $this->get_field_id('image_url2'); ?>" name="<?php echo $this->get_field_name('image_url2'); ?>" type="text" value="<?php echo esc_attr($image_url2); ?>" /></label></p>
		
		<p><label for="<?php echo $this->get_field_id('ref_url2'); ?>">Referal URL 2: 
		<input class="widefat" id="<?php echo $this->get_field_id('ref_url2'); ?>" name="<?php echo $this->get_field_name('ref_url2'); ?>" type="text" value="<?php echo esc_attr($ref_url2); ?>" /></label></p>
		
		<?php }?>
			
<?php
	}
}


//one image
class avia_one_partner_widget extends avia_partner_widget
{
	function avia_one_partner_widget() 
	{
		
		$this->add_cont = 1;
		
		$widget_ops = array('classname' => 'avia_one_partner_widget', 'description' => 'An advertising widget that displays 1 big image' );
		
		$this->WP_Widget( 'avia_one_partner_widget', THEMENAME.' Big Advertising Area', $widget_ops );
	}
}




/**
 * AVIA COMBO WIDGET
 *
 * Widget that retrieves, stores and displays the number of twitter and rss followers
 *  
 * @package AviaFramework
 * @todo replace the widget system with a dynamic one, based on config files for easier widget creation
 */


class avia_combo_widget extends WP_Widget {
	
	function avia_combo_widget() {
		//Constructor
		$widget_ops = array('classname' => 'avia_combo_widget', 'description' => 'A widget that displays your popular posts, recent posts, recent comments and a tagcloud' );
		$this->WP_Widget( 'avia_combo_widget', THEMENAME.' Combo Widget', $widget_ops );
	}

	function widget($args, $instance) 
	{
		// prints the widget

		extract($args, EXTR_SKIP);
		$posts = empty($instance['count']) ? 4 : $instance['count'];
		
		echo $before_widget;
		echo "<div class='tabcontainer tab_initial_open tab_initial_open__1'>";
		
		echo '<div class="tab first_tab widget_tab_popular"><span>'.__('Popular', 'avia_framework').'</span></div>';
		echo "<div class='tab_content'>";
		avia_get_post_list('cat=&orderby=comment_count&posts_per_page='.$posts);
		echo "</div>";
		
		echo '<div class="tab widget_tab_recent"><span>'.__('Recent', 'avia_framework').'</span></div>';
		echo "<div class='tab_content'>";
		avia_get_post_list('showposts='. $posts .'&orderby=post_date&order=desc');
		echo "</div>";
		
		echo '<div class="tab widget_tab_comments"><span>'.__('Comments', 'avia_framework').'</span></div>';
		echo "<div class='tab_content'>";
		avia_get_comment_list( array('number' => $posts, 'status' => 'approve', 'order' => 'DESC') );
		echo "</div>";
		
		echo '<div class="tab last_tab widget_tab_tags"><span>'.__('Tags', 'avia_framework').'</span></div>';
		echo "<div class='tab_content tagcloud'>";
		wp_tag_cloud('smallest=12&largest=12&unit=px');
		echo "</div>";
		
		echo "</div>";
		echo $after_widget;
	}
		

	function update($new_instance, $old_instance) 
	{
		$instance = $old_instance;	
		foreach($new_instance as $key=>$value)
		{
			$instance[$key]	= strip_tags($new_instance[$key]);
		}
	
		return $instance;
	}

	function form($instance) {
		//widgetform in backend
		
		$instance = wp_parse_args( (array) $instance, array('count' => 4) );
		if(!is_numeric($instance['count'])) $instance['count'] = 4;

?>
		<p>
		<label for="<?php echo $this->get_field_id('count'); ?>">Number of posts you want to display: 
		<input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo esc_attr($instance['count']); ?>" /></label></p>
		
	
	<?php	
	}
}

/*-----------------------------------------------------------------------------------
get posts posts
-----------------------------------------------------------------------------------*/
if (!function_exists('avia_get_post_list')) 
{
	function avia_get_post_list( $avia_new_query , $excerpt = false) 
	{
		global $avia_config;
		
		$additional_loop = new WP_Query($avia_new_query);
		
		if($additional_loop->have_posts()) : 
		echo '<ul class="news-wrap">';
		while ($additional_loop->have_posts()) : $additional_loop->the_post();
		
		echo '<li class="news-content">';
		
		//check for preview images:
		$image = "";
		$slides = avia_post_meta(get_the_ID(), 'slideshow');
		
		if( $slides != "" && !empty( $slides[0]['slideshow_image'] ) )
		{
			$image = avia_image_by_id($slides[0]['slideshow_image'], 'widget', 'image');
		}
		
		echo "<a class='news-link' title='".get_the_title()."' href='".get_permalink()."'>";
		echo "<span class='news-thumb'>";
		echo $image;
		echo "</span>";
		echo "<strong class='news-headline'>".avia_backend_truncate(get_the_title(), 55," ");
		echo "<span class='news-time'>".get_the_time("F j, Y, g:i a")."</span>";
		echo "</strong>";
		echo "</a>";
		
		if('display title and excerpt' == $excerpt)
		{
			echo "<div class='news-excerpt'>";
			the_excerpt();
			echo "</div>";
		}
		
		echo '</li>';
		
		
		endwhile;
		echo "</ul>";
		wp_reset_postdata();
		endif;
	}
}





if (!function_exists('avia_get_comment_list')) 
{
	function avia_get_comment_list($avia_new_query) 
	{
		global $avia_config;
		
		$comments = get_comments($avia_new_query);
		
		if(!empty($comments)) : 
		echo '<ul class="news-wrap">';
		foreach($comments as $comment)
		{
		
		echo '<li class="news-content">';
		echo "<a class='news-link' title='".get_the_title($comment->comment_post_ID)."' href='".get_comment_link($comment)."'>";
		echo "<span class='news-thumb'>";
		echo get_avatar($comment,'48');
		echo "</span>";
		echo "<strong class='news-headline'>".avia_backend_truncate($comment->comment_content, 55," ");
		echo "<span class='news-time'>".get_the_time("F j, g:i a", $comment->comment_post_ID)." by ".$comment->comment_author."</span>";
		echo "</strong>";
		echo "</a>";

		echo '</li>';
		
		
		}
		echo "</ul>";
		wp_reset_postdata();
		endif;
	}
}