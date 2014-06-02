<?php
/**
 * Class Note_Post_Type
 *
 * Initialize the 'note' post type
 *
 * @class       Note_Post_Type
 * @author     	Jeroen Sormani
 * @package		WP Dashboard Notes
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Note_Post_Type {
	
	
	/**
	 * __construct functon.
	 *
	 * 
	 */
	public function __construct() {
		 
		 $this->note_register_post_type();
		 
	 }
	 
	 
	/**
	 * Register 'Note' post type
	 */
	public function note_register_post_type() {
		
		$labels = array(
		    'name' 					=> __( 'Notes', 'wp-dashboard-notes' ),
			'singular_name' 		=> __( 'Note', 'wp-dashboard-notes' ),
		    'add_new' 				=> __( 'Add New', 'wp-dashboard-notes' ),
		    'add_new_item' 			=> __( 'Add New Note' , 'wp-dashboard-notes' ),
		    'edit_item' 			=> __( 'Edit Note' , 'wp-dashboard-notes' ),
		    'new_item' 				=> __( 'New Note' , 'wp-dashboard-notes' ),
		    'view_item' 			=> __( 'View Note', 'wp-dashboard-notes' ),
		    'search_items' 			=> __( 'Search Notes', 'wp-dashboard-notes' ),
		    'not_found' 			=> __( 'No Notes', 'wp-dashboard-notes' ),
		    'not_found_in_trash'	=> __( 'No Notes found in Trash', 'wp-dashboard-notes' ),
		);

		register_post_type( 'note', array(
			'label' 				=> 'note',
			'show_ui' 				=> true,
			'show_in_menu' 			=> true,
			'capability_type' 		=> 'post',
			'map_meta_cap' 			=> true,
			'rewrite' 				=> array( 'slug' => 'notes', 'with_front' => true ),
			'_builtin' 				=> false,
			'query_var' 			=> true,
			'supports' 				=> array( 'title', 'editor' ),
			'labels' 				=> $labels,
		) );
		
	}
	
	
}

global $note_post_type;
$note_post_type = new Note_Post_Type();

