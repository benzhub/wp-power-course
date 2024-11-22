<?php
/**
 * Custom Post Type: Email
 */

declare( strict_types=1 );

namespace J7\PowerCourse\PowerEmail\Resources\Email;

/**
 * Class CPT
 */
final class CPT {
	use \J7\WpUtils\Traits\SingletonTrait;

	public const POST_TYPE = 'pe_email';

	public const AS_HOOK = 'as_power_email_email';

	/**
	 * Email headers
	 * used in wp_mail
	 *
	 * @var array
	 */
	public static $email_headers = [ 'Content-Type: text/html; charset=UTF-8' ];

	/**
	 * Constructor
	 */
	public function __construct() {
		\add_action( 'init', [ $this, 'init' ] );
		\add_action( 'power_email_after_send_email', [ $this, 'record_user_id_after_send_email' ], 10, 3 );
	}



	/**
	 * Initialize
	 */
	public function init(): void {
		$this->register_cpt();
	}

	/**
	 * Register power-email custom post type
	 */
	public static function register_cpt(): void {
		$labels = [
			'name'                     => \esc_html__( 'Emails', 'power-email' ),
			'singular_name'            => \esc_html__( 'Email', 'power-email' ),
			'add_new'                  => \esc_html__( 'Add new', 'power-email' ),
			'add_new_item'             => \esc_html__( 'Add new item', 'power-email' ),
			'edit_item'                => \esc_html__( 'Edit', 'power-email' ),
			'new_item'                 => \esc_html__( 'New', 'power-email' ),
			'view_item'                => \esc_html__( 'View', 'power-email' ),
			'view_items'               => \esc_html__( 'View', 'power-email' ),
			'search_items'             => \esc_html__( 'Search power-email', 'power-email' ),
			'not_found'                => \esc_html__( 'Not Found', 'power-email' ),
			'not_found_in_trash'       => \esc_html__( 'Not found in trash', 'power-email' ),
			'parent_item_colon'        => \esc_html__( 'Parent item', 'power-email' ),
			'all_items'                => \esc_html__( 'All', 'power-email' ),
			'archives'                 => \esc_html__( 'Email archives', 'power-email' ),
			'attributes'               => \esc_html__( 'Email attributes', 'power-email' ),
			'insert_into_item'         => \esc_html__( 'Insert to this power-email', 'power-email' ),
			'uploaded_to_this_item'    => \esc_html__( 'Uploaded to this power-email', 'power-email' ),
			'featured_image'           => \esc_html__( 'Featured image', 'power-email' ),
			'set_featured_image'       => \esc_html__( 'Set featured image', 'power-email' ),
			'remove_featured_image'    => \esc_html__( 'Remove featured image', 'power-email' ),
			'use_featured_image'       => \esc_html__( 'Use featured image', 'power-email' ),
			'menu_name'                => \esc_html__( 'Email', 'power-email' ),
			'filter_items_list'        => \esc_html__( 'Filter power-email list', 'power-email' ),
			'filter_by_date'           => \esc_html__( 'Filter by date', 'power-email' ),
			'items_list_navigation'    => \esc_html__( 'Email list navigation', 'power-email' ),
			'items_list'               => \esc_html__( 'Email list', 'power-email' ),
			'item_published'           => \esc_html__( 'Email published', 'power-email' ),
			'item_published_privately' => \esc_html__( 'Email published privately', 'power-email' ),
			'item_reverted_to_draft'   => \esc_html__( 'Email reverted to draft', 'power-email' ),
			'item_scheduled'           => \esc_html__( 'Email scheduled', 'power-email' ),
			'item_updated'             => \esc_html__( 'Email updated', 'power-email' ),
		];

		$args = [
			'label'                 => \esc_html__( 'Email', 'power-email' ),
			'labels'                => $labels,
			'description'           => '',
			'public'                => false,
			'hierarchical'          => true,
			'exclude_from_search'   => true,
			'publicly_queryable'    => WP_DEBUG,
			'show_ui'               => WP_DEBUG,
			'show_in_nav_menus'     => WP_DEBUG,
			'show_in_admin_bar'     => WP_DEBUG,
			'show_in_rest'          => true,
			'can_export'            => true,
			'delete_with_user'      => false,
			'has_archive'           => false,
			'rest_base'             => '',
			'show_in_menu'          => WP_DEBUG,
			'menu_position'         => 6,
			'menu_icon'             => 'dashicons-email-alt',
			'capability_type'       => 'post',
			'supports'              => [ 'title', 'editor', 'thumbnail', 'custom-fields' ],
			'taxonomies'            => [],
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'rewrite'               => [
				'with_front' => true,
			],
		];

		\register_post_type( self::POST_TYPE, $args );
	}

	/**
	 * After send email
	 *
	 * @param Email $email 信件
	 * @param int   $user_id 使用者 ID
	 * @param int   $course_id 課程 ID
	 */
	public function record_user_id_after_send_email( Email $email, int $user_id, int $course_id ): void {
		$sent_user_ids = \get_post_meta( $email->id, 'sent_user_ids', true );
		if (!is_array($sent_user_ids)) {
			$sent_user_ids = [];
		}
		$in_array = \in_array( $user_id, $sent_user_ids );
		if ( $in_array ) {
			return;
		}

		\update_post_meta(
			$email->id,
			'sent_user_ids',
			[
				...$sent_user_ids,
				$user_id,
			]
			);
	}
}
