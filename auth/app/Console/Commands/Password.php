<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Password extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'password:encrypt {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encrypt password';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $password = $this->argument('password');

        $password = password_hash(md5($password), PASSWORD_BCRYPT);
        $this->info('Going to encode password.');
        $this->comment($password);
    }
}
