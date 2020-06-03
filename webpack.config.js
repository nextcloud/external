const path = require('path')

module.exports = {
	entry: {
		admin: path.join(__dirname, 'src', 'admin.js'),
	},
	output: {
		path: path.join(__dirname, 'js', 'dist'),
	},
	devtool: 'source-map',
	mode: process.env.NODE_ENV === 'production' ? 'production' : 'development',
	module: {
		rules: [
			{
				test: /\.js$/,
				loader: 'babel-loader',
				exclude: /node_modules/,
			},
		],
	},
	resolve: {
		extensions: ['.js', '.vue'],
	},
}
