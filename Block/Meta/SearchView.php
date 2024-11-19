<?php

namespace Railsformers\MarketingPack\Block\Meta;

use Railsformers\MarketingPack\Block\Meta\Config;
use Railsformers\MarketingPack\Helper\Data;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

class SearchView extends Config
{
    protected $request;
    protected $productCollectionFactory;
    protected $helper;

    public function __construct(
        Context $context,
        RequestInterface $request,
        ProductCollectionFactory $productCollectionFactory,
        Data $helper,
        array $data = []
    ) {
        $this->request = $request;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->helper = $helper;
        parent::__construct($helper, $context);
    }

    public function getSearchData()
    {
        $query = $this->request->getParam('q');
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToSelect(['entity_id', 'name', 'price', 'sku'])
            ->addAttributeToFilter(
                [
                    ['attribute' => 'name', 'like' => '%' . $query . '%'],
                    ['attribute' => 'sku', 'like' => '%' . $query . '%']
                ]
            );

        $products = [];
        foreach ($productCollection as $product) {
            $products[] = [
                'id' => $product->getSku(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'quantity' => 1
            ];
        }

        $searchData = [
            'query' => $query,
            'content_ids' => array_column($products, 'id'),
            'contents' => $products,
            'content_type' => 'product'
        ];

        $this->helper->sendConversionEvent('Search', [
            'search_string' => $query,
            'content_ids' => $searchData['content_ids'],
            'contents' => $searchData['contents'],
            'content_type' => 'product'
        ]);

        return $searchData;
    }
}
