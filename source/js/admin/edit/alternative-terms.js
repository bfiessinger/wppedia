import $ from "jquery";
import Tagify from '@yaireo/tagify';

/**
 * WordPress dependencies
 */
import { addAction, doAction } from '@wordpress/hooks';

class WPPedia_Edit_Alt_Terms {

	constructor() {
		this.elem = document.getElementById('wppedia_post_alt_tags');

		const callbacks = {};
		const templates = {};

		this.tagify = new Tagify( this.elem, {
			addTagOnBlur: true,
			maxTags: 3,
			whitelist: this.whitelist || [],
			transformTag: ( tagData ) => {
				tagData.value = this.stripTags( tagData.value )
			},
			templates,
			callbacks,
		} );
	}

	stripTags( html ) {
		// First decode.
		html = jQuery('<textarea />').html( html ).text();

		// Strip tags.
		var doc = new DOMParser().parseFromString( html, 'text/html' );
		var output = doc.body.textContent || "";

		// Strip remaining characters.
		return output.replace( /["<>]/g, '' ) || '(invalid)';
	}

}
export default WPPedia_Edit_Alt_Terms;
