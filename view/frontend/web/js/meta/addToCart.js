define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'mage/url',
    'cookieManager' // Přidej cookieManager jako závislost
], function ($, customerData, urlBuilder, cookieManager) {
    'use strict';

    $(document).on('ajax:addToCart', function (event, props) {
        if (!cookieManager.isCategoryAllowed('ads')) {
            console.log('Ad storage consent not granted. Skipping AddToCart tracking.');
            return;
        }

        let lastSku = props.sku;
        setTimeout(function () {
            let cart = customerData.get('cart')();
            let addedItem = cart.items.find(item => String(item.product_sku) === String(lastSku));

            if (addedItem) {
                $.ajax({
                    url: urlBuilder.build('railsformers_marketingpack/product/getProduct'),
                    type: 'GET',
                    data: { product_id: addedItem.product_id, product_qty:  addedItem.qty },
                    success: function (response) {
                        if (!response.error) {
                            fbq('track', 'AddToCart', {
                                content_ids: [response.item_id],
                                content_type: 'product',
                                contents: [{
                                    id: response.item_id,
                                    quantity: addedItem.qty
                                }],
                                currency: response.currency,
                                value: response.price * addedItem.qty
                            });

                            /*console.log('Meta pixel:',{
                                add_to_cart: {
                                    value: response.price * addedItem.qty,
                                    contents: [{
                                        id: response.item_id,
                                        quantity: addedItem.qty
                                    }]
                                }
                            });*/
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
