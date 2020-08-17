/**
 * External Dependencies
 */
import FuzzySearch from 'fuzzy-search';

/**
 * Internal Dependencies
 */
import { fetch_response__json } from './utils';

// Instantiate Postlist Variable
let post_list;

// Fetch JSON
fetch_response__json( wppedia_search_props.postlist_url, {}, 'GET' )
	.then( ( response ) => {
		post_list = response;
	} );

/**
 * 
 * @param {*} str 
 */
function render_postlist( str, appendTo ) {

	// Bail early if the string or the post_list is undefined
	if ( typeof str == undefined || typeof post_list == undefined ) {
		return false;
	}

	/**
	 * Filter!
	 * @see https://www.npmjs.com/package/fuzzy-search
	 */	
	const searcher = new FuzzySearch(
		post_list, 
		[ 
			'post_title' 
		], 
		{
			sort: true,
		}
	);

	const result = searcher.search( str );

	console.log( result );

	/*
	// Map the results to the html we want generated
	const results = filtered.slice(0,50).map(function(result){
		return listItemTemplate({
				freebase: result.original.fb
			, director: result.string
			, movies: result.original.movies
			, score: result.score
		});
	});

	// Update the html
	$('#lists').html(results.join(''));
	*/

	return result;

}

const searchform = document.getElementById( wppedia_search_props.searchform_id );
const search_input = searchform.getElementsByClassName('search-field')[0];

search_input.addEventListener( 'keyup', ( e ) => {

	const str = e.target.value;

	console.log( render_postlist( str ) );

} );
