<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Railsformers\MarketingPack\Observer\Sales;

use Railsformers\MarketingPack\Helper\Data;

class HeurekaOrderPlaceAfter implements \Magento\Framework\Event\ObserverInterface
{
    protected $helper;
    protected $scopeConfig;
    protected $cookieManager;
    protected $cookieMetadataFactory;
    protected $sessionManager;

    public function __construct(
        Data $helper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager
    )
    {
        $this->helper = $helper;
        $this->scopeConfig = $scopeConfig;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionManager = $sessionManager;
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

        $order = $observer->getEvent()->getOrder();
        $isDisabled = $this->cookieManager->getCookie('heurekaDisabled');
        file_put_contents('heureka.log', 'DISABLED: '.$isDisabled.PHP_EOL, FILE_APPEND);
        
        if (!$isDisabled){
            $customerVerificationKey = $this->helper->getOverenozakazniky();


            $itemIds = [];
            foreach ($order->getAllVisibleItems() as $item){
                $itemIds[] = 'itemId[]='.$item->getSku();
            }

            $url = 'https://www.heureka.cz/direct/dotaznik/objednavka.php?id='.$customerVerificationKey.'&email='
                .$order->getCustomerEmail()
                .'&'.join('&',$itemIds)
                .'&orderid='.$order->getIncrementId();

            //todo call url
            file_put_contents('heureka.log', 'AKTIVNI OVERENO: '.$url.PHP_EOL, FILE_APPEND);
            $resp = @file_get_contents($url);
            file_put_contents('heureka.log', $resp.PHP_EOL, FILE_APPEND);

        }else{
            file_put_contents('heureka.log', 'OVERENO DISABLED '.$order->getCustomerEmail().PHP_EOL, FILE_APPEND);
        }

        $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
            ->setDuration(3600)
            ->setPath($this->sessionManager->getCookiePath())
            ->setDomain($this->sessionManager->getCookieDomain())
            ->setHttpOnly(false);

        $this->cookieManager->setPublicCookie('heurekaDisabled','0', $publicCookieMetadata);

        //$this->cookieManager->deleteCookie('heurekaDisabled');

        // if ($this->cookieManager->getCookie('heurekaDisabled')) {
        //     $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
        //     $metadata->setPath('/');

        //     $this->cookieManager->deleteCookie(
        //         'heurekaDisabled',$metadata);
        // }
        //$this->cookieManager->deleteCookie('heurekaDisabled');

    }
}

