'use strict';

var modalId, iframeId, productId, apiUrl;
let pwRatio, pwMaxHeight, customCardHeight;

(function(win) {
	if (typeof jQuery === 'undefined') {
		throw new Error('Bootstrap\'s JavaScript requires jQuery')
	}

	function get_values_js_variables() {
		if ('undefined' === typeof ajax_call) { return false; }

		jQuery.ajax({
			url: ajax_call.url,
			type: 'GET',
			dataType: 'json',
			data: {
				action: ajax_call.action,
				nonce: ajax_call.nonce,
			},
			success: function(result) {
				modalId = result['modalId'];
				iframeId = result['iframeId'];
				productId = result['productId'];
				apiUrl = result['apiUrl'];
				window.showCurrentPluginVersion(result['cwPluginVersion'])
				iframeJsCodeLoader(modalId, iframeId);
			},
			error: function(xhr, ajaxOptions, thrownError) {
				console.log(xhr.status);
				console.log(thrownError);
			}
		});
	}

	get_values_js_variables();

	function iframeJsCodeLoader(modalId, iframeId) {
		let pwRatio, pwMaxHeight;
		if ((typeof loadPW !== 'undefined') && (loadPW == true)) {
			let isIframe = win.isIframe(modalId);
			console.log("cw: iframe js code loaded");
			win.onresize = () => {
				if (isIframe) {
					win.resizeIframe(iframeId, pwRatio, pwMaxHeight, customCardHeight);
				}
			}

			let returnData = {};
			returnData["func"] = "getheight";

			if (isIframe) {
				let iframe = document.getElementById(iframeId);
				iframe.contentWindow.postMessage("func=getheight", apiUrl);
			}
		}

		if (typeof pwOrderID !== 'undefined') {
			jQuery("body").on('DOMSubtreeModified', function () {
				var newOrderID = (new Date().getTime()).toString(16) + Math.random().toString(36).slice(11);
				jQuery('#pw_order_id').val(newOrderID);
			});
		}

		if (typeof pw_onready === 'function') {
			pw_onready();
		}
	}

	function woocommerce_cw_add_to_cart(thumbUrl, orderId) {
		if ('undefined' === typeof ajax_var) { return false; }

		jQuery.ajax({
			type: 'post',
			url: wc_add_to_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'add_to_cart' ),
			data: {
				action: 'woocommerce_ajax_add_to_cart',
				product_id: productId,
				quantity: 1,
			},
			complete: function(response) {
				if (!response) { return;
				}
				if ( response.error && response.product_url ) {
					win.location = response.product_url;
					return;
				}

				jQuery.ajax({
					url: ajax_var.url,
					type: 'POST',
					data: 'action=' + ajax_var.action + '&productId=' + productId + '&thumbUrl=' + thumbUrl + '&orderId=' + orderId,
					success: function(result) {
						win.location = wc_add_to_cart_params.cart_url;
					},
					error: function(xhr, ajaxOptions, thrownError) {
						console.log(xhr.status);
						console.log(thrownError);
					}
				});
			}
		});
	}

	win.addEventListener("message", (e) => {
		if (typeof apiUrl === 'undefined' || e.data instanceof Object || e.origin !== apiUrl) return;

		let isIframe = win.isIframe(modalId);
		let iframe = document.getElementById(iframeId);

		var returnData = JSON.parse('{"' + decodeURI(e.data).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g, '":"') + '"}');
		switch (returnData.func) {
			case "scroll":
				if (!isIframe) {
					jQuery(win).scrollTop(returnData.scrollTo);
				}
				break;
			case "newheight":
				iframe.style.height = returnData['height'];
				break;
			case "ratio":
				jQuery('#currency_symbol_price_message').hide();
				pwRatio = returnData['ratio'];
				pwMaxHeight = returnData['maxHeight'];
				win.resizeIframe(iframeId, pwRatio, pwMaxHeight, customCardHeight);
				jQuery('html, body').animate({scrollTop: jQuery('#' + iframeId).offset().top}, 500);
				break;
			case "requestDimensions":
				let sendData = {};
				sendData["func"] = "newDimensions";
				sendData["width"] = jQuery(win).width();
				sendData["height"] = jQuery(win).height();
				iframe.contentWindow.postMessage(sendData, apiUrl);
				break;
			case "resizeModal":
				var newWidthPer = (returnData['width'] * 100) + '%';
				var newHeightPer = (returnData['height'] * 100) + '%';

				var newWidth = jQuery(win).width() * returnData['width'];
				var newHeight = jQuery(win).height() * returnData['height'];

				var newLeft = (jQuery(win).width() - newWidth) / 2;
				var newTop = (jQuery(win).height() - newHeight) / 2;
				jQuery('#' + modalId + '.fade.in').animate(
					{
						'top': newTop,
						'left': newLeft
					},
					{
						'queue': false,
						'duration': 350
					}
				);

				jQuery('#' + modalId + '.modal').animate(
					{
						'width': newWidth,
						'height': newHeight
					},
					{
						'queue': false,
						'duration': 350,
						'complete': function () {
							jQuery('#' + modalId + '.fade.in').css('left', 'calc((100% - ' + newWidthPer + ')/2)');
							jQuery('#' + modalId + '.fade.in').css('top', 'calc((100% - ' + newHeightPer + ')/2)');
							jQuery('#' + modalId + '.modal').css('width', newWidthPer);
							jQuery('#' + modalId + '.modal').css('height', newHeightPer);
						}
					}
				);
				break;
			case "addCard":
			case "":
				win.addToCart(isIframe, returnData, iframeId, modalId);
				break;
			case undefined:
			case "cancel":
			case "card":
				if (isIframe) {
					jQuery('#' + iframeId).hide();
				} else {
					win.closeDialog(iframeId, modalId);
					if (typeof pw_post_product_modal_cancel === 'function') {
						pw_post_product_modal_cancel(returnData.yourOrderID);
					}
				}
				break;
		}
	}, false);

	win.woocommerce_cw_add_to_cart = woocommerce_cw_add_to_cart;

})(window);
