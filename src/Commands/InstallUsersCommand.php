<?php

namespace Laravilt\Users\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'users:install
                            {--force : Overwrite existing files}';

    /**
     * The console command description.
     */
    protected $description = 'Install Users plugin';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Installing {{ name }} plugin...');
        $this->newLine();

        // Publish config
        $this->publishConfig();


        $this->newLine();
        $this->info('✅ {{ name }} plugin installed successfully!');
        $this->newLine();

        return self::SUCCESS;
    }

    /**
     * Publish configuration file.
     */
    protected function publishConfig(): void
    {
        $this->info('Publishing configuration...');

        $params = ['--tag' => '{{ config }}-config'];

        if ($this->option('force')) {
            $params['--force'] = true;
        }

        Artisan::call('vendor:publish', $params, $this->output);
    }

}
