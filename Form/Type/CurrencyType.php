<?php

namespace Tbbc\MoneyBundle\Form\Type;

use Money\Currency;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tbbc\MoneyBundle\Form\DataMapper\CurrencyDataMapper;

/**
 * Form type for the Currency object.
 */
class CurrencyType extends AbstractType
{
    /** @var  array of string (currency code like "USD", "EUR") */
    protected $currencyCodeList;
    /** @var  string (currency code like "USD", "EUR") */
    protected $referenceCurrencyCode;

    /**
     * CurrencyType constructor.
     *
     * @param array  $currencyCodeList
     * @param string $referenceCurrencyCode
     */
    public function __construct($currencyCodeList, $referenceCurrencyCode)
    {
        $this->currencyCodeList = $currencyCodeList;
        $this->referenceCurrencyCode = $referenceCurrencyCode;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choiceList = array();
        foreach ($options["currency_choices"] as $currencyCode) {
            $choiceList[$currencyCode] = $currencyCode;
        }

        $builder->add('tbbc_name', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            "choices" => $choiceList,
            "preferred_choices" => array($options["reference_currency"]),
            "placeholder" => $options['placeholder'],
            'empty_data' => function (FormInterface $form) use ($options) {
                //Use "empty_data" option from parent and convert it for child
                if (is_callable($options['empty_data'])) {
                    $currency = call_user_func($options['empty_data'], $form);
                } else {
                    $currency =  $options['empty_data'];
                }

                if ($currency === null) {
                    return null;
                }

                if (!$currency instanceof Currency) {
                    throw new UnexpectedTypeException($currency, 'Currency');
                }

                return $currency->getName();
            },
        ));

        $builder->setDataMapper(new CurrencyDataMapper());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(array('reference_currency', 'currency_choices'));
        $resolver->setDefaults(array(
            'data_class' => 'Money\Currency',
            'reference_currency' => $this->referenceCurrencyCode,
            'currency_choices' => $this->currencyCodeList,
            'empty_data' => null,
            'placeholder' => null,
        ));
        $resolver->setAllowedTypes('reference_currency', 'string');
        $resolver->setAllowedTypes('currency_choices', 'array');
        $resolver->setAllowedValues('reference_currency', $this->currencyCodeList);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tbbc_currency';
    }
}
