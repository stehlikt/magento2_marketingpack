define(['cookieManager'], function(cookieManager) {
    return function(config) {
        var orderData = config.orderData;

        var contents = orderData.items.map(function(product) {
            return {
                id: product.id,
                quantity: product.quantity
            };
        });

        var numItems = contents.reduce(function(acc, item) {
            return acc + item.quantity;
        }, 0);
        
        if (!cookieManager.isCategoryAllowed('ads')) {
            console.log('Ad storage consent not granted. Skipping Purchase tracking.');
            return;
        }

        if (typeof fbq === 'function') {
            fbq('track', 'Purchase', {
                content_ids: contents.map(item => item.id),
                contents: contents,
                currency: orderData.currency,
                num_items: numItems,
                value: orderData.value
            });
            /*console.log('Meta',{
                purchase: {
                    contents: contents
                }
            });*/
        } else {
            console.error('fbq is not defined');
        }
    };
});
