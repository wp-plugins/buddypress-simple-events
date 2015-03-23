<?php
/*
Plugin Name: BuddyPress Simple Events
Description: An Events plugin for BuddyPress
Version: 1.0
Author: shanebp
Author URI: http://philopress.com/
*/

if ( !defined( 'ABSPATH' ) ) exit;

function pp_events_init() {

	$vcheck = pp_events_version_check();

	if( $vcheck ) {

		define( 'PP_EVENTS_DIR', dirname( __FILE__ ) );

		load_plugin_textdomain( 'bp-simple-events', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );


		if( ! is_admin() ) {
			require( PP_EVENTS_DIR . '/inc/pp-events-functions.php' );
			require( PP_EVENTS_DIR . '/inc/pp-events-templates.php' );
			require( PP_EVENTS_DIR . '/inc/pp-events-profile.php' );
			require( PP_EVENTS_DIR . '/inc/pp-events-widget.php' );
		}
		else {
		    require( PP_EVENTS_DIR . '/inc/admin/pp-events-admin.php' );
			require( PP_EVENTS_DIR . '/inc/admin/pp-events-admin-settings.php' );
			require( PP_EVENTS_DIR . '/inc/pp-events-widget.php' );
		 }

	}
}
add_action( 'bp_include', 'pp_events_init' );


function pp_events_activation() {

	$vcheck = pp_events_version_check();

	if( $vcheck ) {

		pp_add_event_caps();

		pp_create_post_type_event();

		pp_create_events_page();

		pp_create_events_options();

		flush_rewrite_rules();
	}
}
register_activation_hook(__FILE__, 'pp_events_activation');


function pp_events_deactivation () {
	pp_remove_event_caps();

}
register_deactivation_hook(__FILE__, 'pp_events_deactivation');


function pp_events_uninstall () {
	delete_option( 'pp_events_tab_position' );
	delete_option( 'pp_events_required' );
}
register_uninstall_hook( __FILE__, 'pp_events_uninstall');


function pp_events_version_check() {

	if ( ! defined( 'BP_VERSION' ) )
		return false;

	if( version_compare( BP_VERSION, '2.2', '>=' ) )
		return true;
	else {
		echo '<div id="message" class="error">BuddyPress Simple Events requires at least version 2.2 of BuddyPress.</div>';
		return false;
	}
}


function pp_create_events_options() {

	// tab position on profile pages
	add_option( 'pp_events_tab_position', '201', '', 'no' );

	//default required fields
	add_option( 'pp_events_required', array(), '', 'no' );
}


function pp_create_events_page() {

    $page = get_page_by_path('events');

    if( ! $page ){
		$events_page = array(
		  'post_title'    => 'Events',
		  'post_name'     => 'events',
		  'post_status'   => 'publish',
		  'post_author'   => get_current_user_id(),
		  'post_type'     => 'page'
		);

		$post_id = wp_insert_post( $events_page, true );
    }

}


function pp_activate_events_notice() {

	$notice = get_option( 'events-img-support-notice' );

	if( $notice ) {

		echo '<div class="update-nag"><p>' . $notice . '</p></div>';

		delete_option( 'events-img-support-notice' );
	}
}
add_action('admin_notices', 'pp_activate_events_notice');


function pp_create_post_type_event() {

	register_post_type( 'event',
		array(
		  'labels' => array(
			'name' => __( 'Events' ),
			'singular_name' => __( 'Event' ),
			'add_new' => __( 'Add New' ),
			'add_new_item' => __( 'Add New Event' ),
			'edit' => __( 'Edit' ),
			'edit_item' => __( 'Edit Event' ),
			'new_item' => __( 'New Event' ),
			'view' => __( 'View Events' ),
			'view_item' => __( 'View Event' ),
			'search_items' => __( 'Search Events' ),
			'not_found' => __( 'No Events found' ),
			'not_found_in_trash' => __( 'No Events found in Trash' )
		),
		'public' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'event' ),
		'capability_type' => array('event', 'events'),
		'exclude_from_search' => false,
		'has_archive' => true,
		'map_meta_cap' => true,
		'hierarchical' => false,
		"supports"	=> array("title", "editor", "thumbnail", "author", "comments", "trackbacks", "buddypress-activity"),
		'taxonomies' => array('category'),
		)
	);
	register_taxonomy_for_object_type('category', 'event');

}
add_action( 'init', 'pp_create_post_type_event' );


function pp_add_event_caps() {

	$role = get_role( 'administrator' );
	$role->add_cap( 'delete_published_events' );
	$role->add_cap( 'delete_others_events' );
	$role->add_cap( 'delete_events' );
	$role->add_cap( 'edit_others_events' );
	$role->add_cap( 'edit_published_events' );
	$role->add_cap( 'edit_events' );
	$role->add_cap( 'publish_events' );

}

function pp_remove_event_caps() {
	global $wp_roles;

	$all_roles = $wp_roles->roles;

	foreach( $all_roles as $key => $value ){

		$role = get_role( $key );

		$role->remove_cap( 'delete_published_events' );
		$role->remove_cap( 'delete_others_events' );
		$role->remove_cap( 'delete_events' );
		$role->remove_cap( 'edit_others_events' );
		$role->remove_cap( 'edit_published_events' );
		$role->remove_cap( 'edit_events' );
		$role->remove_cap( 'publish_events' );

	}
}

