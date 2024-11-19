define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'mage/url'
], function ($, customerData, urlBuilder) {
    'use strict';

    function trackCartChanges() {
        let previousCartItems = [];

        customerData.get('cart').subscribe(function (cart) {
            let currentCartItems = cart.items.map(item => ({
                product_id: item.product_id,
                quantity: item.qty
            }));

            let removedItems = previousCartItems.filter(prevItem =>
                !currentCartItems.some(currItem => currItem.product_id === prevItem.product_id)
            );

            if (removedItems.length > 0) {
                removedItems.forEach(function (removedItem) {
                    // Spuštění AJAX požadavku na kontroler pro získání dat produktu
                    $.ajax({
                        url: urlBuilder.build('railsformers_marketingpack/product/getProduct'),
                        type: 'GET',
                        data: { product_id: removedItem.product_id },
                        success: function (response) {
                            if (!response.error) {
                                let categories = response.categories || [];
                                let gaCategories = {};
                                categories.forEach((category, index) => {
                                    if (index == 0)
                                        gaCategories['item_category'] = category;
                                    else
                                        gaCategories['item_category' + (index + 1)] = category;
                                });

                                let items = [{
                                    id: response.item_id,
                                    name: response.item_name,
                                    brand: response.item_brand,
                                    price: response.price,
                                    quantity: removedItem.quantity, // Použijeme reálnou hodnotu množství
                                    google_business_vertical: 'retail',
                                    ...gaCategories
                                }];

                                gtag('event', 'remove_from_cart', {
                                    currency: response.currency,
                                    value: response.price * removedItem.quantity,
                                    items: items
                                });

                               /* console.log({
                                    remove_from_cart: {
                                        value: response.price * removedItem.quantity,
                                        items: items
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
                });
            }

            previousCartItems = currentCartItems;
        });
    }

    $(document).ready(function () {
        trackCartChanges();
    });
});
