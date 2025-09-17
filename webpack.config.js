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
    .enableVersioning(Encore.isProduction());

// Config Babel pour Heroku / ES Modules
Encore.configureBabel((config) => {
    config.presets.push([
        '@babel/preset-env',
        {
            useBuiltIns: 'usage',
            corejs: 3
        }
    ]);
});

module.exports = Encore.getWebpackConfig();
