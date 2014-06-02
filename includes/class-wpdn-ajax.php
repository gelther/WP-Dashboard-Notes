<?php
/**
 * Class WPDN_Ajax
 *
 * Initialize the AJAX funcitons
 *
 * @class       WPDN AJAX
 * @author     	Jeroen Sormani
 * @package		WP Dashboard Notes
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPDN_Ajax {


	/* Construct.
	 *
	 * Add ajax actions in order to work.
	 */
	public function __construct() {
		
		// Update note
		add_action( 'wp_ajax_wpdn_update_note', array( $this, 'wpdn_update_note' ) );
		
		// Note actions
		add_action( 'wp_ajax_wpdn_add_note', array( $this, 'wpdn_add_note' ) );
		add_action( 'wp_ajax_wpdn_delete_note', array( $this, 'wpdn_delete_note' ) );
		
	}
	
	/* 
	 * Update note
	 */
	public function wpdn_update_note() {

		$post = array(
			'ID' 			=> $_POST['post_id'],
			'post_title' 	=> $_POST['post_title'],
			'post_content' 	=> $_POST['post_content'],
		);

		wp_update_post( $post );
		// save data
		die();
		
	}
	
	
	/* 
	 * Delete note
	 */
	public function wpdn_add_note() {
		
		$args = array(
			'post_status' 	=> 'publish',
			'post_type' 	=> 'note',
			'post_title' 	=> __( 'New note', 'wp-dashboard-notes' ),
		);
		$post_id = wp_insert_post( $args );
		
		ob_start();
		?>
		<div id='<?php echo $post_id; ?>' class='postbox temp'>
			<div class='handlediv' title='Click to toggle'><br></div>
			
			<h3 class='hndle'><span>ToDo list</span></h3>
				<div class='inside'>
	
					<div class='wp-dashboard-note-wrap'>
					
						<div class='wp-dashboard-note'>
							
						</div>
				
						<div class='wp-dashboard-note-options'>
							<div class="dashicons dashicons-plus" style='color: #ccc; margin-right: 8px;'></div>
							<input type='text' name='list_item' class='add-list-item' data-id='<?php echo $post_id; ?>' placeholder='<?php _e( 'List item', 'wp-dashboard-notes' ); ?>' value=''>
							<span class='status'></span>
							
							<div class='wpdn-extra'>
								<span class='wpdn-option-visibility'>
			
									<span class='wpdn-toggle-visibility' title='<?php __( 'Visibility:', 'wp-dashboard-notes' ); ?> Public' data-visibility='public'>
										<span style='line-height: 40px;'>Visibility: </span><div class='wpdn-visibility visibility-publish dashicons dashicons-groups'></div>
									</span>
									
									<span class='wpdn-delete-note' title='<?php _e( 'Delete note', 'wp-dashboard-notes' ); ?>'>
										<div class='dashicons dashicons-trash'></div>
									</span>
			
									<span class='wpdn-add-note' title='<?php _e( 'Add new note', 'wp-dashboard-notes' ); ?>'>
										<div class='dashicons dashicons-plus'></div>
									</span>
									
									<span class='wpdn-color-note' title='<?php _e( 'Give it a color!', 'wp-dashboard-notes' ); ?>'>
										<div class='dashicons dashicons-art'></div>
									</span>
									
								</span>
							</div>
						</div> <!-- .wp-dashboard-note-options -->
					</div> <!-- .wp-dashboard-note-wrap -->
				</div> <!-- .inside -->
			</div> <!-- .postbox -->
		<?php
		$return['note'] 	= ob_get_clean();
		$return['post_id'] 	= $post_id;
		
		echo json_encode( $return );
		
		die();
		
	}

	
	/* 
	 * Delete note
	 */
	public function wpdn_delete_note() {
		
		$post_id = (int) $_POST['post_id'];
		wp_delete_post( $post_id, false );
		die();
	}


}
new WPDN_Ajax();
?>