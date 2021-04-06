const path = require('path');

// Plugins
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssoWebpackPlugin = require('csso-webpack-plugin').default;
const TerserJSPlugin = require('terser-webpack-plugin');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');

module.exports = [
	// Compile Javascript
	{
		mode: 'production',
		entry: {
			ajax_tooltip: './source/js/ajax-tooltips.js',
			search: './source/js/wppedia-search.js'
		},
		optimization: {
			minimize: true,
			minimizer: [
				new TerserJSPlugin({
					sourceMap: true,
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
					sourceMap: true,
					terserOptions: {
						output: {
							comments: false,
						},
					},
					extractComments: false,
				}), 
				new OptimizeCSSAssetsPlugin({})
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
    module: {
      rules: [
        {
          test: /\.(png|jpg|jpeg|gif|ico)$/,
          use: [
						{
							loader: 'file-loader',
							options: {
								name: '[name].[ext]',
								outputPath: 'images'
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
								outputPath: 'images'
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
  }
];
