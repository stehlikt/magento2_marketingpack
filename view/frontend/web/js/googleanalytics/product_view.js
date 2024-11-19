define([], function() {
    return function(config) {
        var productData = config.productData;

        if (productData && typeof gtag === 'function') {
            var item = {
                item_id: productData.item_id,
                item_name: productData.item_name,
                item_variant: productData.item_variant,
                price: productData.price,
                quantity: productData.quantity || 1,
            };

            if (productData.item_brand) {
                item.item_brand = productData.item_brand;
            }

            // Dynamicky přidáváme kategorie jako samostatné vlastnosti
            for (var i = 1; i <= productData.categories_count; i++) {
                var categoryKey = i === 1 ? 'item_category' : 'item_category' + i;
                item[categoryKey] = productData['item_category' + i];
            }

            // Log the final item structure
            /*console.log('Item:', item);*/

            gtag("event", "view_item", {
                currency: productData.currency,
                value: productData.price,
                items: [item]
            });
        } else {
            console.error('Product data is null or gtag is not defined');
        }
    };
});
