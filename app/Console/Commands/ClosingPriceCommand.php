<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\PriceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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

    protected $priceService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PriceService $priceService)
    {
        $this->priceService = $priceService;
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
            $this->priceService->updatePriceCompany();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return 1;
        }
        return 0;
    }
}
