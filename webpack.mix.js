let mix = require('laravel-mix');
mix.setPublicPath('public');
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
 
mix.webpackConfig(webpack => {
    return {
        plugins: [
            new webpack.ProvidePlugin({  
			  jQuery: 'jquery',
			  $: 'jquery',
			  'window.jQuery': 'jquery',
			}),
        ],

        module: {
            rules: [
                {
                    test: /\.(js)$/,
                    exclude: /(node_modules)/,
                    loader: "babel-loader",
                    query: {
                        presets: [["@babel/preset-env", { modules: false }]]
                    }
                },
            ]
        },
    };
})

mix.js('resources/assets/js/laradium.js', 'public/laradium/assets/js')
	.extract(['jquery', 'bootstrap', 'vue', 'jquery-datetimepicker'])
	.sass('resources/assets/sass/laradium.scss', 'public/laradium/assets/css');
