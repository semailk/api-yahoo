<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PriceService
{
    private const ONE_DAY = '1d';

    public function __construct(protected string $api_key = '', protected string $url = '', protected string $domain = '')
    {
        $this->api_key = config('services.yahoo-apiKey.key');
        $this->url = config('services.yahoo-apiKey.url');
        $this->domain = config('services.yahoo-apiKey.domain');
    }

    public function updatePriceCompany(Company $company): void
    {
        try {
            if ($company->trading_symbol) {
                $response = Http::withHeaders([
                    'x-api-key' => $this->api_key
                ])->get($this->domain . '/' . $this->url , [
                    'interval' => self::ONE_DAY,
                    'range' => self::ONE_DAY,
                    'symbols' => $company->trading_symbol
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
