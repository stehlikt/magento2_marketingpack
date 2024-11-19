define([], function() {
    return function(config) {
        var gtagId = config.gtagId;
        console.log('Google Tag Manager ID:', gtagId);

        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', gtagId);
    };
});