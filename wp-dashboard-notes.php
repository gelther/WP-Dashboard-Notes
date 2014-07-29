<?php
/*
Plugin Name: WP Dashboard Notes
Plugin URI: http://www.jeroensormani.com
Description: Working with a team on websites? Or just need to take notes? You can do just that with WP Dashboard Notes. Create notes with a nice interface.
Version: 1.0.0
Author: Jeroen Sormani
Author URI: http://www.jeroensormani.com
Text Domain: wp-dashboard-notes
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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
		
	}
	
	
	/**
	 * Register post type.
	 *
	 * @since 1.0.0
	 */
	public function wpdn_register_post_type() {

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
		
		wp_enqueue_script( 'wpdn_admin_js', plugin_dir_url( __FILE__ ) . 'assets/js/wpdn_admin.js' );
		wp_enqueue_style( 'wpdn_admin_css', plugin_dir_url( __FILE__ ) . 'assets/css/wpdn_admin.css' );
		wp_enqueue_script( 'jquery_ui', '//code.jquery.com/ui/1.10.4/jquery-ui.js' );
		wp_enqueue_script( 'jquery', '//code.jquery.com/jquery-1.10.2.js' );
		
	}
	

	/**
	 * Get notes.
	 *
	 * Returns all posts from DB with post type 'note'.
	 *
	 * @since 1.0.0
	 *
	 * @return Array All notes.
	 */
	public function wpdn_get_notes() {
		
		return get_posts( array( 'posts_per_page' => '-1', 'post_type' => 'note' ) );
		
	}
	
	
	/**
	 * Note meta.
	 *
	 * Return note meta selected by note id.
	 *
	 * @since 1.0.0
	 *
	 * @param int $note_id ID of the note.
	 * @return Array Note meta.
	 */
	public static function wpdn_get_note_meta( $note_id ) {
		
		$note_meta = get_post_meta( $note_id, '_note', true );
		
		if ( ! isset( $note_meta['note_type'] ) ) 	{ $note_meta['note_type'] 	= 'regular'; }
		if ( ! isset( $note_meta['color'] ) ) 		{ $note_meta['color'] 		= '#ffffff'; }
		if ( ! isset( $note_meta['visibility'] ) ) 	{ $note_meta['visibility'] 	= 'public'; }
		if ( ! isset( $note_meta['color_text'] ) ) 	{ $note_meta['color_text'] 	= 'white'; }
		
		return $note_meta;
		
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
			
			$note_meta = $this->wpdn_get_note_meta( $note->ID );
			$user = wp_get_current_user();

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
	 * @param object $post Post object.
	 * @param array $args Extra arguments.
	 */
	public function wpdn_render_dashboard_widget( $post, $args ) {

		$note		= $args['args'];
		$note_meta 	= $this->wpdn_get_note_meta( $note->ID );

		?>
		<style>
			#note_<?php echo $note->ID; ?> { background-color: <?php echo $note_meta['color']; ?>; }
			#note_<?php echo $note->ID; ?> .hndle { border: none; }
		</style>
		<?php
		if ( $note_meta['note_type'] == 'regular' ) :
			require plugin_dir_path( __FILE__ ) . 'includes/templates/note.php';
		else :
			require plugin_dir_path( __FILE__ ) . 'includes/templates/note-list.php';		
		endif;
				
	}
	
}
/**
 * AJAX class.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpdn-ajax.php';


global $wp_dashboard_notes;
$wp_dashboard_notes = new WP_Dashboard_Notes();