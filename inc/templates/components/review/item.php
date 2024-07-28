<?php
/**
 * Review Item
 */

use J7\PowerCourse\Templates\Templates;

$default_args = [
	'comment' => null,
];

$args = wp_parse_args( $args, $default_args );

[
	'comment' => $product_comment,
] = $args;

if ( ! $product_comment instanceof WP_Comment ) {
	echo '$product_comment 不是 WP_Comment 實例';
	return;
}
$comment_id      = $product_comment->comment_ID;
$rating          = \get_comment_meta( $comment_id, 'rating', true );
$user_id         = $product_comment->user_id;
$user_name       = \get_user_by( 'ID', $user_id )->display_name;
$user_avatar_url = \get_user_meta($user_id, 'user_avatar_url', true);
$user_avatar_url = $user_avatar_url ? $user_avatar_url : \get_avatar_url( $user_id  );
$comment_date    = \get_comment_date( 'Y-m-d h:i:s', $comment_id );
$comment_content = wpautop( $product_comment->comment_content );

printf(
/*html*/'
<div class="bg-gray-100 p-6 mb-2 rounded">
	<div class="flex gap-4">
		<div class="w-10">
			<img src="%1$s" alt="%2$s" class="w-10 h-10 rounded-full">
		</div>
		<div class="flex-1">
			<div class="flex justify-between text-sm">
				<div class="">%3$s</div>
				<div>%4$s</div>
			</div>
			<p class="text-gray-400 text-xs mb-4">%5$s</p>
			<div class="text-sm [&_p]:mb-0">%6$s</div>
		</div>
	</div>
</div>
',
	$user_avatar_url,
	$user_name,
	$user_name,
	Templates::get(
		'rate',
		[
			'value' => $rating,
		],
		false
		),
	$comment_date,
	$comment_content
);
