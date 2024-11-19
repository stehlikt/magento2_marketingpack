define(['cookieManager'], function(cookieManager) {
    'use strict';

    return function(config) {
        var heurekaId = config.heurekaId;
        var orderData = config.orderData;
        
        if (!cookieManager.isCategoryAllowed('ads')) {
            console.log('Ad storage consent not granted. Skipping Heureka tracking.');
            return;
        }

        //console.log('Heureka id:' + heurekaId);

        heureka('authenticate', heurekaId);
        heureka('set_order_id', orderData.order_id);

        orderData.items.map(function (product, index){
            //console.log('Heureka product:', product)
            heureka('add_product', product.sku, product.name, product.price, product.quantity);
            console.log('Product price: ' + product.price + ' QTY: ' + product.quantity)
        });

        console.log('total: ' + orderData.order_total);
        console.log('currency: ' + orderData.currency);
        heureka('set_total_vat', orderData.order_total);
        heureka('set_currency', orderData.currency);
        heureka('send', 'Order');
    };
});
