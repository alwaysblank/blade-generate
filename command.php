<?php

use Roots\Sage\Container;
use Illuminate\Contracts\Container\Container as ContainerContract;

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

function rsearch($folder, $pattern) {
    $dir = new RecursiveDirectoryIterator($folder);
    $ite = new RecursiveIteratorIterator($dir);
    $files = new RegexIterator($ite, $pattern, RegexIterator::GET_MATCH);
    $fileList = array();
    foreach($files as $file) {
        $fileList = array_merge($fileList, $file);
    }
    return $fileList;
}

/**
 * Generate Blade templates.
 *
 * @when after_wp_load
 */
$compile_command = function() {
	$compiler = App\sage('blade')->compiler();
	foreach(rsearch(get_stylesheet_directory().'/resources/views','/^.+\.blade\.php$/i') as $file) :
		echo 'Compiling '.basename($file).'...';
		$compiler->compile($file);
		echo "👍\n";
	endforeach;
	WP_CLI::success( "Templates rendered!" );
};
WP_CLI::add_command( 'blade compile', $compile_command );
