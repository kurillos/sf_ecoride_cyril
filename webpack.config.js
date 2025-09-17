const Encore = require('@symfony/webpack-encore');
const path = require('path');

Encore.configureBabel((config) => {
    config.presets = [
        ['@babel/preset-env', {
            useBuiltIns: 'usage',
            corejs: '3.25'
        }]
    ];
});

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.js')
    .addStyleEntry('main_styles', './assets/scss/custom_bootstrap.scss')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .enableStimulusBridge('./assets/controllers.json')
    .enableSassLoader()
    .enablePostCssLoader()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction());

module.exports = Encore.getWebpackConfig();
