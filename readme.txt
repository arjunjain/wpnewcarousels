=== WPNewCarousels ===
Contributors: arjunjain08	
Author URI: http://www.arjunjain.info
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=SQC4WR7X5JGDY&lc=IN&item_name=WPNewCarousels&button_subtype=services&currency_code=USD&bn=PP%2dBuyNowBF%3abtn_buynowCC_LG%2egif%3aNonHosted
Plugin URI: http://wordpress.org/extend/plugins/wpnewcarousels/  
Tags: carousel, wordpress carousel, multisite carousel, multisite slider, wordpress slider, carousel plugin, wordpress carousel plugin
Requires at least: 3.0
Tested up to: 3.9.1
Stable tag: 1.7.1

This plugin is used to create the carousel that can be inserted to any wordpress page/post. This plugin also support Wordpress Multisite setup.

== Description ==
This plugin is used to create the carousel that can be inserted to any wordpress page/post. This plugin also support wordpress multisite setup.

**Features**

* wpnewcarousel button added to your default editor
* Support wordpress multisite setup
* Manage carousel width,height,effects,speed,animation using short code or dashboard
* Add multiple carousel on same page
* Manage carousel slide order

== Support ==

* For critical issue related to functionality, create the new issue on [github](https://github.com/arjunjain/wpnewcarousels/issues)
* Refer this [wiki](https://github.com/arjunjain/wpnewcarousels/wiki)
* Fill up this [form](http://www.arjunjain.info/contact) to leave comments,ask question,suggest new feature or directly mail at arjun@arjunjain.info
* For Demo [Click Here](http://www.arjunjain.info/demos/wpnewcarousel/)

== Installation ==

* Unzip
* Upload to your plugin directory
* Enable the plugin

== Using the WPNewCarousels ==

* You can refer [wiki](https://github.com/arjunjain/wpnewcarousels/wiki)

* Add new carousel in wpnewcarousel settings page. 
* Use shortcode on any wordpress page 
 `[wpnewcarousel name="CAROUSEL_NAME" height="" width=""  startslide="" animationspeed="" imagepausetime="" shownav="" hoverpause=""]` 
* "height" and "width" are the optional parameters when using short code, if use then these parameter will replace the default values of height and width.
* Only "name" is the required parameter, other parameter are optional when using short code.
* "effect" is the type of effect you want to show between image transition.
	The effect parameter can be any of the following:		
	sliceDown, sliceDownLeft, sliceUp, sliceUpLeft, sliceUpDown, sliceUpDownLeft,
	fold, fade, random, slideInRight, slideInLeft, boxRandom, boxRain, 
	boxRainReverse, boxRainGrow, boxRainGrowReverse
* "startslide" is the starting slide number, default value is 0.
* "animationspeed" is the speed of carousel animation, default value is 500 [ where 1000 = 1sec ].
* "imagepause" is the time between image transition, default value is 3000.
* "shownav" is the flag to show navigation control with carousel, default value is true.
* "hoverpause" is the flag to stop carousel on mouse over, default value is true.

== Screenshots ==

1. wpnewcarousel button in your default wordpress editor
2. Add New carousel
3. Display add carousel
4. Add carousel data

== Changelog == 

= Version 1.7 (2014-03-02) =
* Add carousel slide delete button
* Fix carousel slide update functionality

= Version 1.6 (2013-09-16) =
* Fix add new slide delete content of unsaved slide bug
* Add manage carousel slide order functionality

= Version 1.5 (2012-12-21) =
* Change Carousel admin dashboard
* Modify database structure
* Provide support to add multiple carousel on same page

= Version 1.4 (2012-9-4) =
* Integrate with wordpress media library, add new upload button with every background image url text box
* Add few more checks with short code at backend to validate correctly.

= Version 1.3 (2012-4-5) =
* Fix IE bugs.
* Add effect parameter with carousel

= Version 1.2 (2012-3-28) =
* Modify carousel short code.
* Add startslide,animationspeed,imagepausetime,shownav,hoverpause parameter with carousel.
* Add carousel button in default wordpress editor.

= Version 1.1 (2012-3-5) =
* Modify manage carousel class.
* Fix dynamic path to stylesheet and script.

= Version 1.0 (2012-1-6) =
* Update readme.txt file.
