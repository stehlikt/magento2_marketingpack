<?php

namespace Railsformers\MarketingPack\Controller\Product;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\CategoryRepository;
use Railsformers\MarketingPack\Helper\Data;

class GetProduct extends Action
{
    protected $resultJsonFactory;
    protected $productRepository;
    protected $categoryRepository;
    protected $helperData;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ProductRepositoryInterface $productRepository,
        CategoryRepository $categoryRepository,
        Data $helperData
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->helperData = $helperData;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $productId = $this->getRequest()->getParam('product_id');

        try {
            $product = $this->productRepository->getById($productId);
            $categories = $this->getCategoryNames($product->getCategoryIds());

            $data = [
                'item_id' => $product->getSku(),
                'item_name' => $product->getName(),
                'item_brand' => $product->getAttributeText('manufacturer'),
                'categories' => $categories,
                'price' => $product->getPrice(),
                'currency' => $this->helperData->getCurrencyCode()
            ];

            // Volání sendConversionEvent pro AddToCart
            $this->helperData->sendConversionEvent('AddToCart', [
                'content_ids' => [$product->getSku()],
                'content_type' => 'product',
                'contents' => [
                    [
                        'id' => $product->getSku(),
                        'quantity' => $this->getRequest()->getParam('product_qty')
                    ]
                ],
                'currency' => $this->helperData->getCurrencyCode(),
                'value' => $product->getPrice()
            ]);

            return $result->setData($data);
        } catch (\Exception $e) {
            return $result->setData(['error' => $e->getMessage()]);
        }
    }

    protected function getCategoryNames($categoryIds)
    {
        $categoryNames = [];
        foreach ($categoryIds as $categoryId) {
            try {
                $category = $this->categoryRepository->get($categoryId);
                $categoryNames[] = $category->getName();
            } catch (\Exception $e) {
                continue;
            }
        }
        return $categoryNames;
    }
}
