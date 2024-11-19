var config = {
    deps: [
        "Railsformers_MarketingPack/js/googleanalytics/addToCart",
        "Railsformers_MarketingPack/js/meta/addToCart",
        "Railsformers_MarketingPack/js/googleanalytics/removeFromCart",
        "Railsformers_MarketingPack/js/cookieManager"
    ],
    map: {
        '*': {
            cookieManager: 'Railsformers_MarketingPack/js/cookieManager'
        }
    }
};