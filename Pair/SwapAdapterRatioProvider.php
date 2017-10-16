<?php
namespace Tbbc\MoneyBundle\Pair;

use Swap\Model\CurrencyPair;
use Money\Currency;
use Money\UnknownCurrencyException;
use Swap\ProviderInterface;
use Tbbc\MoneyBundle\MoneyException;

/**
 * Class SwapAdapterRatioProvider
 * @package Tbbc\MoneyBundle\Pair
 *
 * This adapter takes Swap Provider as an input and fetches rates in format suitable for RatioProviderInterface
 * @deprecated Use for transitional period only, we should be able to use Swap Provider directly in future
 */
final class SwapAdapterRatioProvider implements RatioProviderInterface
{
    /**
     * @var ProviderInterface
     */
    private $swapProvider;

    /**
     * AdapterRatioProvider constructor.
     *
     * @param ProviderInterface $swapProvider
     */
    public function __construct(ProviderInterface $swapProvider)
    {
        $this->swapProvider = $swapProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchRatio($referenceCurrencyCode, $currencyCode)
    {
        $currencyPair = $this->getCurrencyPair($referenceCurrencyCode, $currencyCode);
        $rate = $this->swapProvider->fetchRate($currencyPair);

        return (float) $rate->getValue();
    }

    /**
     * @param string $referenceCurrencyCode
     * @param string $currencyCode
     *
     * @return CurrencyPair
     */
    private function getCurrencyPair($referenceCurrencyCode, $currencyCode)
    {
        $this->ensureValidCurrency($referenceCurrencyCode);
        $this->ensureValidCurrency($currencyCode);

        return new CurrencyPair($referenceCurrencyCode, $currencyCode);
    }

    /**
     * @param string $currencyCode
     *
     * @return Currency
     * @throws MoneyException
     */
    private function ensureValidCurrency($currencyCode)
    {
        try {
            return new Currency($currencyCode);
        } catch (UnknownCurrencyException $e) {
            throw new MoneyException(
                sprintf('The currency code %s does not exists', $currencyCode)
            );
        }
    }
}
