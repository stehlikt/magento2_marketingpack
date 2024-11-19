<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Railsformers\MarketingPack\Observer\Sales;
use Railsformers\MarketingPack\Observer\Sales\ZboziKonverze;
use Railsformers\MarketingPack\Helper\Data;

class ZboziOrderPlaceAfter implements \Magento\Framework\Event\ObserverInterface
{
    protected $scopeConfig;
    protected $cookieManager;
    protected $cookieMetadataFactory;
    protected $sessionManager;
    protected $helper;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        Data $helper
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionManager = $sessionManager;
        $this->helper = $helper;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

        $merchantId = $this->helper->getZboziPlaceId();
        $key = $this->helper->getZboziSecretKey();

        if (!$merchantId || !$key){
            return;
        }

        $order = $observer->getEvent()->getOrder();
        $isDisabled = $this->cookieManager->getCookie('zboziDisabled');
        //file_put_contents('zbozi.log', 'DISABLED: '.$isDisabled.PHP_EOL, FILE_APPEND);

        // $cc = (string)$this->cookieManager->getCookie('cc_cookie');

        // if (!strpos($cc,'targeting')){
        //     file_put_contents('zbozi.log', 'MARKETING DISABLED but I send...: '.$order->getIncrementId().PHP_EOL, FILE_APPEND);
        //     // return;
        // }



        try {

            // inicializace
            $zbozi = new ZboziKonverze($merchantId, $key);

            // testovací režim
            //$zbozi->useSandbox(true);

            $orderParams = array(
                "orderId" => $order->getIncrementId(),
                "email" => $isDisabled ? null : $order->getCustomerEmail(),
                "deliveryType" => null,
                "deliveryPrice" => null,
                "otherCosts" => null,
                "paymentType" => null,
            );

            // if (!$isDisabled){
            //     $orderParams['email'] = $order->getCustomerEmail();
            // }

            // nastavení informací o objednávce
            $zbozi->setOrder($orderParams);

            foreach ($order->getAllVisibleItems() as $item){
                // přidáni zakoupené položky
                $zbozi->addCartItem(array(
                    "itemId" => $item->getSku(),
                    "productName" => $item->getName(),
                    "quantity" => $item->getQtyOrdered(),
                    "unitPrice" => $item->getPrice(),
                ));
            }
            file_put_contents('zbozi.log', var_export(get_object_vars($zbozi),true).PHP_EOL, FILE_APPEND);

            // odeslání
            $zbozi->send();

        } catch (ZboziKonverzeException $e) {
            // zalogování případné chyby
            file_put_contents('zbozi.log', 'ERROR: '.$e->getMessage().PHP_EOL, FILE_APPEND);
        }


    }
}

