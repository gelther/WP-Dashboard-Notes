<?php
/*
 * Plugin Name:		WP Dashboard Notes
 * Plugin URI:		http://www.jeroensormani.com
 * Donate link:		http://www.jeroensormani.com/donate/
 * Description:		Working in a team? Want to make notes? You can do just that with WP Dashboard Notes. Create beautiful notes with a nice user experience.
 * Version:			1.0.5
 * Author:			Jeroen Sormani
 * Author URI:		http://www.jeroensormani.com/
 * Text Domain:		wp-dashboard-notes
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! is_admin() ) return; // Only load plugin when user is in admin

/**
 * Class WP_Dashboard_Notes.
 *
 * Main WPDN class initializes the plugin.
 *
 * @class		WP_Dashboard_Notes
 * @version		1.0.0
 * @author		Jeroen Sormani
 */
class WP_Dashboard_Notes {


	/**
	 * Plugin version number
	 *
	 * @since 1.0.3
	 * @var string $version Plugin version number.
	 */
	public $version = '1.0.5';


	/**
	 * Plugin file.
	 *
	 * @since 1.0.0
	 * @var string $file Plugin file path.
	 */
	public $file = __FILE__;


	/**
	 * Instace of WP_Dashboard_Note.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var object $instance The instance of WP_Dashboard_Notes.
	 */
	private static $instance;


	/**
	 * @var WPDN_Ajax $ajax AJAX class.
	 */
	public $ajax;


	/**
	 * @var WPDN_Post_Type $post_type Post Type class.
	 */
	public $post_type;


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Init plugin parts
		$this->init();

