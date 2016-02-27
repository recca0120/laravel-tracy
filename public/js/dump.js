(function() {
  var bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

  (function() {
    var Dumper;
    Dumper = (function() {
      function Dumper() {
        this.dump = bind(this.dump, this);
        this.format = bind(this.format, this);
        this.sizeOf = bind(this.sizeOf, this);
        this.typeOf = bind(this.typeOf, this);
      }

      Dumper.prototype.typeOf = function(obj) {
        if (obj === null) {
          return "null";
        }
        if (obj instanceof Array) {
          return "array";
        }
        return typeof obj;
      };

      Dumper.prototype.sizeOf = function(obj) {
        var key, size;
        size = 0;
        for (key in obj) {
          if (obj.hasOwnProperty(key)) {
            size++;
          }
        }
        return size;
      };

      Dumper.prototype.format = function(data, options, level) {
        var collapsed, item, key, length, result, type;
        type = this.typeOf(data);
        switch (type) {
          case "object":
          case "array":
            length = type === "object" ? this.sizeOf(data) : data.length;
            if (length === 0) {
              return "<span class=\"tracy-dump-array\">array</span> ()</span>\n";
            }
            collapsed = level >= options.depth - 1 ? "tracy-collapsed" : "";
            result = "";
            result += "<span class=\"tracy-toggle " + collapsed + "\">";
            result += "<span class=\"tracy-dump-array\">array</span> (" + length + ")</span>\n";
            result += "<div class=\"" + collapsed + "\">";
            for (key in data) {
              item = data[key];
              result += "<span class=\"tracy-dump-indent\">   </span><span class=\"tracy-dump-key\">" + key + "</span> =&gt; ";
              result += this.format(item, options, level++);
            }
            result += "</div></span>";
            return result;
          case "boolean":
            result = data === true ? "TRUE" : "FALSE";
            return "<span class=\"tracy-dump-bool\">" + result + "</span>\n";
          case "number":
            return "<span class=\"tracy-dump-number\">" + data + "</span>\n";
          case "null":
            return "<span class=\"tracy-dump-null\">NULL</span>\n";
          default:
            return "<span class=\"tracy-dump-string\">\"" + data + "\"</span> (" + data.length + ")\n";
        }
      };

      Dumper.prototype.dump = function(data, options) {
        if (!options) {
          options = {};
        }
        if (!options.depth) {
          options.depth = 2;
        }
        return ("&lt;pre class=\"tracy-dump\"&gt;" + (this.format(data, options, 0)) + "&lt;/pre&gt;").replace(/&lt;/g, '<').replace(/&gt;/g, '>');
      };

      return Dumper;

    })();
    return window.TracyDump = (function() {
      var dumper;
      dumper = new Dumper;
      return function(data, options) {
        return dumper.dump(data, options);
      };
    })();
  })();

}).call(this);
