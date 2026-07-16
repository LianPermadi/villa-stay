<?php

namespace App\Support;

final class Currency
{
    public const DEFAULT = 'IDR';

    private const DEFINITIONS = [
        'IDR' => ['name' => 'Rupiah Indonesia', 'symbol' => 'Rp', 'decimals' => 0],
        'USD' => ['name' => 'Dolar Amerika Serikat / Timor-Leste', 'symbol' => '$', 'decimals' => 2],
        'SGD' => ['name' => 'Dolar Singapura', 'symbol' => 'S$', 'decimals' => 2],
        'MYR' => ['name' => 'Ringgit Malaysia', 'symbol' => 'RM', 'decimals' => 2],
        'THB' => ['name' => 'Baht Thailand', 'symbol' => '฿', 'decimals' => 2],
        'PHP' => ['name' => 'Peso Filipina', 'symbol' => '₱', 'decimals' => 2],
        'VND' => ['name' => 'Dong Vietnam', 'symbol' => '₫', 'decimals' => 0],
        'BND' => ['name' => 'Dolar Brunei', 'symbol' => 'B$', 'decimals' => 2],
        'KHR' => ['name' => 'Riel Kamboja', 'symbol' => '៛', 'decimals' => 0],
        'LAK' => ['name' => 'Kip Laos', 'symbol' => '₭', 'decimals' => 0],
        'MMK' => ['name' => 'Kyat Myanmar', 'symbol' => 'K', 'decimals' => 0],
    ];

    public static function codes(): array
    {
        return array_keys(self::DEFINITIONS);
    }

    public static function options(): array
    {
        return collect(self::DEFINITIONS)
            ->mapWithKeys(fn (array $definition, string $code) => [
                $code => "{$code} — {$definition['name']}",
            ])
            ->all();
    }

    public static function symbol(?string $currency): string
    {
        return self::DEFINITIONS[$currency ?? self::DEFAULT]['symbol'] ?? ($currency ?: self::DEFAULT);
    }

    public static function decimals(?string $currency): int
    {
        return self::DEFINITIONS[$currency ?? self::DEFAULT]['decimals'] ?? 2;
    }

    public static function format(int|float|string|null $amount, ?string $currency = null): string
    {
        $currency = strtoupper($currency ?: self::DEFAULT);
        $decimals = self::decimals($currency);

        return self::symbol($currency).' '.number_format((float) ($amount ?? 0), $decimals, ',', '.');
    }
}
