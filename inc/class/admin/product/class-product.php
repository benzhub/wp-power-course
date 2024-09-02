<?php
/**
 * Product related features
 */

declare(strict_types=1);

namespace J7\PowerCourse\Admin;

use J7\PowerCourse\Utils\Course as CourseUtils;
use J7\PowerBundleProduct\BundleProduct;

/**
 * Class Product
 */
final class Product {
	use \J7\WpUtils\Traits\SingletonTrait;

	const PRODUCT_OPTION_NAME = 'is_course';


	/**
	 * Constructor
	 */
	public function __construct() {
		\add_filter( 'product_type_options', [ __CLASS__, 'add_product_type_options' ] );
		\add_action( 'save_post_product', [ __CLASS__, 'save_product_type_options' ], 10, 3 );
		\add_filter('post_type_link', [ __CLASS__, 'change_product_permalink' ], 10, 2);
		\add_filter( 'display_post_states', [ __CLASS__, 'custom_display_post_states' ], 10, 2 );
		\add_filter( 'post_row_actions', [ __CLASS__, 'modify_list_row_actions' ], 10, 2 );
		\add_filter('get_edit_post_link', [ __CLASS__, 'modify_edit_post_link' ], 10, 3);
		\add_filter('woocommerce_admin_html_order_item_class', [ __CLASS__, 'add_order_item_class' ], 10, 3);

		\add_action('wp', [ __CLASS__, 'redirect_to_course_page' ], 1);
	}



	/**
	 * Add product type options
	 *
	 * @param array $product_type_options - Product type options
	 *
	 * @return array
	 */
	public static function add_product_type_options( $product_type_options ): array {

		$option = self::PRODUCT_OPTION_NAME;

		$product_type_options[ $option ] = [
			'id'            => "_{$option}",
			'wrapper_class' => 'show_if_simple',
			'label'         => '課程',
			'description'   => '是否為課程商品',
			'default'       => 'no',
		];

		return $product_type_options;
	}

	/**
	 * Save product type options
	 *
	 * @param int      $post_id - Post ID
	 * @param \WP_Post $product_post - Post object
	 * @param bool     $update - Update flag
	 *
	 * @return void
	 */
	public static function save_product_type_options( $post_id, $product_post, $update ): void {
		$option = self::PRODUCT_OPTION_NAME;
		$option = "_{$option}";
		\update_post_meta( $post_id, $option, isset( $_POST[ $option ] ) ? 'yes' : 'no' ); // phpcs:ignore
	}


	/**
	 * Change product permalink
	 *
	 * @param string   $permalink - Permalink
	 * @param \WP_Post $post - Post object
	 *
	 * @return string
	 */
	public static function change_product_permalink( string $permalink, $post ): string {
		$is_course_product = CourseUtils::is_course_product( $post->ID );
		$override          = \get_option('override_course_product_permalink', 'yes') === 'yes';
		if ( $is_course_product && $override ) {
			$course_permalink_structure = \get_option('course_permalink_structure', 'courses');
			$permalink                  = str_replace('product/', "{$course_permalink_structure}/", $permalink);
		}
		return $permalink;
	}

	/**
	 * Custom display post states
	 *
	 * @param array    $post_states - Post states
	 * @param \WP_Post $post - Post object
	 *
	 * @return array
	 */
	public static function custom_display_post_states( array $post_states, $post ): array {
		if ( CourseUtils::is_course_product( $post->ID ) ) {
			$post_states['course'] = '課程商品';
		}
		if ( BundleProduct::is_bundle_product( $post->ID ) ) {
			$post_states['bundle'] = '銷售方案商品';
		}
		return $post_states;
	}

	/**
	 * Modify list row actions
	 *
	 * @param array    $actions - Actions
	 * @param \WP_Post $post - Post object
	 *
	 * @return array
	 */
	public static function modify_list_row_actions( array $actions, \WP_Post $post ): array {
		if ( CourseUtils::is_course_product( $post->ID ) || BundleProduct::is_bundle_product( $post->ID ) ) {
			unset( $actions['inline hide-if-no-js'] );
			$actions['edit'] = sprintf(
			/*html*/'<a href="%s" aria-label="編輯〈課程〉" target="_blank">編輯</a>',
			\admin_url('admin.php?page=power-course')
			);
		}

		if (BundleProduct::is_bundle_product( $post->ID ) ) {
			$course_posts = get_posts(
				[
					'post_type'   => 'product',
					'numberposts' => -1,
					'meta_key'    => 'bundle_ids',
					'meta_value'  => $post->ID,
				]
				);

			if ($course_posts) {
				$course_post     = reset($course_posts);
				$actions['view'] = sprintf(
					/*html*/'<a href="%s" rel="bookmark" target="_blank" aria-label="檢視〈商品〉">檢視</a>',
					\get_the_permalink($course_post)
				);
			} else {
				unset($actions['view']);
			}
		}

		return $actions;
	}

	/**
	 * Modify edit post link
	 *
	 * @param string $link - Link
	 * @param int    $post_id - Post ID
	 * @param string $context - Context
	 *
	 * @return string
	 */
	public static function modify_edit_post_link( string $link, int $post_id, $context ): string {
		if ( CourseUtils::is_course_product( $post_id ) || BundleProduct::is_bundle_product( $post_id ) ) {
			$link = \admin_url('admin.php?page=power-course');
		}

		return $link;
	}

	/**
	 * 針對課程商品 || 銷售方案商品在訂單 detail 添加額外的 class
	 * 不然 WC 會把編輯的連結顯示在畫面上
	 *
	 * @param string                 $class - Class
	 * @param \WC_Order_Item_Product $item - Order item
	 * @param \WC_Order              $order - Order
	 *
	 * @return string
	 */
	public static function add_order_item_class( string $class, \WC_Order_Item_Product $item, \WC_Order $order ): string {
		$product_id = $item->get_product_id();
		if ( CourseUtils::is_course_product( $product_id ) || BundleProduct::is_bundle_product( $product_id ) ) {
			$class .= ' [&_.wc-order-item-name]:pointer-events-none';
		}

		return $class;
	}

	/**
	 * 如果是課程商品，就導向課程銷售頁面
	 */
	public static function redirect_to_course_page(): void {

		if (!is_product()) {
			return;
		}

		$override = \get_option('override_course_product_permalink', 'yes') === 'yes';

		if (!$override) {
			return;
		}

		global $wp_query;
		/**
		 * @var \WP_Post $product_post
		 */
		$product_post = $wp_query->get_queried_object();

		$is_course_product = CourseUtils::is_course_product($product_post->ID);

		if (!$is_course_product) {
			return;
		}

		$course_permalink_structure = \get_option('course_permalink_structure', 'courses');

		\wp_safe_redirect( site_url( "{$course_permalink_structure}/{$product_post->post_name}" ) );
		exit;
	}
}

Product::instance();
