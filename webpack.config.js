var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // uncomment to define the assets of the project
    .addEntry('signin', './assets/js/signin.js')
    .addEntry('app', './assets/js/app.js')

    // uncomment if you use Sass/SCSS files
    .enableSassLoader()

    // uncomment for legacy applications that require $/jQuery as a global variable
    .autoProvidejQuery()


    // allow legacy applications to use $/jQuery as a global variable
    .autoProvideVariables({
        Popper: 'popper.js/dist/umd/popper',
        noUiSlider: 'nouislider'
    })


    .enableSingleRuntimeChunk()

    .addLoader({
        test: /\.(png|jpg|svg|cur)$/,
        loader: 'file-loader',
        options: {
            name: '/[name].[hash:7].[ext]',
            publicPath: '/build',
            outputPath: 'images'
        }
    })
    .copyFiles({
        from: './assets/images',

        // optional target path, relative to the output dir
        to: 'images/[path][name].[ext]',

        // only copy files matching this pattern
        pattern: /\.(png|jpg|jpeg|svg)$/
    })
;

module.exports = Encore.getWebpackConfig();
