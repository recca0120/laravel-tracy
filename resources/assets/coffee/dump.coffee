do () ->
    class Dumper
        typeOf: (obj) =>
            if obj is null
                return "null"

            if obj instanceof Array
                return "array"

            typeof obj

        sizeOf: (obj) =>
            size = 0
            for key of obj
                if obj.hasOwnProperty(key)
                    size++
            size

        format: (data, options, level) =>
            type = @typeOf data
            switch type
                when "object", "array"
                    length = if type is "object"
                        @sizeOf data
                    else
                        data.length

                    if length is 0
                        return "<span class=\"tracy-dump-array\">array</span> ()</span>\n"

                    collapsed = if level >= options.depth-1
                        "tracy-collapsed"
                    else
                        ""

                    result = ""
                    result += "<span class=\"tracy-toggle #{collapsed}\">"
                    result += "<span class=\"tracy-dump-array\">array</span> (#{length})</span>\n"
                    result += "<div class=\"#{collapsed}\">"
                    for key, item of data
                        result += "<span class=\"tracy-dump-indent\">   </span><span class=\"tracy-dump-key\">#{key}</span> =&gt; "
                        result += @format item, options, level++
                    result += "</div></span>"
                    return result

                when "boolean"
                    result = if data is true
                        "TRUE"
                    else
                        "FALSE"
                    return "<span class=\"tracy-dump-bool\">#{result}</span>\n"

                when "number"
                    return "<span class=\"tracy-dump-number\">#{data}</span>\n"

                when "null"
                    return "<span class=\"tracy-dump-null\">NULL</span>\n"

                # when "string"
                else
                    return "<span class=\"tracy-dump-string\">\"#{data}\"</span> (#{data.length})\n"

        dump: (data, options) =>
            unless options
                options = {}
            unless options.depth
                options.depth = 2

            "&lt;pre class=\"tracy-dump\"&gt;#{@format(data, options, 0)}&lt;/pre&gt;"
                .replace(/&lt;/g, '<')
                .replace(/&gt;/g, '>')

    window.TracyDump = do ->
        dumper = new Dumper
        (data, options) ->
            dumper.dump data, options
