<?php

namespace Railsformers\MarketingPack\Block\Meta;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Railsformers\MarketingPack\Helper\Data;

class Config extends Template
{
    /**
     * @var Data;
     */
    protected $helper;

    /**
     * @param Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(Data $helper, Template\Context $context, array $data = [])
    {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getMetaId()
    {
        return $this->helper->getMetaId();
    }

    /**
     * @return mixed
     */
    public function getCurrencyCode()
    {
        return $this->helper->getCurrencyCode();
    }
}