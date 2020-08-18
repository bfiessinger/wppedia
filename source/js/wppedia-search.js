/**
 * External Dependencies
 */
//import FuzzySearch from 'fuzzy-search';
import Fuse from 'fuse.js/dist/fuse.basic.esm';

/**
 * Internal Dependencies
 */
import { 
	fetch_response__json, 
	insertAfter 
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
 * @param {string} str 
 */
function get_results( str, search_options ) {

	// Bail early if the string or the post_list is undefined
	if ( typeof str == undefined || typeof post_list == undefined ) {
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

	return results;

}

function render_results( search_results, render_to ) {

	const results_rendered_ID = 'wppedia_results_rendered';

	let results_rendered;
	if ( ! document.getElementById( results_rendered_ID ) ) {
		results_rendered = document.createElement('ul');
		results_rendered.id = results_rendered_ID;
	} else {
		results_rendered = document.getElementById( results_rendered_ID );
	}

	if ( search_results.length ) {
		results_rendered.style.display = '';
	} else {
		results_rendered.style.display = 'none';
	}

	// Insert Rendered results after `render_to`
	insertAfter( results_rendered, render_to );

	Array.prototype.forEach.call( search_results, ( sr ) => {
		
	} );

}

const search_input = document.getElementById( wppedia_search_props.searchinput_id );
search_input.addEventListener( 'keyup', ( e ) => {

	const _this = e.target;
	const str = _this.value;
	const search_results = get_results( str, wppedia_search_props.search_options );
	render_results( search_results, _this );

} );
