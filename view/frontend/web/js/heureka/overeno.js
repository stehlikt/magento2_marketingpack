define([
    'ko',
    'jquery',
    'uiComponent',
    'mage/url',
    'jquery/jquery.cookie'
], function (ko, $, Component, url) {
    'use strict';
    return Component.extend({
        defaults: {
            template: 'Railsformers_MarketingPack/checkout/heurekaOverenoCheckbox',
            isVisible: ko.observable(false) // Přidáme observable pro viditelnost
        },
        initObservable: function () {
            this._super()
                .observe(['isVisible', 'CheckVals']);

            // Zkontrolujeme, zda je input heureka_overeno vyplněný
            if ($('input[name="heureka_overeno"]').val()) {
                this.isVisible(true); // Nastavíme viditelnost na true, pokud je input vyplněný
            }

            this.CheckVals(parseInt($.cookie('heurekaDisabled')) > 0 ? true : false);

            var checkVal = 0;
            var self = this;

            this.CheckVals.subscribe(function (newValue) {
                checkVal = newValue ? 10 : 0;
                $.cookie('heurekaDisabled', checkVal, {expires: 86400});
            });

            return this;
        }
    });
});
