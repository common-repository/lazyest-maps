=== Lazyest Maps ===
Contributors: macbrink
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=1257529
Tags: lazyest-gallery,maps,googlemaps,google
Requires at least: 3.4
Tested up to: 3.5.1
Stable tag: 0.6.0
License: GPLv3 or later
This plugin shows your Lazyest Gallery images location on a Google Map.

== Description ==

Google Maps plugin for [Lazyest Gallery](http://wordpress.org/extend/plugins/lazyest-gallery/)

The plugin reads locations from images exif data and stores them in Lazyest Gallery.
In folder view, the locations are drawn on a map.

This plugin uses [Google Maps API v3](https://developers.google.com/maps/documentation/javascript/tutorial) and the [GMAP3](http://gmap3.net/) jQuery plugin 

= Since version 0.4.0 =
* Map for images in a folder. It uses simple info windows to display thumbnail over marker
* Button in Folder Manager to (re)read Geo Data from your images.
* Shortcode for posts. Works like the Lazyest Gallery `[lg_folder]` shortcode. Syntax: `[lazyestmap folder="myfolder"]`
* Settings screen: Goto Settings -> Lazyest Gallery -> Thumbnail Options -> Maps

== Installation ==

0. Install and configure [Lazyest Gallery](http://wordpress.org/extend/plugins/lazyest-gallery/)
1. Install Lazyest Maps using the WordPress plugin installer
2. Activate 

== Frequently Asked Questions ==

= Does Lazyest Maps read iptc data? =

Not yet.

== Screenshots ==

1. Example of a map on the Lazyest Gallery folder view

== Upgrade Notice ==

= 0.6.0 =
* Upgrade to GMAP3 version 5.0b

== Changelog ==

= 0.6.0 =
* Changed: GMAP3 library upgraded from 4.1 to 5.0b
* Changed: Settings screen lay out

= 0.5 =
* Bug fix: Incorrect Exif variable used in longitude calculation


= 0.4.1 =
* Tested on WordPress 3.5

= 0.4.0 =
* Added: Show caption and/or description in info window
* Added: Height of map is option
* Added: Settings screen
* Bug Fix: Multiple map shortcodes on front page
* Bug Fix: Json encode marker data 

= 0.3.0 =
* Changed: frontend functionality in frontend.php
* Added: [lazyestmap] shortcode for posts

= 0.2.0 =
* Added: Button in Folder manager to (re)read Geo data from images
* Public release of Lazyest Maps

= 0.1.0 = 
* First release of lazyest-maps plugin
* Based on [GMAP3 version 4.1](http://gmap3.net/)

== License ==
Copyright (c) 2008-2012 - Marcel Brinkkemper
 
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see  [http://www.gnu.org/licenses/](http://www.gnu.org/licenses/).