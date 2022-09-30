const Encore = require('@symfony/webpack-encore');
const path = require("path");

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

const base = "./frontend/";

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    .configureDevServerOptions(options => {
        options.allowedHosts = "all";
    })

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', base + "index.tsx")

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    //.enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    .enableTypeScriptLoader()

    // uncomment if you use React
    .enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()

    .addAliases({
        "react": path.resolve(__dirname, "./node_modules/react"),
        "react-dom": path.resolve(__dirname, "./node_modules/react-dom"),
        "react-redux": path.resolve("./node_modules/react-redux"),
        "@mui/material": path.resolve("./node_modules/@mui/material"),
        "notistack": path.resolve("./node_modules/notistack"),
        "react-router-dom": path.resolve("./node_modules/react-router-dom"),
        "@emotion/react": path.resolve("./node_modules/@emotion/react"),
        "@tanstack/react-query": path.resolve("./node_modules/@tanstack/react-query"),

        "@tilpoe/react-component-button": path.resolve(__dirname, "../../../packages/npm/@tilpoe/react-essentials/packages/component/button/src"),
        "@tilpoe/react-component-label": path.resolve(__dirname, "../../../packages/npm/@tilpoe/react-essentials/packages/component/label/src"),
        "@tilpoe/react-component-util": path.resolve(__dirname, "../../../packages/npm/@tilpoe/react-essentials/packages/component/util/src"),
        "@tilpoe/react-navigation": path.resolve(__dirname, "../../../packages/npm/@tilpoe/react-essentials/packages/module/navigation/src"),
        "@tilpoe/react-core": path.resolve(__dirname, "../../../packages/npm/@tilpoe/react-essentials/packages/module/core/src"),

        "@api": path.resolve(__dirname, base + "api"),
        "@app": path.resolve(__dirname, base + "app"),
        "@components": path.resolve(__dirname, base + "components"),
        "@hooks": path.resolve(__dirname, base + "hooks"),
        "@pages": path.resolve(__dirname, base + "pages"),
        "@qr": path.resolve(__dirname, base + "qr"),
        "@store": path.resolve(__dirname, base + "store")
    })
;

let config = Encore.getWebpackConfig();
module.exports = config;
