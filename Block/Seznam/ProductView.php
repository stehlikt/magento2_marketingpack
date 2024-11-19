<?php
namespace Railsformers\MarketingPack\Block\Seznam;

use Railsformers\MarketingPack\Block\Seznam\Config;
use Magento\Framework\View\Element\Template;
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
        parent::__construct($helper,$context);
    }

    public function getUserData()
    {
        if ($this->customerSession->isLoggedIn()) {
            return $this->customerSession->getCustomer();
        }
        return null;
    }

    public function getProductDetail()
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
                    }
                }
            }

            $categoryPath = implode(' | ', $categoryNames);

            return [
                'product_id' => $product->getSku(),
                'category_path' => $categoryPath,
            ];
        }
        return null;
    }
}
