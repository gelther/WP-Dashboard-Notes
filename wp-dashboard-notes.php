<?php
/*
Plugin Name: WP Dashboard Notes
Plugin URI: http://www.jeroensormani.com
Description: Working with multiple persons on a website? Want to make notes? You can do just that with WP Dashboard Notes. Create beautiful notes with a nice user experience.
Version: 1.0.2
Author: Jeroen Sormani
Author URI: http://www.jeroensormani.com
Text Domain: wp-dashboard-notes
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! is_admin() ) return; // Only load plugin when user is in admin

/**
 * Class WP_Dashboard_Notes
 *
 * Main WPDN class initializes the plugin
 *
 * @class       WP_Dashboard_Notes
 * @version     1.0.0
 * @author      Jeroen Sormani
 */
class WP_Dashboard_Notes {


	/**
	 * __construct function.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Add dashboard widget
		add_action( 'wp_dashboard_setup', array( $this, 'wpdn_init_dashboard_widget' ) );

		// Register post type
		add_action( 'init', array( $this, 'wpdn_register_post_type' ) );

		// Enqueue scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'wpdn_admin_enqueue_scripts' ) );

		// Add note button
		add_filter( 'manage_dashboard_columns', array( $this, 'wpdn_dashboard_columns' ) );

		// Load textdomain
		load_plugin_textdomain( 'wp-dashboard-notes', false, basename( dirname( __FILE__ ) ) . '/languages' );

	}


	/**
	 * Register post type.
	 *
	 * @since 1.0.0
	 */
	public function wpdn_register_post_type() {

		/**
		 * Post type class.
		 */
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-note-post-type.php';

	}


	/**
	 * Enqueue scripts.
	 *
	 * Enqueue Stylesheet and multiple javascripts.
	 *
	 * @since 1.0.0
	 */
	public function wpdn_admin_enqueue_scripts() {

		// Javascript
		wp_enqueue_script( 'wpdn_admin_js', plugin_dir_url( __FILE__ ) . 'assets/js/wpdn_admin.js', array( 'jquery', 'jquery-ui-sortable' ) );

		// Stylesheet
		wp_enqueue_style( 'wpdn_admin_css', plugin_dir_url( __FILE__ ) . 'assets/css/wpdn_admin.css', array( 'dashicons' ) );

	}


	/**
	 * Get notes.
	 *
	 * Returns all posts from DB with post type 'note'.
	 *
	 * @since 1.0.0
	 *
	 * @return Array of all published notes.
	 */
	public function wpdn_get_notes() {

		$notes = get_posts( array( 'posts_per_page' => '-1', 'post_type' => 'note' ) );
		
		return apply_filters( 'wpdn_notes', $notes );

	}


	/**
	 * Note meta.
	 *
	 * Return note meta selected by note id.
	 *
	 * @since 1.0.0
	 *
	 * @param 	int 	$note_id 	ID of the note.
	 * @return 	array 				Note meta.
	 */
	public static function wpdn_get_note_meta( $note_id ) {

		$note_meta = get_post_meta( $note_id, '_note', true );

		if ( ! isset( $note_meta['note_type'] ) ) 	{ $note_meta['note_type'] 	= 'regular'; }
		if ( ! isset( $note_meta['color'] ) ) 		{ $note_meta['color'] 		= '#ffffff'; }
		if ( ! isset( $note_meta['visibility'] ) ) 	{ $note_meta['visibility'] 	= 'public'; }
		if ( ! isset( $note_meta['color_text'] ) ) 	{ $note_meta['color_text'] 	= 'white'; }

		return apply_filters( 'wpdn_note_meta', $note_meta );

	}


	/**
	 * Initialize dashboard notes.
	 *
	 * Get all notes and initialize dashboard widgets.
	 *
	 * @since 1.0.0
	 */
	public function wpdn_init_dashboard_widget() {

		$notes = $this->wpdn_get_notes();

		foreach ( $notes as $note ) :

			$note_meta 	= $this->wpdn_get_note_meta( $note->ID );
			$user 		= wp_get_current_user();

			// Skip if private
			if ( 'private' == $note_meta['visibility'] && $user->ID != $note->post_author ) :
				continue;
			endif;

			// Add widget
			wp_add_dashboard_widget(
				'note_' . $note->ID,
				'<span contenteditable="true" class="wpdn-title">' . $note->post_title . '</span><div class="wpdn-edit-title dashicons dashicons-edit"></div>',
				array( $this, 'wpdn_render_dashboard_widget' ),
				'',
				$note
			);

		endforeach;

	}


	/**
	 * Render dashboard widget.
	 *
	 * Load data and render the widget with the right colors.
	 *
	 * @since 1.0.0
	 *
	 * @param object 	$post Post object.
	 * @param array 	$args Extra arguments.
	 */
	public function wpdn_render_dashboard_widget( $post, $args ) {

		$note		= $args['args'];
		$note_meta 	= $this->wpdn_get_note_meta( $note->ID );

		// Inline styling required for note depending colors.
		?><style>
			#note_<?php echo $note->ID; ?> { background-color: <?php echo $note_meta['color']; ?>; }
			#note_<?php echo $note->ID; ?> .hndle { border: none; }
		</style><?php

		if ( $note_meta['note_type'] == 'regular' ) :
			require plugin_dir_path( __FILE__ ) . 'includes/templates/note.php';
		else :
			require plugin_dir_path( __FILE__ ) . 'includes/templates/note-list.php';
		endif;

	}


	/**
	 * Add button.
	 *
	 * Adds a 'Add note' button to the 'Screen Options' tab.
	 * Triggered via jQuery.
	 *
	 * @since 1.0.0
	 *
	 * @global 	object 	$current_screen	Information about current screen.
	 *
	 * @param 	array 	$columns 		Array of columns within the screen options tab.
	 * @return 	array					Array of columns within the screen options tab.
	 */
	public function wpdn_dashboard_columns( $columns ) {

		global $current_screen;
		
		if ( $current_screen->id ) :
			$columns['add_note'] = __( 'Add note', 'wp-dashboard-notes' );
		endif;
		return $columns;

	}


}
/**
 * AJAX class.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpdn-ajax.php';


global $wp_dashboard_notes;
$wp_dashboard_notes = new WP_Dashboard_Notes();