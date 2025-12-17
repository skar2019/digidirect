define([], function () {
    var link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = require.toUrl('Zip_ZipPayment/css/zipmoney.css');
    document.head.appendChild(link);
});
