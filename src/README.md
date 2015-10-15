tracy-bar-ajax
==============

Tracy is a perfect tool for debugging web apps, but as most of the web these days runs on AJAX, the Debugger Bar gets kind of outdated. This is an attempt to enable the Debugger Bar to update even on AJAX requests.

Basic Usage
-----------

On the server side, just call `AjaxBar::register()` before you enable the Tracy Debugger.

On the client side you need to call the updateDebugger() function in the request handler
and pass it the complete HTTP response header as a string. For example in jQuery you could
 do the following:

```javascript
$.ajax({
    // ...
    success: function(payload, textStatus, xhr) {
        // process payload
    
        // update the debugger
        updateDebugger(xhr.getAllResponseHeaders());
        
    }
    // ...
});
```

Neither the server-side component nor the JS function have any outside dependencies that you need to meet
(apart from using Tracy in your project, obviously). The JS function makes use of the native browser
implementations of `JSON.parse()` and `window.atob()`; if these two are not available, the function
 will return and just silently weep in a corner over your way-too-old browser's soul.

If you need to disable the update for a specific request, you may use the `AjaxBar::disable()` static method.
Also it is kind of important that you don't flush the output buffers before shutdown. AjaxBar starts one
of its own when you register it, just make sure you don't flush all running buffers at the end of your script - that 
would dispatch the HTTP headers and AjaxBar would be f*****.