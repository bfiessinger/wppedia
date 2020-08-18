/**
 * Utility functions to work with Elements
 */

/**
 * Check whether a variable is an Element
 * 
 * @param {any} element 
 */
export function isElement(element) {
	return element instanceof Element || element instanceof HTMLDocument;  
}

/**
 * Append a dynamically generated HTMLElement to an 
 * existing Node. Content might be either a string or
 * another Element.
 * 
 * @param {Element} parentNode 
 * @param {string} elementTag 
 * @param {string} elementId 
 * @param {string|Element} content 
 */
export function appendElement(parentNode, elementTag, elementId, content) {
	const newElement = document.createElement(elementTag);
	newElement.setAttribute('id', elementId);

	if ( isElement( content ) ) {
		newElement.appendChild( content );
	} else {
		newElement.innerHTML = content;
	}

	parentNode.appendChild(newElement);
}

/**
 * Insert dynamically generated elements after existing ones
 * 
 * @param {Element} newNode 
 * @param {Element} existingNode 
 */
export function insertAfter(newNode, existingNode) {
	existingNode.parentNode.insertBefore(newNode, existingNode.nextSibling);
}

/**
 * Remove an Element from the DOM
 * 
 * @param {Element} element 
 */
export function removeElement(element) {
	element.parentNode.removeChild(element);
}