		$this->hooks();

	}


	/**
	 * Instance.
	 *
	 * An global instance of the class. Used to retrieve the instance
	 * to use on other files/plugins/themes.
	 *
	 * @since 1.0.0
	 *
	 * @return object Instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) :
			self::$instance = new self();
		endif;

		return self::$instance;

	}


	/**
	 * Init.
	 *
	 * Initiate plugin parts.
	 *
	 * @since 1.0.5
	 */
	public function init() {

		/**
		 * Post type class.
		 */
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpdn-post-type.php';
		$this->post_type = new WPDN_Post_Type();

		/**
		 * AJAX class.
		 */
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpdn-ajax.php';
		$this->ajax = new WPDN_Ajax();

	}


	/**
	 * Hooks.
	 *
	 * Init actions and filters.
	 *
	 * @since 1.0.5
	 */
	public function hooks() {

		// Add dashboard widget
		add_action( 'wp_dashboard_setup', array( $this, 'init_dashboard_widget' ) );

		// Enqueue scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		// Make URLs clickable
		add_action( 'wpdn_content', array( $this, 'clickable_url' ) );

		// Add note button
		add_filter( 'manage_dashboard_columns', array( $this, 'dashboard_columns' ) );

		// Load textdomain
		load_plugin_textdomain( 'wp-dashboard-notes', false, basename( dirname( __FILE__ ) ) . '/languages' );

		add_action( 'admin_init', array( $this, 'plugin_update' ) );

	}


	/**
	 * Enqueue scripts.
	 *
	 * Enqueue Stylesheet and multiple javascripts.
	 *
	 * @since 1.0.0
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {

		if ( 'index.php' == $hook_suffix ) :

			wp_enqueue_script( 'wpdn_admin_js', plugin_dir_url( __FILE__ ) . 'assets/js/wpdn_admin.js', array( 'jquery', 'jquery-ui-sortable' ), $this->version );
			wp_enqueue_style( 'wpdn_admin_css', plugin_dir_url( __FILE__ ) . 'assets/css/wpdn_admin.css', array( 'dashicons' ), $this->version );

		endif;

	}


	/**
	 * Get notes.
	 *
	 * Returns all posts from DB with post type 'note'.
	 *
	 * @since 1.0.0
	 *
	 * @return Array List of all published notes.
	 */
	public function get_notes() {

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
	 * @param	int		$note_id	ID of the note.
	 * @return	array				Note meta.
	 */
	public static function get_note_meta( $note_id ) {

		$note_meta = get_post_meta( $note_id, '_note', true );

		if ( ! isset( $note_meta['note_type'] ) )	{ $note_meta['note_type']	= 'regular'; }
		if ( ! isset( $note_meta['color'] ) )		{ $note_meta['color']		= '#ffffff'; }
		if ( ! isset( $note_meta['visibility'] ) )	{ $note_meta['visibility']	= 'public'; }
		if ( ! isset( $note_meta['color_text'] ) )	{ $note_meta['color_text']	= 'white'; }

		return apply_filters( 'wpdn_note_meta', $note_meta );

	}


	/**
	 * Initialize dashboard notes.
	 *
	 * Get all notes and initialize dashboard widgets.
	 *
	 * @since 1.0.0
	 */
	public function init_dashboard_widget() {

		global $current_user;

		if ( $notes = $this->get_notes() ) :
			foreach ( $notes as $note ) :

				$note_meta			= $this->get_note_meta( $note->ID );
				$role_permissions	= (array) get_post_meta( $note->ID, '_role_permissions', true );
				$user				= wp_get_current_user();

				// Skip if private
				if ( 'private' == $note_meta['visibility'] && $user->ID != $note->post_author ) :
					continue;
				endif;

				// Skip if user has no permission
				if ( ! array_intersect( $role_permissions, array_keys( $current_user->caps ) ) && $user->ID != $note->post_author ) :
					continue;
				endif;

				// Allow 3rd-party skipping
				if ( apply_filters( 'wpdn_render_note', false, $note ) ) :
					continue;
				endif;

				// Add widget
				wp_add_dashboard_widget(
					'note_' . $note->ID,
					'<span contenteditable="true" class="wpdn-title">' . $note->post_title . '</span><div class="wpdn-edit-title dashicons dashicons-edit"></div><span class="status"></span>',
					array( $this, 'render_dashboard_widget' ),
					'',
					$note
				);

			endforeach;
		endif;

	}


	/**
	 * Render dashboard widget.
	 *
	 * Load data and render the widget with the right colors.
	 *
	 * @since 1.0.0
	 *
	 * @param object	$post Post object.
	 * @param array		$args Extra arguments.
	 */
	public function render_dashboard_widget( $post, $args ) {

		$note				= $args['args'];
		$role_permissions 	= (array) get_post_meta( $note->ID, '_role_permissions', true );
		$note_meta			= $this->get_note_meta( $note->ID );
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

		// Inline styling required for note depending colors.
		?><style>
			#note_<?php echo $note->ID; ?>, #note_<?php echo $note->ID; ?> .visibility-settings { background-color: <?php echo $note_meta['color']; ?>; }
			#note_<?php echo $note->ID; ?> .hndle { border: none; }
		</style><?php

		if ( $note_meta['note_type'] == 'regular' ) :
			require plugin_dir_path( __FILE__ ) . 'includes/templates/note.php';
		else :
			require plugin_dir_path( __FILE__ ) . 'includes/templates/note-list.php';
		endif;

	}


	/**
	 * Clickable URL.
	 *
	 * Filter note content to make links clickable.
	 *
	 * @since 1.0.0
	 *
	 * @param	string	$content	Original content.
	 * @return	string				Edited content.
	 */
	public function clickable_url( $content ) {
		return make_clickable( $content );
	}


	/**
	 * Add button.
	 *
	 * Adds a 'Add note' button to the 'Screen Options' tab.
	 * Triggered via jQuery.
	 *
	 * @since 1.0.0
	 *
	 * @global	object	$current_screen	Information about current screen.
	 *
	 * @param	array	$columns	Array of columns within the screen options tab.
	 * @return	array				Array of columns within the screen options tab.
	 */
	public function dashboard_columns( $columns ) {

		global $current_screen;

		if ( $current_screen->id ) :
			$columns['add_note'] = __( 'Add note', 'wp-dashboard-notes' );
		endif;

		return $columns;

	}


	public function plugin_update() {

		$db_version = get_option( 'wp_dashboard_notes_version', '1.0.0' );

		// Stop current version is up to date
		if ( $db_version >= $this->version ) :
			return;
		endif;

		// Update when version is lower than 1.0.6
		if ( version_compare( '1.0.6', $db_version ) ) :
		error_Log( 'run updaters' );
			$notes = $this->get_notes();
			foreach ( $notes as $note ) :
				$note_meta = get_post_meta( $note->ID, '_note', true );
				$role_permissions = $note_meta['visibility'] == 'private' ? array() : array_keys( get_editable_roles() );
				update_post_meta( $note->ID, '_role_permissions', $role_permissions );
			endforeach;
		endif;

		update_option( 'wp_dashboard_notes_version', $this->version );

	}


}


/**
 * The main function responsible for returning the WP_Dashboard_Notes object.
 *
 * Use this function like you would a global variable, except without needing to declare the global.
 *
 * Example: <?php WP_Dashboard_Notes()->method_name(); ?>
 *
 * @since 1.0.0
 *
 * @return object WP_Dashboard_Notes class object.
 */
if ( ! function_exists( 'WP_Dashboard_Notes' ) ) :

	function WP_Dashboard_Notes() {
		return WP_Dashboard_Notes::instance();
	}

endif;

WP_Dashboard_Notes();


// Backwards compatibility
$GLOBALS['wp_dashboard_notes'] = WP_Dashboard_Notes();
