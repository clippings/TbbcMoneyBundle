<?php

namespace Tbbc\MoneyBundle\Pair\RatioProvider;

use Ivory\HttpAdapter\CurlHttpAdapter;
use Swap\Provider\YahooFinanceProvider;
use Tbbc\MoneyBundle\Pair\SwapAdapterRatioProvider;
use Tbbc\MoneyBundle\Pair\RatioProviderInterface;

/**
 * YahooFinanceRatioProvider
 * Fetches currencies ratios from google finance currency converter.
 * @deprecated Use Swap\Provider\YahooFinanceProvider instead
 */
class YahooFinanceRatioProvider implements RatioProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function fetchRatio($referenceCurrencyCode, $currencyCode)
    {
        $adapter = new SwapAdapterRatioProvider(
            new YahooFinanceProvider(new CurlHttpAdapter())
        );

        return $adapter->fetchRatio($referenceCurrencyCode, $currencyCode);
    }
}
