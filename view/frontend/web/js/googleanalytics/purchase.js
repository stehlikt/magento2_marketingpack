define([], function() {
    return function(config) {
        var orderData = config.orderData;

        var items = orderData.items.map(function(product, index) {
            var item = {
                item_id: product.item_id,
                item_name: product.item_name,
                index: index,
                item_variant: product.item_variant,
                price: product.price,
                quantity: product.quantity,
                discount: product.discount || 0,
                coupon: product.coupon || ''
            };

            if (product.item_brand) {
                item.item_brand = product.item_brand;
            }

            for (var i = 1; i <= product.categories_count; i++) {
                var categoryKey = i === 1 ? 'item_category' : 'item_category' + i;
                item[categoryKey] = product['item_category' + i];
            }

            return item;
        });

        // Kontrola celého pole items
        //console.log('Purchased items:', items);

        if (typeof gtag === 'function') {
            gtag("event", "purchase", {
                transaction_id: orderData.transaction_id,
                value: orderData.value,
                tax: orderData.tax,
                shipping: orderData.shipping,
                currency: orderData.currency,
                coupon: orderData.coupon,
                items: items
            });
        } else {
            console.error('gtag is not defined');
        }
    };
});
