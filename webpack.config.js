const path = require('path');

// Plugins
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssoWebpackPlugin = require('csso-webpack-plugin').default;
const TerserJSPlugin = require('terser-webpack-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');

const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );

const jsExternals = {
	jquery: 'jQuery',
	'@yaireo/tagify': 'Tagify',
	select2: 'select2',
	// WordPress Packages.
	'@wordpress/hooks': 'wp.hooks',
}

const jsEntryPoints = {
	// Frontend
	ajax_tooltip: './source/js/ajax-tooltips.js',
	search: './source/js/wppedia-search.js',
	// Backend
	edit: './source/js/admin/edit/edit.js',
	options: './source/js/admin/options/options.js',
};

const isDev = process.argv[process.argv.indexOf('--mode') + 1] === 'development';

const cssEntryPoints = {
	// Frontend
	style: './source/scss/_main.scss',
	// Frontend for inline usage
	'tooltip-theme-light-border': './source/scss/components/tooltip/tippy-theme-light-border.scss',
	'tooltip-theme-material': './source/scss/components/tooltip/tippy-theme-material.scss',
	'tooltip-theme-translucent': './source/scss/components/tooltip/tippy-theme-translucent.scss',
	// Backend
	admin: './source/scss/admin/_main.scss'
};

module.exports = [
	// Compile Javascript
	{
		mode: isDev ? 'development' : 'production',
		entry: jsEntryPoints,
		optimization: {
			minimize: !isDev,
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
		externals: jsExternals,
		module: {
			rules: [
				{
					test: /\.(jsx?)$/,
					exclude: /node_modules/,
					use: ['babel-loader']
				},
			]
		},
		plugins: [
			new DependencyExtractionWebpackPlugin(),
		]
	},
	// Compile CSS
	{
		mode: isDev ? 'development' : 'production',
		entry: cssEntryPoints,
		output: {
			path: path.resolve(__dirname, 'dist/css'),
			filename: '[name].bundle.js',
			assetModuleFilename: '../asset/[name][ext]',
		},
		optimization: {
			minimize: !isDev,
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
					type: 'asset/resource'
				},
				{
					test: /\.svgz?$/,
					type: 'asset/resource',
					use: [
						{
							loader: 'svgo-loader'
						}
					]
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
				filename: '[name].min.css',
				chunkFilename: '[id].css'
			}),
			new CssoWebpackPlugin()
		],
	},
	{
		mode: isDev ? 'development' : 'production',
		entry: {},
		output: {
			path: path.resolve(__dirname, 'dist'),
			filename: '[name].bundle.js'
		},
		plugins: [
			new CopyWebpackPlugin({
				patterns: [
					{
						from: 'tagify.(min.js|css)',
						to: path.resolve(__dirname, 'dist/vendor/tagify'),
						context: path.resolve(__dirname, 'node_modules/@yaireo/tagify/dist')
					},
					{
						from: 'select2.min.css',
						to: path.resolve(__dirname, 'dist/vendor/select2'),
						context: path.resolve(__dirname, 'node_modules/select2/dist/css')
					},
					{
						from: 'select2.min.js',
						to: path.resolve(__dirname, 'dist/vendor/select2'),
						context: path.resolve(__dirname, 'node_modules/select2/dist/js')
					},
				]
			})
		]
	}
];
