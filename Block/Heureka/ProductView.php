<?php

namespace Railsformers\MarketingPack\Block\Heureka;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Railsformers\MarketingPack\Helper\Data;

class ProductView extends Template{

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(Template\Context $context, Data $helper, array $data = [])
    {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Get Heureka ID
     *
     * @return string
     */
    public function getHeurekaId()
    {
        return $this->helper->getHeurekaId();
    }
}