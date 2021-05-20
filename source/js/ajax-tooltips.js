/**
 * External Dependencies
 */
import tippy from 'tippy.js';

/**
 * Internal Dependencies
 */
import { fetch_response__text } from './utils';

// Halt requests for later usage
const requested = [];

tippy(document.querySelectorAll('.wppedia-crosslink'), {
	appendTo: document.body,
	content: '<div class="wppedia-tooltip-loading">Loading&hellip;</div>',
	theme: wppedia_tooltip_props.tooltip_theme,
	maxWidth: 320,
	animation: 'shift-toward',
	interactive: true,
	allowHTML: true,
	onCreate( instance ) {

		instance._isFetching = false;
		instance._error = null;

	},
	onShow( instance ) {

		if (
			instance._isFetching || 
			instance._error
		) {
      return;
		}
		
		instance._isFetching = true;

		const cur_ref = instance.reference;
		const similar_refs = document.querySelectorAll('[data-post_id="' + cur_ref.getAttribute('data-post_id') + '"]');

		// Skip Ajax loading if the request has been done before
		if (cur_ref.getAttribute('data-request') && requested[cur_ref.getAttribute('data-request')]) {
			instance.setContent(requested[cur_ref.getAttribute('data-request')]);
			instance._isFetching = false;
			return;
		}

		fetch_response__text( wppedia_tooltip_props.ajaxurl, {
			action: 'wppedia_generate_tooltip',
			post_id: cur_ref.getAttribute( 'data-post_id' )
		} )
			.then( ( response ) => {

				instance.setContent( response );
				requested.push(response);

				if (similar_refs) {
					Array.prototype.forEach.call(similar_refs, (ref) => {
						ref.setAttribute('data-request', requested.length - 1);
					});
				}

			} )
			.catch( ( error ) => {

				instance._error = error;
				instance.setContent( '<div class="wppedia-tooltip-error">Request failed</div>' );

			} )
			.finally(() => {
			
				instance._isFetching = false;
			
			});

	},
  onHidden( instance ) {

    instance.setContent('<div class="wppedia-tooltip-loading">Loading&hellip;</div>');
    // Unset these properties so new network requests can be initiated
		instance._error = null;
		
  },
});
