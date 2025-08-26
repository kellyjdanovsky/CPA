<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\NumberFormat;

class TestNumberFormat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:number-format';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the custom number formatting function';

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
     * @return int
     */
    public function handle()
    {
        // Test cases
        $testCases = [
            28.50,
            28.51,
            28.59,
            28.999,
            29.00,
            15.756,
            10.123456,
            5.0,
            0.1,
            0.01,
            0.001
        ];

        $this->info("Testing NumberFormat::formatWithoutRounding function:\n");

        foreach ($testCases as $number) {
            $formatted = NumberFormat::formatWithoutRounding($number, 2);
            $this->line("Input: $number -> Output: $formatted");
        }

        return 0;
    }
}