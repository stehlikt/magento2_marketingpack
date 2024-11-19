define(
    [
        'ko',
        'jquery',
        'uiComponent',
        'mage/url',
        'jquery/jquery.cookie'
    ],
    function (ko, $, Component, url) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Railsformers_MarketingPack/checkout/zboziCheckbox',
                isVisible: ko.observable(false) // Přidáme observable pro viditelnost
            },
            initObservable: function () {
                this._super()
                    .observe(['isVisible', 'CheckVals']);

                // Zkontrolujeme, zda je hodnota inputu zbozi_type "standart"
                if ($('input[name="zbozi_type"]').val() === 'standard') {
                    this.isVisible(true); // Nastavíme viditelnost na true, pokud je hodnota "standart"
                }

                this.CheckVals(parseInt($.cookie('zboziDisabled')) > 0 ? true : false);

                var checkVal = 0;
                var self = this;

                this.CheckVals.subscribe(function (newValue) {
                    checkVal = newValue ? 1 : 0;
                    $.cookie('zboziDisabled', checkVal, {expires: 86400});
                });

                return this;
            }
        });
    }
);
