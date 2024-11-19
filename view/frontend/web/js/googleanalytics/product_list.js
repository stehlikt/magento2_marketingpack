define([], function() {
    return function(config) {
        var productListData = config.productListData;

        var items = productListData.map(function(product, index) {
            var item = {
                item_id: product.item_id,
                item_name: product.item_name,
                index: index,
                item_variant: product.item_variant,
                price: product.price
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

        var chunkSize = 20;
        for (var i = 0; i < items.length; i += chunkSize) {
            var chunk = items.slice(i, i + chunkSize);
            /*console.log('Sending chunk:', chunk);*/

            if (typeof gtag === 'function') {
                gtag("event", "view_item_list", {
                    item_list_id: "category_products",
                    item_list_name: "Category products",
                    items: chunk
                });
            } else {
                console.error('gtag is not defined');
            }
        }
    };
});
