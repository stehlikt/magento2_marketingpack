define(['cookieManager'], function(cookieManager) {
    'use strict';

    return function(config) {
        var searchData = config.searchData;
        
        if (!cookieManager.isCategoryAllowed('ads')) {
            console.log('Ad storage consent not granted. Skipping Search tracking.');
            return;
        }
        
        fbq('track', 'Search', {
            content_ids: searchData.content_ids,
            content_type: searchData.content_type,
            contents: searchData.contents,
            search_string: searchData.query,
        });
    };
});
