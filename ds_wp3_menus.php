<?php
/*
Plugin Name: Menus
Plugin URI: http://dsader.snowotherway.org/wordpress-plugins/menus/
Description: WP3.0 Multisite "mu-plugin" to toggle more of the administration menus in the same way "Plugins" is already toggled. Go to Super Admin-->Options to "Enable administration menus". All menus are unchecked and disabled by default, except for Super Admin.
Author: D. Sader
Version: 3.0.2
Author URI: http://dsader.snowotherway.org

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 
Notes: if you turn all menus off, where should the last redirect go? I disable the dashboard in my install, and keep the profile. Hence no wp_redirect for profile with all other "dead ends" redirecting to profile. Profile is always url accessible, even though all(most) menus can be disabled. Disabling parent menus for pages added by plugins/templates may work, or not. I usually resort to editing plugin files to disable menus if needed. Happy testing.

*/ 

//------------------------------------------------------------------------//
//---Hooks----------------------------------------------------------------//
//------------------------------------------------------------------------//
add_filter( 'mu_menu_items', 'ds_mu_menu_options' ); // hook SuperAdmin->Options
add_action( 'wpmu_options','ds_menu_option', -99 ); // add a note below the SuperAdmin->Options
add_action( '_admin_menu', 'ds_menu_disable' ); // toggles the menus
add_filter('favorite_actions', 'ds_reduce_favorite_actions'); //hook admin head favorites
add_action('admin_menu', 'ds_remove_themes_utility_last'); // remove and redirect requests for theme editor
//------------------------------------------------------------------------//
//---Functions to Enable/Disable admin menus------------------------------//
//------------------------------------------------------------------------//
function ds_reduce_favorite_actions ($actions) {
	$menu_perms = get_site_option( "menu_items" );

	if(( $menu_perms[ 'super_admin' ] != '1' ) && (is_super_admin())) 
	return $actions;

		$remove_menu_items = array(''); // start with an empty array
		
			if( $menu_perms[ 'posts_new' ] != '1' && current_user_can('edit_posts') ) {
		$remove_menu_items = array('post-new.php','edit.php?post_status=draft');
			}
			if( $menu_perms[ 'pages_new' ] != '1' && current_user_can('edit_pages')) {
		$remove_menu_items = array_merge(array('post-new.php?post_type=page'),$remove_menu_items); // merge the existing or empty arrays and continue
			}
			if( $menu_perms[ 'media_new' ] != '1' && current_user_can('upload_files')) {
		$remove_menu_items = array_merge(array('media-new.php'),$remove_menu_items); 
			}

			if( $menu_perms[ 'comments' ] != '1' && current_user_can('edit_posts')) {
		$remove_menu_items = array_merge(array('edit-comments.php'),$remove_menu_items); 
			}

		foreach($remove_menu_items as $menu_item)
		{
			if(array_key_exists($menu_item, $actions))
			{
				unset($actions[$menu_item]);
			}
		}
	
	return $actions;
}


