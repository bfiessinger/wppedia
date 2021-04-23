import jQuery from "jquery";

/**
 * WordPress dependencies
 */
import { addAction, doAction } from '@wordpress/hooks';

/**
 * Internal dependencies
 */
import WPPedia_Edit_Alt_Terms from './alternative-terms';

class WPPedia_Edit {

	setup() {
		this.registerComponents();
		addAction('wppedia_loaded', 'wppedia', this.registerComponents, 1);
	}

	registerComponents() {
		this.components = {};
		this.components.alternativeTerms = new WPPedia_Edit_Alt_Terms();
	}

}

jQuery(() => {
	window.WPPedia_Edit = new WPPedia_Edit();
	window.WPPedia_Edit.setup();
})

jQuery(window).on('load', () => {
	jQuery.when( jQuery.ready ).then(() => {
		doAction('wppedia_loaded');
	})
});
