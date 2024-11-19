define(['cookieManager'], function(cookieManager) {
    return function(config) {
        var productListData = config.productListData;

        var contents = productListData.map(function(product) {
            return {
                id: product.id,
                quantity: product.quantity
            };
        });

        var numItems = contents.reduce(function(acc, item) {
            return acc + item.quantity;
        }, 0);

        var totalValue = contents.reduce(function(acc, item) {
            return acc + (item.price * item.quantity);
        }, 0);
        
        if (!cookieManager.isCategoryAllowed('ads')) {
            console.log('Ad storage consent not granted. Skipping InitiateCheckout tracking.');
            return;
        }

        if (typeof fbq === 'function') {
            fbq('track', 'InitiateCheckout', {
                content_ids: contents.map(item => item.id),
                contents: contents,
                currency: config.currency,
                num_items: numItems,
                value: totalValue
            });
            /*console.log({
                initiate_checkout: {
                    contents: contents
                }
            });*/
        } else {
            console.error('fbq is not defined');
        }
    };
});
