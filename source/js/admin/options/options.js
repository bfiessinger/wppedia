import jQuery from "jquery";

/**
 * WordPress dependencies
 */
import { addAction, doAction } from '@wordpress/hooks';

/**
 * Internal dependencies
 */
import WPPedia_Select2 from './select';

class WPPedia_Options {

	setup() {
		this.registerComponents();
		addAction('wppedia_loaded', 'wppedia', this.registerComponents, 1);
	}

	registerComponents() {
		this.components = {};
		this.components.select2 = new WPPedia_Select2();
	}

}

jQuery(() => {
	window.WPPedia_Options = new WPPedia_Options();
	window.WPPedia_Options.setup();
})

jQuery(window).on('load', () => {
	jQuery.when( jQuery.ready ).then(() => {
		doAction('wppedia_loaded');
	})
});
