const path = require('path');
const glob = require('glob-all');

// Plugins
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const PurgecssPlugin = require('purgecss-webpack-plugin');

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
  // Compile CSS
  {
    mode: 'production',
    entry: {
      style: './source/scss/_main.scss',
    },
    output: {
      path: path.resolve(__dirname, 'dist/css'),
      filename: '[name].bundle.js'
    },
    plugins: [
      new MiniCssExtractPlugin({
        filename: '[name].css',
        chunkFilename: '[id].css'
      }),
      new PurgecssPlugin({
        paths: PurgecssFiles,
        whitelist: [
          ...purgecssHTMLTags.whitelist,						            // HTML Tags Whitelist
        ],
        whitelistPatterns: [],
        whitelistPatternsChildren: []
      })
    ],
    module: {
      rules: [
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
