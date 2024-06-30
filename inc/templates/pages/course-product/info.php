<?php

use J7\PowerCourse\Templates\Templates;

/**
 * @var array $args
 */
$items = $args;

if ( ! is_array( $items ) ) {
	echo 'items 必須是陣列';
	$items = [];
}

echo '<div class="w-full grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">';
foreach ( $items as $index => $item ) :
	printf(
		'
        <div class="flex items-center gap-3">
			<div class="bg-blue-500 rounded-xl h-8 w-8 flex items-center justify-center">
		        %1$s
            </div>
            <div>
                %2$s
            </div>
            <div class="font-semibold">
                %3$s
            </div>
        </div>
        ',
		Templates::safe_get(
			'icon/' . $item['icon'],
			[
				'class' => 'h-4 w-4',
				'color' => '#ffffff',
			],
			false,
			false
		),
		$item['label'],
		$item['value']
	);
endforeach;
echo '</div>';
