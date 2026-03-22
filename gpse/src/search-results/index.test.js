import { getBlockType, unregisterBlockType } from '@wordpress/blocks';
import './index.js';

describe( 'gpse/search-results block', () => {
	afterAll( () => {
		unregisterBlockType( 'gpse/search-results' );
	} );

	it( 'is registered', () => {
		expect( getBlockType( 'gpse/search-results' ) ).toBeDefined();
	} );

	it( 'has correct name', () => {
		expect( getBlockType( 'gpse/search-results' ).name ).toBe( 'gpse/search-results' );
	} );

	it( 'has correct category', () => {
		expect( getBlockType( 'gpse/search-results' ).category ).toBe( 'widgets' );
	} );

	it( 'has correct title', () => {
		expect( getBlockType( 'gpse/search-results' ).title ).toBe( 'GPSE Search Results' );
	} );

	it( 'save returns null (server-side rendered)', () => {
		const blockType = getBlockType( 'gpse/search-results' );
		expect( blockType.save( {} ) ).toBeNull();
	} );
} );
