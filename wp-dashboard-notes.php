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

class WP_Dashboard_Notes {
	
	
	public function __construct() {
		
		// Add dashboard widget
		add_action( 'wp_dashboard_setup', array( $this, 'wpdn_add_dashboard_widget' ) );
		
		// Register post type
		add_action( 'init', array( $this, 'wpdn_register_post_type' ) );
		
		// Enqueue scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'wpdn_admin_enqueue_scripts' ) );
		
	}
	
	
	public function wpdn_register_post_type() {

		require_once plugin_dir_path( __FILE__ ) . 'includes/class-note-post-type.php';

	}
	
	public function wpdn_admin_enqueue_scripts() {
		
		wp_enqueue_script( 'wpdn_admin_js', plugin_dir_url( __FILE__ ) . 'assets/js/wpdn_admin.js' );
		wp_enqueue_style( 'wpdn_admin_css', plugin_dir_url( __FILE__ ) . 'assets/css/wpdn_admin.css' );
		wp_enqueue_script( 'jquery_ui', '//code.jquery.com/ui/1.10.4/jquery-ui.js' );
		wp_enqueue_script( 'jquery', '//code.jquery.com/jquery-1.10.2.js' );
		
	}
	
	public function wpdn_get_notes() {
		
		return get_posts( array( 'posts_per_page' => '-1', 'post_type' => 'note' ) );
		
	}
	
	public function wpdn_add_dashboard_widget() {
		
		$notes = $this->wpdn_get_notes();
		
		foreach ( $notes as $note ) :
			
			$note_meta = get_post_meta( $note->ID, '_note', true );
			$user = wp_get_current_user();
			if ( 'private' == $note_meta['visibility'] && $user->ID != $note->post_author ) :
				continue; // Skip if private
			endif;

			wp_add_dashboard_widget(
				'note_' . $note->ID,
				'<span contenteditable="true" class="wpdn-title">' . $note->post_title . '</span><div class="wpdn-edit-title dashicons dashicons-edit"></div>',
				array( $this, 'wpdn_render_dashboard_widget' ),
				'',
				$note
			);
			
		endforeach;
		
	}

	
	public function wpdn_render_dashboard_widget( $post, $args ) {

		$note = $args['args'];
		$note_meta = get_post_meta( $note->ID, '_note', true );

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
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpdn-ajax.php';


global $wp_dashboard_notes;
$wp_dashboard_notes = new WP_Dashboard_Notes();