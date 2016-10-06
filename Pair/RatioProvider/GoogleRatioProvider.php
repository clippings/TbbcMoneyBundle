<?php
namespace Tbbc\MoneyBundle\Pair\RatioProvider;

use Ivory\HttpAdapter\CurlHttpAdapter;
use Swap\Provider\GoogleFinanceProvider;
use Tbbc\MoneyBundle\Pair\SwapAdapterRatioProvider;
use Tbbc\MoneyBundle\Pair\RatioProviderInterface;

/**
 * GoogleRatioProvider
 * Fetches currencies ratios from google finance currency converter
 * @deprecated Use Swap\Provider\GoogleFinanceProvider instead
 */
class GoogleRatioProvider implements RatioProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function fetchRatio($referenceCurrencyCode, $currencyCode)
    {
        $adapter = new SwapAdapterRatioProvider(
            new GoogleFinanceProvider(new CurlHttpAdapter())
        );

        return $adapter->fetchRatio($referenceCurrencyCode, $currencyCode);
    }
}
