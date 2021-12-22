<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PriceService
{
    public function updatePriceCompany(Company $company): void
    {
        $apiKey = config('services.yahoo-apiKey.key');

        try {
            if ($company->trading_symbol) {
                $response = Http::get('https://yfapi.net/v8/finance/spark?interval=1d&range=1d&symbols=' . $company->trading_symbol, [
                    'x-api-key' => $apiKey
                ]);
                $company->price = $response->json()[$company->trading_symbol]['close'][0];
                $company->save();
                Log::info('Company price updated: ' . $company->name . '.');
            }
        } catch (\Exception $exception) {
            $company->price = null;
            $company->trading_symbol = null;
            $company->save();
            Log::error('Incorrect company symbol: ' . $company->name . ' assigned to null.');
        }
    }
}
