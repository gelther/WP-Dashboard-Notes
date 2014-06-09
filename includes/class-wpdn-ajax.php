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
		add_action( 'wp_ajax_wpdn_toggle_note', array( $this, 'wpdn_toggle_note' ) );
		
		// Add / Delete note
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

		$note_meta = array(
			'color' 		=> $_POST['note_color'],
			'color_text' 	=> $_POST['note_color_text'],
			'visibility' 	=> $_POST['note_visibility'],
			'note_type' 	=> $_POST['note_type'],
		);
		update_post_meta( $_POST['post_id'], '_note', $note_meta );

		die();
		
	}
	
	
	public function wpdn_toggle_note() {
		
		$note = get_post( $_POST['post_id'] );
		$note_meta = get_post_meta( $note->ID, '_note', true );

		?>
		<style>
			#note_<?php echo $note->ID; ?> { background-color: <?php echo $note_meta['color']; ?>; }
			#note_<?php echo $note->ID; ?> .hndle { border: none; }
		</style>
		<?php
		if ( $_POST['note_type'] == 'regular' ) :
			require plugin_dir_path( __FILE__ ) . 'templates/note.php';
		else :
			require plugin_dir_path( __FILE__ ) . 'templates/note-list.php';		
		endif;
			
		
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
		$note_meta = array(
			'color' 		=> '#ffffff',
			'color_text' 	=> 'white',
			'visibility' 	=> 'public',
		);

		
		ob_start();
		?>
		<div id='note_<?php echo $post_id; ?>' class='postbox'>
			<div class='handlediv' title='Click to toggle'><br></div>
			<h3 class="hndle">
				<span>
					<span contenteditable="true" class="wpdn-title">New note</span>
					<div class="wpdn-edit-title dashicons dashicons-edit"></div>
				</span>
			</h3>
			
			<div class='inside'>
			<style>
				#note_<?php echo $post_id; ?> .hndle { border: none; }
			</style>
				<?php require plugin_dir_path( __FILE__ ) . 'templates/note-list.php'; ?>
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