<?php

namespace Railsformers\MarketingPack\Block\GoogleAnalytics;

use Railsformers\MarketingPack\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Config extends Template
{
    /**
     *
     * @var Data
     */
    protected $helper;
    /**
     *
     * @param Data $helper
     * @param Context $context
     */
    public function __construct(Data $helper, Context $context)
    {
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     *
     * @return mixed
     */
    public function getGoogleAnalyticsId()
    {
        return $this->helper->getGoogleAnalyticsId();
    }
}