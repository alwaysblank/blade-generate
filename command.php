<?php

use BladeGenerator\Compile;

if (! class_exists('WP_CLI')) {
    return;
}

WP_CLI::add_command('blade', 'Compile');
