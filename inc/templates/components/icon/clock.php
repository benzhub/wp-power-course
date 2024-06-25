<?php
use J7\PowerCourse\Utils\Base;

$props = $args;

$default_props = [
	'type'  => '',  // '
	'class' => 'w-6 h-6',
	'color' => Base::PRIMARY_COLOR,
];

$props = array_merge( $default_props, $props );

$html = sprintf(
	'<svg class="%1$s" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
<path stroke="%2$s" d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke-width="2"/>
<path stroke="%2$s" d="M12 7L12 11.5L12 11.5196C12 11.8197 12.15 12.1 12.3998 12.2665V12.2665L15 14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>',
	$props['class'],
	$props['color']
);

echo $html;
