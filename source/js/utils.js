/**
 * Common Helper Utils
 * 
 * @since 1.0.0
 */

/**
 * External Dependencies
 */
import { isEmpty } from 'lodash';

/**
 * serialize an object
 * 
 * @param {object} obj 
 * @param {string} prefix 
 */
export const obj_serialize = ( obj, prefix ) => {

	let str = [];
	let p;
	
	for (p in obj) {

		if (obj.hasOwnProperty(p)) {
			const k = prefix ? prefix + '[' + p + ']' : p;
			const v = obj[p];
			str.push((v !== null && typeof v === 'object') ?
				serialize(v, k) :
				encodeURIComponent(k) + '=' + encodeURIComponent(v));
		}
		
	}
	
	return str.join('&');
	
}

/**
 * Make an AJAX fetch Call
 * 
 * @param {string} url 
 * @param {object} data
 */
export async function fetch_request( url = '', data = {} ) {

  const response = await fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8',
    },
		body: obj_serialize( data ),
		credentials: 'same-origin'
	});

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
export async function fetch_response__text( url = '', data = {} ) {
	const response = await fetch_request( url, data );
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
export async function fetch_response__blob( url = '', data = {} ) {
	const response = await fetch_request( url, data );
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
export async function fetch_response__json( url = '', data = {} ) {
	const response = await fetch_request( url, data );
	return response.json();
}
