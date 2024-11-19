define(['cookieManager'], function(cookieManager) {
    'use strict';

    return function(config) {
        var productData = config.productData;
        
        if (!cookieManager.isCategoryAllowed('ads')) {
            console.log('Ad storage consent not granted. Skipping ViewContent tracking.');
            return;
        }

        fbq('track', 'ViewContent', {
            content_name: productData.product_name,
            content_category: productData.category_path,
            content_ids: [productData.product_sku],
            content_type: 'product',
            value: productData.price,
            currency: productData.currency
        });
    };
});
