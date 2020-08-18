/**
 * Utility functions to work with objects
 */

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
