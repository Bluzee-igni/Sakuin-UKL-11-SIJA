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

        $formattedNumber = number_format($amount, $decimals, $decimalSeparator, $thousandSeparator);

        if ($includeSymbol) {
            // Add space for Rp, RM, S$, A$. No space for $, €, £, ¥
            $spacedSymbols = ['Rp', 'RM', 'S$', 'A$', 'ر.س'];
            $space = in_array($symbol, $spacedSymbols) ? ' ' : '';
            return $symbol . $space . $formattedNumber;
        }

        return $formattedNumber;
    }
}
