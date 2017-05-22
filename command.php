<?php

use Roots\Sage\Container;
use Symfony\Component\Finder\Finder;
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
	$finder = new Finder();
	$finder->files()->name('/^.+\.blade\.php$/i')->in(get_stylesheet_directory());
	$compiler = App\sage('blade')->compiler();
	foreach($finder as $file) :
		echo 'Compiling '.$file->getBasename().'...';
		$compiler->compile($file->getRealPath());
		echo "👍\n";
	endforeach;
	WP_CLI::success( "Templates rendered!" );
};
WP_CLI::add_command( 'blade compile', $compile_command );
