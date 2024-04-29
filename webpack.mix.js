const mix = require("laravel-mix");
const fs = require('fs');
const wpPot = require("wp-pot");

mix.options({
    autoprefixer: {
        remove: false,
    },
    processCssUrls: false,
    terser: {
        terserOptions: {
            keep_fnames: true
        }
    }
});

mix.webpackConfig({
    target: "web",
    externals: {
        jquery: "window.jQuery",
        $: "window.jQuery",
        wp: "window.wp",
        _rolemaster_suite: "window._rolemaster_suite",
    },
});

mix.sourceMaps(false, 'source-map');

// Disable notification on dev mode
if (process.env.NODE_ENV.trim() !== "production") mix.disableNotifications();

if (process.env.NODE_ENV.trim() === 'production') {

    // Language pot file generator
    wpPot({
        destFile: "languages/rolemaster-suite.pot",
        domain: "rolemaster-suite",
        package: "RoleMaster Suite",
        src: "**/*.php",
    });
}

// SCSS to CSS
mix.sass("dev/scss/sdk.scss", "assets/css/rolemaster-suite-sdk.min.css");
mix.sass("dev/scss/survey.scss", "assets/css/rolemaster-suite-survey.css");


// mix.sass("dev/scss/admin-settings.scss", "assets/css/rolemaster-suite-admin-settings.min.css");
// mix.sass("dev/scss/premium/rolemaster-suite-pro-styles.scss", "Pro/assets/css/rolemaster-suite-pro.min.css");

// Scripts to js - regular
// mix.scripts( 'dev/js/rolemaster-suite.js', 'assets/js/rolemaster-suite.js' );


// Third Party Plugins Support
// fs.readdirSync('dev/scss/plugins').forEach(
//     file => {
//         mix.sass('dev/scss/plugins/' + file, 'assets/css/plugins/' + file.substring(1).replace('.scss', '.min.css'));
//     }
// );

// fs.readdirSync('dev/scss/premium/plugins/').forEach(
//     file => {
//         mix.sass('dev/scss/premium/plugins/' + file, 'Pro/assets/css/plugins/' + file.substring(1).replace('.scss', '.min.css'));
//     }
// );
