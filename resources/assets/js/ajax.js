(function() {
  var bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

  (function() {
    var AjaxMonitor, log, monitor;
    log = function() {
      if (console.log && window.debug === true) {
        return console.log.apply(console, arguments);
      }
    };
    AjaxMonitor = (function() {
      function AjaxMonitor() {
        this.makeRequest = bind(this.makeRequest, this);
        this.onReadyStateChange = bind(this.onReadyStateChange, this);
        this.zlib = bind(this.zlib, this);
        this.pako = bind(this.pako, this);
      }

      AjaxMonitor.prototype.tag = "lt-";

      AjaxMonitor.prototype.pako = function(compressed) {
        var code;
        return code = pako.inflate(compressed, {
          to: "string"
        });
      };

      AjaxMonitor.prototype.zlib = function(compressed) {
        var code, i, inflate, output, ref, s, temp;
        temp = [];
        ref = compressed.split('');
        for (i in ref) {
          s = ref[i];
          temp.push(s.charCodeAt(0));
        }
        inflate = new Zlib.Inflate(temp);
        output = inflate.decompress();
        code = "";
        for (i in output) {
          s = output[i];
          code += String.fromCharCode(s);
        }
        return code;
      };

      AjaxMonitor.prototype.onReadyStateChange = function(e) {
        var a, b, base64Data, c, code, d, data, error, headers, request;
        request = e.currentTarget;
        if (request.readyState === 4 && request.status === 200 && request.responseType.toLowerCase() !== "arraybuffer") {
          try {
            headers = request.getAllResponseHeaders();
            data = [];
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
            base64Data = data.join("");
            if (window.pako) {
              code = this.pako(atob(base64Data));
            } else {
              code = this.zlib(atob(base64Data));
            }
            eval(code);
            log(base64Data.length, code.length);
            headers = null;
            data = null;
            base64Data = null;
            return code = null;
          } catch (error) {
            e = error;
            return log(e);
          }
        }
      };

      AjaxMonitor.prototype.makeRequest = function(originalRequest) {
        return (function(_this) {
          return function(mode) {
            var request;
            request = new originalRequest;
            if (request.addEventListener) {
              request.addEventListener("readystatechange", _this.onReadyStateChange);
            } else if (request.attachEvent) {
              request.attachEvent("onreadystatechange", _this.onReadyStateChange);
            } else {
              request.readystatechange = _this.onReadyStateChange;
            }
            return request;
          };
        })(this);
      };

      return AjaxMonitor;

    })();
    monitor = new AjaxMonitor;
    if (window.ActiveXobject) {
      window.ActiveXObject = monitor.makeRequest(window.ActiveXObject);
    }
    if (window.XMLHttpRequest) {
      return window.XMLHttpRequest = monitor.makeRequest(window.XMLHttpRequest);
    }
  })();

}).call(this);
