'use strict';

import pako from 'pako';
import Base64 from 'base-64';

class AjaxMonitor {
    constructor() {
        Object.assign(this, {
            tag: 'lt-'
        });
    }

    makeRequest(originalRequest) {
        return (mode) => {
            let request = new originalRequest;
            if (request.addEventListener) {
                request.addEventListener('readystatechange', this.onReadyStateChange.bind(this));
            } else if (request.attachEvent) {
                request.attachEvent('onreadystatechange', this.onReadyStateChange.bind(this));
            } else {
                request.readystatechange = this.onReadyStateChange.bind(this);
            }

            return request;
        }
    }

    onReadyStateChange(e) {
        let request = e.currentTarget;
        if (request.readyState === 4 && request.status === 200) {
            if (!window.Tracy) {
                return;
            }
            try {
                let headers = request.getAllResponseHeaders();
                let data = [];
                let tag = this.tag;
                let a;
                let b;
                let c;
                let d;
                while ((a = headers.indexOf(this.tag)) !== -1) {
                    headers = headers.substr(a + this.tag.length);
                    b = headers.indexOf(':');
                    c = parseInt(headers.substr(0, b));
                    d = b;
                    while (headers.charAt(++d) === ' ') {
                        a = headers.indexOf('\n');
                    }
                    data[c] = headers.substring(d, a);
                    headers = headers.substr(a);
                }
                if (!data.length) {
                    return;
                }
                let code = pako.inflate(Base64.decode(data.join("")), {
                    to: 'string'
                });
                eval(code);
                headers = null;
                data = null;
                code = null;
            } catch (e) {
            }
        }
    }
}

let monitor = new AjaxMonitor;

if (window.ActiveXobject) {
    window.ActiveXobject = monitor.makeRequest(window.ActiveXObject);
}

if (window.XMLHttpRequest) {
    window.XMLHttpRequest = monitor.makeRequest(window.XMLHttpRequest);
}
