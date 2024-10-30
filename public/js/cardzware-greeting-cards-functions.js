(function(win) {

    function getOrigViewportString() {
        let viewport = document.querySelector("meta[name=viewport]");
        return viewport != null ? viewport.content : "";
    }

    function openDialog(page, iframeId, modalId) {
        win.generateCardzwareModal(iframeId, modalId);
        document.ontouchmove = function (e) {
            e.preventDefault();
        }

        if (getOrigViewportString() != "") {
            let viewport = document.querySelector("meta[name=viewport]");
            viewport.setAttribute('content', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0');
        } else {
            var metaTag = document.createElement('meta');
            metaTag.name = "viewport"
            metaTag.content = "width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"
            document.getElementsByTagName('head')[0].appendChild(metaTag);
        }

        document.getElementById(iframeId).setAttribute('src', page);
        if (typeof pw_disable_scrolltop === 'undefined') {
            document.body.setAttribute('top', document.body.scrollTop * -1 + 'px');
        }
        var modal = document.getElementById(modalId);
        modal.classList.add('in');
        var modalBackdrop = document.getElementById('cw-modal-backdrop');
        modalBackdrop.classList.add('fade', 'in');
        modal.style.position = 'fixed';
        document.body.classList.add('pw-modal-open');
        modal.style.display = 'block';
        modalBackdrop.addEventListener('click', () => {
            closeDialog(iframeId, modalId);
        });
    }

    function closeDialog(iframeId, modalId) {
        document.ontouchmove = function (e) { return true; }

        let body = document.body;
        var offset = parseInt(jQuery("body").css('top'), 10);
        jQuery("body").removeClass("modal-open pw-modal-open");
        jQuery("body").css({'padding-right': '0px'});
        if (typeof pw_disable_scrolltop === 'undefined') {
            jQuery(win).scrollTop(offset * -1);
        }
        let viewport = document.querySelector("meta[name=viewport]");
        viewport.setAttribute('content', getOrigViewportString());
        jQuery('#' + iframeId).attr('src', 'about:blank');
        jQuery('#' + modalId).removeClass('in');
        jQuery('.modal-backdrop').removeClass('fade in');
        jQuery('#' + modalId).hide();

        setTimeout(win.clearThumbnailsCache, 1000);
    }

    function editOrder(url, iframeId, modalId) {
        win.openDialog(url, iframeId, modalId);
    }

    function addToCart(isIframe, jsonData, iframeId, modalId) {
        if (!isIframe) {
            closeDialog(iframeId, modalId);
        }

        let thumbURL = decodeURIComponent(jsonData.thumbURL);
        let orderId = parseInt(jsonData.yourOrderID);
        let isEditOrderMode = jsonData.editOrderMode == "yes" ? true : false;
        if (!isEditOrderMode) {
            win.woocommerce_cw_add_to_cart(thumbURL, orderId);
        }
    }

    function resizeIframe(elementId, pwRatio, pwMaxHeight, customCardHeight) {
        let iframe = document.getElementById(elementId);
        let newHeight = iframe.offsetWidth * pwRatio;
        newHeight = Math.min(newHeight, pwMaxHeight);
        if (typeof customCardHeight !== 'undefined') {
            newHeight = Math.min(newHeight, customCardHeight);
        }

        iframe.style.height = newHeight + 'px';
    }

    function isIframe(modalId) {
        let iframe = document.getElementById(modalId);
        return iframe == null ? true : iframe.style.display == 'none';
    }

    function showCurrentPluginVersion(version) {
        console.log('----------------------------------------');
        console.log("Cardzware Plugin Version: " + version);
        console.log('----------------------------------------');
    }

    win.openDialog = openDialog;
    win.closeDialog = closeDialog;
    win.editOrder = editOrder;
    win.addToCart = addToCart;
    win.resizeIframe = resizeIframe;
    win.isIframe = isIframe;
    win.showCurrentPluginVersion = showCurrentPluginVersion;

})(window);
