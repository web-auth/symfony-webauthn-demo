var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    .enableSingleRuntimeChunk()

    .enableReactPreset()

    .addEntry('react', './assets/js/index.js')

    .configureBabel(function(babelConfig) {
        babelConfig.plugins.push('@babel/plugin-proposal-class-properties');
    })
;

module.exports = Encore.getWebpackConfig();
