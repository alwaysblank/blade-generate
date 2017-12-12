<?php namespace BladeGenerator;

use \Roots\Sage\Container;
use \Symfony\Component\Finder\Finder;
use \Illuminate\Contracts\Container\Container as ContainerContract;

class Compile extends WP_CLI_Command
{

    /**
     * Compile a single file
     */
    private function compileFile($file_path)
    {
        if (!file_exists($file_path)) :
            WP_CLI::warning(sprintf("I couldn't find `%s`!", $file_path));
            return;
        else :
            $compiler = App\sage('blade')->compiler();
                    $compiler->compile($file_path);
            unset($compiler);
            return;
        endif;
    }

    /**
     * Generates Blade templates for you.
     *
     * ## OPTIONS
     *
     * [--directory=<directory>]
     * : Specify template directory below theme root
     * (defaults to theme root)
     * ---
     * default: all
     * ---
     *
     * ## EXAMPLES
     *
     *     # Only render layouts
     *     wp blade compile --directory=views/layouts
     *
     *     # Render all tamples
     *     wp blade compile
     *
     * @when after_wp_load
     */
    public function compile($args, $assoc_args)
    {
        $sush = WP_CLI::get_config('quiet');
        $finder = new Finder();

        if (isset($assoc_args['directory']) && $assoc_args['directory'] != 'all') :
            $user_dir = trim($assoc_args['directory'], " /\\");
            $user_dir_full = trailingslashit(get_stylesheet_directory()).$user_dir;
        endif;

        if (isset($user_dir_full)) :
            if (!file_exists($user_dir_full)) :
                WP_CLI::error("Your directory `".$user_dir_full."` does not exist!");
            endif;
            $directory = $user_dir_full;
        else :
            $directory = get_stylesheet_directory();
        endif;

        WP_CLI::log('Finding files...');
        $finder->files()->name('/^.+\.blade\.php$/i')->in($directory);
        $compiler = App\sage('blade')->compiler();

        if (!$sush) :
            $progress = \WP_CLI\Utils\make_progress_bar('Compiling templates', $finder->count());
        endif;

        $list = [];
        foreach ($finder as $file) :
            $this->compileFile($file->getRealPath());
            $list[] = ['File' => $file->getBasename('.blade.php'), 'Status' => '👍'];
            if (isset($progress)) :
                $progress->tick();
            endif;
        endforeach;

        if (isset($progress)) :
            $progress->finish();
        endif;

        WP_CLI::success("Templates rendered!");

        if (!$sush) :
            WP_CLI\Utils\format_items('table', $list, array( 'File', 'Status' ));
        endif;
    }
}
