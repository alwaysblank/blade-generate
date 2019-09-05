<?php

use Roots\Sage\Container;
use Symfony\Component\Finder\Finder;
use Illuminate\Filesystem\Filesystem;

if (! class_exists('WP_CLI')) {
    return;
}

class BladeCommand extends WP_CLI_Command
{

	/**
	 * Checks for the existence of Sage, and stops immediately if not found.
	 *
	 * @throws \WP_CLI\ExitException
	 */
	private function checkForSage()
	{
		if (!function_exists("App\\sage")) {
			WP_CLI::error("You don't appear to have installed or activated Sage 9!");
		}
	}

    /**
     * Determines which directory to parse for Blade templates.
     *
     * @param str $directory
     * @return str
     */
    private function determineDirectory($argument = false)
    {
        $user_dir_full = false;

        // If we were passed a directory, process it a little bit.
        if ($argument && $argument != 'all') :
            $user_dir = trim($argument, " /\\");
            $user_dir_full = trailingslashit(get_stylesheet_directory()).$user_dir;
        endif;

        // Return the user directory, if set.
        // Otherwise, return the default directory.
        if ($user_dir_full) :
            if (!file_exists($user_dir_full)) :
                WP_CLI::error("Your directory `".$user_dir_full."` does not exist!");
            endif;
            $directory = $user_dir_full;
        else :
            $directory = get_stylesheet_directory();
        endif;

        return $directory;
    }

    /**
     * Compile a single file
     */
    private function compileFile($file_path)
    {
	    $this->checkForSage();
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
     *     # Render all templates
     *     wp blade compile
     *
     * @when after_wp_load
     */
    public function compile($args, $assoc_args)
    {
    	$this->checkForSage();
        $sush = WP_CLI::get_config('quiet');
        $finder = new Finder();

        $directory = $this->determineDirectory(isset($assoc_args['directory']) ? $assoc_args['directory'] : false);

        WP_CLI::log('Finding files...');
        $finder->files()->name('/^.+\.blade\.php$/i')->in($directory);
        $compiler = App\sage('blade')->compiler();

        if (!$sush) :
            $progress = \WP_CLI\Utils\make_progress_bar('Compiling templates', $finder->count());
        endif;

        $list = [];
        foreach ($finder as $file) :
            $this->compileFile($file->getRealPath());
            $list[] = [
                'File' => str_replace([$directory.'/', '.blade.php'], '', $file->getRealPath()),
                'Status' => '👍',
            ];
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

    /**
     * Clears cached files for your Blades.
     *
     * NOTE: This removes the cached files _for the Blades it finds_
     * it does _not_ remove all cached files. For that use, `wp blade wipe`.
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
     *     # Only clear layouts
     *     wp blade clear --directory=views/layouts
     *
     *     # Clear all templates
     *     wp blade clear
     *
     * @when after_wp_load
     *
     */
    public function clear($args, $assoc_args)
    {
	    $this->checkForSage();
        $finder = new Finder();
        $blade = App\sage('blade');
        $filesystem = new Filesystem();

        $directory = $this->determineDirectory(isset($assoc_args['directory']) ? $assoc_args['directory'] : false);

        $finder->files()->name('/^.+\.blade\.php$/i')->in($directory);

        $cache_files = [];
        foreach ($finder as $file) {
            $cache_files[] = $blade->compiledPath($file->getRealPath());
        }

        $success = $filesystem->delete($cache_files);

        if ($success) {
            WP_CLI::success("Templates cleared!");
        } else {
            WP_CLI::error("Uh-oh, something went wrong!");
        }
    }

    /**
     * Clear out all files in cache directory.
     *
     * @when after_wp_load
     *
     */
    public function wipe()
    {
	    $this->checkForSage();
        $finder = new Finder();
        $filesystem = new Filesystem();
        $all_files = array_map(function ($file) {
            return $file->getRealPath();
        }, iterator_to_array($finder->files()->in(App\config('view.compiled'))));

        $success = $filesystem->delete($all_files);

        if ($success) {
            WP_CLI::success(sprintf("All files in `%s` removed!", App\config('view.compiled')));
        } else {
            WP_CLI::error("Uh-oh, something went wrong!");
        }
    }
}
WP_CLI::add_command('blade', 'BladeCommand');
