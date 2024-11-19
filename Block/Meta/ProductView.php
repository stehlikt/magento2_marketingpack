<?php

namespace Railsformers\MarketingPack\Block\Meta;

use Railsformers\MarketingPack\Block\Meta\Config;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Registry;
use Magento\Catalog\Model\CategoryRepository;
use Railsformers\MarketingPack\Helper\Data;

class ProductView extends Config
{
    protected $customerSession;
    protected $registry;
    protected $categoryRepository;
    protected $helper;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        Registry $registry,
        CategoryRepository $categoryRepository,
        Data $helper
    ) {
        $this->customerSession = $customerSession;
        $this->registry = $registry;
        $this->categoryRepository = $categoryRepository;
        $this->helper = $helper;
        parent::__construct($helper, $context);
    }

    /**
     * @return array|null
     */
    public function getProductData()
    {
        $product = $this->registry->registry('current_product');
        if ($product && $product->getId()) {
            $categoryIds = $product->getCategoryIds();
            $categoryNames = [];

            foreach ($categoryIds as $categoryId) {
                if ($categoryId != 2) {
                    try {
                        $category = $this->categoryRepository->get($categoryId);
                        $categoryNames[] = $category->getName();
                    } catch (\Exception $e) {
                        // Handle exception if needed
                    }
                }
            }

            $categoryPath = implode(' > ', $categoryNames);

            $productData = [
                'product_sku' => $product->getSku(),
                'category_path' => $categoryPath,
                'product_name' => $product->getName(),
                'price' => $product->getFinalPrice(),
                'currency' => $this->helper->getCurrencyCode()
            ];
            
            $this->helper->sendConversionEvent('ViewContent', [
                'content_ids' => [$product->getSku()],
                'content_name' => $product->getName(),
                'content_type' => 'product',
                'currency' => $this->helper->getCurrencyCode(),
                'value' => $product->getFinalPrice()
            ]);

            return $productData;
        }
        return null;
    }
}
