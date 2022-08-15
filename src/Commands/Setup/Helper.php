<?php namespace Arifrh\Themes\Commands\Setup;

use CodeIgniter\CLI\CLI;

class Helper
{
    /**
     * @param string $file     source file path
     * @param array  $replaces [search => replace]
     * @param string $target   target file replaced
     */
    public static function copyAndReplaceTarget(string $file, array $replaces, string $target): void
    {
        $content = file_get_contents($file);

        $content = strtr($content, $replaces);

        self::writeFile($target, $content);
    }

    /**
     * Write a file, catching any exceptions and showing a
     * nicely formatted error.
     *
     * @param string $file Relative file path like 'Config/Auth.php'.
     */
    public static function writeFile(string $file, string $content): void
    {
        $directory = dirname($file);

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        if (file_exists($file)) {
            $overwrite = (bool) CLI::getOption('f');

            if (
                ! $overwrite
                && CLI::prompt("  File '" . $file . "' already exists in destination. Overwrite?", ['n', 'y']) === 'n'
            ) {
                CLI::error("  Skipped " . $file . ". If you wish to overwrite, please use the '-f' option or reply 'y' to the prompt.");

                return;
            }
        }

        if (write_file($file, $content)) {
            CLI::write(CLI::color('  Created: ', 'green') . $file);
        } else {
            CLI::error("  Error creating {$file}. ");
        }
    }

    /**
	 * Recursive Copy
	 *
	 * @param string $src
	 * @param string $dst
	 *
	 * @return void
	 */
	public static function recursiveCopy(string $src, string $dst)
	{
		if (! is_dir($dst))
		{ 
			mkdir($dst, 0755);
		}

		$iterator = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($src, \RecursiveDirectoryIterator::SKIP_DOTS),
			\RecursiveIteratorIterator::SELF_FIRST
		);

		foreach ($iterator as $file) 
        {
            $dir = $dst . '/' . $iterator->getSubPathName();

			if ($file->isDir())
			{
                if (! is_dir($dir))
		        { 
				    mkdir($dir);
                }
			} 
			else
			{
                if (file_exists($file)) {
                    $overwrite = (bool) CLI::getOption('f');
        
                    if (
                        ! $overwrite
                        && CLI::prompt("  File '" . $file . "' already exists in destination. Overwrite?", ['n', 'y']) === 'n'
                    ) {
                        CLI::error("  Skipped " . $file . ". If you wish to overwrite, please use the '-f' option or reply 'y' to the prompt.");
        
                        return;
                    }
                }

				copy($file, $dir);
			}
		}
	}
}
