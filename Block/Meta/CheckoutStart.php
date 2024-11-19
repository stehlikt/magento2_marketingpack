<?php

namespace Railsformers\MarketingPack\Block\Meta;

use Railsformers\MarketingPack\Block\Meta\Config;
use Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\CategoryRepository;
use Railsformers\MarketingPack\Helper\Data;

class CheckoutStart extends Config
{
    protected $checkoutSession;
    protected $productRepository;
    protected $categoryRepository;
    protected $helper;

    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        Data $helper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->helper = $helper;
        parent::__construct($helper, $context);
    }

    public function getProductListData()
    {
        $items = $this->checkoutSession->getQuote()->getAllVisibleItems();
        $productListData = [];

        foreach ($items as $item) {
            $product = $this->productRepository->getById($item->getProductId());

            $productData = [
                'id' => $product->getSku(),
                'quantity' => $item->getQty()
            ];

            $productListData[] = $productData;
        }

        // Odeslání události pro začátek procesu checkout
        $this->helper->sendConversionEvent('InitiateCheckout', [
            'content_ids' => array_column($productListData, 'id'),
            'contents' => $productListData,
            'content_type' => 'product',
            'currency' => $this->helper->getCurrencyCode(),
            'value' => $this->checkoutSession->getQuote()->getGrandTotal()
        ]);

        return $productListData;
    }
}
