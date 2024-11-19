<?php

namespace Railsformers\MarketingPack\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\LayoutInterface;
use Railsformers\MarketingPack\Helper\Data;

class CompleteRegistrationObserver implements ObserverInterface
{
    protected $layout;
    protected $helper;

    public function __construct(
        LayoutInterface $layout,
        Data $helper
    ) {
        $this->layout = $layout;
        $this->helper = $helper;
    }

    public function execute(Observer $observer)
    {
        $this->helper->sendConversionEvent('CompleteRegistration', [
        ]);

        echo "<script>
            fbq('track', 'CompleteRegistration');
        </script>";
    }
}
