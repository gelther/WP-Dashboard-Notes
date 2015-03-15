<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class WPDN_Ajax.
 *
 * Class to handle all AJAX calls.
 *
 * @class		WPDN_Ajax
 * @version		1.0.0
 * @package		WP Dashboard Notes
 * @author		Jeroen Sormani
 */
class WPDN_Ajax {


	/**
	 * Constructor.
	 *
	 * Add ajax actions.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Update note
		add_action( 'wp_ajax_wpdn_update_note', array( $this, 'wpdn_update_note' ) );
		add_action( 'wp_ajax_wpdn_toggle_note', array( $this, 'wpdn_toggle_note' ) );

		// Add / Delete note
		add_action( 'wp_ajax_wpdn_add_note', array( $this, 'wpdn_add_note' ) );
		add_action( 'wp_ajax_wpdn_delete_note', array( $this, 'wpdn_delete_note' ) );

		add_action( 'wp_ajax_wpdn_search_user', array( $this, 'search_users' ) );

	}


	/**
	 * Update note.
	 *
	 * Update note + meta when the jQuery update trigger is triggered.
	 *
	 * @since 1.0.0
	 */
	public function wpdn_update_note() {

		// Bail if user cannot save posts
		if ( ! current_user_can( 'edit_posts' ) ) :
			die();
		endif;

		$post_id			= absint( $_POST['post_id'] );
		$permissions_form	= wp_parse_args( $_POST['permissions_form'] );
		$user_permissions	= isset( $permissions_form['user_permission'] ) ? $permissions_form['user_permission'] : array();

		if ( isset( $user_permissions['user_role'] ) && is_array( $user_permissions['user_role'] ) ) :
			array_walk( $user_permissions['user_role'], 'sanitize_text_field' );
		else :
			$user_permissions['user_role'] = '';
		endif;

		if ( isset( $user_permissions['user'] ) && is_array( $user_permissions['user'] ) ) :
			array_walk( $user_permissions['user'], 'absint' );
		else :
			$user_permissions['user'] = '';
		endif;

		$post = array(
			'ID'			=> $post_id,
			'post_title'	=> sanitize_title( $_POST['post_title'] ),
			'post_author'	=> get_current_user_id(),
			'post_content'	=> sanitize_text_field( $_POST['post_content'] ),
		);
		wp_update_post( $post );

		$note_meta = array(
			'color'			=> sanitize_text_field( $_POST['note_color'] ),
			'color_text'	=> sanitize_text_field( $_POST['note_color_text'] ),
			'note_type'		=> sanitize_text_field( $_POST['note_type'] ),
		);
		update_post_meta( $post_id, '_note', $note_meta );
		update_post_meta( $post_id, '_role_permissions', $user_permissions['user_role'] );
		update_post_meta( $post_id, '_user_permissions', $user_permissions['user'] );

		die();

	}


	/**
	 * Toggle note.
	 *
	 * Toggle note type, from 'regular note' to 'list note' or vice versa.
	 *
	 * @since 1.0.0
	 */
	public function wpdn_toggle_note() {

		$note				= get_post( absint( $_POST['post_id'] ) );
		$role_permissions 	= get_post_meta( $note->ID, '_role_permissions', true );
		$user_permissions 	= get_post_meta( $note->ID, '_user_permissions', true );
		$content			= apply_filters( 'wpdn_content', $note->post_content );
		$colors				= apply_filters( 'wpdn_colors', array(
			'white'		=> '#fff',
			'red'		=> '#f7846a',
			'orange'	=> '#ffbd22',
			'yellow'	=> '#eeee22',
			'green'		=> '#bbe535',
			'blue'		=> '#66ccdd',
			'black'		=> '#777777',
		) );
		$note_meta = WP_Dashboard_Notes::wpdn_get_note_meta( $note->ID );

		?><style>
			#note_<?php echo $note->ID; ?>, #note_<?php echo $note->ID; ?> .visibility-settings { background-color: <?php echo esc_html( $note_meta['color'] ); ?>; }
			#note_<?php echo $note->ID; ?> .hndle { border: none; }
		</style><?php

		if ( $_POST['note_type'] == 'regular' ) :
			require plugin_dir_path( __FILE__ ) . 'templates/note.php';
		else :
			require plugin_dir_path( __FILE__ ) . 'templates/note-list.php';
		endif;

		die();

	}


