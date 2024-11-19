<?php

namespace Railsformers\MarketingPack\Block\GoogleAnalytics;

use Railsformers\MarketingPack\Block\GoogleAnalytics\Config;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\OrderFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Checkout\Model\Session as CheckoutSession;
use Railsformers\MarketingPack\Helper\Data;

class Purchase extends Config
{
    protected $orderFactory;
    protected $productRepository;
    protected $categoryRepository;
    protected $checkoutSession;
    protected $helper;

    public function __construct(
        Context $context,
        OrderFactory $orderFactory,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        CheckoutSession $checkoutSession,
        Data $helper,
    ) {
        $this->orderFactory = $orderFactory;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($helper ,$context);
    }

    public function getOrderData()
    {
        $orderId = $this->checkoutSession->getLastOrderId();
        $order = $this->orderFactory->create()->load($orderId);
        $items = $order->getAllVisibleItems();
        $orderData = [
            'transaction_id' => $order->getIncrementId(),
            'value' => $order->getGrandTotal(),
            'tax' => $order->getTaxAmount(),
            'shipping' => $order->getShippingAmount(),
            'currency' => $order->getOrderCurrencyCode(),
            'coupon' => $order->getCouponCode(),
            'items' => []
        ];

        foreach ($items as $item) {
            $product = $this->productRepository->getById($item->getProductId());
            $categories = $this->getCategoryHierarchy($product->getCategoryIds());

            $productData = [
                'item_id' => $product->getSku(),
                'item_name' => $product->getName(),
                'price' => $item->getPrice(),
                'quantity' => $item->getQtyOrdered(),
                'discount' => $item->getDiscountAmount(),
                'coupon' => $order->getCouponCode(),
                'index' => $item->getId(), // Můžete upravit podle potřeby
                'categories_count' => count($categories)
            ];

            if ($product->getAttributeText('manufacturer')) {
                $productData['item_brand'] = $product->getAttributeText('manufacturer');
            }

            foreach ($categories as $index => $category) {
                $productData['item_category' . ($index + 1)] = $category;
            }

            $orderData['items'][] = $productData;
        }

        return $orderData;
    }

    protected function getCategoryHierarchy($categoryIds)
    {
        $categories = [];
        foreach ($categoryIds as $categoryId) {
            $category = $this->categoryRepository->get($categoryId);
            $categories[] = $category->getName();
        }
        return $categories;
    }
}
