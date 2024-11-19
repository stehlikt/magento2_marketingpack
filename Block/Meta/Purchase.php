<?php

namespace Railsformers\MarketingPack\Block\Meta;

use Railsformers\MarketingPack\Block\Meta\Config;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\OrderFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Session as CheckoutSession;
use Railsformers\MarketingPack\Helper\Data;

class Purchase extends Config
{
    protected $orderFactory;
    protected $productRepository;
    protected $checkoutSession;
    protected $helper;

    public function __construct(
        Context $context,
        OrderFactory $orderFactory,
        ProductRepository $productRepository,
        CheckoutSession $checkoutSession,
        Data $helper,
        array $data = []
    ) {
        $this->orderFactory = $orderFactory;
        $this->productRepository = $productRepository;
        $this->checkoutSession = $checkoutSession;
        $this->helper = $helper;
        parent::__construct($helper, $context);
    }

    public function getOrderData()
    {
        $orderId = $this->checkoutSession->getLastOrderId();
        $order = $this->orderFactory->create()->load($orderId);
        $items = $order->getAllVisibleItems();
        $orderData = [
            'transaction_id' => $order->getIncrementId(),
            'value' => $order->getGrandTotal(),
            'currency' => $order->getOrderCurrencyCode(),
            'items' => []
        ];

        $contentIds = [];
        foreach ($items as $item) {
            $product = $this->productRepository->getById($item->getProductId());

            $productData = [
                'id' => $product->getSku(),
                'quantity' => $item->getQtyOrdered()
            ];

            $orderData['items'][] = $productData;
            $contentIds[] = $product->getSku();
        }

        $customerEmail = $order->getCustomerEmail();
        $customerPhone = $order->getBillingAddress()->getTelephone();

        $customerData = [];
        if ($customerEmail) {
            $customerData['em'] = hash('sha256', $customerEmail);
        }

        if ($customerPhone) {
            $customerData['ph'] = hash('sha256', $customerPhone);
        }

        $this->helper->sendConversionEvent('Purchase', [
            'content_ids' => $contentIds,
            'contents' => $orderData['items'],
            'content_type' => 'product',
            'currency' => $order->getOrderCurrencyCode(),
            'value' => $order->getGrandTotal(),
            'transaction_id' => $order->getIncrementId()
        ],$customerData);

        return $orderData;
    }
}
