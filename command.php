<?php

use Roots\Sage\Container;
use Illuminate\Contracts\Container\Container as ContainerContract;

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

/**
 * Says "Hello World" to new users
 *
 * @when after_wp_load
 */
$hello_world_command = function() {
	var_dump(get_class_methods(App\sage('blade')));
	var_dump(App\sage('blade')->compiler()->compile(get_stylesheet_directory().'/templates/single.blade.php'));
	WP_CLI::success( "HEY THERE PAL" );
};
WP_CLI::add_command( 'hello-world', $hello_world_command );
