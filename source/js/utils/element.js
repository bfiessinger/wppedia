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
 * existing Node
 * 
 * @param {Element} parentNode 
 * @param {string} elementTag 
 * @param {string} elementId 
 * @param {string} html 
 */
export function appendElement(parentNode, elementTag, elementId, html) {
	const newElement = document.createElement(elementTag);
	newElement.setAttribute('id', elementId);
	newElement.innerHTML = html;
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
 * @param {Element} elementId 
 */
export function removeElement(elementId) {
	// Removes an element from the document
	var element = document.getElementById(elementId);
	element.parentNode.removeChild(element);
}
