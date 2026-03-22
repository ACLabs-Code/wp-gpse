module.exports = {
	extends: [ 'plugin:@wordpress/eslint-plugin/recommended' ],
	rules: {
		// @wordpress/* packages are WordPress runtime globals — not npm packages.
		// They are resolved by webpack externals during build, not by Node.
		'import/no-unresolved': [ 'error', { ignore: [ '^@wordpress/' ] } ],
	},
};
