<script>
(function($) {
    if (!$) {
        return
    }
    var updateDebugger = function(headers) {
        if (!window.JSON || !window.atob) {
            return;
        }
        var data = [],
            a, b, c, d;
        while ((a = headers.indexOf('X-Tracy-Error-Ajax-')) !== -1) {
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

    var xhrs = [];
    var ajaxButton = $("<a id='tracy-ajax'>Tracy Ajax Loader (<span id='tracy-ajax-counts'></span>)</a>")
        .appendTo(document.body)
        .hide();
    var ajaxCounts = $("#tracy-ajax-counts");


    var opacity = .5
    ajaxButton.css({
        position: "fixed",
        right: 0,
        bottom: 0,
        zIndex: 10000000,
        "font-weight": "bolder",
        background: "red",
        color: "#fff",
        padding: "5px 20px",
        opacity: opacity
    });

    ajaxButton.on("mouseenter mouseleave", function(e) {
        var newOpacity = opacity;
        if (e.type == 'mouseenter') {
            newOpacity = 1;
        }
        ajaxButton.css({
            opacity: newOpacity
        });
    });

    ajaxButton.on("click", function(e) {
        xhr = xhrs.shift();
        updateDebugger(xhr.getAllResponseHeaders());
        if (xhrs.length == 0) {
            ajaxButton.hide();
        }
    });

    $(document).ajaxSuccess(function(event, xhr, settings) {
        // ajaxButton.data("xhr", xhr).show();
        xhrs.push(xhr);
        ajaxCounts.html(xhrs.length);
        ajaxButton.show();
    });
}(jQuery))

</script>