function ds_menu_disable() {
	global $submenu, $menu;
		$menu_perms = get_site_option( "menu_items" );
		if( is_array( $menu_perms ) == false )
		$menu_perms = array();
		


			if(( $menu_perms[ 'super_admin' ] != '1' ) && (is_super_admin())) 
			return;

	// 'Dashboard'
	if( $menu_perms[ 'menu-dashboard' ] != '1' && current_user_can('read')) {
		if(!empty($menu)) {
			foreach($menu as $key => $sm) {
			if(__($sm[0]) == "Dashboard" || $sm[2] == "index.php") {
				unset($menu[$key]);
				unset( $submenu[ 'index.php' ] );
				break; 
				}
			}
		}
	}

	// 'Dashboard Dashboard'
	if( $menu_perms[ 'dash_dash' ] != '1' && current_user_can('read')) {
		if(!empty($submenu['index.php'])) {
		foreach($submenu['index.php'] as $key => $sm) {
			if(__($sm[0]) == "Dashboard" || $sm[2] == "index.php") {
				unset($submenu['index.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'index.php'))	
			wp_redirect('profile.php');
	}

	// 'Dashboard My Sites'
	if( $menu_perms[ 'dash_mysites' ] != '1' && current_user_can('read')) {
		if(!empty($submenu['index.php'])) {
		foreach($submenu['index.php'] as $key => $sm) {
			if(__($sm[0]) == "My Sites" || $sm[2] == "my-sites.php") {
				unset($submenu['index.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'my-sites.php'))	
			wp_redirect('profile.php');
	}

	// 'Posts'
	if( $menu_perms[ 'menu-posts' ] != '1' && (current_user_can('edit_posts'))) {
		if(!empty($menu)) {
			foreach($menu as $key => $sm) {
			if(__($sm[0]) == "Posts" || $sm[2] == "edit.php") {
				unset($menu[$key]);
				unset($submenu['edit.php']);
				break; 
				}
			}
		} // disable child menus to add a redirect
	}
	
	// 'Posts Posts'
	if( $menu_perms[ 'posts_posts' ] != '1' && current_user_can('edit_posts')) {
		if(!empty($submenu['edit.php'])) {
		foreach($submenu['edit.php'] as $key => $sm) {
			if(__($sm[0]) == __('Posts') || $sm[2] == "edit.php") {
				unset($submenu['edit.php'][$key]);
				break;
				}
			}
		}

		if( strpos($_SERVER['REQUEST_URI'], 'edit.php'))	// plugin settings will redirect, too.	
			wp_redirect('profile.php');
	}
		
	// 'Posts Add New'
	if( $menu_perms[ 'posts_new' ] != '1' && current_user_can('edit_posts') ) {
		if(!empty($submenu['edit.php'])) {
		foreach($submenu['edit.php'] as $key => $sm) {
			if(__($sm[0]) == "Add New" || $sm[2] == "post-new.php") {
				unset($submenu['edit.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'post-new.php'))		
			wp_redirect('profile.php');
	}	

	// 'Posts Tags'
	if( $menu_perms[ 'posts_tags' ] != '1' && current_user_can('manage_categories')) {
		if(!empty($submenu['edit.php'])) {
		foreach($submenu['edit.php'] as $key => $sm) {
			if(__($sm[0]) == "Post Tags" || $sm[2] == "edit-tags.php") {
				unset($submenu['edit.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'edit-tags.php'))		
			wp_redirect('profile.php');
	}
	
	// 'Posts Categories'
	if( $menu_perms[ 'posts_cats' ] != '1' && current_user_can('manage_categories') ) {
		if(!empty($submenu['edit.php'])) {
		foreach($submenu['edit.php'] as $key => $sm) {
			if(__($sm[0]) == "Categories" || $sm[2] == "categories.php") {
				unset($submenu['edit.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], '/categories.php'))  // '/' needed to keep edit-link-categories.php from redirecting		
			wp_redirect('profile.php');
	}
	
	// 'Media'
	if( $menu_perms[ 'menu-media' ] != '1' && current_user_can('upload_files')) {
		if(!empty($menu)) {
		foreach($menu as $key => $sm) {
			if(__($sm[0]) == "Media" || $sm[2] == "upload.php") {
				unset ($menu[$key]); 
				unset( $submenu[ 'upload.php' ] );
				break;
				}
			}
		}
	}
		
	// 'Media Library'
	if( $menu_perms[ 'media_lib' ] != '1' && current_user_can('upload_files')) {
		if(!empty($submenu['upload.php'])) {
		foreach($submenu['upload.php'] as $key => $sm) {
			if(__($sm[0]) == "Library" || $sm[2] == "upload.php") {
				unset($submenu['upload.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'upload.php'))	
		wp_die('Sorry, Super Admin has disabled Media Library.'); // kinda dumb if the media_buttons are not hidden in the post edit form.
	}
	
	// 'Media Add New'
	if( $menu_perms[ 'media_new' ] != '1' && current_user_can('upload_files')) {
		if(!empty($submenu['upload.php'])) {
		foreach($submenu['upload.php'] as $key => $sm) {
			if(__($sm[0]) == "Add New" || $sm[2] == "media-new.php") {
				unset($submenu['upload.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'media-new.php') || strpos($_SERVER['REQUEST_URI'], 'media-upload.php'))			wp_die('Sorry, Super Admin has disabled Media Uploads.'); // kinda dumb if the media_buttons are not hidden in the post edit form.
	}
	
	// 'Links'
	if( $menu_perms[ 'menu-links' ] != '1' && (current_user_can('manage_links'))) {
		if(!empty($menu)) {
			foreach($menu as $key => $sm) {
			if(__($sm[0]) == "Links" || $sm[2] == "link-manager.php") {
				unset($menu[$key]);
				unset( $submenu[ 'link-manager.php' ] );
				break; 
				}
			}
		} // disable child menus to get a redirect
	}
	
	// 'Links Links'
	if( $menu_perms[ 'links_links' ] != '1' && current_user_can('manage_links')) {
		if(!empty($submenu['link-manager.php'])) {
		foreach($submenu['link-manager.php'] as $key => $sm) {
			if(__($sm[0]) == "Links" || $sm[2] == "link-manager.php") {
				unset($submenu['link-manager.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'link-manager.php'))		
			wp_redirect('profile.php');
	}
	
	// 'Links Add New'
	if( $menu_perms[ 'links_new' ] != '1' && current_user_can('manage_links') ) {
		if(!empty($submenu['link-manager.php'])) {
		foreach($submenu['link-manager.php'] as $key => $sm) {
			if(__($sm[0]) == "Link" || $sm[2] == "link-add.php") {
				unset($submenu['link-manager.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'link-add.php'))		
			wp_redirect('profile.php');
	}
	
	// 'Links Link Categories'
	if( $menu_perms[ 'links_cats' ] != '1' && current_user_can('manage_categories')) {
		if(!empty($submenu['link-manager.php'])) {
		foreach($submenu['link-manager.php'] as $key => $sm) {
			if(__($sm[0]) == "Link Categories" || $sm[2] == "edit-link-categories.php") { 
				unset($submenu['link-manager.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'edit-link-categories.php'))		
			wp_redirect('profile.php');
	}
	
	// 'Pages'
	if( $menu_perms[ 'menu-pages' ] != '1' && (current_user_can('edit_pages'))) {
		if(!empty($menu)) {
			foreach($menu as $key => $sm) {
			if(__($sm[0]) == "Pages" || $sm[2] == "edit.php?post_type=page" ) {
				unset($menu[$key]);
				unset( $submenu[ 'edit.php?post_type=page' ] );
				break; 
				}
			}
		} // disable child menus to get a redirect
	}
	
	// 'Pages Pages'
	if( $menu_perms[ 'pages_pages' ] != '1' && current_user_can('edit_pages')) {
		if(!empty($submenu['edit.php?post_type=page'])) {
		foreach($submenu['edit.php?post_type=page'] as $key => $sm) {
			if(__($sm[0]) == "Pages" || $sm[2] == "edit.php?post_type=page") {
				unset($submenu['edit.php?post_type=page'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'edit.php?post_type=page'))		
			wp_redirect('profile.php');
	}
	
	// 'Pages Add New'
	if( $menu_perms[ 'pages_new' ] != '1' && current_user_can('edit_pages')) {
		if(!empty($submenu['edit.php?post_type=page'])) {
		foreach($submenu['edit.php?post_type=page'] as $key => $sm) {
			if(__($sm[0]) == "Add New" || $sm[2] == "edit.php?post_type=page") {
				unset($submenu['edit.php?post_type=page'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'edit.php?post_type=page'))		
			wp_redirect('profile.php');
	}
	
	// 'Comments'
	if( $menu_perms[ 'menu-comments' ] != '1' && current_user_can('edit_posts')) {
		if(!empty($menu)) {
		foreach($menu as $key => $sm) {
			if(__($sm[0]) == "Comments" || $sm[2] == "edit-comments.php") {
				unset ($menu[$key]); // kinda dumb if comments are open and awaiting moderation
				unset( $submenu[ 'edit-comments.php' ] );
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'edit-comments.php'))		
			wp_redirect('profile.php');
	}	
	
	// If 'Appearance' is hidden, Widgets and Themes are still url accessible
	if( $menu_perms[ 'menu-appearance' ] != '1' && current_user_can('switch_themes')) {
		if(!empty($menu)) {
		foreach($menu as $key => $sm) {
			if(__($sm[0]) == "Appearance") {
				unset ($menu[$key]); 
				unset($submenu['themes.php']);
				break;
				}
			}
		}
	}	 // best not to redirect here, either
	
	// 'Appearance Themes'
	if( $menu_perms[ 'app_themes' ] != '1' && current_user_can('switch_themes')) { 
		if(!empty($submenu['themes.php'])) {
		foreach($submenu['themes.php'] as $key => $sm) {
			if(__($sm[0]) == "Themes" || $sm[2] == "themes.php") {
				unset($submenu['themes.php'][$key]);
				break;
				}
			}
		}
		/*********
		//  redirecting themes uri may break more than it is worth ... ie theme options pages
		if( strpos($_SERVER['REQUEST_URI'], 'themes.php'))	
			wp_redirect('widgets.php'); 
			***********/
	}

	// 'Appearance Menus'
	if( $menu_perms[ 'app_men' ] != '1' && current_user_can('edit_theme_options')) { 
		if(!empty($submenu['themes.php'])) {
		foreach($submenu['themes.php'] as $key => $sm) {
			if(__($sm[0]) == "Menus" || $sm[2] == "nav-menus.php") {
				unset($submenu['themes.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'nav-menus.php'))	
			wp_redirect('profile.php');
	}

	// 'Plugins Plugins'
	if( $menu_perms[ 'plug_plug' ] != '1' && current_user_can('activate_plugins')) { 
		if(!empty($submenu['plugins.php'])) {
		foreach($submenu['plugins.php'] as $key => $sm) {
			if(__($sm[0]) == "Plugins" || $sm[2] == "plugins.php") {
				unset($submenu['plugins.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'plugins.php'))	
			wp_redirect('profile.php'); 
	}
	
	// 'Plugins Add New'
	if( $menu_perms[ 'plug_ad' ] != '1' && current_user_can('install_plugins')) { 
		if(!empty($submenu['plugins.php'])) {
		foreach($submenu['plugins.php'] as $key => $sm) {
			if(__($sm[0]) == "Add New" || $sm[2] == "plugin-install.php") {
				unset($submenu['plugins.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'plugin-install.php'))	
			wp_redirect('profile.php'); 
	}

	// 'Plugins Editor'
	if( $menu_perms[ 'plug_ed' ] != '1' && current_user_can('edit_plugins')) { 
		if(!empty($submenu['plugins.php'])) {
		foreach($submenu['plugins.php'] as $key => $sm) {
			if(__($sm[0]) == "Editor" || $sm[2] == "plugin-editor.php") {
				unset($submenu['plugins.php'][$key]);
				break;
				}
			}
		}

		if( strpos($_SERVER['REQUEST_URI'], 'plugin-editor.php'))	
			wp_redirect('profile.php'); 
	}
	
	// if no 'Users' promote 'Profile'
	if( $menu_perms[ 'menu-users' ] != '1' && current_user_can('list_users') ) {
		if(!empty($menu)) {
		foreach($menu as $key => $sm) {
			if(__($sm[0]) == "Users") {
				if( $menu_perms[ 'user_profile' ] == '1' && current_user_can('read')) {
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
	
	// 'Users Authors and Users'
	if( $menu_perms[ 'users_user' ] != '1' && current_user_can('list_users')) {
		if(!empty($submenu['users.php'])) {
		foreach($submenu['users.php'] as $key => $sm) {
			if(__($sm[0]) == "Authors &amp; Users" || $sm[2] == "users.php") {
				unset($submenu['users.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], '/users.php'))  // '/' needed to keep wpmu-users.php from redirecting
			wp_redirect('profile.php');
	}
	
	// 'Users Add New'
	if( $menu_perms[ 'users_new' ] != '1' && current_user_can('create_users')) {
		if(!empty($submenu['users.php'])) {
		foreach($submenu['users.php'] as $key => $sm) {
			if(__($sm[0]) == "Add New" || $sm[2] == "users-new.php") {
				unset($submenu['users.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'users-new.php')) 
			wp_redirect('profile.php');
	}
	
	// 'Users Your Profile'
	if( $menu_perms[ 'user_profile' ] != '1' && current_user_can('read')) {
		if(!empty($submenu['users.php'])) {
		foreach($submenu['users.php'] as $key => $sm) {
			if(__($sm[0]) == "Your Profile" || $sm[2] == "profile.php") {
				unset($submenu['users.php'][$key]);
				break;
				}
			} 
		} elseif(( $menu_perms[ 'user_profile' ] != '1' && current_user_can('read')) && !empty($menu)) {
			foreach($menu as $key => $sm) {
				if(!empty($sm[0])) {
			if(__($sm[0]) == "Profile" || $sm[2] == "profile.php") {
				unset($menu[$key]);
				break; 
					} // enabling a redirect here may be more trouble than it is worth. Shouldn't every user at least see a profile page?
				}
			}
		}
	}
	
	// 'Tools'
	if( $menu_perms[ 'menu-tools' ] != '1' && current_user_can('edit_posts')) {
		if(!empty($menu)) {
		foreach($menu as $key => $sm) {
			if(__($sm[0]) == "Tools" || $sm[2] == "tools.php") {
				unset ($menu[$key]); 
				unset( $submenu[ 'tools.php' ] );
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'tools.php'))		
			wp_redirect('profile.php');
	}
		
	// 'Tools Tools'
	if( $menu_perms[ 'tools_tools' ] != '1' && current_user_can('edit_posts')) {
		if(!empty($submenu['tools.php'])) {
		foreach($submenu['tools.php'] as $key => $sm) {
			if(__($sm[0]) == "Tools" || $sm[2] == "tools.php") {
				unset($submenu['tools.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'tools.php'))	
			wp_redirect('profile.php');
	}
	
	// 'Tools Import'
	if( $menu_perms[ 'tools_im' ] != '1' && current_user_can('import')) {
		if(!empty($submenu['tools.php'])) {
		foreach($submenu['tools.php'] as $key => $sm) {
			if(__($sm[0]) == "Import" || $sm[2] == "import.php") {
				unset($submenu['tools.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'import.php'))	
			wp_redirect('profile.php');
	}
	
	// 'Tools Export'
	if( $menu_perms[ 'tools_ex' ] != '1' && current_user_can('import')) {
		if(!empty($submenu['tools.php'])) {
		foreach($submenu['tools.php'] as $key => $sm) {
			if(__($sm[0]) == "Export" || $sm[2] == "export.php") {
				unset($submenu['tools.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'export.php'))	
			wp_redirect('profile.php');
	}
	
	// 'Tools Delete Site'
	if( $menu_perms[ 'tools_del' ] != '1' && current_user_can('manage_options')) {
		if(!empty($submenu['tools.php'])) {
		foreach($submenu['tools.php'] as $key => $sm) {
			if(__($sm[0]) == "Delete Site" || $sm[2] == "ms-delete-site.php") {
				unset($submenu['tools.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'export.php'))	
			wp_redirect('profile.php');
	}

	// 'Settings'
		if( $menu_perms[ 'menu-settings' ] != '1' && current_user_can('manage_options')) {
			if(!empty($menu)) {
		foreach($menu as $key => $sm) {
			if(__($sm[0]) == "Settings" || $sm[2] == "options-general.php") {
				unset($menu[$key]);
				unset( $submenu[ 'options-general.php' ] );
				break; 
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'options-general.php'))		
			wp_redirect('profile.php');
	}
	
	// 'Settings General'
	if( $menu_perms[ 'settings_gen' ] != '1' && current_user_can('manage_options')) {
		if(!empty($submenu['options-general.php'])) {
		foreach($submenu['options-general.php'] as $key => $sm) {
			if(__($sm[0]) == "General" || $sm[2] == "options-general.php") {
				unset($submenu['options-general.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'options-general.php'))		
			wp_redirect('profile.php');
	}
	
	// 'Settings Writing'
	if( $menu_perms[ 'settings_writ' ] != '1' && current_user_can('manage_options')) {
		if(!empty($submenu['options-general.php'])) {
		foreach($submenu['options-general.php'] as $key => $sm) {
			if(__($sm[0]) == "Writing" || $sm[2] == "options-writing.php") {
				unset($submenu['options-general.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'options-writing.php'))		
			wp_redirect('profile.php');
	}
	
	// 'Settings Reading'
	if( $menu_perms[ 'settings_read' ] != '1' && current_user_can('manage_options')) {
		if(!empty($submenu['options-general.php'])) {
		foreach($submenu['options-general.php'] as $key => $sm) {
			if(__($sm[0]) == "Reading" || $sm[2] == "options-reading.php") {
				unset($submenu['options-general.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'options-reading.php'))		
			wp_redirect('profile.php');
	}
	
	// 'Settings Discussion'
	if( $menu_perms[ 'settings_disc' ] != '1' && current_user_can('manage_options')) {
		if(!empty($submenu['options-general.php'])) {
		foreach($submenu['options-general.php'] as $key => $sm) {
			if(__($sm[0]) == "Discussion" || $sm[2] == "options-discussion.php") {
				unset($submenu['options-general.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'options-discussion.php'))		
			wp_redirect('profile.php');
	}
	
	// 'Settings Media'
	if( $menu_perms[ 'settings_med' ] != '1' && current_user_can('manage_options')) {
		if(!empty($submenu['options-general.php'])) {
		foreach($submenu['options-general.php'] as $key => $sm) {
			if(__($sm[0]) == "Media" || $sm[2] == "options-media.php") {
				unset($submenu['options-general.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'options-media.php'))		
			wp_redirect('profile.php');
	}
	
	// 'Settings Privacy'
	if( $menu_perms[ 'settings_priv' ] != '1' && current_user_can('manage_options')) {
		if(!empty($submenu['options-general.php'])) {
		foreach($submenu['options-general.php'] as $key => $sm) {
			if(__($sm[0]) == "Privacy" || $sm[2] == "options-privacy.php") {
				unset($submenu['options-general.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'options-privacy.php'))		
			wp_redirect('profile.php');
	}
	
	// 'Settings Permalinks'
	if( $menu_perms[ 'settings_perm' ] != '1' && current_user_can('manage_options')) {
		if(!empty($submenu['options-general.php'])) {
		foreach($submenu['options-general.php'] as $key => $sm) {
			if(__($sm[0]) == "Permalinks" || $sm[2] == "options-permalink.php") {
				unset($submenu['options-general.php'][$key]);
				break;
				}
			}
		}
		if( strpos($_SERVER['REQUEST_URI'], 'options-permalink.php'))		
			wp_redirect('profile.php');
	}

}

// 'Appearance Editor'
function ds_remove_themes_utility_last() {
	$menu_perms = get_site_option( "menu_items" );
	if( is_array( $menu_perms ) == false )
		$menu_perms = array();
			if(( $menu_perms[ 'super_admin' ] != '1' ) && (is_super_admin()))
			return;
	if( $menu_perms[ 'app_ed' ] != '1' ) {
			remove_action('admin_menu', '_add_themes_utility_last',101);

		if( strpos($_SERVER['REQUEST_URI'], 'theme-editor.php'))	
			wp_redirect('profile.php'); 
	}
}

//------------------------------------------------------------------------//
//---Function Super Admin->Options------------------------------------------//
//---Options are saved as site_options on ms-options.php page-----------//
function ds_mu_menu_options() {
	$menu_items = array( 
		'plugins' 			=> __( 'Plugins' ),
		'super_admin'	=> __('Super Admin gets the following limited menus, too?'),
		'menu-dashboard'=> __('Dashboard'),
		'dash_dash'		=> __('Dashboard Dashboard'),
		'dash_mysites'	=> __('Dashboard My Sites'),			
		'menu-posts'				=> __('Posts'),
		'posts_posts'	=> __('Posts Posts'),
		'posts_new'		=> __('Posts Add New'),
		'posts_tags'	=> __('Posts Tags'),
		'posts_cats'	=> __('Posts Categories'),
		'menu-links'				=> __('Links'),
		'links_links'	=> __('Links Links'),
		'links_new'		=> __('Links Add New'),
		'links_cats'	=> __('Links Link Categories'),
		'menu-pages'			=> __('Pages'),
		'pages_pages'	=> __('Pages Pages'),
		'pages_new'		=> __('Pages Add New'),
		'menu-media'			=> __('Media'),
		'media_lib'		=> __('Media Library'),
		'media_new'		=> __('Media Add New'),
		'menu-comments'				=> __('Comments'),
		'menu-appearance'			=> __('Appearance'), 
		'app_themes'	=> __('Appearance Themes'),
		'app_men'		=> __('Appearance Menus'),
		'app_ed'		=> __('Appearance Editor'),
		'plug_plug'		=> __('Plugins Plugins'),
		'plug_ad'		=> __('Plugins Add New'),
		'plug_ed'		=> __('Plugins Editor'),
		'menu-users'				=> __('Users'), 
		'users_user'	=> __('Users Authors and Users'),
		'users_new'		=> __('Users Add New'),
		'user_profile'	=> __('Users Your Profile'),
		'menu-tools'				=> __('Tools'),
		'tools_tools'	=> __('Tools Tools'),
		'tools_im'		=> __('Tools Import'),
		'tools_ex'		=> __('Tools Export'),
		'tools_del'		=> __('Tools Delete Site'),
		'menu-settings'				=> __('Settings'),
		'settings_gen'	=> __('Settings General'),
		'settings_writ'	=> __('Settings Writing'),
		'settings_read'	=> __('Settings Reading'),
		'settings_disc'	=> __('Settings Discussion'),
		'settings_med'	=> __('Settings Media'),  
		'settings_priv'	=> __('Settings Privacy'),
		'settings_perm'	=> __('Settings Permalinks'),
		 );
		 return $menu_items;
}
function ds_menu_option() {
	echo '<small>Menu Settings note: Disabling "Your Profile" may not be a good idea, there needs to be a page every user can see. Even though a menu(or submenu) is disabled, access to the menu page(or submenu pages) via the url may still be possible. Disabling "Media Edit" will add a "Sorry, uploads are closed." to the Media Upload Buttons as well. Plugins adding submenu items to an Adminbar type plugin may not be hidden in all browsers. Happy testing!</small>';
}	
?>