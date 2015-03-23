<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;



// activity entries for new events
function pp_event_tracking_args_activity() {

    if ( ! bp_is_active( 'activity' ) )
        return;

    bp_activity_set_post_type_tracking_args( 'event', array(
        'component_id'             => 'events',  //'activity',  // default
        'action_id'                => 'new_event',
        'bp_activity_admin_filter' => __( 'Events', 'bp-simple-events' ),
        'bp_activity_front_filter' => __( 'Events', 'bp-simple-events' ),
        'contexts'                 => array( 'activity', 'member' ),
        'activity_comment'         => true,
        'bp_activity_new_post'     => __( '%1$s created a new <a href="%2$s">Event</a>', 'bp-simple-events' ),
        'bp_activity_new_post_ms'  => __( '%1$s created a new <a href="%2$s">Event</a>, on the site %3$s', 'bp-simple-events' ),
        'position'                 => 100,
    ) );
}
add_action( 'bp_init', 'pp_event_tracking_args_activity' );



// pagination for Events loop page
function pp_events_pagination( $wp_query ) {

	$big = 999999999;

	echo paginate_links( array(
		'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'format' => '?paged=%#%',
		'current' => max( 1, get_query_var('paged') ),
		'total' => $wp_query->max_num_pages
	) );
}

// so event cpt is found on assigned cat archive page
function pp_event_query_post_type($query) {

	if( is_category() || is_tag() ) {
		$post_type = get_query_var('post_type');
		if($post_type)
			$post_type = $post_type;
		else
			$post_type = array( 'event', 'nav_menu_item');

		$query->set('post_type',$post_type);


		return $query;
	}

}
add_filter('pre_get_posts', 'pp_event_query_post_type');


// redirect when Event is trashed on front-end
function pp_event_trash_redirect(){
    if (is_404()){
        global $wp_query, $wpdb;
        $page_id = $wpdb->get_var( $wp_query->request );
        $post_status = get_post_status( $page_id );
        if($post_status == 'trash'){
            wp_redirect(site_url('/events/'), 301);
            die();
        }
    }
}
add_action('template_redirect', 'pp_event_trash_redirect');


// turn Event > Url to a link
function pp_event_convert_url( $text, $scheme = 'http://' ) {

	$url = parse_url( $text, PHP_URL_SCHEME) === null ? $scheme . $text : $text;

	$disallowed = array('http://', 'https://');
	foreach( $disallowed as $d ) {
		if( strpos( $text, $d ) === 0 )
			$text = str_replace( $d, '', $text );
	}

	return '<a href="' . $url . '" rel="nofollow">' . $text . '</a>';
}

