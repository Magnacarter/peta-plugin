<?php
/**
 * Custom Post Types
 *
 * @package PETA\plugin\admin\custom
 * @since   1.0.0
 */
namespace PETA\plugin\admin\custom;

/**
 * Class Custom_Post_Types
 */
class Custom_Post_Types {

	/**
	 * Hold Custom_Post_Types instance
	 *
	 * @var string
	 */
	public static $instance;

	/**
	 * Class Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_website_custom_post_type' ) );
	}

	/**
	 * Get all the post type features for the given post type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_type Given post type
	 * @param array $exclude_features Array of features to exclude
	 *
	 * @return array
	 */
	public function get_all_post_type_features( $post_type = 'post', $exclude_features = array() ) {
		$configured_features = get_all_post_type_supports( $post_type );

		if ( ! $exclude_features ) {
			return array_keys( $configured_features );
		}

		$features = array();

		foreach ( $configured_features as $feature => $value ) {
			if ( in_array( $feature, $exclude_features ) ) {
				continue;
			}
			$features[] = $feature;
		}

		return $features;
	}

	/**
	 * Register the custom post type.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_website_custom_post_type() {
		$labels = array(
			'name'               => _x( 'Websites', 'post type general name', 'exercise' ),
			'singular_name'      => _x( 'Website', 'post type singular name', 'website' ),
			'menu_name'          => _x( 'Websites', 'admin menu', 'website' ),
			'name_admin_bar'     => _x( 'Website', 'add new on admin bar', 'website' ),
			'add_new'            => _x( 'Add New Website', 'team-bios', 'website' ),
			'add_new_item'       => __( 'Add New Website', 'website' ),
			'new_item'           => __( 'New Website', 'website' ),
			'edit_item'          => __( 'Edit Website', 'website' ),
			'view_item'          => __( 'View Website', 'website' ),
			'all_items'          => __( 'All Websites', 'website' ),
			'search_items'       => __( 'Search Websites', 'website' ),
			'parent_item_colon'  => __( 'Parent Websites:', 'website' ),
			'not_found'          => __( 'No Websites found.', 'website' ),
			'not_found_in_trash' => __( 'No Websites found in Trash.', 'website' ),
		);

		$features = $this->get_all_post_type_features( 'post', array(
			'excerpt',
			'comments',
			'trackbacks',
			'author',
			'revisions',
			'editor',
			'thumbnail',
			'custom-fields',
		) );

		$capabilities = array(
			'edit_post'          => 'update_core',
			'read_post'          => 'update_core',
			'delete_post'        => 'update_core',
			'edit_posts'         => 'update_core',
			'edit_others_posts'  => 'update_core',
			'delete_posts'       => 'update_core',
			'publish_posts'      => 'update_core',
			'read_private_posts' => 'update_core'
		);

		$args = array(
			'label'         => __( 'Websites', 'website' ),
			'labels'        => $labels,
			'public'        => true,
			'supports'      => $features,
			'menu_icon'     => 'dashicons-admin-page',
			'hierarchical'  => false,
			'has_archive'   => false,
			'menu_position' => 10,
			'capabilities'  => $capabilities
		);

		register_post_type( 'website', $args );
	}

	/**
	 * Return active instance of Custom_Post_Types, create one if it doesn't exist
	 *
	 * @return Custom_Post_Types
	 */
	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			$class = __CLASS__;
			self::$instance = new $class;
		}

		return self::$instance;
	}
}
Custom_Post_Types::get_instance();
