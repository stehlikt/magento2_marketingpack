define([], function() {
    return function(config) {
        var productListData = config.productListData;

        var items = productListData.map(function(product, index) {
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

        var totalValue = items.reduce(function(acc, item) {
            return acc + (item.price * item.quantity);
        }, 0);


        if (typeof gtag === 'function') {
            gtag("event", "begin_checkout", {
                currency: "CZK",
                value: totalValue,
                coupon: config.coupon || '', // Předpokládáme, že celkový kupón je součástí config
                items: items
            });
            /*console.log({
                begin_checkout: {
                    items: items
                }
            }); */
        } else {
            console.error('gtag is not defined');
        }
    };
});
