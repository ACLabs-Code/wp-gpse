import metadata from './block.json';

describe( 'gpse/search-results block metadata', () => {
	it( 'has correct name', () => {
		expect( metadata.name ).toBe( 'gpse/search-results' );
	} );

	it( 'has correct category', () => {
		expect( metadata.category ).toBe( 'widgets' );
	} );

	it( 'has correct title', () => {
		expect( metadata.title ).toBe( 'GPSE Search Results' );
	} );

	it( 'uses Block API v3', () => {
		expect( metadata.apiVersion ).toBe( 3 );
	} );

	it( 'has correct text domain', () => {
		expect( metadata.textdomain ).toBe( 'gpse' );
	} );
} );
