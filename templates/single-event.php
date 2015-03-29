<?php

 /**
 * Template for displaying a single Event
 * You can copy this file to your-theme
 * and then edit the layout.
 */

wp_register_script( 'google-maps-api', 'http://maps.google.com/maps/api/js?sensor=false' );

get_header();

function pp_single_map_css() {
	echo '<style type="text/css"> .single_map_canvas img { max-width: none; } </style>';
}
add_action( 'wp_head', 'pp_single_map_css' );

wp_print_scripts( 'google-maps-api' );

?>

<div id="primary" class="content-area">
	<div id="content" class="site-content" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<div class="entry-content">
				<br/>
				<h2 class="entry-title">
					<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
					<?php the_title(); ?></a>
				</h2>

				<?php
				$author_id = get_the_author_meta('ID');
				$author_name = get_the_author_meta('display_name');
				$user_link = bp_core_get_user_domain( $author_id );
				if( get_current_user_id() == $author_id )
					$is_author = true;
				else
					$is_author = false;
				?>

				<?php
				if( $is_author || is_super_admin() ) :

					$edit_link = wp_nonce_url( $user_link . 'events/create?eid=' . $post->ID, 'editing', 'edn');

					$delLink = get_delete_post_link( $post->ID );

				?>

					<span class="edit"><a href="<?php echo $edit_link; ?>" title="Edit  Event">Edit</a></span>
					&nbsp; &nbsp;
					<span class="trash"><a onclick="return confirm('Are you sure you want to delete this Event?')" href="<?php echo $delLink; ?>" title="Delete Event" class="submit">Delete</a></span>

					<?php echo '<br/>'; ?>

				<?php endif; ?>

				<br/>

				<a href="<?php echo bp_core_get_user_domain( $author_id ); ?>">
				<?php echo bp_core_fetch_avatar( array( 'item_id' => $author_id, 'type' => 'thumb' ) ); ?>
				&nbsp;<?php echo $author_name; ?></a>


				<?php
				if ( has_post_thumbnail() ) {
					echo '<br/>';
					the_post_thumbnail( 'large' );
					echo '<br/>';
				}
				?>

				<?php the_content(); ?>

				<?php
				$meta = get_post_meta($post->ID );

				if( ! empty( $meta['event-date'][0] ) )
					echo __( 'Date', 'bp-simple-events' ) . ':&nbsp;' . $meta['event-date'][0];

				if( ! empty( $meta['event-time'][0] ) )
					echo '<br/>' . __( 'Time', 'bp-simple-events' ) . ':&nbsp;' . $meta['event-time'][0];

				if( ! empty( $meta['event-address'][0] ) )
					echo '<br/>' . __( 'Location', 'bp-simple-events' ) . ':&nbsp;' . $meta['event-address'][0];

				if( ! empty( $meta['event-url'][0] ) )
					echo '<br/>' . __( 'Url', 'bp-simple-events' ) . ':&nbsp;' . pp_event_convert_url( $meta['event-url'][0] );

				?>

				<br/>
				Category: <?php the_category(', ') ?>

				<?php if( ! empty( $meta['event-latlng'][0] ) ) : ?>

					<br/>
					<div class="single_map_canvas" id="single_event_map" style="height: 225px; width: 450px;"></div>

					<script type="text/javascript">
					function initialize() {
					  var singleLatlng = new google.maps.LatLng(<?php echo $meta['event-latlng'][0]; ?>);
					  var mapOptions = {
					    zoom: 12,
					    center: singleLatlng
					  }
					  var map = new google.maps.Map(document.getElementById('single_event_map'), mapOptions);

					  var marker = new google.maps.Marker({
					      position: singleLatlng,
					      map: map
					  });
					}

					google.maps.event.addDomListener(window, 'load', initialize);
					</script>

				<?php endif; ?>

			</div>

			<br/>
			<div class="entry-content">
				<nav class="nav-single">
					<span class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'bp-simple_events' ) . '</span> %title' ); ?></span>
					&nbsp; &nbsp;
					<span class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'bp-simple_events' ) . '</span>' ); ?></span>
				</nav><!-- .nav-single -->
			</div>
			<?php comments_template( '', true ); ?>

		<?php endwhile; ?>

	</div><!-- #content -->
</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>