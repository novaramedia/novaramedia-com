<?php
add_action( 'init', 'novara_live_rewrites' );

function novara_live_rewrites() {
	$novara_live_cat = get_category_by_slug('novara-live');

	if ($novara_live_cat) {
		add_rewrite_rule(
			'tyskysour',
			'index.php?category_name=' . $novara_live_cat->slug,
			'top'
		);

		add_rewrite_rule(
			'novara-live',
			'index.php?category_name=' . $novara_live_cat->slug,
			'top'
		);	}
}
