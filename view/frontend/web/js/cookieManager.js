define(['jquery'], function ($) {
    'use strict';

    var cookieconsent;

    function init() {
        if (typeof initCookieConsent === 'function' && !cookieconsent) {
            cookieconsent = initCookieConsent();
        }
    }

    return {
        isCategoryAllowed: function (category) {
            init();
            if (cookieconsent) {
                return cookieconsent.allowedCategory(category);
            }
            console.error('CookieConsent library is not loaded.');
            return false;
        }
    };
});
