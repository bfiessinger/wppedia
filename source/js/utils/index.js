/**
 * Common Helper Utils
 * 
 * @since 1.0.0
 */

import {
	isElement,
	appendElement,
	insertAfter,
	removeElement
} from './element';

import {
	obj_serialize
} from './object';

import {
	fetch_request,
	fetch_response__text,
	fetch_response__blob,
	fetch_response__json
} from './ajax';

export {
	// Element Utils
	isElement,
	appendElement,
	insertAfter,
	removeElement,
	// Object Utils
	obj_serialize,
	// AJAX Utils
	fetch_request,
	fetch_response__text,
	fetch_response__blob,
	fetch_response__json
};
