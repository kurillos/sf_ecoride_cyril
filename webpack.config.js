const Encore = require('@symfony/webpack-encore');
const path = require('path');

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
    .enableVersioning(Encore.isProduction())
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .configureBabel((babelConfig) => {
        babelConfig.sourceType = 'unambiguous';
    });

module.exports = Encore.getWebpackConfig();
