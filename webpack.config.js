const Encore = require('@symfony/webpack-encore');
const path = require('path');

if (!Encore.isProduction()) {
    const dotenv = require('dotenv');
    dotenv.config();
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    .addEntry('app', './assets/app.js')
    .addStyleEntry('main_styles', './assets/scss/custom_bootstrap.scss')

    .splitEntryChunks()
    
    .enableStimulusBridge('./assets/controllers.json')
    .enableSingleRuntimeChunk()
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.4';
    })
    .autoProvidejQuery()
    .enableSassLoader()
    .enablePostCssLoader()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction());

module.exports = Encore.getWebpackConfig();
