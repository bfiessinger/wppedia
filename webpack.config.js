const path = require('path');
const glob = require('glob-all');

// Plugins
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const PurgecssPlugin = require('purgecss-webpack-plugin');
const CssoWebpackPlugin = require('csso-webpack-plugin').default;
const TerserJSPlugin = require('terser-webpack-plugin');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');

// PurgeCss Whitelists
const purgecssHTMLTags = require('purgecss-whitelist-htmltags');

const PurgecssFiles = glob.sync([
  '**/*.js',
  '*.js',               // Get all JavaScript Files
  '**/*.php',
  '*.php',              // Get all PHP Files
  '!node_modules/**/*', // Exclude node_modules folder
  '!vendor/**/*',       // Exclude vendor folder
  '!classes/**/*'	      // Exclude classes folder
]);

module.exports = [
	// Compile Javascript
	/*
	{
		mode: 'production',
		entry: {
			none: './source/js/none.js'
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
	},*/
  // Compile CSS
  {
    mode: 'production',
    entry: {
			style: './source/scss/_main.scss',
			components_navigation: './source/scss/components/initial-char-navigation.scss'
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
      }),
      new PurgecssPlugin({
				paths: PurgecssFiles,
        extractors: [
          {
            extractor: content => content.match(/[A-z0-9-:\/]+/g) || [],
            extensions: ['js', 'ts', 'php']
          }
        ],
        whitelist: [
          ...purgecssHTMLTags.whitelist,	// HTML Tags Whitelist
        ],
        whitelistPatterns: [],
        whitelistPatternsChildren: []
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
            'sass-loader',
          ],
        },
      ],
    },
  }
];
