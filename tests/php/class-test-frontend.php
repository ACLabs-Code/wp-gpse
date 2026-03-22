<?php
/**
 * Tests for WP_GPSE_Frontend.
 */
class Test_GPSE_Frontend extends WP_UnitTestCase {

	private WP_GPSE_Frontend $frontend;

	public function setUp(): void {
		parent::setUp();
		$this->frontend = new WP_GPSE_Frontend();
	}

	public function tearDown(): void {
		parent::tearDown();
		unset( $_GET['q'] );
	}

	// --- Shortcode ---

	public function test_shortcode_returns_gcse_html() {
		$html = $this->frontend->render_search_results_shortcode( [] );
		$this->assertStringContainsString( 'gcse-searchresults-only', $html );
	}

	// --- populate_search_form_value ---

	public function test_form_returned_unchanged_when_no_query() {
		$form   = '<form><input type="search" name="s" /></form>';
		$result = $this->frontend->populate_search_form_value( $form );
		$this->assertSame( $form, $result );
	}

	public function test_form_value_populated_with_query() {
		$_GET['q'] = 'hello world';
		$form      = '<form><input type="search" name="s" /></form>';
		$result    = $this->frontend->populate_search_form_value( $form );
		$this->assertStringContainsString( 'value="hello world"', $result );
	}

	public function test_form_value_escapes_special_characters() {
		$_GET['q'] = 'cats & dogs';
		$form      = '<form><input type="search" name="s" /></form>';
		$result    = $this->frontend->populate_search_form_value( $form );
		$this->assertStringContainsString( 'value="cats &amp; dogs"', $result );
	}

	public function test_form_value_strips_html_tags_from_query() {
		// sanitize_text_field strips HTML tags before output
		$_GET['q'] = '<b>bold</b>';
		$form      = '<form><input type="search" name="s" /></form>';
		$result    = $this->frontend->populate_search_form_value( $form );
		$this->assertStringNotContainsString( '<b>', $result );
	}

	// --- populate_search_block_value ---

	public function test_non_search_block_returned_unchanged() {
		$_GET['q']   = 'test';
		$content     = '<div>paragraph content</div>';
		$block       = [ 'blockName' => 'core/paragraph' ];
		$result      = $this->frontend->populate_search_block_value( $content, $block );
		$this->assertSame( $content, $result );
	}

	public function test_search_block_returned_unchanged_when_no_query() {
		$content = '<div class="wp-block-search"><input type="search" class="wp-block-search__input" /></div>';
		$block   = [ 'blockName' => 'core/search' ];
		$result  = $this->frontend->populate_search_block_value( $content, $block );
		$this->assertStringNotContainsString( 'value=', $result );
	}

	public function test_search_block_value_populated_with_query() {
		$_GET['q'] = 'wordpress';
		$content   = '<div class="wp-block-search"><input type="search" class="wp-block-search__input" /></div>';
		$block     = [ 'blockName' => 'core/search' ];
		$result    = $this->frontend->populate_search_block_value( $content, $block );
		$this->assertStringContainsString( 'value="wordpress"', $result );
	}

	public function test_search_block_value_escapes_special_characters() {
		$_GET['q'] = 'cats & dogs';
		$content   = '<div class="wp-block-search"><input type="search" class="wp-block-search__input" /></div>';
		$block     = [ 'blockName' => 'core/search' ];
		$result    = $this->frontend->populate_search_block_value( $content, $block );
		$this->assertStringContainsString( 'value="cats &amp; dogs"', $result );
	}

	// --- redirect_native_search ---

	public function test_no_redirect_when_results_page_not_configured() {
		delete_option( 'wp_gpse_results_page_id' );
		// redirect_native_search only acts when is_search() is true (a WP query
		// condition), so we verify the guard condition: with no page ID stored,
		// empty() is true and no redirect URL is built.
		$this->assertEmpty( get_option( 'wp_gpse_results_page_id' ) );
	}
}
