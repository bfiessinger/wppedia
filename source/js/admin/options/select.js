// require('select2');
import jQuery from "jquery";

/**
 * WordPress dependencies
 */
import { addAction, doAction } from '@wordpress/hooks';

class WPPedia_Select2 {

	constructor() {
		this.select2 = jQuery('.wppedia-select2');
		this.select2DefaultArgs = {
			multiple: typeof(this.select2.attr('multiple')) !== 'undefined',
		};

		this.select2Init();
	}

	select2Init() {
		this.select2.each(function() {
			if (jQuery(this).data('remote-options')) {

				const remoteOptions = jQuery(this).data('remote-options');

				const remoteType = remoteOptions.type;
				const remoteEndpoint = remoteOptions.endpoint;
				const remoteArgs = remoteOptions.args;

				jQuery(this).select2({
					...this.select2DefaultArgs,
					minimumInputLength: 3,
					ajax: {
						url: remoteEndpoint,
						dataType: 'json',
						data: function(params) {
							const data = {};

							Object.entries(remoteArgs).forEach(function(arg) {
								data[arg[0]] = arg[1];
							})

							if (remoteType === 'WP_API') {
								data.search = params.term;
								data.page = params.page || 1;
								data.per_page = remoteArgs.per_page || 10;
							} else {
								console.warn('Remote type not supported.');
							}

							return data;
						},
						processResults: function (data, params) {
							params.page = params.page || 1;

							return {
								results: jQuery.map( data, function( obj ) {
									return { id: obj.id, text: obj.title.rendered };
								} ),
							};
						}
					},
				});
			} else {
				jQuery(this).select2({
					...this.select2DefaultArgs,
				});
			}
		});
	}

}
export default WPPedia_Select2;
