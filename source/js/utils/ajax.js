/**
 * Utility functions to work with AJAX
 */

/**
 * External Dependencies
 */
import { isEmpty } from '@s-libs/micro-dash';

/**
 * Internal Dependencies
 */
import { obj_serialize } from './object';

/**
 * Make an AJAX fetch Call
 * 
 * @param {string} url 
 * @param {object} data
 */
export async function fetch_request( url = '', data = {}, method = 'POST' ) {

	let fetch_arguments = {
		method: method,
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8',
		},
		credentials: 'same-origin'
	};

	if ( method != 'GET' && method != 'HEAD' && ! isEmpty( data ) ) {
		fetch_arguments.body = obj_serialize( data );
	}

  const response = await fetch(url, fetch_arguments);

	return response;
	
}

/**
 * Fetch data and return text
 * 
 * @uses fetch_request()
 * 
 * @param {string} url 
 * @param {object} data
 */
export async function fetch_response__text( url = '', data = {}, method = 'POST' ) {
	const response = await fetch_request( url, data, method );
	return response.text();
}

/**
 * Fetch data and return a blob
 * 
 * @uses fetch_request()
 * 
 * @param {string} url 
 * @param {object} data
 */
export async function fetch_response__blob( url = '', data = {}, method = 'POST' ) {
	const response = await fetch_request( url, data, method );
	return response.blob();
}

/**
 * Fetch data and return json
 * 
 * @uses fetch_request()
 * 
 * @param {string} url 
 * @param {object} data
 */
export async function fetch_response__json( url = '', data = {}, method = 'POST' ) {
	const response = await fetch_request( url, data, method );
	return response.json();
}
