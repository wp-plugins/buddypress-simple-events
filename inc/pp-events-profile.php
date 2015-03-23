<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


function pp_events_profile_admin_bar() {
	global $wp_admin_bar, $bp;

	if ( !bp_use_wp_admin_bar() || defined( 'DOING_AJAX' ) )
		return;

	if ( ! current_user_can('publish_events') )
		return;

	if ( is_user_logged_in() ) {
		$user_domain = bp_loggedin_user_domain();
		$item_link = trailingslashit( $user_domain . 'events' );

		$wp_admin_bar->add_menu( array(
			'parent' => $bp->my_account_menu_id,
			'id'     => 'my-account-events',
			'title'  => __( 'Events',  'bp-simple-events' ),
			'href'   => trailingslashit( $item_link ),
			'meta'   => array( 'class' => 'menupop' )
		) );

		// submenu
		$wp_admin_bar->add_menu( array(
			'parent' => 'my-account-events',
			'id'     => 'my-account-events-upcoming',
			'title'  => __( 'Upcoming', 'bp-simple-events' ),
			'href'   => trailingslashit( $item_link ) . 'upcoming'
		) );

		// submenu
		$wp_admin_bar->add_menu( array(
			'parent' => 'my-account-events',
			'id'     => 'my-account-events-archive',
			'title'  => __( 'Archive', 'bp-simple-events' ),
			'href'   => trailingslashit( $item_link ) . 'archive'
		) );

		// submenu
		$wp_admin_bar->add_menu( array(
			'parent' => 'my-account-events',
			'id'     => 'my-account-events-create',
			'title'  => __( 'Create', 'bp-simple-events' ),
			'href'   => trailingslashit( $item_link ) . 'create'
		) );

	}

}
add_action( 'bp_setup_admin_bar', 'pp_events_profile_admin_bar', 300 );


function pp_events_profile_tabs() {
	global $bp;

	if( ! user_can( bp_displayed_user_id(), 'publish_events' ) )
		return;


	$user_has_access = false;
	if( bp_is_my_profile() || is_super_admin() )
		$user_has_access = true;

	$tab_position = get_option( 'pp_events_tab_position' );
	$count        = pp_events_count_profile();
	$class        = ( 0 === $count ) ? 'no-count' : 'count';

	bp_core_new_nav_item( array(
		'name'                => sprintf( __( 'Events <span class="%s">%s</span>', 'bp-simple-events' ), esc_attr( $class ), number_format_i18n( $count ) ),
		'slug'                => 'events',
		'position'            => $tab_position,
		'screen_function'     => 'pp_events_profile',
		'default_subnav_slug' => 'upcoming',
		'item_css_id'         => 'member-events'
	) );

	bp_core_new_subnav_item( array(
		'name'              => 'Upcoming',
		'slug'              => 'upcoming',
		'parent_url'        => trailingslashit( bp_displayed_user_domain() . 'events' ),
		'parent_slug'       => 'events',
		'screen_function'   => 'pp_events_profile',
		'position'          => 20,
		'item_css_id'       => 'member-events-upcoming'
		//'user_has_access'   => $user_has_access
		)
	);

	// for Upcoming tab pagination
	if( $bp->current_action != 'archive' && $bp->current_action != 'create' ) {

		if ( (int) pp_events_pop_cur_page() > 0) {

		    bp_core_new_subnav_item( array(
			    'name'              => 'Page ' . pp_events_pop_cur_page(),
			    'slug'              => 'page',
			    'parent_slug'       => 'events',
			    'parent_url'        => trailingslashit( bp_displayed_user_domain() . 'events' ),
				'screen_function'   => 'pp_events_profile',
				'position'          => 40,
				'item_css_id'       => 'member-events-hide'
		    ) );

		    bp_core_new_subnav_item( array(
			    'name'              => 'Page ' . pp_events_pop_cur_page(),
			    'slug'              => pp_events_pop_cur_page(),
			    'parent_slug'       => 'events/page',
			    'parent_url'        => trailingslashit( bp_displayed_user_domain() . 'events/page' ),
				'screen_function'   => 'pp_events_profile',
				'position'          => 40,
				'item_css_id'       => 'member-events-hide'
		    ) );

	    }

	}


	bp_core_new_subnav_item( array(
		'name'              => 'Archive',
		'slug'              => 'archive',
		'parent_url'        => trailingslashit( bp_displayed_user_domain() . 'events' ),
		'parent_slug'       => 'events',
		'screen_function'   => 'pp_events_profile_archive',
		'position'          => 25,
		'item_css_id'       => 'member-events-archive'
		//'user_has_access'   => $user_has_access
		)
	);

	// for Archive tab pagination
	if( $bp->current_action == 'archive' ) {

		if ( (int) pp_events_pop_cur_page() > 0) {

		    bp_core_new_subnav_item( array(
			    'name'              => 'Page ' . pp_events_pop_cur_page(),
			    'slug'              => 'page',
			    'parent_slug'       => 'archive',
			    'parent_url'        => trailingslashit( bp_displayed_user_domain() . 'events/archive' ),
				'screen_function'   => 'pp_events_profile_archive',
				'position'          => 25,
				//'user_has_access'   => $user_has_access
		    ) );

		    bp_core_new_subnav_item( array(
			    'name'              => 'Page ' . pp_events_pop_cur_page(),
			    'slug'              => pp_events_pop_cur_page(),
			    'parent_slug'       => 'archive/page',
			    'parent_url'        => trailingslashit( bp_displayed_user_domain() . 'events/archive/page' ),
				'screen_function'   => 'pp_events_profile_archive',
				'position'          => 25,
				//'user_has_access'   => $user_has_access
		    ) );

		}
	}


	bp_core_new_subnav_item( array(
		'name'              => 'Create Event',
		'slug'              => 'create',
		'parent_url'        => trailingslashit( bp_displayed_user_domain() . 'events' ),
		'parent_slug'       => 'events',
		'screen_function'   => 'pp_events_profile_create',
		'position'          => 30,
		'item_css_id'       => 'member-events-create',
		'user_has_access'   => $user_has_access
		)
	);
}
add_action( 'bp_setup_nav',   'pp_events_profile_tabs' );


