/**
 * External Dependencies
 */
import Fuse from 'fuse.js/dist/fuse.basic.esm';
import { isEmpty } from '@s-libs/micro-dash';

/**
 * Internal Dependencies
 */
import { 
	fetch_response__json, 
	insertAfter,
	appendElement,
	removeElement
} from './utils';

// Instantiate Postlist Variable
let post_list;

// Fetch JSON
fetch_response__json( wppedia_search_props.postlist_url, {}, 'GET' )
	.then( ( response ) => {
		post_list = response;
	} );

/**
 * Get search results from string
 * 
 * @param {string} str 
 * 
 * @return {boolean|Fuse} Search results
 */
function get_results( str, search_options ) {

	// Bail early if the string or the post_list is undefined
	if ( typeof str == 'undefined' || str == '' || typeof post_list == 'undefined' ) {
		return false;
	}

	/**
	 * Filter!
	 * @see https://www.npmjs.com/package/fuse.js
	 */
	const search_opt_obj = JSON.parse( search_options );

	// Create the Fuse index
	const search_index = Fuse.createIndex(search_opt_obj.keys, post_list);
	const searcher = new Fuse(post_list, search_opt_obj, search_index);

	const results = searcher.search( str, {limit: 15} );

	// Return false on an empty object
	if ( isEmpty( results ) ) {
		return false;
	}

	return results;

}

/**
 * Render the results wrapper and return the
 * element
 */
function render_result_wrapper() {

	const results_rendered_ID = 'wppedia_results_rendered';

	let results_rendered;
	if ( ! document.getElementById( results_rendered_ID ) ) {
		results_rendered = document.createElement('ul');
		results_rendered.id = results_rendered_ID;
	} else {
		results_rendered = document.getElementById( results_rendered_ID );
	}

	return results_rendered;

}

/**
 * Render Search Results
 * using "false" as the search_results param removes
 * the results  
 * 
 * @param {boolean|Fuse} search_results 
 * @param {Element} render_to 
 * 
 * @return {void}
 */
function render_results( search_results, render_to ) {

	const results_rendered = render_result_wrapper();

	// Insert Rendered results after `render_to`
	insertAfter( results_rendered, render_to );
	
	results_rendered.innerHTML = '';

	if ( search_results === false ) {
		removeElement( results_rendered );
		return;
	}

	// Re/Create Result list
	Array.prototype.forEach.call( search_results, ( sr ) => {

		const res_item = sr.item;

		const listitem_content = document.createElement('a');
		listitem_content.href = res_item.url;
		// Make Elements accessible
		listitem_content.tabIndex = 0;
		listitem_content.innerHTML = res_item.post_title;

		appendElement( results_rendered, 'li', 'res_id_' + res_item.post_id, listitem_content );

	} );

}

const search_input = document.getElementById( wppedia_search_props.searchinput_id );

if ( search_input ) {

	// Event Listeners to trigger the renderer
	const input_listeners = [
		'keyup',
		'focus',
		'click',
		'search'
	];

	input_listeners.forEach( ( listener ) => {

		search_input.addEventListener( listener, ( e ) => {

			const _this = e.target;
			const str = _this.value;
			const search_results = get_results( str, wppedia_search_props.search_options );

			render_results( search_results, _this.form.lastChild );
		
		} );

	} );

	// Detect clicks outside the searchform
	document.addEventListener('click', function(event) {

		const isClickInside = search_input.parentElement.contains( event.target );
		if ( ! isClickInside ) {
			render_results( false, document.body );
		}
		
	});

}
