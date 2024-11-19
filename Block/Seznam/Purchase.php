<?php

namespace Railsformers\MarketingPack\Block\Seznam;

use Railsformers\MarketingPack\Block\Seznam\Config;
use Railsformers\MarketingPack\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\OrderFactory;
use Magento\Checkout\Model\Session as CheckoutSession;

class Purchase extends Config
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @param Data $helper
     * @param Context $context
     * @param CheckoutSession $checkoutSession
     * @param OrderFactory $orderFactory
     */
    public function __construct(
        Data $helper,
        Context $context,
        CheckoutSession $checkoutSession,
        OrderFactory $orderFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->orderFactory = $orderFactory;
        parent::__construct($helper,$context);
    }

    /**
     * Get the last order object
     *
     * @return \Magento\Sales\Model\Order|null
     */
    public function getLastOrder()
    {
        $orderId = $this->checkoutSession->getLastOrderId();
        if ($orderId) {
            return $this->orderFactory->create()->load($orderId);
        }
        return null;
    }
}
