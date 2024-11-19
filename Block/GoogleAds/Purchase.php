<?php

namespace Railsformers\MarketingPack\Block\GoogleAds;

use Railsformers\MarketingPack\Block\GoogleAds\Config;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\OrderFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Railsformers\MarketingPack\Helper\Data;

class Purchase extends Config
{
    protected $orderFactory;
    protected $checkoutSession;
    protected $helper;

    public function __construct(
        Context $context,
        OrderFactory $orderFactory,
        CheckoutSession $checkoutSession,
        Data $helper,
    ) {
        $this->orderFactory = $orderFactory;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($helper,$context);
    }

    public function getOrderData()
    {
        $orderId = $this->checkoutSession->getLastOrderId();
        $order = $this->orderFactory->create()->load($orderId);

        return [
            'currency' => $this->helper->getCurrencyCode(),
            'order_id' => $order->getIncrementId(),
            'order_total' => $order->getGrandTotal()
        ];
    }

    public function getBillingAddress()
    {
        $orderId = $this->checkoutSession->getLastOrderId();
        $order = $this->orderFactory->create()->load($orderId);
        return $order->getBillingAddress();
    }
}
