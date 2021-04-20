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

		this.tagify = new Tagify( this.elem, {
			addTagOnBlur: true,
			maxTags: 3,
			whitelist: this.whitelist || [],
			transformTag: ( tagData ) => {
				tagData.value = this.stripTags( tagData.value )
			},
			templates: {
				tag: (tagData) => {
					console.log(tagData);
					return `
						<tag title="${(tagData.title || tagData.value)}"
							contenteditable='false'
							spellcheck='false'
							tabIndex="-1"
							class="${this.settings.classNames.tag} ${tagData.class ? tagData.class : ""}"
							${this.getAttributes(tagData)}>
						<x title='' class="${this.settings.classNames.tagX}" role='button' aria-label='remove tag'></x>
						<div>
								<span class="${this.settings.classNames.tagText}">${tagData[this.settings.tagTextProp] || tagData.value}</span>
						</div>
					</tag>`
				},
			},
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
