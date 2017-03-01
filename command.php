<?php

use Roots\Sage\Container;
use Illuminate\Contracts\Container\Container as ContainerContract;

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

/**
 * Generate Blade templates.
 *
 * @when after_wp_load
 */
$compile_command = function() {
	$compiler = App\sage('blade')->compiler();
	foreach(glob(get_stylesheet_directory().'/templates/**/*.blade.php') as $file) :
		echo 'Compiling '.basename($file).'...';
		$compiler->compile($file);
		echo "👍\n";
	endforeach;
	WP_CLI::success( "Templates rendered!" );
};
WP_CLI::add_command( 'blade compile', $compile_command );
