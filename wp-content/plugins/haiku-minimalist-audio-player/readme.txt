=== Haiku minimalist audio player ===

Contributors: daltonrooney 

Tags: audio player, jplayer, html5, audio, flash, mp3, music, minimalist, jquery, haiku

Requires at least: 2.8

Donate link: http://madebyraygun.com/donate/

Tested up to: 3.2

Stable tag: 0.4.3



== Description ==



A simple HTML5-based audio player that inserts a text link or graphical player for audio playback. Compatible with WP Audio Player style links, fully accessible and degrades gracefully, and includes Google Analytics tracking of audio plays. Based on [jPlayer](http://www.jplayer.org/).



== Installation ==



Extract the zip file and upload the contents to the wp-content/plugins/ directory of your WordPress installation, and then activate the plugin from plugins page. 



Use the shortcode [haiku url="http://example.com/file.mp3" title="Title of audio file"] to play an audio file. Use the full URL of the audio file unless you've set a default file location in the settings page. The title field is recommended for search engine and accessibility purposes and required if you are using Google Analytics.



= Settings Options =



The default file location field allows you to specify a folder in your site for your MP3 files. If, for example, all of your audio files are in http://yoursite.com/audio, set the default folder to /audio, and use the shortcode like this: [haiku url="file.mp3"]. This is also helpful if you're replacing an existing WordPress Audio Player installation. You can overwrite this setting on a per-player basis with the attribute defaultpath=disabled. For example, if you wanted to link to a file in a different folder you would use the shortcode [haiku url="http://yoursite/audiofolder2/file.mp3" defaultpath=disabled].

		

The Google Analytics setting enables a script which tracks play events in your Google Analytics account using the title field. You must already have Google Analytics tracking installed on your site, using the asynchronous version of the script in the head of your HTML document.

		

The player includes the ability to automatically turn all MP3 links into an audio player instance. Simply check the "Replace all mp3 links" checkbox on the settings. You may experience conflicts if you have other plugins that also override MP3 links, like Shadowbox.

		

The player is now drop-in compatible with WordPress Audio Player. If you're replacing a WordPress Audio Player install, check the "replace WP Audio Player" box to automatically replace all instances of the WP Audio Player shortcode. (WP Audio Player must be disabled or removed for this to work). The Haiku shortcode is still the recommended format as it allows the "Title" field for Google Analytics support.

				

The player includes a graphical mode, which is turned off by default. Enable graphical mode by changing the default setting in the settings panel or by adding the attribute graphical="true" to your shortcode. Can be overridden on a per-player basis.



Please note that the graphical player is at an early stage of development and should be tested before you deploy it to a large audience. It is likely that the design of the player will change in future versions. It's just HTML & CSS, so feel free to experiment with your own examples.



== Frequently Asked Questions ==





= Google Analytics tracking isn't working = 



You must have Google Analytics installed in asyncrhonous mode at the top of your HTML document for the tracking code to work. If you don't know how to do this yourself, consider a plugin like [Asynchronous Google Analytics for WordPress](http://wordpress.org/extend/plugins/async-google-analytics/)



To learn how to include the asynchronous snippet yourself, check out the [Google documentation](http://code.google.com/apis/analytics/docs/tracking/asyncTracking.html) for the feature.



Audio plays will show up under the "Plays" category in the Events area of the Content section. More information on event tracking can be found in the [Analytics user guide](http://code.google.com/apis/analytics/docs/tracking/eventTrackerGuide.html).



Make sure you include the title attribute for the shortcode, that's what Analytics uses as the name of the event.



= The graphical mode is all screwed up! =



The graphical mode is just basic HTML & CSS. I've done my best to make it compatible with a variety of themes, but I can't test every single one. Send me a link via the [forum](http://madebyraygun.com/support/forum) and I'll try to help you out.



= How can I customize the graphical mode with my own colors, text, or what have you? =



Since the player is straight up HTML & CSS, feel free to make any changes you want. The original assets are zipped up in the "Resources" folder. If you would like advanced customization, [my company](http://madebyraygun.com) is available to help.



= I've got a suggestion, how should I send it to you? =



You might want to drop a link in the [forum](http://madebyraygun.com/forum).



== Screenshots ==





1. Example of inserted player.



2. Settings page.



== Changelog ==





= 0.4.3 =

* Change javascript loading behavior so plugin doesn't try to start too early and cause a Flash error on some browsers.

= 0.4.2 =

* Minor PHP cleanup.


= 0.4.1 =



* MP3 auto-replace now replaces all links on page (thanks pasevin!).





* Optimized database updates & admin page load. 



* Settings link in plugins list.



= 0.4.0 =



* Option to replace all mp3 links with a player instance.



* Drop-in compatibility with the WordPress Audio Player plugin shortcode.



* Option for default folder for all of your audio files.



= 0.3.1 =



* Added titles and alt tags to improve accessibility.



= 0.3.0 = 



* Added Google Analytics tracking.



= 0.2.0 =



* Added new graphical player option



* Removed pause button from text version of player



= 0.1.3 =



* Updated folder structure as per WordPress repository requirements



= 0.1.2 =



* Incorrect shortcode in documentation.



= 0.1.1 =



* Fixed "stop on pause" issue.



= 0.1 =



* First version





== Upgrade Notice ==



= 0.3.0 =



* Google Analytics tracking for play events.



= 0.2.0 =



New graphical version of plugin. Please note, the pause button has been removed in the latest version of the text player.