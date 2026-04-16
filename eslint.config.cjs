const defaultConfig = require( '@wordpress/scripts/config/eslint.config.cjs' );

module.exports = [
	...defaultConfig,
	{
		rules: {
			// @wordpress/* packages are WordPress runtime globals — not npm packages.
			// They are resolved by webpack externals during build, not by Node.
			'import/no-unresolved': [ 'error', { ignore: [ '^@wordpress/' ] } ],
		},
	},
];
