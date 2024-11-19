define(['cookieManager'], function(cookieManager) {
    'use strict';

    return function(config) {
        var data = config.data;

        if (data.user) {
            //console.log('Updating identities with user data.');
            window.sznIVA.IS.updateIdentities({
                eid: data.user.email,
                aid: {
                    "a1": "Česká republika",
                    "a2": data.user.city,
                    "a3": data.user.street,
                    "a4": data.user.region,
                    "a5": data.user.postcode
                },
                tid: data.user.telephone
            });
        } else {
            window.sznIVA.IS.updateIdentities({
                eid: null
            });
        }

        var consent = cookieManager.isCategoryAllowed('ads') ? 1 : 0;

        var retargetingConf = {
            rtgId: config.rtgId, // ID Sklik RTG
            itemId: data.product.id, // ID položky
            category: data.product.category_path, // Kategorie produktu
            pageType: "offerdetail", // typ stránky
            rtgUrl: window.location.href, // aktuální URL
            consent: consent // 1 = souhlasil s cookies, 0 nesouhlasil s cookies
        };

        //console.log('Retargeting Configuration:', retargetingConf);
        window.rc.retargetingHit(retargetingConf);
    };
});
