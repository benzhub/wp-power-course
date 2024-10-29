<?php
/**
 * 帶有價格，用於銷售用的卡片
 */

use J7\PowerCourse\Plugin;
use J7\PowerCourse\Utils\Base;
use J7\PowerCourse\Utils\Course as CourseUtils;
use J7\PowerCourse\Utils\AVLCourseMeta;
use J7\PowerCourse\Utils\User as UserUtils;

$default_args = [
	'product' => $GLOBALS['product'] ?? null,
];

/**
 * @var array $args
 * @phpstan-ignore-next-line
 */
$args = wp_parse_args( $args, $default_args );

[
	'product' => $product,
] = $args;

if ( ! ( $product instanceof \WC_Product ) ) {
	throw new \Exception( 'product 不是 WC_Product' );
}

$product_id  = $product->get_id();
$chapter_ids = CourseUtils::get_sub_chapters($product_id, return_ids :true);

$name              = $product->get_name();
$product_image_url = Base::get_image_url_by_product( $product, 'full' );
$teacher_ids       = \get_post_meta( $product_id, 'teacher_ids', false );
$teacher_name      = 'by ';
foreach ( $teacher_ids as $key => $teacher_id ) {
	$is_last       = $key === count( $teacher_ids ) - 1;
	$connect       = $is_last ? '' : ' & ';
	$teacher       = \get_user_by( 'id', $teacher_id );
	$teacher_name .= $teacher->display_name . $connect;
}
$teacher_name = count($teacher_ids) > 0 ? $teacher_name : '&nbsp;';

$current_user_id   = get_current_user_id();
$limit_labels      = CourseUtils::get_limit_label_by_product( $product );
$expire_date       = AVLCourseMeta::get( $product_id, $current_user_id, 'expire_date', true );
$expire_date_label = empty($expire_date) ? '無限期' : '至' . \wp_date('Y/m/d H:i', $expire_date);
$is_expired        = CourseUtils::is_expired($product, $current_user_id);
$avl_status        = CourseUtils::get_avl_status($product, $current_user_id);


// 標籤顯示
$is_popular  = \get_post_meta( $product_id, 'is_popular', true ) === 'yes';
$is_featured = \get_post_meta( $product_id, 'is_featured', true ) === 'yes';

$tags_html = '<div class="flex gap-2 items-center my-2 h-6">';
if ($is_popular) {
	$tags_html .= Plugin::get('badge/popular', null, false);
}
if ($is_featured) {
	$tags_html .= Plugin::get('badge/feature', null, false);
}

if (!$is_popular && !$is_featured) {
	$tags_html .= Plugin::get('badge/join', null, false);
}

$tags_html .= '</div>';

// 課程時長
$course_hour        = (int) $product->get_meta( 'course_hour' );
$course_minute      = (int) $product->get_meta( 'course_minute' );
$course_length      = "{$course_hour} 小時 {$course_minute} 分";
$course_length_html = Plugin::get('icon/clock', null, false) . $course_length;

// 學員人數
$total_student      = ( UserUtils::count_student( $product->get_id() ) ) + ( (int) $product->get_meta( 'extra_student_count' ) );
$total_student_html = Plugin::get('icon/team', null, false) . $total_student;

printf(
	/*html*/'
<div class="pc-course-card">
	<a href="%1$s">
		<div class="pc-course-card__image-wrap pc-course-card__image-wrap-product group mb-0">
			<img class="pc-course-card__image group-hover:scale-110 transition duration-300 ease-in-out" src="%2$s" alt="%3$s" loading="lazy">
	  </div>
  </a>
	%4$s
	<a href="%1$s">
		<h3 class="pc-course-card__name">%3$s</h3>
	</a>
	<p class="pc-course-card__teachers !mb-4">%5$s</p>
	<div class="pc-course-card__price">%6$s</div>
	<div class="flex gap-2 items-center justify-between border-y border-x-0 border-solid border-gray-300 py-2 mt-2">
		<div class="text-gray-800 text-xs font-semibold flex items-center gap-1 [&_svg]:w-3.5 [&_svg]:h-3.5 [&_svg_path]:stroke-gray-400">%7$s</div>
		<div class="text-gray-800 text-xs font-semibold flex items-center gap-1 [&_svg]:w-3.5 [&_svg]:h-3.5 [&_svg]:fill-gray-400">%8$s</div>
	</div>
</div>
',
$product->get_permalink(),
	$product_image_url,
	$name,
	$tags_html,
	$teacher_name,
	Base::get_price_html($product),
	$course_length_html,
	$total_student_html,
);