	/**
	 * Add new note.
	 *
	 * Create a new note, return two variables (post ID | note content) to jQuery through json_encode.
	 *
	 * @since 1.0.0
	 */
	public function wpdn_add_note() {

		$args = array(
			'post_status'	=> 'publish',
			'post_type'		=> 'note',
			'post_title'	=> __( 'New note', 'wp-dashboard-notes' ),
		);
		$post_id = wp_insert_post( $args );

		$note		= (object) array( 'ID' => $post_id, 'post_content' => '' );
		$note_meta	= apply_filters( 'wpdn_new_note_meta', array(
			'color'			=> '#ffffff',
			'color_text'	=> 'white',
			'visibility'	=> 'Everyone',
			'note_type'		=> 'list',
		) );
		$content	= apply_filters( 'wpdn_content', $note->post_content );
		$colors		= apply_filters( 'wpdn_colors', array(
			'white'		=> '#fff',
			'red'		=> '#f7846a',
			'orange'	=> '#ffbd22',
			'yellow'	=> '#eeee22',
			'green'		=> '#bbe535',
			'blue'		=> '#66ccdd',
			'black'		=> '#777777',
		) );
		$note_meta = apply_filters( 'wpdn_new_note_meta', $note_meta );
		update_post_meta( $post_id, '_note', $note_meta );

		ob_start();

		?><div id='note_<?php echo $post_id; ?>' class='postbox'>
			<div class='handlediv' title='Click to toggle'><br></div>
			<h3 class="hndle">
				<span>
					<span contenteditable="true" class="wpdn-title"><?php _e( 'New note', 'wp-dashboard-notes' ); ?></span>
					<div class="wpdn-edit-title dashicons dashicons-edit"></div>
					<span class="status"></span>
				</span>
			</h3>

			<div class='inside'>

			<style>
				#note_<?php echo $post_id; ?> { background-color: <?php echo $note_meta['color']; ?>; }
				#note_<?php echo $post_id; ?> .hndle { border: none; }
			</style><?php

				if ( 'regular' == $note_meta['note_type'] ) :
					require plugin_dir_path( __FILE__ ) . 'templates/note.php';
				else :
					require plugin_dir_path( __FILE__ ) . 'templates/note-list.php';
				endif;

			?></div> <!-- .inside -->
		</div> <!-- .postbox --><?php

		$return['note']		= ob_get_clean();
		$return['post_id']	= $post_id;

		echo json_encode( $return );

		die();

	}


	/**
	 * Delete note.
	 *
	 * Post is trashed and not permanently deleted.
	 *
	 * @since 1.0.0
	 */
	public function wpdn_delete_note() {

		wp_trash_post( absint( $_POST['post_id'] ) );
		die();

	}


	public function search_users() {

// 		check_ajax_referer( 'search-customers', 'security' );

		$search = wc_clean( stripslashes( $_GET['search'] ) );

		if ( empty( $search ) ) :
			die();
		endif;

		$found_users = array();

		add_action( 'pre_user_query', array( $this, 'json_search_customer_name' ) );

		$user_query = new WP_User_Query( apply_filters( 'woocommerce_json_search_customers_query', array(
			'fields'         => 'all',
			'orderby'        => 'display_name',
			'search'         => '*' . $search . '*',
			'search_columns' => array( 'ID', 'user_login', 'user_email', 'user_nicename' )
		) ) );

		remove_action( 'pre_user_query', array( $this, 'json_search_customer_name' ) );

		if ( $users = $user_query->get_results() ) :
			foreach ( $users as $user ) :
				$found_users[ $user->ID ] = $user->display_name . ' (#' . $user->ID . ')';
			endforeach;
		endif;

		wp_send_json( $found_users );

	}


	public static function json_search_customer_name( $query ) {
		global $wpdb;

		$search = wc_clean( stripslashes( $_GET['search'] ) );
		if ( method_exists( $wpdb, 'esc_like' ) ) :
			$search = $wpdb->esc_like( $search );
		else :
			$search = like_escape( $search );
		endif;

		$query->query_from  .= " INNER JOIN {$wpdb->usermeta} AS user_name ON {$wpdb->users}.ID = user_name.user_id AND ( user_name.meta_key = 'first_name' OR user_name.meta_key = 'last_name' ) ";
		$query->query_where .= $wpdb->prepare( " OR user_name.meta_value LIKE %s ", '%' . $search . '%' );
	}


}
