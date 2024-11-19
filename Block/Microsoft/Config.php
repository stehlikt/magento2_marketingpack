<?php
namespace Railsformers\MarketingPack\Block\Microsoft;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Railsformers\MarketingPack\Helper\Data;

class Config extends Template{

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
     * @return mixed
     */
    public function getClarityId()
    {
        return $this->helper->getClarityId();
    }
}