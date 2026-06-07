<?php

if (!function_exists('format_currency')) {
    /**
     * Format number to user's currency.
     *
     * @param float|int $amount
     * @param bool $includeSymbol whether to include the currency symbol
     * @return string
     */
    function format_currency($amount, $includeSymbol = true)
    {
        $user = auth()->user();
        $currencyCode = $user ? $user->mata_uang : 'IDR';

        $symbols = [
            'IDR' => 'Rp',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'MYR' => 'RM',
            'SGD' => 'S$',
            'AUD' => 'A$',
            'SAR' => 'ر.س',
            'KRW' => '₩',
        ];

        $symbol = $symbols[$currencyCode] ?? 'Rp';

        // Get exchange rates from Cache or Fetch from API (Cached for 12 hours)
        $ratesFromIDR = cache()->remember('exchange_rates_idr', 43200, function () {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(5)->get('https://api.exchangerate-api.com/v4/latest/IDR');
                if ($response->successful()) {
                    $rates = $response->json('rates');
                    if ($rates && is_array($rates)) {
                        // The API returns multiplier (e.g. IDR to JPY is ~0.00886)
                        // Our logic below divides by rate ($amount / $rate)
                        // So we convert multiplier to divisor (1 / multiplier)
                        $computedRates = [];
                        foreach ($rates as $currency => $multiplier) {
                            $computedRates[$currency] = $multiplier > 0 ? (1 / $multiplier) : 1;
                        }
                        return $computedRates;
                    }
                }
            } catch (\Exception $e) {
                // Ignore exception and use fallback below
            }

            // Fallback Approximate exchange rates from IDR (Base Currency)
            return [
                'IDR' => 1,
                'USD' => 16200,
                'EUR' => 17500,
                'GBP' => 20500,
                'JPY' => 105,
                'MYR' => 3450,
                'SGD' => 12000,
                'AUD' => 10800,
                'SAR' => 4300,
                'KRW' => 12,
            ];
        });

        // Convert the amount (assuming the database stores IDR)
        $rate = $ratesFromIDR[$currencyCode] ?? 1;
        $convertedAmount = $amount / $rate;

        // Formatting rules
        // For IDR, JPY, KRW usually 0 decimals. For others, 2 decimals.
        $zeroDecimalCurrencies = ['IDR', 'JPY', 'KRW'];
        
        $decimals = in_array($currencyCode, $zeroDecimalCurrencies) ? 0 : 2;
        $decimalSeparator = in_array($currencyCode, ['EUR']) ? ',' : '.';
        $thousandSeparator = in_array($currencyCode, ['EUR']) ? '.' : ',';
        
        // IDR convention: using '.' for thousands and ',' for decimal
        if ($currencyCode === 'IDR') {
            $decimalSeparator = ',';
            $thousandSeparator = '.';
        }

        $formattedNumber = number_format($convertedAmount, $decimals, $decimalSeparator, $thousandSeparator);

        if ($includeSymbol) {
            // Add space for Rp, RM, S$, A$. No space for $, €, £, ¥
            $spacedSymbols = ['Rp', 'RM', 'S$', 'A$', 'ر.س'];
            $space = in_array($symbol, $spacedSymbols) ? ' ' : '';
            return $symbol . $space . $formattedNumber;
        }

        return $formattedNumber;
    }
}

if (!function_exists('get_currency_symbol')) {
    function get_currency_symbol() {
        $user = auth()->user();
        $currencyCode = $user ? $user->mata_uang : 'IDR';
        $symbols = [
            'IDR' => 'Rp', 'USD' => '$', 'EUR' => '€', 'GBP' => '£',
            'JPY' => '¥', 'MYR' => 'RM', 'SGD' => 'S$', 'AUD' => 'A$',
            'SAR' => 'ر.س', 'KRW' => '₩',
        ];
        return $symbols[$currencyCode] ?? 'Rp';
    }
}

if (!function_exists('convert_currency_value')) {
    function convert_currency_value($amount) {
        $user = auth()->user();
        $currencyCode = $user ? $user->mata_uang : 'IDR';
        $ratesFromIDR = cache()->get('exchange_rates_idr', [
            'IDR' => 1, 'USD' => 16200, 'EUR' => 17500, 'GBP' => 20500,
            'JPY' => 105, 'MYR' => 3450, 'SGD' => 12000, 'AUD' => 10800,
            'SAR' => 4300, 'KRW' => 12,
        ]);
        $rate = $ratesFromIDR[$currencyCode] ?? 1;
        return $amount / $rate;
    }
}

if (!function_exists('convert_to_idr')) {
    /**
     * Convert an inputted amount from the user's currency BACK to IDR before saving to the database.
     */
    function convert_to_idr($amount) {
        if (is_string($amount)) {
            // Remove thousand separators (dots/commas) and convert decimal comma to dot
            // Handles id-ID format: 1.000.000,50 → 1000000.50
            $amount = str_replace('.', '', $amount);
            $amount = str_replace(',', '.', $amount);
        }
        $amount = (float) $amount;

        $user = auth()->user();
        $currencyCode = $user ? $user->mata_uang : 'IDR';
        
        if ($currencyCode === 'IDR') return $amount;

        $ratesFromIDR = cache()->get('exchange_rates_idr', [
            'IDR' => 1, 'USD' => 16200, 'EUR' => 17500, 'GBP' => 20500,
            'JPY' => 105, 'MYR' => 3450, 'SGD' => 12000, 'AUD' => 10800,
            'SAR' => 4300, 'KRW' => 12,
        ]);
        
        $rate = $ratesFromIDR[$currencyCode] ?? 1;
        
        // Convert to IDR: IDR = Foreign * Rate
        return round($amount * $rate);
    }
}
