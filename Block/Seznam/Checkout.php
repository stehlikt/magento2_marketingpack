<?php
namespace Railsformers\MarketingPack\Block\Seznam;

use Railsformers\MarketingPack\Block\Seznam\Config;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Registry;
use Magento\Catalog\Model\CategoryRepository;
use Railsformers\MarketingPack\Helper\Data;

class Checkout extends Config
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
    
}
