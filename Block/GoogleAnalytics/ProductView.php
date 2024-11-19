<?php

namespace Railsformers\MarketingPack\Block\GoogleAnalytics;

use Railsformers\MarketingPack\Block\GoogleAnalytics\Config;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\Registry;
use Railsformers\MarketingPack\Helper\Data;

class ProductView extends Config
{

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Context $context
     * @param ProductRepository $productRepository
     * @param CategoryRepository $categoryRepository
     * @param Registry $registry
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        Registry $registry,
        Data $helper
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->registry = $registry;
        parent::__construct($helper, $context);
    }

    /**
     * @return array
     */
    public function getProductData()
    {

        $product = $this->getProduct();
        if (!$product) {
            return null;
        }

        $categories = $this->getCategoryHierarchy($product->getCategoryIds());
        $variant = $this->getVariantAttribute($product);

        $productData = [
            'item_id' => $product->getSku(),
            'item_name' => $product->getName(),
            'price' => $product->getFinalPrice(),
            'item_variant' => $variant,
            'quantity' => 1,
            'currency' => $this->helper->getCurrencyCode()
        ];

        if ($product->getAttributeText('manufacturer')) {
            $productData['item_brand'] = $product->getAttributeText('manufacturer');
        }

        foreach ($categories as $index => $category) {
            $productData['item_category' . ($index + 1)] = $category;
        }

        $productData['categories_count'] = count($categories);
        
        return $productData;
    }

    /**
     * Get category hierarchy
     *
     * @param array $categoryIds
     * @return array
     */
    protected function getCategoryHierarchy($categoryIds)
    {
        $categories = [];
        $highestLevelCategory = null;

        foreach ($categoryIds as $categoryId) {
            $category = $this->categoryRepository->get($categoryId);
            if ($highestLevelCategory === null || $category->getLevel() > $highestLevelCategory->getLevel()) {
                $highestLevelCategory = $category;
            }
        }

        if ($highestLevelCategory) {
            $path = explode('/', $highestLevelCategory->getPath());
            foreach ($path as $pathCategoryId) {
                if ($pathCategoryId != 1 && $pathCategoryId != $highestLevelCategory->getId()) {
                    $pathCategory = $this->categoryRepository->get($pathCategoryId);
                    if ($pathCategory->getLevel() > 1) { // Vynecháme defaultní kategorii
                        $categories[] = $pathCategory->getName();
                    }
                }
            }
            $categories[] = $highestLevelCategory->getName();
        }

        return $categories;
    }

    /**
     * Get variant attribute
     *
     * @param $product
     * @return string
     */
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

    /**
     * Get current product
     *
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }
}
