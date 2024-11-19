define([
    'jquery',
    'cookieManager' // Přidejte cookieManager jako závislost
], function ($, cookieManager) {
    'use strict';

    return function (config) {
        // Zkontroluj, zda je 'ad_storage' povoleno
        var consent = cookieManager.isCategoryAllowed('ads') ? 1 : 0;

        var conversionConf = {
            zboziId: config.zboziPlaceId,
            orderId: config.orderId,
            zboziType: config.zboziType || "standard",
            consent: consent
        };

        if (config.sklikConversionId) {
            conversionConf.id = config.sklikConversionId;
            conversionConf.value = config.total;
        }

        console.log({zboziConv: conversionConf});

        // Ujistěte se, že metoda existuje, předtím než ji zavoláte
        if (window.rc && window.rc.conversionHit) {
            console.log('Success');
            window.rc.conversionHit(conversionConf);
        }

        // Aktualizace identit pro Seznam.cz
        if (window.sznIVA && window.sznIVA.IS && window.sznIVA.IS.updateIdentities) {
            var streetArray = config.billingAddress.street || [];
            var streetLine = streetArray[0] || '';

            var streetName = '';
            var streetNumber = '';

            if (streetLine.match(/^(.*?)(\s+\d+.*)$/)) {
                streetName = RegExp.$1.trim();
                streetNumber = RegExp.$2.trim();
            } else {
                streetName = streetLine;
                streetNumber = '';
            }

            window.sznIVA.IS.updateIdentities({
                eid: config.customerEmail,
                aid: {
                    "a1": config.billingAddress.countryId,
                    "a2": config.billingAddress.city,
                    "a3": streetName,
                    "a4": streetNumber,
                    "a5": config.billingAddress.postcode
                },
                tid: config.billingAddress.telephone
            });
        }
    };
});
