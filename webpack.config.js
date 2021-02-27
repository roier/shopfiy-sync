const defaults = require("@wordpress/scripts/config/webpack.config");

module.exports = {
  ...defaults,
  mode: 'development',
  externals: {
    'react': 'React',
    'react-dom': 'ReactDOM'
  },
  resolve: {
    extensions: ['.js', '.jsx']
  }
};