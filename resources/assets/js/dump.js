'use strict';

class Dumper {
    typeOf(obj) {
        if (obj === null) {
            return 'null';
        }

        if (obj instanceof Array) {
            return 'array';
        }
        return typeof obj;
    }

    sizeOf(obj) {
        let length = 0;
        for(let prop in obj){
            if(obj.hasOwnProperty(prop)) {
                length++;
            }
        }
        return length;
    }

    format(data, options, level) {
        let type = this.typeOf(data);
        let result = '';
        switch (type) {
            case 'object':
            case 'array':
                let length = (type === 'object') ? this.sizeOf(data) : data.length;
                if (length === 0) {
                    return `<span class=\"tracy-dump-array\">array</span> ()</span>\n`;
                }

                let collapsed = (level >= options.depth-1)?'tracy-collapsed':'';

                result = `<span class=\"tracy-toggle ${collapsed}\">`;
                result += `<span class=\"tracy-dump-array\">array</span> (${length})</span>\n`;
                result += `<div class=\"${collapsed}\">`;
                for(let key in data){
                    let item = data[key];
                    result += `<span class=\"tracy-dump-indent\">   </span><span class=\"tracy-dump-key\">${key}</span> =&gt; `;
                    result += this.format(item, options, level++);
                }
                result += '</div></span>';
                break;
            case 'boolean':
                let temp = (data == true)?'TRUE':'FALSE';
                result = `<span class=\"tracy-dump-bool\">${temp}</span>\n`;
                break;
            case 'number':
                result = `<span class=\"tracy-dump-number\">${data}</span>\n`;
                break;
            case 'null':
                result = `<span class=\"tracy-dump-null\">NULL</span>\n`;
                break;
            default:
                result = `<span class=\"tracy-dump-string\">\"${data}\"</span> (${data.length})\n`
                break;
        }
        return result;
    }

    dump(data, options = {}) {
        if (!options.depth) {
            options.depth = 2;
        }

        return `&lt;pre class=\"tracy-dump\"&gt;${this.format(data, options, 0)}&lt;/pre&gt;`
            .replace(/&lt;/g, '<')
            .replace(/&gt;/g, '>');
    }


}

let dumper = new Dumper;

window.TracyDump = (data, options) => {
    return dumper.dump(data, options);
}

export default dumper;
