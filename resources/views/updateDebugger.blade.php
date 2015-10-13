<script>
(function($) {
    if ($) {
        var updateDebugger = function(headers) {
            if (!window.JSON || !window.atob) {
                return;
            }
            var data = [],
                a, b, c, d;
            while ((a = headers.indexOf('tracy-ajax-')) !== -1) {
                headers = headers.substr(a + 'tracy-ajax-'.length);
                b = headers.indexOf(':');
                c = parseInt(headers.substr(0, b));
                d = b;
                while (headers.charAt(++d) === ' ');
                a = headers.indexOf("\n");
                data[c] = headers.substring(d, a);
                headers = headers.substr(a);
            }
            if (!data.length) {
                return;
            }
            data = window.atob(data.join(''));
            if (data.length < 2) {
                return;
            }

            data = window.JSON.parse(data);
            a = data.indexOf('var debug =');
            b = data.lastIndexOf('debug.style.display = \'block\';');
            b += 'debug.style.display = \'block\';'.length;
            data = data.substring(a, b);
            d = document.getElementById('tracy-debug');
            d.parentNode.removeChild(d);
            eval(data);
        };
        $(document).ajaxSuccess(function(event, xhr, settings) {
            updateDebugger(xhr.getAllResponseHeaders());
        });
    }
}(jQuery))

</script>
