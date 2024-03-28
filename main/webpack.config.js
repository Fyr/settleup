const path = require('path');
const webpack = require('webpack');
const {WebpackManifestPlugin} = require('webpack-manifest-plugin');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
    target: ['web'],
    entry: './public/js/index.js',
    output: {
        publicPath: '',
        filename: 'app.[fullhash].js',
        path: path.resolve(__dirname, 'public/dist'),
        chunkFormat: false,
    },
    module: {
        rules: [
            {
                test: /\.scss$/,
                use: [MiniCssExtractPlugin.loader, "css-loader"]
            },
            {
                test: /\.css$/,
                use: [MiniCssExtractPlugin.loader, "css-loader"]
            }
        ]
    },
    plugins: [
        new MiniCssExtractPlugin(),
        new WebpackManifestPlugin({
            publicPath: '',
        }),
        new CleanWebpackPlugin()
    ]
};
