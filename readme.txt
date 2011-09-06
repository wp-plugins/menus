=== Menus ===
Contributors: dsader
Donate link: http://dsader.snowotherway.org
Tags: menus, administration menus, admin menus, multisite, toggle admin menus
Requires at least: 3.2.1
Tested up to: 3.2.1
Stable tag: Trunk

WP3.2.1 multisite "mu-plugin" to toggle adminstration menus for the entire network of sites. Just drop in mu-plugins.

== Description ==
Enable or disable WP3 Multisite Backend Menus. Adds options to toggle administration menus at Network Admin->Settings page under "Menu Settings". WP3 already toggles the Plugins menu, I've added a bunch more in the same/similar way.

I use the plugin to simplify the menus available to the entire network of sites. I use this plugin in a school(k-12) WP3 Multisite installation to disable the Deltete Blog, Permalinks, Import, Media Upload, Add Users and Themes menus.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `ds_wp3_menus.php` to the `/wp-content/mu-plugins/` directory
2. Set multisite "Menu Settings" option at Network Admin->Settings page

== Frequently Asked Questions ==

* Will this plugin also hide menus added by plugins? Maybe, if the parent page is hidden. Menus added by plugins will not be listed at the SiteAdmin->Options list.
* Will this plugin hide the corresponding items from the admin head favorites? Yes.
* Will this plugin disable media uploads? Yes.
* Will this plugin hide the media upload buttons? No. That is already a Super Admin->Option.
* Can I have different menus for different roles of users on different blogs? No, this plugin toggles menus for all users and all blogs regardless of Cap/Role (only SuperAdmin can overide the limits of the plugin however).

== Screenshots ==

1. Menu Settings: Enable Adminstration Menus
2. Favorites Admin Head Dropdown Shortcuts
3. Uploads Disabled by Super Admin

== Changelog ==
= 3.2.1.1 =
* Tested up to: WP3.2.1

= 3.0.3.1 =
* fixed typo where if Add New Post menu was hidden, Add New Page would redirect, too.

= 3.0.3 = 
* Added WPLANG multi language support tags

= 3.0.2 = 
* Fixed a typo keeping Plugin Editor menu enabled for SuperAdmins.

= 3.0.1 = 
* Fixed a typo keeping Plugins menu disabled.

= 3.0.0 = 
* initial release

== Upgrade Notice ==
= 3.0.3.1 =
* fixed typo where if Add New Post menu was hidden, Add New Page would redirect, too.

= 3.0.3 =
* Added WPLANG multi language support tags

= 3.0.1 =
* Fixed a typo keeping Plugins menu disabled.

= 3.0.0 = 
* initial release