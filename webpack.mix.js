const mix = require('laravel-mix');

// mix.options({
//     processCssUrls: false
// });

mix.js('resources/js/app.js', 'public/js/app.js')
  .vue()
  .sass('resources/scss/app.scss', 'public/css/app.css')
  .version();

// mix.sourceMaps(false, 'source-map');
// mix.extract();
//
// if (!mix.inProduction()) {
//     mix.webpackConfig({
//         devtool: 'inline-source-map'
//     })
// }
