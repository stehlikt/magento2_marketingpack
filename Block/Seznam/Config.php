<?php

namespace Railsformers\MarketingPack\Block\Seznam;

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
     * @return mixed
     */
    public function getRetargetingId()
    {
        return $this->helper->getSklikRetargetingId();
    }
    
    /**
     * Get Sklik Conversion ID
     *
     * @return string
     */
    public function getSklikConversionId()
    {
        return $this->helper->getSklikConversionId();
    }

    /**
     * Get Zbozi Type
     *
     * @return string
     */
    public function getZboziType()
    {
        return $this->helper->getZboziType();
    }

    /**
     * Get Zbozi Place ID
     *
     * @return string
     */
    public function getZboziPlaceId()
    {
        return $this->helper->getZboziPlaceId();
    }
}