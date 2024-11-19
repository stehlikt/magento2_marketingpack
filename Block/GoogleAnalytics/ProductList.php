<?php

namespace Railsformers\MarketingPack\Block\GoogleAnalytics;

use Railsformers\MarketingPack\Block\GoogleAnalytics\Config;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Railsformers\MarketingPack\Helper\Data;

class ProductList extends Config
{

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var LayerResolver
     */
    protected $layerResolver;

    protected $helper;

    /**
     * @param Context $context
     * @param CollectionFactory $productCollectionFactory
     * @param CategoryRepository $categoryRepository
     * @param LayerResolver $layerResolver
     */
    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        CategoryRepository $categoryRepository,
        LayerResolver $layerResolver,
        Data $helper
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryRepository = $categoryRepository;
        $this->layerResolver = $layerResolver;
        parent::__construct($helper,$context);
    }

    /**
     * @return array
     */
    public function getProductListData()
    {
        $layer = $this->layerResolver->get();
        $productCollection = $layer->getProductCollection();
        $productCollection->addAttributeToSelect('*');
        $productData = [];

        foreach ($productCollection as $product) {
            $categories = $this->getCategoryHierarchy($product->getCategoryIds());
            $variant = $this->getVariantAttribute($product);

            $productItem = [
                'item_id' => $product->getSku(),
                'item_name' => $product->getName(),
                'price' => $product->getFinalPrice(),
                'item_variant' => $variant
            ];

            if ($product->getAttributeText('manufacturer')) {
                $productItem['item_brand'] = $product->getAttributeText('manufacturer');
            }

            foreach ($categories as $index => $category) {
                $productItem['item_category' . ($index + 1)] = $category;
            }
            $productItem['categories_count'] = count($categories);

            $productData[] = $productItem;
        }
        
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
                if (($pathCategoryId != 1 || $pathCategoryId != 2) && $pathCategoryId != $highestLevelCategory->getId()) {
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
}
