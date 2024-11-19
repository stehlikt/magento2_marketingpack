define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'mage/url'
], function ($, customerData, urlBuilder) {
    'use strict';

    if (typeof cc !== 'undefined' && cc !== null && !cc.allowedCategory('targeting')) {
        console.log('Marketing forbidden');
        return;
    }

    $(document).on('ajax:addToCart', function (event, props) {
        let lastSku = props.sku;
        setTimeout(function () {
            let cart = customerData.get('cart')();
            let addedItem = cart.items.find(item => String(item.product_sku) === String(lastSku));

            if (addedItem) {
                $.ajax({
                    url: urlBuilder.build('railsformers_marketingpack/product/getProduct'),
                    type: 'GET',
                    data: { product_id: addedItem.product_id },
                    success: function (response) {
                        if (!response.error) {
                            // Připravíme kategorie pro Google Analytics
                            let categories = response.categories || [];
                            let gaCategories = {};
                            categories.forEach((category, index) => {
                                if(index == 0)
                                    gaCategories['item_category'] = category;
                                else
                                    gaCategories['item_category' + (index + 1)] = category;
                            });

                            let items = [{
                                id: response.item_id,
                                name: response.item_name,
                                brand: response.item_brand,
                                price: response.price,
                                quantity: addedItem.qty,
                                google_business_vertical: 'retail',
                                ...gaCategories
                            }];

                            gtag('event', 'add_to_cart', {
                                currency: response.currency,
                                value: response.price * addedItem.qty,
                                items: items
                            });

                           /* console.log({
                                add_to_cart: {
                                    value: response.price * addedItem.qty,
                                    items: items
                                }
                            }); */
                        } else {
                            console.error('Error fetching product data:', response.error);
                        }
                    },
                    error: function () {
                        console.error('Failed to fetch product data.');
                    }
                });
            }
        }, 1000);
    });
});