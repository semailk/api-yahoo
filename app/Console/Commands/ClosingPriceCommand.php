<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\PriceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ClosingPriceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'closing:price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find out the closing price';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(protected PriceService $priceService)
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
        try {
            Company::all()->each(function (Company $company){
                $this->priceService->updatePriceCompany($company);
            });
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
        return 0;
    }
}
