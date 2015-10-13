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

    var ajaxButton = $("<a id='tracy-ajax'>AjaxLoad</a>")
        .css({
            position: "fixed",
            top: 0,
            left: 0,
            zIndex: 10000000,
            background: "red",
            color: "#fff",
            padding: "5px 10px",
            opacity: .3
        })
        .hide()
        .appendTo(document.body);

    ajaxButton.on("mouseenter mouseleave", function(e) {
        opacity = .3;
        if (e.type == 'mouseenter') {
            opacity = 1;
        }
        ajaxButton.css({
            opacity: opacity
        });
    });

    ajaxButton.on("click", function(e) {
        var $this = $(this);
        var xhr = $this.data("xhr");
        updateDebugger(xhr.getAllResponseHeaders())
        $this.hide();
    });

    $(document).ajaxSuccess(function(event, xhr, settings) {
        ajaxButton.data("xhr", xhr).show();
    });
}(jQuery))

</script>
