=== Plugin Name ===

Contributors: aueda

Donate link: http://tempspace.net/plugins/

Tags: audio,player,cross-browser,cross-platform,accessibility,accessible,cross,browser,platform,slideshow,mp3,mp3 player,audio player,soundmanager2

Requires at least: 3.2.0

Tested up to: 3.3

Stable tag: 1.6.1



This is a simple, cross-browser, accessible audio player (MP3 player) plugin.



== Description ==



This is a simple, cross platform, accessible audio player.



(Basic usage)

Write a shortcode like the following:<br>

<br>

[esplayer url="http://example.com/wp-content/uploads/sample.mp3" width="200" height="25"]<br>

<br>

For more details, please see the document from the following URL:<br>

http://tempspace.net/plugins/?page_id=4





This audio player has three different modes:



(1)Simple mode<br>

What the player has is only a play-button. Or when its width is specified as greater than 2x of height, a positioning slider is displayed.



(2)Image mode<br>

When an image on the wordpress page or post is clicked, specified music begins playing.



(3)Slideshow mode<br>

Playing slideshow and music. Timings of changing images are specified in an timetable.



I tested this plugin with IE8,Chrome,Firefox,Opera,Safari(WindowsXP), IE9(Windows7), iPod touch(iOS 4.3.5), and Android(2.3, emulator).



This audio player has accessibility features. 



* Text browser users can download audio file. 

* Screen reader users can manipulate the player by selecting play button, stop button, etc. 



You can enable or disable these feature in the admin page. I tested them with Microsoft Narrator, JAWS(demo version), Focus Talk(demo version) , ALTAIR and NVDA.





== Installation ==



Install the plugin like usual ones. Then activate it.



== Frequently Asked Questions ==



= What EsAudioPlayer stands for? =



It means 'Extremely Simple Audio Player'. At the beginning, this player had only simple mode, which had only play/stop button. 



== Screenshots ==



1. This is a preview in the color setting in the admin screen



== Changelog ==



= 1.6.1 =

* Improved stability.



= 1.6.0 =

* Update accessibility features. Added a "title" parameter and you can include title in the button speech.

* Solved the problem that player does not appear under some environments.

* Added an autoplay feature.



= 1.5.2 =

* Solved the problem that accessibility features were broken.



= 1.5.1 =

* Solved the problem that player cannot be put in the sidebar text widget.

* Solved the problem of showing some garbage strings in excerpts of posts.



= 1.5.0 =

* Added a live preview in the color setting screen.

* Added adjustment of corner radius.

* Added adjustment of size for smartphones.



= 1.4.0 =

* You can put a player in a sidebar text widget.

* Setting page moved from plugin tab to setting tab.

* Solved the problem that pleayer buttons are displayed with wrong colors.



= 1.3.2 =

* Solved the problem that accessible buttons are not created when the accessibility function is enabled.



= 1.3.1 =

* Solved the problem that sometimes a shortcord is not interpreted when there are only whitespaces between the shortcord and another one.



= 1.3.0 =

* Added accessibility features.



= 1.2.0 =

* Changed usage of 'image mode'. Now ID of the image must be provided instead of its URL.

* Improved performance of procedure of before displaying the player button.



= 1.1.1 =

* Removed dependency on jQuery-mobile and solved some compatibility issues.



= 1.1.0 =

* Added color picker to the configuration page.



= 1.0.0 =

* Improved stability.



= 0.01h-pre7 =

* Changed plugin URL.



= 0.01h-pre6 =

* First release.

