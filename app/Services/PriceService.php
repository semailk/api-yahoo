<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PriceService
{
    public function updatePriceCompany(): void
    {
        $apiKey = config('services.yahoo-apiKey.key');
        Company::all()
            ->each(function (Company $company) use ($apiKey) {
                try {
                    if (!is_null($company->trading_symbol)) {
                        $response = Http::withHeaders([
                            'x-api-key' => $apiKey
                        ])->get('https://yfapi.net/v8/finance/spark?interval=1d&range=1d&symbols=' . $company->trading_symbol);
                        $company->price = $response->json()[$company->trading_symbol]['close'][0];
                        $company->save();
                        Log::info('Company price updated: ' . $company->name . '.');
                    }
                } catch (\Exception $exception) {
                    Log::error('Incorrect company symbol: ' . $company->name . ' assigned to null.');
                    $company->price = null;
                    $company->trading_symbol = null;
                }
            });
    }
}
