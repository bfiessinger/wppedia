/**
 * External Dependencies
 */
import tippy from 'tippy.js';

/**
 * Internal Dependencies
 */
import { obj_serialize } from './utils';


/**
 * Get Postdata to build the tooltip
 * 
 * @param {string} url 
 * @param {object} data
 */
async function get_tooltip_postdata ( url = '', data = {} ) {

  const response = await fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8',
    },
		body: obj_serialize( data ),
		credentials: 'same-origin'
	});

	return response.text();
	
}

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

		get_tooltip_postdata( wppedia_tooltip_props.ajaxurl, {
			action: 'wppedia_generate_tooltip',
			post_id: cur_ref.getAttribute( 'data-post_id' )
		} )
			.then( ( response ) => {

				instance.setContent( response );

			} )
			.catch( ( error ) => {

				instance._error = error;
				instance.setContent( 'Request failed' );

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
