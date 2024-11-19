require([
    'jquery'
], function ($) {
    'use strict';
    $(document).ready(function() {
        var link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://cdn.jsdelivr.net/gh/orestbida/cookieconsent@v2.9.1/dist/cookieconsent.css';
        var script = document.createElement('script');
        script.defer = true;
        script.src = 'https://cdn.jsdelivr.net/gh/orestbida/cookieconsent@v2.9.1/dist/cookieconsent.js';
        script.onload = function () {
            loadCookieConsent()
        };
        document.querySelector('head').append(link);
        document.querySelector('body').append(script);


        function consentGtagUpdate(cookieconsent) {
            if (cookieconsent.allowedCategory('analytics')) {
                typeof dataLayer === 'object' && dataLayer.push({'event': 'analytics_storage'});
                typeof gtag === 'function' && gtag('consent', 'update', {'analytics_storage': 'granted'});
                console.log('analytics_storage: granted');
            }
            else {
                typeof gtag === 'function' && gtag('consent', 'update', {'analytics_storage': 'denied'});
                console.log('analytics_storage: denied');
            }

            if (cookieconsent.allowedCategory('ads')) {
                typeof dataLayer === 'object' && dataLayer.push({'event': 'ad_storage'});
                typeof gtag === 'function' && gtag('consent', 'update', {'ad_storage': 'granted'});
                console.log('ad_storage: granted');
            }
            else {
                typeof gtag === 'function' && gtag('consent', 'update', {'ad_storage': 'denied'});
                console.log('ad_storage: denied');
            }

            if (cookieconsent.allowedCategory('ad_user')) {
                typeof dataLayer === 'object' && dataLayer.push({'event': 'ad_user_data'});
                typeof gtag === 'function' && gtag('consent', 'update', {'ad_user_data': 'granted'});
                console.log('ad_user_data: granted');
            }
            else {
                typeof gtag === 'function' && gtag('consent', 'update', {'ad_user_data': 'denied'});
                console.log('ad_user_data: denied');
            }

            if (cookieconsent.allowedCategory('ad_personalization')) {
                typeof dataLayer === 'object' && dataLayer.push({'event': 'ad_personalization'});
                typeof gtag === 'function' && gtag('consent', 'update', {'ad_personalization': 'granted'});
                console.log('ad_personalization: granted');
            }
            else {
                typeof gtag === 'function' && gtag('consent', 'update', {'ad_personalization': 'denied'});
                console.log('ad_personalization: denied');
            }
        }

        function loadCookieConsent() {
            var cookieconsent = initCookieConsent();
            cookieconsent.run({
                current_lang: 'document',
                autoclear_cookies: true,
                cookie_expiration: 365,
                current_lang: document.documentElement.getAttribute('lang'),
                gui_options: {
                    consent_modal: {
                        layout: 'box',
                        position: 'middle center',
                        transition: 'slide',
                        swap_buttons: false
                    },
                    settings_modal: {
                        layout: 'box',
                        transition: 'slide'
                    }
                },
                languages: {
                    'cs': {
                        consent_modal: {
                            title: '',
                            description: 'Tyto webové stránky používají k poskytování svých služeb soubory cookies, abychom vám zlepšili procházení stránek. Mezi ně patří nezbytně nutné soubory k používání webových stránek, dále pak technologie, které slouží např. k vytváření anonymních statistik, nástrojů třetích stran pro vylepšení vašeho procházení stránek nebo pro inzerci personalizovaných reklam. Více najdete v <a href="/zasady-zpracovani-cookies/">zásadách zpracování cookies</a>',
                            primary_btn: {
                                text: 'Souhlasím',
                                role: 'accept_all'
                            },
                            secondary_btn: {
                                text: 'Nastavit',
                                role: 'settings'
                            }
                        },
                        settings_modal: {
                            title: 'Nastavení cookies',
                            save_settings_btn: 'Uložit nastavení',
                            accept_all_btn: 'Povolit vše',
                            reject_all_btn: 'Zamítnout vše',
                            close_btn_label: 'Zavřít',
                            cookie_table_headers: [
                                {col1: 'Název'},
                                {col2: 'Expirace'},
                                {col3: 'Popis'},
                            ],
                            blocks: [
                                {
                                    title: 'Co jsou cookies',
                                    description: 'Cookies jsou malé soubory s malým množstvím dat, které jsou ukládány ve vašem koncovém zařízení (počítači, tabletu, mobilním telefonu apod). Tyto soubory umožňují správné fungování systému případně usnadňují práci se systémem jak návštěvníkům, tak i správcům systému. Cookies nejsou nebezpečné pro dané zařízení, ale ze svého principu je jejich zpracování podřízeno ochraně osobních údajů. Na této stránce máte možnost přizpůsobit soubory cookie dle jednotlivých kategorií, v souladu s vlastními preferencemi.'
                                }, {
                                    title: 'Povinné pro funkčnost stránek',
                                    description: 'Soubory cookie, které jsou nutné pro funkčnost stránek a musí být povolené. Patří k nim soubory cookie, které umožňují si vás zapamatovat při procházení v rámci jedné relace.',
                                    toggle: {
                                        value: 'necessary',
                                        enabled: true,
                                        readonly: true
                                    },
                                }, {
                                    title: 'Analytické',
                                    description: 'Tyto soubory nám pomáhají v měření používání webu, vytváření anonymních statistik a používáme je zároveň i pro zlepšení funkcí stránek pro vás.',
                                    toggle: {
                                        value: 'analytics',
                                        enabled: false,
                                        readonly: false
                                    },
                                }, {
                                    title: 'Marketingové',
                                    description: 'Soubory nám pomáhají rozšiřovat povědomí pomocí reklamy relevantním uživatelům. Zároveň nám umožňují přesnější cílení reklam a lepší vyhodnocení kampaní.',
                                    toggle: {
                                        value: 'ads',
                                        enabled: false,
                                        readonly: false
                                    },}, {
                                    title: 'Uživatelské data',
                                    description: 'Nastaví souhlas s odesíláním údajů o uživatelích souvisejících s reklamami.',
                                    toggle: {
                                        value: 'ad_user',
                                        enabled: false,
                                        readonly: false
                                    }, }, {
                                    title: 'Personalizovaná inzerce',
                                    description: 'Slouží k nastavení souhlasu s personalizovanou inzercí.',
                                    toggle: {
                                        value: 'ad_personalization',
                                        enabled: false,
                                        readonly: false
                                    },
                                },
                            ]
                        }
                    },
                },

                onFirstAction: function (user_preferences, cookie) {
                },
                onAccept: function (cookie) {
                    consentGtagUpdate(cookieconsent);
                },
                onChange: function (cookie, changed_preferences) {
                    consentGtagUpdate(cookieconsent);
                },
            });
        }
    });
});
