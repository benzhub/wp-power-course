<?php
/**
 * Video component
 */

$default_args = [
	'class'      => 'rounded-xl',
	'library_id' => '',
	'video_id'   => '',
];

/**
 * @var array $args
 * @phpstan-ignore-next-line
 */
$args = wp_parse_args( $args, $default_args );

[
	'library_id' => $library_id,
	'video_id'   => $video_id,
	'class'      => $class,
] = $args;

if ( ! $library_id || ! $video_id ) {
	printf(
		/*html*/'
	<div class="bg-primary aspect-video w-full text-white flex flex-col items-center justify-center">
		<p class="font-bold text-4xl mb-2">OOPS! 找不到影片🤯</p>
		<p class="text-base">%1$s</p>
	</div>
	',
		'缺少 ' . ( ! $library_id ? 'library_id' : 'video_id' ) . ' ，請聯絡老師'
	);

	return;
}

$base_url = "https://iframe.mediadelivery.net/embed/{$library_id}/{$video_id}";

$iframe_url = add_query_arg(
	[
		'autoplay'   => 'true',
		'loop'       => 'false',
		'muted'      => 'false',
		'preload'    => 'true',
		'responsive' => 'true',
		'controls'   => 'true',
	],
	$base_url
);

echo '<div class="relative" style="padding-top:56.25%;">';
printf(
	/*html*/'
	<iframe class="z-20 border-0 absolute top-0 left-0 w-full h-full %2$s" src="%1$s" loading="lazy"
			allow="accelerometer;gyroscope;autoplay;encrypted-media;picture-in-picture;"
			allowfullscreen="true"></iframe>
	<div class="z-10 animate-pulse aspect-video bg-gray-200 text-gray-400 tracking-widest flex items-center justify-center absolute top-0 left-0 w-full  %2$s">LOADING...</div>
			',
	$iframe_url,
	$class
);
echo '</div>';
