<?php
/**
 * Render Posts
 *
 * @package PETA\plugin\admin\custom
 * @since 1.0.0
 * @author PETA
 * @licence GNU-2.0+
 */
namespace PETA\plugin\admin\custom;

/**
 * Class Render Posts
 */
class Render_Posts {

	/**
	 * Hold Render_Posts instance
	 *
	 * @var string
	 */
	public static $instance;

	/**
	 * Website address
	 *
	 * @var array
	 */
	public $website_addresses;

	/**
	 * Class constructor
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ) );
		}
		add_action( 'wp_enqueue_scripts',          array( $this, 'peta_enqueue_script' ) );
		add_action( 'wp_ajax_approve_site',        array( $this, 'approve_site' ) );
		add_action( 'wp_ajax_nopriv_approve_site', array( $this, 'approve_site' ) );
	}

	/**
	 * Add a widget to the dashboard.
	 *
	 * @add_action wp_dashboard_setup
	 * @return void
	 */
	public function add_dashboard_widgets() {
		wp_add_dashboard_widget(
			'peta_dashboard_widget',                     // Widget slug.
			'PETA Dashboard Widget',                     // Title.
			array( $this, 'dashboard_widget_function')   // Display function.
		);
	}

	/**
	 * Create the function to output the contents of our Dashboard Widget.
	 *
	 * @return void
	 */
	public function dashboard_widget_function( $post, $callback_args ) {
		?>
		<div class="filter-sites">
			<form action="" method="post">
				<label>Filter Sites</label>
				<select class="form-control" name="filter_sites" onchange="this.form.submit()">
					<option>Filter Sites</option>
					<option value="https://wordpress.org/news">WordPress News</option>
					<option value="https://wptavern.com">WP Tavern</option>
					<option value="https://wpmayor.com">WP Mayor</option>
				</select>
			</form>
		</div>

		<div class="post-count">
			<form action="" method="post">
				<label>Filter Post Count</label>
				<select class="form-control" name="filter_post_count" onchange="this.form.submit()">
					<option>Filter Post Count</option>
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
					<option value="6">6</option>
					<option value="7">7</option>
					<option value="8">8</option>
					<option value="9">9</option>
					<option value="10">10</option>
				</select>
			</form>
		</div>
		</br>
		<?php

		$post_count    = isset( $_POST['filter_post_count'] ) ? $_POST['filter_post_count'] : false;
		$selected_site = isset( $_POST['filter_sites'] ) ? $_POST['filter_sites'] : false;

		$this->get_posts_via_rest( $selected_site, $post_count );
	}

	/**
	 * Get posts via REST API.
	 *
	 * @param string $selected_site
	 * @return array $all_posts
	 */
	public function get_posts_via_rest( $selected_site, $post_count ) {
		if ( $selected_site !== false ) {
			if ( $post_count !== false ) {
				$response = wp_remote_get( $selected_site . '/wp-json/wp/v2/posts?per_page=' . $post_count );
			} else {
				$response = wp_remote_get( $selected_site . '/wp-json/wp/v2/posts' );
			}
			$this->sort_response( $response );
		} else {
			$sites = array(
				'WordPress News' => 'https://wordpress.org/news',
				'WP Tavern'      => 'https://wptavern.com',
				'WP Mayor'       => 'https://wpmayor.com'
			);

			foreach ( $sites as $site_name => $response ) {
				if ( $post_count !== false ) {
					$response = wp_remote_get( $response . '/wp-json/wp/v2/posts?per_page=' . $post_count );
				} else {
					$response = wp_remote_get( $response . '/wp-json/wp/v2/posts' );
				}
				printf( '<h2>%s</h2>', esc_html( $site_name ) );
				$this->sort_response( $response, $site_name );
			}
		}
	}

	/**
	 * Sort response
	 *
	 * @return void
	 */
	public function sort_response( $response, $site_name = "" ) {
		// Exit if error.
		if ( is_wp_error( $response ) ) {
			return;
		}

		// Get the body.
		$posts = json_decode( wp_remote_retrieve_body( $response ) );

		// Exit if nothing is returned.
		if ( empty( $posts ) ) {
			return;
		}

		// If there are posts.
		if ( ! empty( $posts ) ) {

			// For each post.
			foreach ( $posts as $post ) {

				// Use print_r($post); to get the details of the post and all available fields
				// Format the date.
				$fordate = date( 'n/j/Y', strtotime( $post->modified ) );
				$approve_site = sprintf('<a data-title="%s" class="approval button button-primary" href="#">Approve</a>', esc_html( $post->title->rendered ) );

				// Show a linked title and post date.
				$all_posts .= $approve_site . ' <a href="' . esc_url( $post->link ) . '" target=\"_blank\">' . esc_html( $post->title->rendered ) . '</a> ' . esc_html( $fordate ) . '<br />';
			}

			print_r( $all_posts );
		}
	}

	/**
	 * Get user input
	 *
	 * Do secutrity checks, sanitize and escape user inputs via AJAX
	 *
	 * @since 1.0.0
	 * @return string $website_url
	 */
	public function approve_site() {
		if (
			'POST' === $_SERVER['REQUEST_METHOD']
			||
			! isset( $_POST['approve_site'] )
		) {
			$approved_site = $_POST['approve_site'];
			$current_user  = wp_get_current_user();
			$meta_value    = array( $current_user->display_name, current_time( 'timestamp', $gmt = 0 ) );
			update_option( $approved_site, $meta_value );

			$data = array(
				'approvedSite'  => $approved_site,
			);

			wp_send_json_success( $data );

		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Enqueue scripts
	 *
	 * Load jQuery and make admin-ajax.php URL accessible
	 * on the front-end.
	 *
	 * @since 1.0.0
	 * @action wp_enqueue_scripts
	 * @return void
	 */
	function peta_enqueue_script() {
		wp_localize_script( 'jquery', 'peta_enqueue_scripts',
			array(
				'ajaxurl'      => admin_url( 'admin-ajax.php' ),
				'php_callback' => __NAMESPACE__ . '\approve_site',
			)
		);
	}

	/**
	 * Return active instance of Render_Posts, create one if it doesn't exist
	 *
	 * @return Render_Posts
	 */
	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			$class = __CLASS__;
			self::$instance = new $class;
		}

		return self::$instance;
	}
}
Render_Posts::get_instance();
