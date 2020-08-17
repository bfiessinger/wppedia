/**
 * External Dependencies
 */
import fuzzy from 'fuzzy';

/**
 * Internal Dependencies
 */
import { fetch_response__json } from './utils';

let post_list;

fetch_response__json( wppedia_search_props.posts_url, {}, 'GET' )
	.then( ( response ) => {

		post_list = response;

	} );

function display_results( str ) {

	// Bail early if the string is undefined
	if ( ! str ) {
		return;
	}
	
	const options = {
		pre: '<b>', 
		post: '</b>',
		/**
		 * Each Element in the postlist is an object. We can pass in a
		 * function that is called on each element in the array to extract the
		 * string to fuzzy search against. In this case, element.dir
		 * 
		 * @param {object} entry 
		 */
		extract: function(entry) {
			return entry.post_title;
		}
	}

  // Filter!
	const filtered = fuzzy.filter(str, post_list, options);
	
	return filtered;

}

const wpPedia_search = document.getElementsByClassName('wppedia-search')[0];
const search_input = wpPedia_search.getElementsByClassName('search-field')[0];

search_input.addEventListener( 'keyup', ( e ) => {

	const str = e.target.value;

	console.log( display_results( str ) );

} );
