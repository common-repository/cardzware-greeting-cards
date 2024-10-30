(function(win) {

    function generateCardzwareModal(iframeId, modalId) {
        document.body.innerHTML += '<div class="bootstrap-wrapper"><div class="modal fade" id="' + modalId  + '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" style="display:none;" ><div class="modal-dialog"><div class="modal-content"><div class="modal-body"><iframe id="' + iframeId  + '" style="border: 0px;" src="about:blank" width="100%" height="100%"></iframe></div></div></div></div><div id="cw-modal-backdrop" class="modal-backdrop"></div></div>';
    }

    function clearThumbnailsCache () {
        let xpath = "//img[contains(@src,'https://pwcdn.net/thumbnails/') or contains(@srcset,'https://pwcdn.net/thumbnails/') or contains(@src,'viewthumb.php') or contains(@srcset,'viewthumb.php')]";
        let matchingElements = document.evaluate(xpath, document, null, XPathResult.ORDERED_NODE_SNAPSHOT_TYPE, null);
        for(let i = 0; i < matchingElements.snapshotLength; i++) {
            let img = matchingElements.snapshotItem(i);
            let newSrc;
            if (img['src'].indexOf('?o=') !== -1) {
                newSrc = img['src'].split('&r=')[0] + '&r=' + new Date().getTime();
            } else {
                newSrc = img['src'].split('?r=')[0] + '?r=' + new Date().getTime();
            }

            img.setAttribute('src', newSrc);
            if (img.getAttribute('srcset') !== undefined) {
                img.setAttribute('srcset', newSrc);
            }
        }
    }
    
    function addClickEventToDynamicElements() {
        const cartItems = document.querySelectorAll('.card-edit-order-link');
        cartItems.forEach(cartItem => {
            if (!cartItem.classList.contains('event-added')) {
                cartItem.classList.add('event-added');
                cartItem.addEventListener('click', (e) => {
                    e.preventDefault();
                    win.openDialog(cartItem.dataset.openurl, cartItem.dataset.iframeid, cartItem.dataset.modalid);
                    return false;
                });
            }
        });
    }

    const observer = new MutationObserver((mutations) => {
        mutations.forEach(mutation => {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                addClickEventToDynamicElements();
            }
        });
    });

    observer.observe(document, {
        childList: true,
        subtree: true
    });

    win.clearThumbnailsCache = clearThumbnailsCache;
    win.generateCardzwareModal = generateCardzwareModal;
})(window);
