<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


//  load single event template from theme or plugin
function pp_event_single_template( $single_template ) {
	global $post;

	if ( $post->post_type == 'eventx' ) {

		$theme_template = 'single-event.php';

		if ( $theme_template == locate_template( 'single-event.php' ) )
		   $single_template = $theme_template;
		else
		   $single_template = PP_EVENTS_DIR . '/templates/single-event.php';

	}

	return $single_template;
}
add_filter( 'single_template', 'pp_event_single_template' );


//  load event loop template from theme or plugin
function pp_events_template_include( $template ) {
    global $wp_query;

	if( ! bp_is_user() ) {

		$page_title = $wp_query->post->post_title;

		if ( $page_title == __( 'Events', 'bp-simple-events' ) ) {

			$theme_template = 'events-loop.php';

			if ( $theme_template = locate_template( 'events-loop.php' ) )
			   $template = $theme_template;
			else
			   $template = PP_EVENTS_DIR . '/templates/events-loop.php';			
			
		}
	}
	
	return $template;
}
add_action( 'template_include', 'pp_events_template_include' );



// profile templates
function pp_events_register_template_location() {
    return PP_EVENTS_DIR . '/templates/';
}

function pp_events_template_start() {

    if( function_exists( 'bp_register_template_stack' ) )
        bp_register_template_stack( 'pp_events_register_template_location' );

}
add_action( 'bp_init', 'pp_events_template_start' );

