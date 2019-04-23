const Encore = require('@symfony/webpack-encore')
const path = require('path')

Encore.setOutputPath('public/build/')
    .setPublicPath('/build')

    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    .enableSingleRuntimeChunk()

    .enableReactPreset()

    .addEntry('react', './assets/index.jsx')

    .enableSassLoader()

    .configureBabel(babelConfig => {
        babelConfig.plugins.push('@babel/plugin-proposal-class-properties')
    })

const config = Encore.getWebpackConfig()
config.resolve.alias.assets = path.resolve(__dirname, './assets/assets')
config.resolve.alias.store = path.resolve(__dirname, './assets/store')
config.resolve.alias.components = path.resolve(__dirname, './assets/components')
config.resolve.alias.views = path.resolve(__dirname, './assets/views')

module.exports = config
