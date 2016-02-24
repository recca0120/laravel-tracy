(function() {
    var AjaxMonitor = function(request) {
        return function(mode) {
            var req = new request(mode);
            var onReadyStateChange = function() {
                if (req.readyState === 4 && req.status === 200) {
                    window.req = req;
                    try {
                        if (req.responseType.toLowerCase() != "arraybuffer") {
                            var data = eval("("+req.responseText+")");
                        }
                    } catch (e) {
                    }
                }
            }
            req.addEventListener("readystatechange", onReadyStateChange);
            return req;
        }
    };
    if (window.ActiveXObject) {
        window.ActiveXObject = AjaxMonitor(window.ActiveXObject);
    }

    if (window.XMLHttpRequest) {
        window.XMLHttpRequest = AjaxMonitor(window.XMLHttpRequest);
    }
})();
