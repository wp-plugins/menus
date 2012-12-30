=== Menus ===
Contributors: dsader
Donate link: http://dsader.snowotherway.org
Tags: menus, administration menus, admin menus, multisite, toggle admin menus
Requires at least: 3.5
Tested up to: 3.5
Stable tag: Trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WP3.5 multisite "mu-plugin" to toggle adminstration menus for the entire network of sites.

== Description ==
Enable or disable WP3 Multisite Backend Menus. Adds options to toggle administration menus at Network Admin->Settings page under "Menu Settings". WP3 already toggles the Plugins menu, I've added a bunch more in the same/similar way.

I use the plugin to simplify the menus available to the entire network of sites. I use this plugin in a WP3.5 Multisite installation to disable the Deltete Blog, Permalinks, Import, Add Users and Themes menus.

The plugin also removes a some of the admin bar menu items as well.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `ds_wp3_menus.php` to the `/wp-content/mu-plugins/` directory
2. Set multisite "Menu Settings" option at Network->Settings page

== Frequently Asked Questions ==

* Will this plugin also hide menus added by plugins? Maybe, if the parent page is hidden. Menus added by plugins will not be listed at the SiteAdmin->Options list.
* Will this plugin hide the corresponding items from the admin head favorites? Yes. The +New and a couple others
* Will this plugin disable media uploads? Yes. Well, from the Media menus at least. 
* Will this plugin hide the media upload buttons? No. The Media Upload Buttons in the edit post/page form can be removed with my Toggle Meta Boxes Sitewide plugin.
* Can I have different menus for different roles of users on different blogs? No, this plugin toggles menus for all users and all blogs regardless of Cap/Role (only SuperAdmin can overide the limits of the plugin however).

== Screenshots ==

1. Menu Settings: Enable Adminstration Menus
2. Admin Bar Dropdown Shortcuts

== Changelog ==
= 3.5 =
* Tested up to: WP 3.5 major rewrite for 3.5 admin bar

== Upgrade Notice ==
= 3.5 =
* Tested up to: WP 3.5
