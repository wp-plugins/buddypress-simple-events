<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Settings Page class
 */


class PP_Simple_Events_Admin_Settings {

	private $roles_message = '';
	private $settings_message = '';

    public function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

	}

	function admin_menu() {
		add_options_page(  __( 'BP Simple Events', 'bp-simple-events'), __( 'BP Simple Events', 'bp-simple-events' ), 'manage_options', 'bp-simple-events', array( $this, 'settings_admin_screen' ) );
	}

	function settings_admin_screen(){
		global $wp_roles;

		if( !is_super_admin() )
			return;

		$this->roles_update();
		$this->settings_update();

		$all_roles = $wp_roles->roles;
		?>

		<h3>BuddyPress Simple Events Settings</h3>

		<table border="0" cellspacing="10" cellpadding="10">
		<tr>
		<td style="vertical-align:top; border: 1px solid #ccc;" >

			<h3><?php echo __('Assign User Roles', 'bp-simple-events'); ?></h3>
			<?php echo $this->roles_message; ?>
			<em><?php echo __('Which roles can create Events?', 'bp-simple-events'); ?></em><br/>
			<form action="" name="access-form" id="access-form"  method="post" class="standard-form">

			<?php wp_nonce_field('allowedroles-action', 'allowedroles-field'); ?>

			<ul id="pp-user_roles">

			<?php foreach(  $all_roles as $key => $value ){

				if( $key == 'administrator' ) :
				?>

					<li><label><input type="checkbox" id="admin-preset-role" name="admin-preset" checked="checked" disabled /> <?php echo ucfirst($key); ?></label></li>

				<?php else:

					if( array_key_exists('publish_events', $value["capabilities"]) )
						$checked = ' checked="checked"';
					else
						$checked = '';

				?>

					<li><label for="allow-roles-<?php echo $key ?>"><input id="allow-roles-<?php echo $key ?>" type="checkbox" name="allow-roles[]" value="<?php echo $key ?>" <?php echo  $checked ; ?> /> <?php echo ucfirst($key); ?></label></li>

				<?php endif;

			}?>

			</ul>
			<hr/>

			<input type="hidden" name="role-access" value="1"/>
			<input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save Roles', 'bp-simple-events'); ?>"/>
			</form>

		</td>

		<td style="vertical-align:top; border: 1px solid #ccc;" >
			<h3><?php echo __('Settings', 'bp-simple-events'); ?></h3>
			<?php echo $this->settings_message; ?>
			<form action="" name="settings-form" id="settings-form"  method="post" class="standard-form">

				<?php wp_nonce_field('settings-action', 'settings-field'); ?>

				<h4><?php echo __('Profile', 'bp-simple-events'); ?></h4>
				<?php $tab_position = get_option( 'pp_events_tab_position' ); ?>
				<input type="text" size="5" id="pp-tab-position" name="pp-tab-position" value="<?php echo $tab_position; ?>" />
				<label for="pp-tab-position"><?php echo __( 'Tab Position <em>Numbers only.</em>', 'bp-simple-events' ); ?></label>
				<hr/>


				<h4><?php echo __('Required Fields', 'bp-simple-events'); ?></h4>
				<?php echo __('Select fields to be required when creating an Event.', 'bp-simple-events'); ?>
				<br/>

				<ul id="pp-fielders">

					<li><label><input type="checkbox" name="event-dummy[]" checked="checked" disabled /> Title</label></li>

					<li><label><input type="checkbox" name="event-dummy[]" checked="checked" disabled /> Description</label></li>

					<?php
					$required_fields = get_option( 'pp_events_required' );
					$checked = ' checked';
					?>

					<li><label for="required-date"><input id="required-date" type="checkbox" name="pp-required[]" value="date" <?php if( in_array( 'date', $required_fields ) ) echo $checked ; ?> /> <?php echo __( 'Date', 'bp-simple-events' ); ?></label></li>

					<li><label for="required-time"><input id="required-time" type="checkbox" name="pp-required[]" value="time" <?php if( in_array( 'time', $required_fields ) ) echo $checked ; ?> /> <?php echo __( 'Time', 'bp-simple-events' ); ?></label></li>

					<li><label for="required-location"><input id="required-location" type="checkbox" name="pp-required[]" value="location" <?php if( in_array( 'location', $required_fields ) ) echo $checked ; ?> /> <?php echo __( 'Location', 'bp-simple-events' ); ?></label></li>

					<li><label for="required-url"><input id="required-url" type="checkbox" name="pp-required[]" value="url" <?php if( in_array( 'url', $required_fields ) ) echo $checked ; ?> /> <?php echo __( 'Url', 'bp-simple-events' ); ?></label></li>

					<li><label for="required-url"><input id="required-categories" type="checkbox" name="pp-required[]" value="categories" <?php if( in_array( 'categories', $required_fields ) ) echo $checked ; ?> /> <?php echo __( 'Categories', 'bp-simple-events' ); ?></label></li>


				</ul>
				<hr/>


				<br/>
				<input type="hidden" name="settings-access" value="1"/>
				<input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save Settings', 'bp-simple-events'); ?>"/>
			</form>

		</td></tr></table>
	<?php
	}


	//  save any changes to role access options
	private function roles_update() {
		global $wp_roles;

		if( isset( $_POST['role-access'] ) ) {

			if( !wp_verify_nonce($_POST['allowedroles-field'],'allowedroles-action') )
				die('Security check');

			if( !is_super_admin() )
				return;

			$updated = false;

			$all_roles = $wp_roles->roles;

			foreach(  $all_roles as $key => $value ){

				if( 'administrator' != $key ) {

					$role = get_role( $key );

					$role->remove_cap( 'delete_published_events' );
					$role->remove_cap( 'delete_events' );
					$role->remove_cap( 'edit_published_events' );
					$role->remove_cap( 'edit_events' );
					$role->remove_cap( 'publish_events' );

					$updated = true;
				}
			}


			if( isset( $_POST['allow-roles'] ) ) {

				foreach( $_POST['allow-roles'] as $key => $value ){

					if( array_key_exists($value, $all_roles ) ) {

						if( 'administrator' != $value ) {

							$role = get_role( $value );
							$role->add_cap( 'delete_published_events' );
							$role->add_cap( 'delete_events' );
							$role->add_cap( 'edit_published_events' );
							$role->add_cap( 'edit_events' );
							$role->add_cap( 'publish_events' );

						}
					}
				}

			}

			if( $updated )
				$this->roles_message .=
					"<div class='updated below-h2'>" .
					__('User Roles have been updated.', 'bp-simple-events') .
					"</div>";
			else
				$this->roles_message .=
					"<div class='updated below-h2' style='color: red'>" .
					__('No changes were detected re User Roles.', 'bp-simple-events') .
					"</div>";
		}
	}

	//  save any changes to settings options
	private function settings_update() {

		if( isset( $_POST['settings-access'] ) ) {

			if( !wp_verify_nonce($_POST['settings-field'],'settings-action') )
				die('Security check');

			if( !is_super_admin() )
				return;

			if( ! empty( $_POST['pp-tab-position'] ) ) {

				 if( is_numeric( $_POST['pp-tab-position'] ) )
				    $tab_value = $_POST['pp-tab-position'];
				else
					$tab_value = 52;
			}
			else
				$tab_value = 52;

			update_option( 'pp_events_tab_position', $tab_value );


			if ( ! empty( $_POST['pp-groups'] ) )
				update_option( 'pp_events_groups', '1' );
			else
				update_option( 'pp_events_groups', '0' );


			delete_option( 'pp_events_required' );
			$required_fields = array();
			if( ! empty( $_POST['pp-required'] ) ) {
				foreach ( $_POST['pp-required'] as $value )
					$required_fields[] = $value;
			}
			update_option( 'pp_events_required', $required_fields );


			$this->settings_message .=
				"<div class='updated below-h2'>" .
				__('Settings have been updated.', 'bp-simple-events') .
				"</div>";
		}
	}

} // end of PP_Simple_Events_Admin_Settings class

$pp_se_admin_settings_instance = new PP_Simple_Events_Admin_Settings();