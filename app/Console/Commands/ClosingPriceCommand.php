<?php

namespace App\Console\Commands;

use App\Models\Company;
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
        $apiKey = config('services.yahoo-apiKey.key');
        DB::table('companies')->get()
            ->each(function ($company) use ($apiKey){
                try {
                    if (!is_null($company->trading_symbol)){
                        $response = Http::withHeaders([
                            'x-api-key' => $apiKey
                        ])->get('https://yfapi.net/v8/finance/spark?interval=1d&range=1d&symbols=' . $company->trading_symbol);
                        Company::find($company->id)->update([
                            'price' => json_decode($response->body(), true)[$company->trading_symbol]['close'][0]
                        ]);
                        $this->info('Price updated for the company: ' . $company->name);
                    }
                }catch (\Exception $exception){
                    $this->error('Incorrect company symbol: ' . $company->name . ' assigned to null.');
                    Log::error('Incorrect company symbol: ' . $company->name . ' assigned to null.');
                    Company::find($company->id)->update([
                        'price' => null,
                        'trading_symbol' => null
                        ]);
                }
            });
        return 0;
    }
}