function pp_events_profile() {
	add_action( 'bp_template_content', 'pp_events_profile_screen' );
	bp_core_load_template( 'members/single/plugins' );
}


function pp_events_profile_screen() {
	echo '<style> li#member-events-hide-personal-li { visibility: hidden; } </style>';
	bp_get_template_part('members/single/profile-events-loop');
}


function pp_events_profile_create() {
	require( PP_EVENTS_DIR . '/inc/pp-events-create-class.php' );
	add_action( 'bp_template_title', 'pp_events_profile_create_title' );
	add_action( 'bp_template_content', 'pp_events_profile_create_screen' );
	bp_core_load_template( 'members/single/plugins' );
}

function pp_events_profile_create_title() {

	if( isset( $_GET['eid'] ) )
	    echo __( 'Edit Event', 'bp-simple-events' );
	else
		echo __( 'Create an Event', 'bp-simple-events' );
}


function pp_events_profile_create_screen() {
	bp_get_template_part('members/single/profile-events-create');
}


function pp_events_profile_archive() {
	add_action( 'bp_template_content', 'pp_events_profile_archive_screen' );
	bp_core_load_template( 'members/single/plugins' );
}

function pp_events_profile_archive_screen() {
	bp_get_template_part('members/single/profile-events-archive');
}


function pp_events_profile_enqueue() {

	if ( ( bp_is_my_profile() || is_super_admin() ) && 'create' == bp_current_action() ) {

		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-ui-timepicker', plugin_dir_url(__FILE__) . '/js/jquery.ui.timepicker.min.js' );
		wp_enqueue_style( 'jquery-ui-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css', true);

		wp_enqueue_script('script', plugin_dir_url(__FILE__) . '/js/events.js', array('jquery'));

		wp_register_script( 'google-places-api', 'http://maps.google.com/maps/api/js?libraries=places' );
		wp_print_scripts( 'google-places-api' );

	}
}
add_action('wp_enqueue_scripts', 'pp_events_profile_enqueue');

// total events per member for Events profile tab
function pp_events_count_profile( $user_id = 0 ) {
	global $wpdb;

	if ( empty( $user_id ) )
		$user_id = bp_displayed_user_id();

	return $wpdb->get_var( "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_author = $user_id AND post_type = 'event' AND post_status = 'publish'" );

}


// prevent 404s for pagination on profile/events & profile/archive
function pp_events_pop_cur_page() {

	$pageURL = 'http';

	if ( isset( $_SERVER["HTTPS"] ) && strtolower( $_SERVER["HTTPS"] ) == "on" )
		$pageURL .= "s";

	$pageURL .= "://";

	if ($_SERVER["SERVER_PORT"] != "80")
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	else
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

	$urlEnd = substr($pageURL, -3);

	return str_replace("/", "", $urlEnd);
}
