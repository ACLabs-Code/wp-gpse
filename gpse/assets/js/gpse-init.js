/**
 * GPSE Search - Initialization Detection & Error Handling
 * Detects if Google CSE loads successfully and provides fallback messaging.
 *
 * @since 1.2.1
 */
(function() {
	'use strict';

	// Wait for DOM to be ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initGPSE);
	} else {
		initGPSE();
	}

	function initGPSE() {
		// Find all GPSE results containers
		var containers = document.querySelectorAll('.gcse-searchresults-only');

		if (containers.length === 0) {
			return; // No GPSE results on this page
		}

		// Set timeout to check if Google CSE initialized
		setTimeout(function() {
			containers.forEach(function(container) {
				// Check if Google CSE populated the container
				var hasResults = container.children.length > 1 ||
				                 container.querySelector('.gsc-results') !== null;

				if (!hasResults) {
					// Google CSE failed to initialize - show error message
					container.innerHTML = '<div class="gpse-error-message">' +
						'<p><strong>Search results could not be loaded.</strong></p>' +
						'<p>Please try refreshing the page or use the search box above to search again.</p>' +
						'<p><small>If this problem persists, please contact the site administrator.</small></p>' +
						'</div>';

					// Log error for debugging
					console.error('GPSE: Google CSE failed to initialize on this device');
					console.log('GPSE Debug Info:', {
						container: container,
						userAgent: navigator.userAgent,
						queryParam: new URLSearchParams(window.location.search).get('q')
					});
				}
			});
		}, 5000); // Wait 5 seconds for Google CSE to initialize
	}
})();
