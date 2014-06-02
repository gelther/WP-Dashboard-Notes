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
		
	}
	
	public function wpdn_add_dashboard_widget() {
		
		$notes = get_posts( array( 'posts_per_page' => '-1', 'post_type' => 'note' ) );
		
		foreach ( $notes as $note ) :
			
			wp_add_dashboard_widget(
				$note->ID,
				'<span contenteditable="true" class="wpdn-title">' . $note->post_title . '</span><div class="wpdn-edit-title dashicons dashicons-edit"></div>',
				array( $this, 'wpdn_render_dashboard_widget' ),
				'',
				$note
			);
			
		endforeach;
		
	}

	
	public function wpdn_render_dashboard_widget( $post, $args ) {

		$note = $args['args'];
		$note_meta = get_post_meta( $note->ID, '_note_meta', true );
		?>
		<div class='wp-dashboard-note-wrap'>
		
			<div class='wp-dashboard-note'>
				<?php echo $note->post_content; ?>
			</div>

			<div class='wp-dashboard-note-options'>
				<div class="dashicons dashicons-plus" style='color: #ccc; margin-right: 8px;'></div>
				<input type='text' name='list_item' class='add-list-item' data-id='<?php echo $note->ID; ?>' placeholder='<?php _e( 'List item', 'wp-dashboard-notes' ); ?>' value=''>
				<span class='status'></span>
				<div class='wpdn-extra'>
					<span class='wpdn-option-visibility'>
						<?php 
						if ( 'publish' == $note->post_status ) :
							$status['icon'] 		= 'dashicons-groups';
							$status['title'] 		= __( 'Public', 'wp-dashboard-notes' );
							$status['visibility'] 	= 'publish';
						elseif ( 'private' == $note->post_status ) :
							$status['icon'] 		= 'dashicons-lock';
							$status['title'] 		= __( 'Private', 'wp-dashboard-notes' );
							$status['visibility'] 	= 'private';
						endif; ?>
						
						<span class='wpdn-toggle-visibility' title='<?php __( 'Visibility:', 'wp-dashboard-notes' ); ?> <?php echo $status['title']; ?>' data-visibility='<?php echo $status['visibility']; ?>'>
							<span style='line-height: 40px;'>Visibility: </span><div class="wpdn-visibility visibility-publish dashicons <?php echo $status['icon']; ?>"></div>
						</span>
						
						<span class='wpdn-delete-note' title='<?php _e( 'Delete note', 'wp-dashboard-notes' ); ?>'>
							<div class="dashicons dashicons-trash"></div>
						</span>

						<span class='wpdn-add-note' title='<?php _e( 'Add new note', 'wp-dashboard-notes' ); ?>'>
							<div class="dashicons dashicons-plus"></div>
						</span>
						
						<span class='wpdn-color-note' title='<?php _e( 'Give it a color!', 'wp-dashboard-notes' ); ?>'>
							<div class="dashicons dashicons-art"></div>
						</span>
						
					</span>
				</div>
			</div>
			
		</div>
		
		<?php
				
	}
	
}
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpdn-ajax.php';


global $wp_dashboard_notes;
$wp_dashboard_notes = new WP_Dashboard_Notes();