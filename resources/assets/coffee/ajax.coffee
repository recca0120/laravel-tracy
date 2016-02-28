do ->
    # chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/='
    # InvalidCharacterError = (message) ->
    #     @message = message
    # InvalidCharacterError.prototype = new Error
    # InvalidCharacterError.prototype.name = 'InvalidCharacterError'
    # batob = (input) ->
    #     str = String(input).replace(RegExp("=+$"), "")
    #     throw new InvalidCharacterError("'atob' failed: The string to be decoded is not correctly encoded.")  if str.length % 4 is 1
    #
    #     # initialize result and counters
    #     bc = 0
    #     bs = undefined
    #     buffer = undefined
    #     idx = 0
    #     output = ""
    #
    #
    #     # get next character
    #     while buffer = str.charAt(idx++)
    #
    #         # character found in table? initialize bit storage and add its ascii value;
    #
    #         # and if not first of each 4 characters,
    #         # convert the first 8 bits to one ascii character
    #
    #         # try to find character in table (0-63, not found => -1)
    #         buffer = chars.indexOf(buffer)
    #         (if ~buffer and (bs = (if bc % 4 then bs * 64 + buffer else buffer)
    #         bc++ % 4
    #         ) then output += String.fromCharCode(255 & bs >> (-2 * bc & 6)) else 0)
    #     output

    log = ->
        if console.log and window.debug is true
            console.log.apply console, arguments

    class AjaxMonitor
        tag: "lt-"
        pako: (compressed) =>
            code = pako.inflate compressed,
                to: "string"

        zlib: (compressed) =>
            temp = []
            for i, s of compressed.split('')
                temp.push s.charCodeAt 0
            inflate = new Zlib.Inflate temp
            output = inflate.decompress()
            code = ""
            for i, s of output
                code += String.fromCharCode s
            code

        onReadyStateChange: (e) =>
            request = e.currentTarget
            if request.readyState is 4 && request.status is 200
                unless window.Tracy
                    return
                try
                    headers = request.getAllResponseHeaders()
                    data = []
                    while (a = headers.indexOf(@tag)) != -1
                        headers = headers.substr(a + @tag.length)
                        b = headers.indexOf(':')
                        c = parseInt(headers.substr(0, b))
                        d = b
                        while headers.charAt(++d) == ' '
                            a = headers.indexOf('\n')
                        data[c] = headers.substring(d, a)
                        headers = headers.substr(a)

                    unless data.length
                        return

                    base64Data = data.join("")
                    if window.pako
                        code = @pako atob(base64Data)
                    else
                        code = @zlib atob(base64Data)
                    eval code
                    log base64Data.length, code.length

                    headers = null
                    data = null
                    base64Data = null
                    code = null
                catch e
                    log e

        makeRequest: (originalRequest) =>
            (mode) =>
                request = new originalRequest
                if (request.addEventListener)
                    request.addEventListener "readystatechange", @onReadyStateChange
                else if (request.attachEvent)
                    request.attachEvent "onreadystatechange", @onReadyStateChange
                else
                    request.readystatechange = @onReadyStateChange
                request


    monitor = new AjaxMonitor
    if (window.ActiveXobject)
        window.ActiveXObject = monitor.makeRequest(window.ActiveXObject)

    if (window.XMLHttpRequest)
        window.XMLHttpRequest = monitor.makeRequest(window.XMLHttpRequest)
