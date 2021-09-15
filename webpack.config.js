// eslint-disable-next-line @typescript-eslint/no-var-requires
const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    // .addEntry('app-admin-authorization', './assets/admin/js/app-admin-authorization/app-admin-authorization.js')
    .addEntry('app-admin-main', './assets/admin/js/app.ts')
    // .addEntry('app-front', './assets/front/js/app.js')

    .splitEntryChunks()
    .enableTypeScriptLoader()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .enableVueLoader(() => {}, {runtimeCompilerBuild: false})
    .enableSassLoader()
    .addLoader({
        enforce: 'pre',
        test: /\.(js|vue)$/,
        loader: 'eslint-loader',
        exclude: /node_modules/,
        options: {
            fix: false,
            emitError: true,
            emitWarning: true,
        },
    })
;

module.exports = Encore.getWebpackConfig();
