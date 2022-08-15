<?php

namespace Arifrh\Themes\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Commands\Database\Migrate;
use Arifrh\Themes\Commands\Setup\Helper;
use Config\Services;

class Setup extends BaseCommand
{
    /**
     * The group the command is lumped under
     * when listing commands.
     *
     * @var string
     */
    protected $group = 'Themes';

    /**
     * The Command's name
     *
     * @var string
     */
    protected $name = 'themes:setup';

    /**
     * the Command's short description
     *
     * @var string
     */
    protected $description = 'Initial setup for CI4 Themes.';

    /**
     * the Command's usage
     *
     * @var string
     */
    protected $usage = 'themes:setup';

    /**
     * the Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * the Command's Options
     *
     * @var array
     */
    protected $options = [
        '-f' => 'Force overwrite ALL existing files in destination.',
    ];

    /**
     * The path to `CodeIgniter\Shield\` src directory.
     *
     * @var string
     */
    protected $sourcePath;

    protected $viewPath = APPPATH . 'Views';

    protected $publicPath = FCPATH;

    /**
     * Displays the help for the spark cli script itself.
     */
    public function run(array $params): void
    {
        $this->sourcePath = __DIR__ . '/../';

        $this->publishConfigThemes();
        $this->publishDefaultThemes();
        $this->runMigrations();
    }

    private function publishConfigThemes(): void
    {
        $file     = 'Config/Themes.php';
        $replaces = [
            'namespace Arifrh\\Themes\\Config'     => 'namespace Config',
            'use CodeIgniter\\Config\\BaseConfig;' => 'use Arifrh\\Themes\\Config\\Themes as ThemesConfig;',
            'extends BaseConfig'                   => 'extends ThemesConfig',
        ];

        Helper::copyAndReplaceTarget($this->sourcePath . $file, $replaces, APPPATH .  $file);
    }

    private function publishDefaultThemes(): void
    {
        Helper::recursiveCopy("{$this->sourcePath}/Views/themes", "{$this->viewPath}/themes");
        Helper::recursiveCopy("{$this->sourcePath}/public/themes", "{$this->publicPath}/themes");
    }

    private function runMigrations(): void
    {
        if (
            $this->cliPrompt('  Run `spark migrate --all` now?', ['y', 'n']) === 'n'
        ) {
            return;
        }

        $command = new Migrate(Services::logger(), Services::commands());
        $command->run(['all' => null]);
    }

    /**
     * This method is for testing.
     */
    protected function cliPrompt(string $field, array $options): string
    {
        return CLI::prompt($field, $options);
    }
}
