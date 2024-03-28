<?php

namespace App\Console\Commands;

use App\Models\UserToken;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class ClearOldTokens extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tokens:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear Old Access Tokens.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $updatedDate = Carbon::now()->subHour()->toDateTimeString();

        UserToken::query()
            ->where('updated_at', '<', $updatedDate)
            ->delete();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
//			['example', InputArgument::OPTIONAL, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $result = $this->fire();
        echo $result;
    }
}
