<?php

namespace Railsformers\MarketingPack\Block\GoogleAnalytics;

use Railsformers\MarketingPack\Block\GoogleAnalytics\Config;
use Magento\Framework\View\Element\Template;
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
            $categories = $this->getCategoryHierarchy($product->getCategoryIds());

            $productData = [
                'item_id' => $product->getSku(),
                'item_name' => $product->getName(),
                'price' => $product->getFinalPrice(),
                'item_variant' => $this->getVariantAttribute($product),
                'quantity' => $item->getQty(),
                'discount' => $item->getDiscountAmount(),
                'coupon' => $this->checkoutSession->getQuote()->getCouponCode(),
                'categories_count' => count($categories)
            ];

            if ($product->getAttributeText('manufacturer')) {
                $productData['item_brand'] = $product->getAttributeText('manufacturer');
            }

            foreach ($categories as $index => $category) {
                $productData['item_category' . ($index + 1)] = $category;
            }

            $productListData[] = $productData;
        }

        return $productListData;
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

    protected function getVariantAttribute($product)
    {
        $variantAttributes = ['color', 'size', 'material'];
        foreach ($variantAttributes as $attribute) {
            if ($product->getData($attribute)) {
                return $product->getData($attribute);
            }
        }
        return '';
    }

    public function getCouponCode()
    {
        return $this->checkoutSession->getQuote()->getCouponCode();
    }
}
