const path = require('path');

// Plugins
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssoWebpackPlugin = require('csso-webpack-plugin').default;
const TerserJSPlugin = require('terser-webpack-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');

const externals = {
	jquery: 'jQuery',
	'@yaireo/tagify': 'Tagify',
	// WordPress Packages.
	'@wordpress/hooks': 'wp.hooks',
}

const entryPoints = {
	// Frontend
	ajax_tooltip: './source/js/ajax-tooltips.js',
	search: './source/js/wppedia-search.js',
	// Backend
	edit: './source/js/admin/edit/edit.js'
};

module.exports = [
	// Compile Javascript
	{
		mode: 'production',
		entry: entryPoints,
		optimization: {
			minimize: true,
			minimizer: [
				new TerserJSPlugin({
					terserOptions: {
						output: {
							comments: false,
						},
					},
					extractComments: false,
				})
			],
		},
		output: {
			path: path.resolve(__dirname, 'dist/js'),
			filename: '[name].bundle.js'
		},
		externals: externals,
		module: {
			rules: [
				{
					test: /\.(jsx?)$/,
					exclude: /node_modules/,
					use: ['babel-loader']
				},
			]
		}
	},
  // Compile CSS
  {
    mode: 'production',
    entry: {
			// Frontend
			style: './source/scss/_main.scss',
			// Components
			admin: './source/scss/admin/_main.scss'
    },
    output: {
      path: path.resolve(__dirname, 'dist/css'),
      filename: '[name].bundle.js'
		},
		optimization: {
			minimize: true,
			minimizer: [
				new TerserJSPlugin({
					terserOptions: {
						output: {
							comments: false,
						},
					},
					extractComments: false,
				}), 
				new CssMinimizerPlugin({})
			],
		},
    module: {
      rules: [
        {
          test: /\.(png|jpg|jpeg|gif|ico)$/,
          use: [
						{
							loader: 'file-loader',
							options: {
								name: '[name].[ext]',
								outputPath: '../images'
							}
						}
					],
				},
				{
					test: /\.svgz?$/,
					use: [
						{
							loader: 'file-loader',
							options: {
								name: '[name].[ext]',
								outputPath: '../images'
							}
						},
						{
							loader: 'svgo-loader'
						}
					],
				},
        {
          test: /\.(pc|sa|sc|c)ss$/,
          use: [
            {
              loader: MiniCssExtractPlugin.loader,
              options: {
                esModule: false
              }
            },
            'css-loader',
						'postcss-loader',
						'resolve-url-loader',
						'sass-loader',
          ],
        },
      ],
    },
		plugins: [
      new MiniCssExtractPlugin({
        filename: '[name].css',
        chunkFilename: '[id].css'
			}),
      new CssoWebpackPlugin({
        pluginOutputPostfix: 'min'
      })
    ],
  }
];
