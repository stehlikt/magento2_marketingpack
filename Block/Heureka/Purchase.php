<?php
namespace Railsformers\MarketingPack\Block\Heureka;

use Railsformers\MarketingPack\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\OrderFactory;
use Magento\Checkout\Model\Session as CheckoutSession;

class Purchase extends Template
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
        Data            $helper,
        Context         $context,
        CheckoutSession $checkoutSession,
        OrderFactory    $orderFactory
    )
    {
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
        $this->orderFactory = $orderFactory;
        parent::__construct($context);
    }

    /**
     * Get the last order data
     *
     * @return array|null
     */
    public function getOrderData()
    {
        $orderId = $this->checkoutSession->getLastOrderId();
        if ($orderId) {
            $order = $this->orderFactory->create()->load($orderId);
            $orderData = [
                'order_id' => $order->getId(),
                'order_total' => $this->getTotalOrder($order->getAllVisibleItems()),
                'currency' => $this->getCurrency(),
                'items' => []
            ];

            foreach ($order->getAllVisibleItems() as $item) {
                $orderData['items'][] = [
                    'id' => $item->getProductId(),
                    'sku' => $item->getSku(),
                    'name' => $item->getName(),
                    'quantity' => $item->getQtyOrdered(),
                    'price' => round($item->getPriceInclTax(),2),
                    'row_total' => $item->getRowTotal()
                ];
            }

            return $orderData;
        }
        return null;
    }

    /**
     * Get Heureka ID
     *
     * @return string
     */
    public function getHeurekaId()
    {
        return $this->helper->getHeurekaId();
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->helper->getCurrencyCode();
    }

    public function getTotalOrder($items)
    {
        $order_total = 0;
        foreach ($items as $item)
        {
            $order_total += $item->getPriceInclTax()*$item->getQtyOrdered();
        }

        return round($order_total, 2);
    }
}
