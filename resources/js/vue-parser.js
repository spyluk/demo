function ParseTemplate(text) {
    /*
     * css-parser.js
     *
     * Distributed under terms of the MIT license.
     */
    /* jshint ignore:start */

    /* jshint ignore:end */

    var css_parser = (function(){
        "use strict";
        var idCount = 0;

        var functionString = function(parsed) {
            if (typeof document === "undefined")  // you are running optimization ( r.js )
                var document = createDocumentMock();

            var css = parsed.css;
            var attributeId = "data-style" + idCount;
            if ( css === false ) {
                return "";
            } else {
                if (parsed.scoped) {
                    css = css
                        .replace(/(^|,)( *)\.([^ \s\n\r\t]+)[\s{]/gm, "$1$2[" + attributeId + "].$3");
                }
                css = css // turn into one line
                    .replace(/([^\\])'/g, "$1\\'")
                    .replace(/''/g, "'\\'")
                    .replace(/[\n\r]+/g, " ")
                    .replace(/ {2,20}/g, " ");
            }

            return css;
        };

        var createDocumentMock = function() {
            return {
                createElement: function() {},
                head: {},
                getElementsByTagName: function() {},
                createTextNode: function() {}
            };
        };

        var parseElement = function(doc) {
            idCount = idCount + 1;
            var queryResult = doc.getElementsByTagName("style");
            if (!queryResult || !queryResult.length) {
                return {
                    id: 0,
                    css: "",
                    scoped: false,
                    functionString: ""
                };
            }
            var style = queryResult[0];
            var scoped = style.hasAttribute("scoped");

            var result = {};
            result.css = style.innerHTML;
            result.id = idCount;
            result.scoped = scoped;
            result.functionString =  functionString(result);

            return result;
        };

        return {
            parseElement: parseElement
        };
    })();

    /*
     * template-parser.js
     *
     * Distributed under terms of the MIT license.
     */
    /* jshint ignore:start */

    /* jshint ignore:end */

    var template_parser = (function(){
        var isOnBrowser = !!document;

        var parseOnBrowser = function(text, css_result) {
            var root;
            if (typeof text === "string") {
                root = document.createElement("div");
                root.insertAdjacentHTML("afterbegin", text);
            } else {
                root = text;
            }

            var queryResult = root.getElementsByTagName("template");
            if (!queryResult || !queryResult.length) {
                return "";
            }
            var template = queryResult[0];
            if (css_result.scoped) {
                template.innerHTML = template
                    .innerHTML.replace(/([ \t]+class=['"])/g, " data-style" + css_result.id + "$1");
            }
            return template.innerHTML;//clearTemplateText(template.innerHTML);
        };

        var clearTemplateText = function(text) {
            if (!text) {  return ""; }
            return text
                    .replace(/([^\\])'/g, "$1\\'")
                    .replace(/^(.*)$/mg, "'$1' + ") // encapsulate template code between ' and put a +
                    .replace(/ {2,20}/g, " ") + "''";
        };

        var extractTemplate = function(text) {
            var start = text.indexOf("<template>");
            var end   = text.lastIndexOf("</template>");
            return clearTemplateText(text.substring(start + 10, end));
        };

        if (isOnBrowser) {
            extractTemplate = parseOnBrowser;
        }

        return {
            extractTemplate: extractTemplate
        };

    })();

    /*
     * vue.js
     *
     * Distributed under terms of the MIT license.
     */
    /* global Promise */
    /* jshint ignore:start */

    /* jshint ignore:end */

    function parse(text) {
        "use strict";

        var parse = function(text) {
            var doc = document.implementation.createHTMLDocument("");
            doc.body.innerHTML = text;
            var scriptElement = doc.getElementsByTagName("script")[0];
            var source = scriptElement ? scriptElement.innerHTML : '';
            var css_result = css_parser.parseElement(doc);

            var template = template_parser.extractTemplate(doc, css_result);
            if (source && source.indexOf("export default") !== -1) {
                source = source.replace(/[\s\S]+export default/m,"new function(){ return ").trim() + '}';
                source = source.replace(/\n/g," ");
            }

            try {
                source = eval(source);
            } catch (err) {
                console.log("Error parse vue component data!");
                console.log(err);
                source = {};
            }

            return {source: source ? eval(source) : {}, template: template, css: css_result.functionString};
        };

        return parse(text);
    }

    return parse(text);
};

module.exports = ParseTemplate;