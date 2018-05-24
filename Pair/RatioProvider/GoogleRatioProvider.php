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
        $isoCurrencies = new ISOCurrencies();

        $baseCurrency = new Currency($referenceCurrencyCode);
        if (!$baseCurrency->isAvailableWithin($isoCurrencies)) {
            throw new MoneyException(
                sprintf('The currency code %s does not exists', $referenceCurrencyCode)
            );
        }

        $currency = new Currency($currencyCode);
        if (!$currency->isAvailableWithin($isoCurrencies)) {
            throw new MoneyException(
                sprintf('The currency code %s does not exists', $currencyCode)
            );
        }

        $baseUnits = 1000;
        $endpoint = $this->getEndpoint($baseUnits, $baseCurrency, $currency);
        $responseString = file_get_contents($endpoint);
        $convertedAmount = $this->getConvertedAmountFromResponse($responseString);
        $ratio = $convertedAmount / $baseUnits;

        return $ratio;
    }

    /**
     * @param string   $units
     * @param Currency $referenceCurrency
     * @param Currency $currency
     * @return string The endpoint to get Currency conversion
     */
    protected function getEndpoint($units, Currency $referenceCurrency, Currency $currency)
    {
        return sprintf(
            'https://finance.google.com/bctzjpnsun/converter?a=%s&from=%s&to=%s',
            $units,
            $referenceCurrency->getCode(),
            $currency->getCode()
        );
    }

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
