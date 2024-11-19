<?php

namespace Railsformers\MarketingPack\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class ZboziType implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            '' => __('Nezvoleno'),
            'standard' => __('Standardní měření'),
            'limited' => __('Omezené měření')
        ];
    }
}
