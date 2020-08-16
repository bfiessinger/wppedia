/**
 * External Dependencies
 */
import tippy from 'tippy.js';

/**
 * Internal Dependencies
 */
import { fetch_response__text } from './utils';

tippy(document.querySelectorAll('.wppedia-crosslink'), {
	content: '<div class="wppedia-tooltip-loading">Loading&hellip;</div>',
	theme: 'light',
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

		fetch_response__text( wppedia_tooltip_props.ajaxurl, {
			action: 'wppedia_generate_tooltip',
			post_id: cur_ref.getAttribute( 'data-post_id' )
		} )
			.then( ( response ) => {

				instance.setContent( response );

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
