const Encore = require('@symfony/webpack-encore');

if (!Encore.isProduction()) {
    Encore.configureDevServerOptions(options => {
        options.compress = true;
        options.disableHostCheck = true;
        options.headers = { 'Access-Control-Allow-Origin': '*' };
        options.host = '0.0.0.0';
        options.hot = false;
        options.https = false;
        options.port = 8000;
    });
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.js')
    .enableStimulusBridge('./assets/controllers.json')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.38';
    })
    .enableSassLoader(options => {
        options.implementation = require('sass');
        options.additionalData = `@import "../../node_modules/bootstrap/scss/functions"; @import "../../node_modules/bootstrap/scss/variables"; @import "../../node_modules/bootstrap/scss/mixins";`;
    })
    .enablePostCssLoader(options => {
        options.postcssOptions = {
            ident: 'postcss',
            syntax: 'postcss-scss',
            plugins: [],
        };
    })
    .addStyleEntry('main_styles', './assets/scss/custom_bootstrap.scss')
    .copyFiles({
        from: './assets/images',
        to: 'images/[path][name].[ext]',
    })
    .addEntry('form_validation', './assets/js/form_validation.js')
    .addEntry('login_form', './assets/js/login_form_validation.js')
    .addEntry('rating', './assets/js/rating.js')
    .addEntry('review_decision', './assets/js/review_decision.js')
    
    .addEntry('report_management', './assets/js/report_management.js')
    .addEntry('contact_form_validation', './assets/js/contact_form_validation.js')
    .addEntry('admin', './assets/js/admin.js');

module.exports = Encore.getWebpackConfig();
