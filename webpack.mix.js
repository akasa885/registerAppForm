const mix = require('laravel-mix');
const WebpackObfuscator = require('webpack-obfuscator');

/*
|--------------------------------------------------------------------------
| Mix Asset Management
|--------------------------------------------------------------------------
|
| Mix provides a clean, fluent API for defining some Webpack build steps
| for your Laravel application. By default, we are compiling the Sass
| file for the application as well as bundling up all the JS files.
|
 */

mix
  .js('resources/js/app.js', 'public/js')
  .postCss('resources/css/app.css', 'public/css', [
    require('postcss-import'),
    require('tailwindcss'),
    require('postcss-nested'),
    require('autoprefixer'),
  ]);
mix
  .js('resources/js/pages/page.js', 'public/js')
mix
  .js('resources/js/front/front.js', 'public/js');

if (mix.inProduction()) {
  mix
    .version();
  mix
    .webpackConfig({
      plugins: [
          new WebpackObfuscator({
              rotateStringArray: true,
              stringArray: true,
              stringArrayThreshold: 0.75,
          }, ['app.js'])
      ]
  });
}
