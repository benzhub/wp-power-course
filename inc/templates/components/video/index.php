<?php
/**
 * Video component
 */

use J7\PowerCourse\Templates\Templates;

$default_args = [
	'class'      => 'rounded-xl',
	'video_info' => [
		'type' => 'youtube',
		'id'   => '',
		'meta' => [],
	],
];

/**
 * @var array $args
 * @phpstan-ignore-next-line
 */
$args = wp_parse_args( $args, $default_args );


/**
 * @var array{type: string, id: string, meta: ?array} $video_info
 */
[
	'class'      => $class,
	'video_info'   => $video_info,
] = $args;

[
	'type' => $video_type
] = $video_info;

if ('youtube' === $video_type) {
	Templates::get(
		'video/iframe/youtube',
		[
			'video_info' => $video_info,
			'class'      => $class,
		]
		);
}

if ('vimeo' === $video_type) {
	Templates::get(
		'video/iframe/vimeo',
		[
			'video_info' => $video_info,
			'class'      => $class,
		]
		);
}

if ('bunny-stream-api' === $video_type) {
	$library_id = \get_option( 'library_id', '' );
	Templates::get(
		'video/bunny',
		[
			'library_id' => $library_id,
			'video_info' => $video_info,
			'class'      => $class,
		]
		);
}
