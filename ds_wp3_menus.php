<?php
/*
Plugin Name: Menus
Plugin URI: http://wordpress.org/extend/plugins/menus/
Version: 3.7.1
Description: WP3.7.1 Multisite "mu-plugin" to toggle more of the administration menus in the same way "Plugins" is already toggled. Go to Network-->Settings->Menu Settings to "Enable administration menus". All menus are unchecked and disabled by default, except when logged in as Network Admin.
Author: dsader
Author URI: http://dsader.snowotherway.org
Network: true

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 
*/

class ds_menus {
function ds_menus() {
//------------------------------------------------------------------------//
//---Hooks----------------------------------------------------------------//
//------------------------------------------------------------------------//
add_filter( 'mu_menu_items', array(&$this, 'ds_mu_menu_options' )); // hook Network->Settings
add_action( 'wpmu_options', array(&$this, 'ds_menu_option'), -99 ); // add a note below the Network->Settings
add_action( 'admin_menu', array(&$this, 'ds_menu_disable'), 99 ); // toggles the menus - high priority catches widgets menu, too.
add_action( 'jetpack_admin_menu', array(&$this, 'ds_jetpack_admin_menu_disable'), 99 ); // toggles the menus added by jetpack, too.
add_action( 'admin_menu', array(&$this, 'ds_menu_plugins_disable'), 999 ); // toggles the menus added by plugins, too.
add_filter( 'admin_bar_menu', array(&$this, 'ds_reduce_favorite_actions'), 999 ); //hook admin head favorites
add_action( 'admin_page_access_denied',  array(&$this, 'ds_access_denied_splash'), 99 );

}
//------------------------------------------------------------------------//
//---Functions to Enable/Disable admin menus------------------------------//
//------------------------------------------------------------------------//
function ds_access_denied_splash() {
	//redirects WP "You do not have sufficient permissions to access this page." to dashboard or add custom message instead.
			$user_id = get_current_user_id();
			$redirect = get_dashboard_url( $user_id );
			wp_redirect( $redirect );
//			wp_die(__('The White Rabbit says you do not have sufficient permissions to access this page.'));
			exit;
	
}

function ds_menu_plugins_disable() {
	global $submenu, $menu;
		$menu_perms = get_site_option( "menu_items" );
		if( is_array( $menu_perms ) == false )
		$menu_perms = array();

			if( !isset($menu_perms[ 'super_admin' ] ) && is_super_admin()) 
			return;
//http://wordpress.stackexchange.com/questions/95837/remove-a-menu-item-created-by-a-plugin
//	remove_menu_page( 'polls' );
//	remove_menu_page( 'ratings' );
//	remove_menu_page( 'feedback' );
//	remove_submenu_page( 'feedback', 'polls' );
//	remove_submenu_page( 'feedback', 'ratings' );
//	remove_submenu_page( 'themes.php', 'editcss' );
//	remove_submenu_page( 'themes.php', 'custom-header' );
//	remove_submenu_page( 'themes.php', 'custom-background' );
//	remove_submenu_page( 'themes.php', 'theme_options' );

}

function ds_jetpack_admin_menu_disable() {
	global $submenu, $menu;
		$menu_perms = get_site_option( "menu_items" );
		if( is_array( $menu_perms ) == false )
		$menu_perms = array();

			$user_id = get_current_user_id();
			$redirect = get_dashboard_url( $user_id );
		
			if( !isset($menu_perms[ 'super_admin' ] ) && is_super_admin()) 
			return;
			

	if( !isset($menu_perms[ 'menu_jetpack' ]) && current_user_can('manage_options')) {
        remove_menu_page( 'jetpack' );
		if( strpos($_SERVER['REQUEST_URI'], 'jetpack'))		
			{ wp_redirect( $redirect ); exit(); }
	}

	if( !isset($menu_perms[ 'jetpack_jetpack' ]) && current_user_can('manage_options')) {
        remove_submenu_page( 'jetpack', 'jetpack' );
		if( strpos($_SERVER['REQUEST_URI'], 'jetpack'))		
			{ wp_redirect( $redirect ); exit(); }
	}

	if( !isset($menu_perms[ 'jetpack_akismet_key_config' ]) && current_user_can('manage_options')) {
        remove_submenu_page( 'jetpack', 'akismet-key-config' );
		if( strpos($_SERVER['REQUEST_URI'], 'akismet-key-config'))		
			{ wp_redirect( $redirect ); exit(); }
	}

	if( !isset($menu_perms[ 'jetpack_akismet_stats_display' ]) && current_user_can('manage_options')) {
        remove_submenu_page( 'jetpack', 'akismet-stats-display' );
		if( strpos($_SERVER['REQUEST_URI'], 'akismet-stats-display'))		
			{ wp_redirect( $redirect ); exit(); }
	}

}

function ds_reduce_favorite_actions ($wp_toolbar) {
	$menu_perms = get_site_option( "menu_items" );

	if( !isset( $menu_perms[ 'super_admin' ] ) && is_super_admin()) 
	return $wp_toolbar;
		
		if( !isset( $menu_perms[ 'posts_new' ] ) && current_user_can('edit_posts') ) {
			$wp_toolbar->remove_node( 'new-post' );
		}
		if( !isset( $menu_perms[ 'media_new' ] ) && current_user_can('upload_files')) {
			$wp_toolbar->remove_node( 'new-media' );
		}
		if( !isset( $menu_perms[ 'links_new' ] ) && current_user_can('manage_links')) {
			$wp_toolbar->remove_node( 'new-link' );
		}
		if( !isset( $menu_perms[ 'pages_new' ] ) && current_user_can('edit_pages')) {
			$wp_toolbar->remove_node( 'new-page' );
		}
		if( !isset( $menu_perms[ 'users_new' ] ) && current_user_can('create_users')) {
			$wp_toolbar->remove_node( 'new-user' );
		}
		if( !isset( $menu_perms[ 'menu_comments' ] ) && current_user_can('edit_posts')) {
			$wp_toolbar->remove_node( 'comments' );
		}
		if( !isset( $menu_perms[ 'menu_content' ] ) && current_user_can('edit_posts')) {
			$wp_toolbar->remove_node( 'new-content' );
		}
		if( !isset( $menu_perms[ 'dash_mysites' ] ) && current_user_can('read')) {
			$wp_toolbar->remove_node( 'my-sites' );
		}
		if( !isset($menu_perms[ 'user_profile' ]) && current_user_can('read')) {
			$wp_toolbar->remove_node( 'edit-profile' );
			$wp_toolbar->remove_node( 'user-info' );
			$wp_toolbar->remove_node( 'my-account' );
		}
		if( !isset( $menu_perms[ 'menu_dashboard' ] ) && current_user_can('read') ) {
			$wp_toolbar->remove_node( 'dashboard' );
			$wp_toolbar->remove_node( 'site-name' );
		}
		if( !isset( $menu_perms[ 'app_themes' ] ) && current_user_can('switch_themes') ) {
			$wp_toolbar->remove_node( 'themes' );
		}
		if( !isset( $menu_perms[ 'app_cus' ] ) && current_user_can('edit_theme_options') ) {
			$wp_toolbar->remove_node( 'customize' );
		}
		if( !isset( $menu_perms[ 'app_widgets' ] ) && current_user_can('edit_theme_options') ) {
			$wp_toolbar->remove_node( 'widgets' );
		}
		if( !isset( $menu_perms[ 'app_men' ] ) && current_user_can('edit_theme_options') ) {
			$wp_toolbar->remove_node( 'menus' );
		}
		if( !isset( $menu_perms[ 'app_head' ] ) && current_user_can('edit_theme_options') ) {
			$wp_toolbar->remove_node( 'header' );
		}
}

function ds_menu_disable() {
	global $submenu, $menu;
		$menu_perms = get_site_option( "menu_items" );
		if( is_array( $menu_perms ) == false )
		$menu_perms = array();

			if( !isset($menu_perms[ 'super_admin' ] ) && is_super_admin()) 
			return;

			$user_id = get_current_user_id();
			$redirect = get_dashboard_url( $user_id );


	// 'Dashboard'
	if( !isset($menu_perms[ 'menu_dashboard' ]) && current_user_can('read')) {
		if(!empty($menu)) {
			foreach($menu as $key => $sm) {
			if(__($sm[0]) == __('Dashboard') || $sm[2] == "index.php") {
				unset($menu[$key]);
				unset( $submenu[ 'index.php' ] );
				break; 
				}
			}
		}
	}

	// 'Dashboard My Sites'
	if( !isset($menu_perms[ 'dash_mysites' ]) && current_user_can('read')) {
		if(!empty($submenu['index.php'])) {
		foreach($submenu['index.php'] as $key => $sm) {
			if(__($sm[0]) == __('My Sites') || $sm[2] == "my-sites.php") {
				unset($submenu['index.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'my-sites.php'))	
			{ wp_redirect( $redirect ); exit(); }

	}

	// 'Posts'
	if( !isset($menu_perms[ 'menu_posts' ]) && current_user_can('edit_posts')) {
        remove_menu_page( 'edit.php' );
	}
	
	// 'Posts Posts'
	if( !isset($menu_perms[ 'posts_posts' ]) && current_user_can('edit_posts')) {
        remove_submenu_page( 'edit.php', 'edit.php' );
		//else 'Pages' will redirect too	
		if( strpos($_SERVER['REQUEST_URI'], 'edit.php') && !strpos($_SERVER['REQUEST_URI'], 'edit.php?post_type=page'))
			{ wp_redirect( $redirect ); exit(); }
	}
		
	// 'Posts Add New'
	if( !isset($menu_perms[ 'posts_new' ]) && current_user_can('edit_posts') ) {
        remove_submenu_page( 'edit.php', 'post-new.php' );
	//else 'Pages Add New' will redirect too	
		if( strpos($_SERVER['REQUEST_URI'], 'post-new.php') && !strpos($_SERVER['REQUEST_URI'], 'post-new.php?post_type=page'))		
			{ wp_redirect( $redirect ); exit(); }
	}	

	// 'Posts Tags'
	if( !isset($menu_perms[ 'posts_tags' ]) && current_user_can('manage_categories')) {
        remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=post_tag' );
		if( strpos($_SERVER['REQUEST_URI'], 'edit-tags.php?taxonomy=post_tag'))		
			{ wp_redirect( $redirect ); exit(); }
	}
	
	// 'Posts Categories'
	if( !isset($menu_perms[ 'posts_cats' ]) && current_user_can('manage_categories') ) {
        remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=category' );
		if( strpos($_SERVER['REQUEST_URI'], 'edit-tags.php?taxonomy=category'))  // '/' needed to keep edit-link-categories.php from redirecting		
			{ wp_redirect( $redirect ); exit(); }
	}
	
	// 'Media'
	if( !isset($menu_perms[ 'menu_media' ]) && current_user_can('upload_files')) {
        remove_menu_page( 'upload.php' );
	}
		
	// 'Media Library'
	if( !isset($menu_perms[ 'media_lib' ]) && current_user_can('upload_files')) {
        remove_submenu_page( 'upload.php', 'upload.php' );
		if( strpos($_SERVER['REQUEST_URI'], 'upload.php'))	
		wp_die('Sorry, Super Admin has disabled Media Library.'); // kinda dumb if the media_buttons are not hidden in the post edit form.
	}
	
	// 'Media Add New'
	if( !isset($menu_perms[ 'media_new' ]) && current_user_can('upload_files')) {
        remove_submenu_page( 'upload.php', 'media-new.php' );
		if( strpos($_SERVER['REQUEST_URI'], 'media-new.php') || strpos($_SERVER['REQUEST_URI'], 'media-upload.php'))
			wp_die('Sorry, Network Admin has disabled Media Uploads.'); // kinda dumb if the media_buttons are not hidden in the post edit form.
	}
	
	// 'Links'
	if( !isset($menu_perms[ 'menu_links' ]) && current_user_can('manage_links')) {
        remove_menu_page( 'link-manager.php' );
	}
	
	// 'Links Links'
	if( !isset($menu_perms[ 'links_links' ]) && current_user_can('manage_links')) {
        remove_submenu_page( 'link-manager.php', 'link-manager.php' );
		if( strpos($_SERVER['REQUEST_URI'], 'link-manager.php'))		
			{ wp_redirect( $redirect ); exit(); }
	}
	
	// 'Links Add New'
	if( !isset($menu_perms[ 'links_new' ]) && current_user_can('manage_links') ) {
        remove_submenu_page( 'link-manager.php', 'link-add.php' );
		if( strpos($_SERVER['REQUEST_URI'], 'link-add.php'))		
			{ wp_redirect( $redirect ); exit(); }
	}
	
	// 'Links Link Categories'
	if( !isset($menu_perms[ 'links_cats' ]) && current_user_can('manage_categories')) {
        remove_submenu_page( 'link-manager.php', 'edit-tags.php?taxonomy=link_category' );
		if( strpos($_SERVER['REQUEST_URI'], 'edit-tags.php?taxonomy=link_category'))		
			{ wp_redirect( $redirect ); exit(); }
	}
	
	// 'Pages'
	if( !isset($menu_perms[ 'menu_pages' ]) && current_user_can('edit_pages')) {
        remove_menu_page( 'edit.php?post_type=page' );

	}
	
	// 'Pages Pages'
	if( !isset($menu_perms[ 'pages_pages' ]) && current_user_can('edit_pages')) {
        remove_submenu_page( 'edit.php?post_type=page', 'edit.php?post_type=page' );
		if( strpos($_SERVER['REQUEST_URI'], 'edit.php?post_type=page'))		
			{ wp_redirect( $redirect ); exit(); }
	}
	
	// 'Pages Add New'
	if( !isset($menu_perms[ 'pages_new' ]) && current_user_can('edit_pages')) {
        remove_submenu_page( 'edit.php?post_type=page', 'post-new.php?post_type=page' );
		if( strpos($_SERVER['REQUEST_URI'], 'post-new.php?post_type=page'))		
			{ wp_redirect( $redirect ); exit(); }
	}

	// 'Pages Tags'
	if( !isset($menu_perms[ 'pages_tags' ]) && current_user_can('manage_categories')) {
        remove_submenu_page( 'edit.php?post_type=page', 'edit-tags.php?taxonomy=post_tag&amp;post_type=page' );
		if( strpos($_SERVER['REQUEST_URI'], 'edit-tags.php?taxonomy=post_tag&post_type=page'))		
			{ wp_redirect( $redirect ); exit(); }
	}
	
	// 'Comments'
	if( !isset($menu_perms[ 'menu_comments' ]) && current_user_can('edit_posts')) {
        remove_menu_page( 'edit-comments.php' );
		if( strpos($_SERVER['REQUEST_URI'], 'edit-comments.php'))		
			{ wp_redirect( $redirect ); exit(); }
	}	
	
	// If 'Appearance' is hidden, Widgets and Themes are still url accessible
	if( !isset($menu_perms[ 'menu_appearance' ]) && current_user_can('switch_themes')) {
        remove_menu_page( 'themes.php' ); 
	}	 // best not to redirect here, either
	
	// 'Appearance Themes'
	if( !isset($menu_perms[ 'app_themes' ]) && current_user_can('switch_themes')) { 
        remove_submenu_page( 'themes.php', 'themes.php' );//promote customize=>widgets=>nav-menus
		if( strpos($_SERVER['REQUEST_URI'], 'themes.php'))	
			{ wp_redirect( $redirect ); exit(); }
	}

	// 'Appearance Customize'
	if( !isset($menu_perms[ 'app_cus' ]) && current_user_can('edit_theme_options')) { 
        remove_submenu_page( 'themes.php', 'customize.php' );
		if( strpos($_SERVER['REQUEST_URI'], 'customize.php'))	
			{ wp_redirect( $redirect ); exit(); }
	}
	
	// 'Appearance Widgets'
	if( !isset($menu_perms[ 'app_widgets' ]) && current_user_can('edit_theme_options')) { 
        remove_submenu_page( 'themes.php', 'widgets.php' );
		if( strpos($_SERVER['REQUEST_URI'], 'widgets.php'))	
			{ wp_redirect( $redirect ); exit(); }
	}

	// 'Appearance Menus'
	if( !isset($menu_perms[ 'app_men' ]) && current_user_can('edit_theme_options')) { 
        remove_submenu_page( 'themes.php', 'nav-menus.php' );
		if( strpos($_SERVER['REQUEST_URI'], 'nav-menus.php'))	
			{ wp_redirect( $redirect ); exit(); }
	}

	// 'Appearance Header'
	if( !isset($menu_perms[ 'app_head' ]) && current_user_can('edit_theme_options')) { 
        remove_submenu_page( 'themes.php', 'custom-header' );
		if( strpos($_SERVER['REQUEST_URI'], 'custom-header'))	
			{ wp_redirect( $redirect ); exit(); }
	}

	// 'Plugins Plugins'
	if( !isset($menu_perms[ 'plug_plug' ]) && current_user_can('activate_plugins')) {
        remove_submenu_page( 'plugins.php', 'plugins.php' );
		if( strpos($_SERVER['REQUEST_URI'], 'plugins.php'))		
			{ wp_redirect( $redirect ); exit(); }
	}	
	
	// if no 'Users' promote 'Profile'
	if( !isset($menu_perms[ 'menu_users' ]) && current_user_can('list_users') ) {
		if(!empty($menu)) {
		foreach($menu as $key => $sm) {
			if(__($sm[0]) == "Users") {
				if( !isset($menu_perms[ 'user_profile' ]) && current_user_can('read')) {
				$menu[$key] = array( __('Profile'), 'read', 'profile.php', '', 'menu-top menu-icon-users', 'menu-users', 'div' ); // promote
				} else {
				unset($menu[$key]);
				unset( $submenu[ 'users.php' ] );
				}
				break;
				}
			}
		} //	the redirect here is not possible, must also disable Author & Users to enable the redirect
	}
	
	// 'All Users'
	if( !isset($menu_perms[ 'users_user' ]) && current_user_can('list_users')) {
        remove_submenu_page( 'users.php', 'users.php' );
		if( strpos($_SERVER['REQUEST_URI'], '/users.php'))  // '/' needed to keep wpmu-users.php from redirecting
			{ wp_redirect( $redirect ); exit(); }
	}
	
	// 'Users Add New'
	if( !isset($menu_perms[ 'users_new' ]) && current_user_can('create_users')) {
        remove_submenu_page( 'users.php', 'user-new.php' );
		if( strpos($_SERVER['REQUEST_URI'], 'user-new.php')) 
			{ wp_redirect( $redirect ); exit(); }
	}
	
	// 'Users Your Profile'
	if( !isset($menu_perms[ 'user_profile' ]) && current_user_can('read')) {
        remove_submenu_page( 'users.php', 'profile.php' );
        remove_menu_page( 'profile.php' ); //if promoted to menu
		if( strpos($_SERVER['REQUEST_URI'], 'profile.php'))		
			{ wp_redirect( $redirect ); exit(); }
	}
	
	// 'Tools'
	if( !isset($menu_perms[ 'menu_tools' ]) && current_user_can('edit_posts')) {
        remove_menu_page( 'tools.php' );
		if( strpos($_SERVER['REQUEST_URI'], 'tools.php'))		
			{ wp_redirect( $redirect ); exit(); }
	}
		
	// 'Tools Tools'
	if( !isset($menu_perms[ 'tools_tools' ]) && current_user_can('edit_posts')) {
        remove_submenu_page( 'tools.php', 'tools.php' );
		if( strpos($_SERVER['REQUEST_URI'], 'tools.php'))	
			{ wp_redirect( $redirect ); exit(); }
	}
	
	// 'Tools Import'
	if( !isset($menu_perms[ 'tools_im' ]) && current_user_can('import')) {
        remove_submenu_page( 'tools.php', 'import.php' );
		if( strpos($_SERVER['REQUEST_URI'], 'import.php'))	
			{ wp_redirect( $redirect ); exit(); }
	}
	
	// 'Tools Export'
	if( !isset($menu_perms[ 'tools_ex' ]) && current_user_can('import')) {
        remove_submenu_page( 'tools.php', 'export.php' );
		if( strpos($_SERVER['REQUEST_URI'], 'export.php'))	
			{ wp_redirect( $redirect ); exit(); }
	}
	
	// 'Tools Delete Site'
	if( !isset($menu_perms[ 'tools_del' ]) && current_user_can('manage_options')) {
        remove_submenu_page( 'tools.php', 'ms-delete-site.php' );
		if( strpos($_SERVER['REQUEST_URI'], 'ms-delete-site.php'))	
			{ wp_redirect( $redirect ); exit(); }
	}

	// 'Settings'
		if( !isset($menu_perms[ 'menu_settings' ]) && current_user_can('manage_options')) {
        remove_menu_page( 'options-general.php' );
		if( strpos($_SERVER['REQUEST_URI'], 'options-general.php'))		
			{ wp_redirect( $redirect ); exit(); }
	}
	
	// 'Settings General'
	if( !isset($menu_perms[ 'settings_gen' ]) && current_user_can('manage_options')) {
        remove_submenu_page( 'options-general.php', 'options-general.php' );
		if( strpos($_SERVER['REQUEST_URI'], 'options-general.php'))		
			{ wp_redirect( $redirect ); exit(); }
	}
	
	// 'Settings Writing'
	if( !isset($menu_perms[ 'settings_writ' ]) && current_user_can('manage_options')) {
        remove_submenu_page( 'options-general.php', 'options-writing.php' );
		if( strpos($_SERVER['REQUEST_URI'], 'options-writing.php'))		
			{ wp_redirect( $redirect ); exit(); }
	}
	
	// 'Settings Reading'
	if( !isset($menu_perms[ 'settings_read' ]) && current_user_can('manage_options')) {
        remove_submenu_page( 'options-general.php', 'options-reading.php' );
		if( strpos($_SERVER['REQUEST_URI'], 'options-reading.php'))		
			{ wp_redirect( $redirect ); exit(); }
	}
	
	// 'Settings Discussion'
	if( !isset($menu_perms[ 'settings_disc' ]) && current_user_can('manage_options')) {
        remove_submenu_page( 'options-general.php', 'options-discussion.php' );
		if( strpos($_SERVER['REQUEST_URI'], 'options-discussion.php'))		
			{ wp_redirect( $redirect ); exit(); }
	}
	
	// 'Settings Media'
	if( !isset($menu_perms[ 'settings_med' ]) && current_user_can('manage_options')) {
        remove_submenu_page( 'options-general.php', 'options-media.php' );
		if( strpos($_SERVER['REQUEST_URI'], 'options-media.php'))		
			{ wp_redirect( $redirect ); exit(); }
	}
	
	// 'Settings Permalinks'
	if( !isset($menu_perms[ 'settings_perm' ]) && current_user_can('manage_options')) {
        remove_submenu_page( 'options-general.php', 'options-permalink.php' );
			if( strpos($_SERVER['REQUEST_URI'], 'options-permalink.php'))		
				{ wp_redirect( $redirect ); exit(); }
		}

	}
	
	//------------------------------------------------------------------------//
	//---Function Super Admin->Options------------------------------------------//
	//---Options are saved as site_options on network/settings.php page-----------//
	function ds_mu_menu_options() {
		$menu_items = array( 
			'super_admin'	=> __('Super Admin gets the following limited menus, too?'),
			'menu_dashboard'=> __('Dashboard'),
			'dash_mysites'	=> __('Dashboard My Sites'),			
			'menu_jetpack'		=> __('Jetpack'),
			'jetpack_jetpack'	=> __('Jetpack Jetpack'),
			'jetpack_akismet_key_config'	=> __('Akismet'),
			'jetpack_akismet_stats_display'	=> __('Akismet Stats'),
			'menu_posts'				=> __('Posts'),
			'posts_posts'	=> __('Posts Posts'),
			'posts_new'		=> __('Posts Add New'),
			'posts_cats'	=> __('Posts Categories'),
			'posts_tags'	=> __('Posts Tags'),
			'menu_media'			=> __('Media'),
			'media_lib'		=> __('Media Library'),
			'media_new'		=> __('Media Add New'),
			'menu_links'				=> __('Links'),
			'links_links'	=> __('Links Links'),
			'links_new'		=> __('Links Add New'),
			'links_cats'	=> __('Links Link Categories'),
			'menu_pages'			=> __('Pages'),
			'pages_pages'	=> __('Pages Pages'),
			'pages_new'		=> __('Pages Add New'),
			'pages_tags'	=> __('Pages Tags'),
			'menu_comments'				=> __('Comments'),
			'menu_content'				=>__('+ New'),
			'menu_appearance'			=> __('Appearance'), 
			'app_themes'	=> __('Appearance Themes'),
			'app_cus'		=> __('Appearance Customize'),
			'app_widgets'	=> __('Appearance Widgets'),
			'app_men'		=> __('Appearance Menus'),
			'app_head'		=> __('Appearance Header'),
			'plugins' 			=> __( 'Plugins' ),
			'plug_plug'					=> __('Plugins Plugins'),
			'menu_users'				=> __('Users'), 
			'users_user'	=> __('Users All Users'),
			'users_new'		=> __('Users Add New'),
			'user_profile'	=> __('Users Your Profile'),
			'menu_tools'				=> __('Tools'),
			'tools_tools'	=> __('Tools Available Tools'),
			'tools_im'		=> __('Tools Import'),
			'tools_ex'		=> __('Tools Export'),
			'tools_del'		=> __('Tools Delete Site'),
			'menu_settings'				=> __('Settings'),
			'settings_gen'	=> __('Settings General'),
			'settings_writ'	=> __('Settings Writing'),
			'settings_read'	=> __('Settings Reading'),
			'settings_disc'	=> __('Settings Discussion'),
			'settings_med'	=> __('Settings Media'),  
			'settings_perm'	=> __('Settings Permalinks'),
			 );
			 return $menu_items;
	}
	function ds_menu_option() {
		echo '<small>Menu Settings note: Disabling "Dashboard" may not be a good idea, there needs to be a page every user can see. Even though a menu(or submenu, or adminbar node) is disabled, access to the page via the url may still be possible. Plugins adding submenu items or adminbar nodes may conflict. Try <a href="http://wordpress.org/extend/plugins/toggle-meta-boxes-sitewide/">Toggle Meta Boxes Sitewide</a> plugin for removal of other extras. Happy testing!</small>';
	}
}
if (class_exists("ds_menus")) {
	$ds_menus = new ds_menus();	
}
?>