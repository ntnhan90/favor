// Mustache Template Engine
var Mustache; (function(global,factory){typeof exports==="object"&&typeof module!=="undefined"?module.exports=factory():typeof define==="function"&&define.amd?define(factory):(global=global||self,global.Mustache=factory())})(this,function(){"use strict";var objectToString=Object.prototype.toString;var isArray=Array.isArray||function isArrayPolyfill(object){return objectToString.call(object)==="[object Array]"};function isFunction(object){return typeof object==="function"}function typeStr(obj){return isArray(obj)?"array":typeof obj}function escapeRegExp(string){return string.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g,"\\$&")}function hasProperty(obj,propName){return obj!=null&&typeof obj==="object"&&propName in obj}function primitiveHasOwnProperty(primitive,propName){return primitive!=null&&typeof primitive!=="object"&&primitive.hasOwnProperty&&primitive.hasOwnProperty(propName)}var regExpTest=RegExp.prototype.test;function testRegExp(re,string){return regExpTest.call(re,string)}var nonSpaceRe=/\S/;function isWhitespace(string){return!testRegExp(nonSpaceRe,string)}var entityMap={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;","/":"&#x2F;","`":"&#x60;","=":"&#x3D;"};function escapeHtml(string){return String(string).replace(/[&<>"'`=\/]/g,function fromEntityMap(s){return entityMap[s]})}var whiteRe=/\s*/;var spaceRe=/\s+/;var equalsRe=/\s*=/;var curlyRe=/\s*\}/;var tagRe=/#|\^|\/|>|\{|&|=|!/;function parseTemplate(template,tags){if(!template)return[];var lineHasNonSpace=false;var sections=[];var tokens=[];var spaces=[];var hasTag=false;var nonSpace=false;var indentation="";var tagIndex=0;function stripSpace(){if(hasTag&&!nonSpace){while(spaces.length)delete tokens[spaces.pop()]}else{spaces=[]}hasTag=false;nonSpace=false}var openingTagRe,closingTagRe,closingCurlyRe;function compileTags(tagsToCompile){if(typeof tagsToCompile==="string")tagsToCompile=tagsToCompile.split(spaceRe,2);if(!isArray(tagsToCompile)||tagsToCompile.length!==2)throw new Error("Invalid tags: "+tagsToCompile);openingTagRe=new RegExp(escapeRegExp(tagsToCompile[0])+"\\s*");closingTagRe=new RegExp("\\s*"+escapeRegExp(tagsToCompile[1]));closingCurlyRe=new RegExp("\\s*"+escapeRegExp("}"+tagsToCompile[1]))}compileTags(tags||mustache.tags);var scanner=new Scanner(template);var start,type,value,chr,token,openSection;while(!scanner.eos()){start=scanner.pos;value=scanner.scanUntil(openingTagRe);if(value){for(var i=0,valueLength=value.length;i<valueLength;++i){chr=value.charAt(i);if(isWhitespace(chr)){spaces.push(tokens.length);indentation+=chr}else{nonSpace=true;lineHasNonSpace=true;indentation+=" "}tokens.push(["text",chr,start,start+1]);start+=1;if(chr==="\n"){stripSpace();indentation="";tagIndex=0;lineHasNonSpace=false}}}if(!scanner.scan(openingTagRe))break;hasTag=true;type=scanner.scan(tagRe)||"name";scanner.scan(whiteRe);if(type==="="){value=scanner.scanUntil(equalsRe);scanner.scan(equalsRe);scanner.scanUntil(closingTagRe)}else if(type==="{"){value=scanner.scanUntil(closingCurlyRe);scanner.scan(curlyRe);scanner.scanUntil(closingTagRe);type="&"}else{value=scanner.scanUntil(closingTagRe)}if(!scanner.scan(closingTagRe))throw new Error("Unclosed tag at "+scanner.pos);if(type==">"){token=[type,value,start,scanner.pos,indentation,tagIndex,lineHasNonSpace]}else{token=[type,value,start,scanner.pos]}tagIndex++;tokens.push(token);if(type==="#"||type==="^"){sections.push(token)}else if(type==="/"){openSection=sections.pop();if(!openSection)throw new Error('Unopened section "'+value+'" at '+start);if(openSection[1]!==value)throw new Error('Unclosed section "'+openSection[1]+'" at '+start)}else if(type==="name"||type==="{"||type==="&"){nonSpace=true}else if(type==="="){compileTags(value)}}stripSpace();openSection=sections.pop();if(openSection)throw new Error('Unclosed section "'+openSection[1]+'" at '+scanner.pos);return nestTokens(squashTokens(tokens))}function squashTokens(tokens){var squashedTokens=[];var token,lastToken;for(var i=0,numTokens=tokens.length;i<numTokens;++i){token=tokens[i];if(token){if(token[0]==="text"&&lastToken&&lastToken[0]==="text"){lastToken[1]+=token[1];lastToken[3]=token[3]}else{squashedTokens.push(token);lastToken=token}}}return squashedTokens}function nestTokens(tokens){var nestedTokens=[];var collector=nestedTokens;var sections=[];var token,section;for(var i=0,numTokens=tokens.length;i<numTokens;++i){token=tokens[i];switch(token[0]){case"#":case"^":collector.push(token);sections.push(token);collector=token[4]=[];break;case"/":section=sections.pop();section[5]=token[2];collector=sections.length>0?sections[sections.length-1][4]:nestedTokens;break;default:collector.push(token)}}return nestedTokens}function Scanner(string){this.string=string;this.tail=string;this.pos=0}Scanner.prototype.eos=function eos(){return this.tail===""};Scanner.prototype.scan=function scan(re){var match=this.tail.match(re);if(!match||match.index!==0)return"";var string=match[0];this.tail=this.tail.substring(string.length);this.pos+=string.length;return string};Scanner.prototype.scanUntil=function scanUntil(re){var index=this.tail.search(re),match;switch(index){case-1:match=this.tail;this.tail="";break;case 0:match="";break;default:match=this.tail.substring(0,index);this.tail=this.tail.substring(index)}this.pos+=match.length;return match};function Context(view,parentContext){this.view=view;this.cache={".":this.view};this.parent=parentContext}Context.prototype.push=function push(view){return new Context(view,this)};Context.prototype.lookup=function lookup(name){var cache=this.cache;var value;if(cache.hasOwnProperty(name)){value=cache[name]}else{var context=this,intermediateValue,names,index,lookupHit=false;while(context){if(name.indexOf(".")>0){intermediateValue=context.view;names=name.split(".");index=0;while(intermediateValue!=null&&index<names.length){if(index===names.length-1)lookupHit=hasProperty(intermediateValue,names[index])||primitiveHasOwnProperty(intermediateValue,names[index]);intermediateValue=intermediateValue[names[index++]]}}else{intermediateValue=context.view[name];lookupHit=hasProperty(context.view,name)}if(lookupHit){value=intermediateValue;break}context=context.parent}cache[name]=value}if(isFunction(value))value=value.call(this.view);return value};function Writer(){this.templateCache={_cache:{},set:function set(key,value){this._cache[key]=value},get:function get(key){return this._cache[key]},clear:function clear(){this._cache={}}}}Writer.prototype.clearCache=function clearCache(){if(typeof this.templateCache!=="undefined"){this.templateCache.clear()}};Writer.prototype.parse=function parse(template,tags){var cache=this.templateCache;var cacheKey=template+":"+(tags||mustache.tags).join(":");var isCacheEnabled=typeof cache!=="undefined";var tokens=isCacheEnabled?cache.get(cacheKey):undefined;if(tokens==undefined){tokens=parseTemplate(template,tags);isCacheEnabled&&cache.set(cacheKey,tokens)}return tokens};Writer.prototype.render=function render(template,view,partials,tags){var tokens=this.parse(template,tags);var context=view instanceof Context?view:new Context(view,undefined);return this.renderTokens(tokens,context,partials,template,tags)};Writer.prototype.renderTokens=function renderTokens(tokens,context,partials,originalTemplate,tags){var buffer="";var token,symbol,value;for(var i=0,numTokens=tokens.length;i<numTokens;++i){value=undefined;token=tokens[i];symbol=token[0];if(symbol==="#")value=this.renderSection(token,context,partials,originalTemplate);else if(symbol==="^")value=this.renderInverted(token,context,partials,originalTemplate);else if(symbol===">")value=this.renderPartial(token,context,partials,tags);else if(symbol==="&")value=this.unescapedValue(token,context);else if(symbol==="name")value=this.escapedValue(token,context);else if(symbol==="text")value=this.rawValue(token);if(value!==undefined)buffer+=value}return buffer};Writer.prototype.renderSection=function renderSection(token,context,partials,originalTemplate){var self=this;var buffer="";var value=context.lookup(token[1]);function subRender(template){return self.render(template,context,partials)}if(!value)return;if(isArray(value)){for(var j=0,valueLength=value.length;j<valueLength;++j){buffer+=this.renderTokens(token[4],context.push(value[j]),partials,originalTemplate)}}else if(typeof value==="object"||typeof value==="string"||typeof value==="number"){buffer+=this.renderTokens(token[4],context.push(value),partials,originalTemplate)}else if(isFunction(value)){if(typeof originalTemplate!=="string")throw new Error("Cannot use higher-order sections without the original template");value=value.call(context.view,originalTemplate.slice(token[3],token[5]),subRender);if(value!=null)buffer+=value}else{buffer+=this.renderTokens(token[4],context,partials,originalTemplate)}return buffer};Writer.prototype.renderInverted=function renderInverted(token,context,partials,originalTemplate){var value=context.lookup(token[1]);if(!value||isArray(value)&&value.length===0)return this.renderTokens(token[4],context,partials,originalTemplate)};Writer.prototype.indentPartial=function indentPartial(partial,indentation,lineHasNonSpace){var filteredIndentation=indentation.replace(/[^ \t]/g,"");var partialByNl=partial.split("\n");for(var i=0;i<partialByNl.length;i++){if(partialByNl[i].length&&(i>0||!lineHasNonSpace)){partialByNl[i]=filteredIndentation+partialByNl[i]}}return partialByNl.join("\n")};Writer.prototype.renderPartial=function renderPartial(token,context,partials,tags){if(!partials)return;var value=isFunction(partials)?partials(token[1]):partials[token[1]];if(value!=null){var lineHasNonSpace=token[6];var tagIndex=token[5];var indentation=token[4];var indentedValue=value;if(tagIndex==0&&indentation){indentedValue=this.indentPartial(value,indentation,lineHasNonSpace)}return this.renderTokens(this.parse(indentedValue,tags),context,partials,indentedValue,tags)}};Writer.prototype.unescapedValue=function unescapedValue(token,context){var value=context.lookup(token[1]);if(value!=null)return value};Writer.prototype.escapedValue=function escapedValue(token,context){var value=context.lookup(token[1]);if(value!=null)return typeof value==="number"?String(value):mustache.escape(value)};Writer.prototype.rawValue=function rawValue(token){return token[1]};var mustache={name:"mustache.js",version:"4.0.1",tags:["{{","}}"],clearCache:undefined,escape:undefined,parse:undefined,render:undefined,Scanner:undefined,Context:undefined,Writer:undefined,set templateCache(cache){defaultWriter.templateCache=cache},get templateCache(){return defaultWriter.templateCache}};var defaultWriter=new Writer;mustache.clearCache=function clearCache(){return defaultWriter.clearCache()};mustache.parse=function parse(template,tags){return defaultWriter.parse(template,tags)};mustache.render=function render(template,view,partials,tags){if(typeof template!=="string"){throw new TypeError('Invalid template! Template should be a "string" '+'but "'+typeStr(template)+'" was given as the first '+"argument for mustache#render(template, view, partials)")}return defaultWriter.render(template,view,partials,tags)};mustache.escape=escapeHtml;mustache.Scanner=Scanner;mustache.Context=Context;mustache.Writer=Writer;return mustache});

/**
 * Ajax Autocomplete for jQuery, version 1.2.7 (c) 2013 Tomas Kirda
 *
 * Ajax Autocomplete for jQuery is freely distributable under the terms of an
 * MIT-style license. For details, see the web site:
 * http://www.devbridge.com/projects/autocomplete/jquery/
 *
 */

/* jslint browser: true, white: true, plusplus: true */
/* global define, window, document, jQuery */

// Expose plugin as an AMD module if AMD loader is present:
(function (factory) {
    'use strict';
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($) {
    'use strict';

    var
        utils = (function () {
            return {
                escapeRegExChars: function (value) {
                    return value.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
                },
                createNode: function (html) {
                    var div = document.createElement('div');
                    div.innerHTML = html;
                    return div.firstChild;
                }
            };
        }()),

        keys = {
            ESC: 27,
            TAB: 9,
            RETURN: 13,
            LEFT: 37,
            UP: 38,
            RIGHT: 39,
            DOWN: 40
        };

    function Autocomplete(el, options) {
        var noop = function () { },
            that = this,
            defaults = {
                autoSelectFirst: false,
                appendTo: 'body',
                serviceUrl: null,
                lookup: null,
                onSelect: null,
                width: 'auto',
                minChars: 1,
                maxHeight: 300,
                deferRequestBy: 0,
                params: {},
                formatResult: Autocomplete.formatResult,
                delimiter: null,
                zIndex: 9999,
                type: 'GET',
                noCache: false,
                offsetX: 0,
                onSearchStart: noop,
                onSearchComplete: noop,
                containerClass: 'autocomplete-suggestions',
                tabDisabled: false,
                dataType: 'text',
                currentRequest: null,
                lookupFilter: function (suggestion, originalQuery, queryLowerCase) {
                    return suggestion.value.toLowerCase().indexOf(queryLowerCase) !== -1;
                },
                paramName: 'query',
                transformResult: function (response) {
                    return typeof response === 'string' ? $.parseJSON(response) : response;
                }
            };

        // Shared variables:
        that.element = el;
        that.el = $(el);
        that.suggestions = [];
        that.badQueries = [];
        that.selectedIndex = -1;
        that.currentValue = that.element.value;
        that.intervalId = 0;
        that.cachedResponse = [];
        that.onChangeInterval = null;
        that.onChange = null;
        that.isLocal = false;
        that.suggestionsContainer = null;
        that.options = $.extend({}, defaults, options);
        that.classes = {
            selected: 'autocomplete-selected',
            suggestion: 'autocomplete-suggestion'
        };
        that.hint = null;
        that.hintValue = '';
        that.selection = null;

        // Initialize and set options:
        that.initialize();
        that.setOptions(options);
    }

    Autocomplete.utils = utils;

    $.Autocomplete = Autocomplete;

    Autocomplete.formatResult = function (suggestion, currentValue) {
        var pattern = '(' + utils.escapeRegExChars(currentValue) + ')';

        return suggestion.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>');
    };

    Autocomplete.prototype = {

        killerFn: null,

        initialize: function () {
            var that = this,
                suggestionSelector = '.' + that.classes.suggestion,
                selected = that.classes.selected,
                options = that.options,
                container;

            // Remove autocomplete attribute to prevent native suggestions:
            that.element.setAttribute('autocomplete', 'off');

            that.killerFn = function (e) {
                if ($(e.target).closest('.' + that.options.containerClass).length === 0) {
                    that.killSuggestions();
                    that.disableKillerFn();
                }
            };

            that.suggestionsContainer = Autocomplete.utils.createNode('<div class="' + options.containerClass + '" style="position: absolute; display: none;"></div>');

            container = $(that.suggestionsContainer);

            container.appendTo(options.appendTo);

            // Only set width if it was provided:
            if (options.width !== 'auto') {
                container.width(options.width);
            }

            // Listen for mouse over event on suggestions list:
            container.on('mouseover.autocomplete', suggestionSelector, function () {
                that.activate($(this).data('index'));
            });

            // Deselect active element when mouse leaves suggestions container:
            container.on('mouseout.autocomplete', function () {
                that.selectedIndex = -1;
                container.children('.' + selected).removeClass(selected);
            });

            // Listen for click event on suggestions list:
            container.on('click.autocomplete', suggestionSelector, function () {
                that.select($(this).data('index'));
            });

            that.fixPosition();

            that.fixPositionCapture = function () {
                if (that.visible) {
                    that.fixPosition();
                }
            };

            $(window).on('resize', that.fixPositionCapture);

            that.el.on('keydown.autocomplete', function (e) { that.onKeyPress(e); });
            that.el.on('keyup.autocomplete', function (e) { that.onKeyUp(e); });
            that.el.on('blur.autocomplete', function () { that.onBlur(); });
            that.el.on('focus.autocomplete', function () { that.fixPosition(); });
            that.el.on('change.autocomplete', function (e) { that.onKeyUp(e); });
        },

        onBlur: function () {
            this.enableKillerFn();
        },

        setOptions: function (suppliedOptions) {
            var that = this,
                options = that.options;

            $.extend(options, suppliedOptions);

            that.isLocal = $.isArray(options.lookup);

            if (that.isLocal) {
                options.lookup = that.verifySuggestionsFormat(options.lookup);
            }

            // Adjust height, width and z-index:
            $(that.suggestionsContainer).css({
                'max-height': options.maxHeight + 'px',
                'width': options.width + 'px',
                'z-index': options.zIndex
            });
        },

        clearCache: function () {
            this.cachedResponse = [];
            this.badQueries = [];
        },

        clear: function () {
            this.clearCache();
            this.currentValue = '';
            this.suggestions = [];
        },

        disable: function () {
            this.disabled = true;
        },

        enable: function () {
            this.disabled = false;
        },

        fixPosition: function () {
            var that = this,
                offset;

            // Don't adjsut position if custom container has been specified:
            if (that.options.appendTo !== 'body') {
                return;
            }

            offset = that.el.offset();

            $(that.suggestionsContainer).css({
                top: (offset.top + that.el.outerHeight()) + 'px',
                left: (offset.left  + that.options.offsetX) + 'px'
            });
        },

        enableKillerFn: function () {
            var that = this;
            $(document).on('click.autocomplete', that.killerFn);
        },

        disableKillerFn: function () {
            var that = this;
            $(document).off('click.autocomplete', that.killerFn);
        },

        killSuggestions: function () {
            var that = this;
            that.stopKillSuggestions();
            that.intervalId = window.setInterval(function () {
                that.hide();
                that.stopKillSuggestions();
            }, 300);
        },

        stopKillSuggestions: function () {
            window.clearInterval(this.intervalId);
        },

        isCursorAtEnd: function () {
            var that = this,
                valLength = that.el.val().length,
                selectionStart = that.element.selectionStart,
                range;

            if (typeof selectionStart === 'number') {
                return selectionStart === valLength;
            }
            if (document.selection) {
                range = document.selection.createRange();
                range.moveStart('character', -valLength);
                return valLength === range.text.length;
            }
            return true;
        },

        onKeyPress: function (e) {
            var that = this;

            // If suggestions are hidden and user presses arrow down, display
			// suggestions:
            if (!that.disabled && !that.visible && e.which === keys.DOWN && that.currentValue) {
                that.suggest();
                return;
            }

            if (that.disabled || !that.visible) {
                return;
            }

            switch (e.which) {
                case keys.ESC:
                    that.el.val(that.currentValue);
                    that.hide();
                    break;
                case keys.RIGHT:
                    if (that.hint && that.options.onHint && that.isCursorAtEnd()) {
                        that.selectHint();
                        break;
                    }
                    return;
                case keys.TAB:
                    if (that.hint && that.options.onHint) {
                        that.selectHint();
                        return;
                    }
                    // Fall through to RETURN
                case keys.RETURN:
                    if (that.selectedIndex === -1) {
                        that.hide();
                        return;
                    }
                    that.select(that.selectedIndex);
                    if (e.which === keys.TAB && that.options.tabDisabled === false) {
                        return;
                    }
                    break;
                case keys.UP:
                    that.moveUp();
                    break;
                case keys.DOWN:
                    that.moveDown();
                    break;
                default:
                    return;
            }

            // Cancel event if function did not return:
            e.stopImmediatePropagation();
            e.preventDefault();
        },

        onKeyUp: function (e) {
            var that = this;

            if (that.disabled) {
                return;
            }

            switch (e.which) {
                case keys.UP:
                case keys.DOWN:
                    return;
            }

            clearInterval(that.onChangeInterval);

            if (that.currentValue !== that.el.val()) {
                that.findBestHint();
                if (that.options.deferRequestBy > 0) {
                    // Defer lookup in case when value changes very quickly:
                    that.onChangeInterval = setInterval(function () {
                        that.onValueChange();
                    }, that.options.deferRequestBy);
                } else {
                    that.onValueChange();
                }
            }
        },

        onValueChange: function () {
            var that = this,
                q;

            if (that.selection) {
                that.selection = null;
                (that.options.onInvalidateSelection || $.noop)();
            }

            clearInterval(that.onChangeInterval);
            that.currentValue = that.el.val();

            q = that.getQuery(that.currentValue);
            that.selectedIndex = -1;

            if (q.length < that.options.minChars) {
                that.hide();
            } else {
                that.getSuggestions(q);
            }
        },

        getQuery: function (value) {
            var delimiter = this.options.delimiter,
                parts;

            if (!delimiter) {
                return $.trim(value);
            }
            parts = value.split(delimiter);
            return $.trim(parts[parts.length - 1]);
        },

        getSuggestionsLocal: function (query) {
            var that = this,
                queryLowerCase = query.toLowerCase(),
                filter = that.options.lookupFilter;

            return {
                suggestions: $.grep(that.options.lookup, function (suggestion) {
                    return filter(suggestion, query, queryLowerCase);
                })
            };
        },

        getSuggestions: function (q) {
            var response,
                that = this,
                options = that.options,
                serviceUrl = options.serviceUrl;

            response = that.isLocal ? that.getSuggestionsLocal(q) : that.cachedResponse[q];

            if (response && $.isArray(response.suggestions)) {
                that.suggestions = response.suggestions;
                that.suggest();
            } else if (!that.isBadQuery(q)) {
                options.params[options.paramName] = q;
                if (options.onSearchStart.call(that.element, options.params) === false) {
                    return;
                }
                if ($.isFunction(options.serviceUrl)) {
                    serviceUrl = options.serviceUrl.call(that.element, q);
                }
                if(this.currentRequest != null) {
                    this.currentRequest.abort();
                }
                this.currentRequest = $.ajax({
                    url: serviceUrl,
                    data: options.ignoreParams ? null : options.params,
                    type: options.type,
                    dataType: options.dataType
                }).done(function (data) {
                    that.processResponse(data, q);
                    options.onSearchComplete.call(that.element, q);
                });
            }
        },

        isBadQuery: function (q) {
            var badQueries = this.badQueries,
                i = badQueries.length;

            while (i--) {
                if (q.indexOf(badQueries[i]) === 0) {
                    return true;
                }
            }

            return false;
        },

        hide: function () {
            var that = this;
            that.visible = false;
            that.selectedIndex = -1;
            $(that.suggestionsContainer).hide();
            that.signalHint(null);
        },

        suggest: function () {
            if (this.suggestions.length === 0) {
                this.hide();
                return;
            }

            var that = this,
                formatResult = that.options.formatResult,
                value = that.getQuery(that.currentValue),
                className = that.classes.suggestion,
                classSelected = that.classes.selected,
                container = $(that.suggestionsContainer),
                html = '',
                width;

            // Build suggestions inner HTML:
            $.each(that.suggestions, function (i, suggestion) {
                html += '<div class="' + className + '" data-index="' + i + '">' + formatResult(suggestion, value) + '</div>';
            });

            // If width is auto, adjust width before displaying suggestions,
            // because if instance was created before input had width, it will
			// be zero.
            // Also it adjusts if input width has changed.
            // -2px to account for suggestions border.
            if (that.options.width === 'auto') {
                width = that.el.outerWidth() - 2;
                container.width(width > 0 ? width : 300);
            }

            container.html(html).show();
            that.visible = true;

            // Select first value by default:
            if (that.options.autoSelectFirst) {
                that.selectedIndex = 0;
                container.children().first().addClass(classSelected);
            }

            that.findBestHint();
        },

        findBestHint: function () {
            var that = this,
                value = that.el.val().toLowerCase(),
                bestMatch = null;

            if (!value) {
                return;
            }

            $.each(that.suggestions, function (i, suggestion) {
                var foundMatch = suggestion.value.toLowerCase().indexOf(value) === 0;
                if (foundMatch) {
                    bestMatch = suggestion;
                }
                return !foundMatch;
            });

            that.signalHint(bestMatch);
        },

        signalHint: function (suggestion) {
            var hintValue = '',
                that = this;
            if (suggestion) {
                hintValue = that.currentValue + suggestion.value.substr(that.currentValue.length);
            }
            if (that.hintValue !== hintValue) {
                that.hintValue = hintValue;
                that.hint = suggestion;
                (this.options.onHint || $.noop)(hintValue);
            }
        },

        verifySuggestionsFormat: function (suggestions) {
            // If suggestions is string array, convert them to supported format:
            if (suggestions.length && typeof suggestions[0] === 'string') {
                return $.map(suggestions, function (value) {
                    return { value: value, data: null };
                });
            }

            return suggestions;
        },

        processResponse: function (response, originalQuery) {
            var that = this,
                options = that.options,
                result = options.transformResult(response, originalQuery);

            result.suggestions = that.verifySuggestionsFormat(result.suggestions);

            // Cache results if cache is not disabled:
            if (!options.noCache) {
                that.cachedResponse[result[options.paramName]] = result;
                if (result.suggestions.length === 0) {
                    that.badQueries.push(result[options.paramName]);
                }
            }

            // Display suggestions only if returned query matches current value:
            if (originalQuery === that.getQuery(that.currentValue)) {
                that.suggestions = result.suggestions;
                that.suggest();
            }
        },

        activate: function (index) {
            var that = this,
                activeItem,
                selected = that.classes.selected,
                container = $(that.suggestionsContainer),
                children = container.children();

            container.children('.' + selected).removeClass(selected);

            that.selectedIndex = index;

            if (that.selectedIndex !== -1 && children.length > that.selectedIndex) {
                activeItem = children.get(that.selectedIndex);
                $(activeItem).addClass(selected);
                return activeItem;
            }

            return null;
        },

        selectHint: function () {
            var that = this,
                i = $.inArray(that.hint, that.suggestions);

            that.select(i);
        },

        select: function (i) {
            var that = this;
            that.hide();
            that.onSelect(i);
        },

        moveUp: function () {
            var that = this;

            if (that.selectedIndex === -1) {
                return;
            }

            if (that.selectedIndex === 0) {
                $(that.suggestionsContainer).children().first().removeClass(that.classes.selected);
                that.selectedIndex = -1;
                that.el.val(that.currentValue);
                that.findBestHint();
                return;
            }

            that.adjustScroll(that.selectedIndex - 1);
        },

        moveDown: function () {
            var that = this;

            if (that.selectedIndex === (that.suggestions.length - 1)) {
                return;
            }

            that.adjustScroll(that.selectedIndex + 1);
        },

        adjustScroll: function (index) {
            var that = this,
                activeItem = that.activate(index),
                offsetTop,
                upperBound,
                lowerBound,
                heightDelta = 25;

            if (!activeItem) {
                return;
            }

            offsetTop = activeItem.offsetTop;
            upperBound = $(that.suggestionsContainer).scrollTop();
            lowerBound = upperBound + that.options.maxHeight - heightDelta;

            if (offsetTop < upperBound) {
                $(that.suggestionsContainer).scrollTop(offsetTop);
            } else if (offsetTop > lowerBound) {
                $(that.suggestionsContainer).scrollTop(offsetTop - that.options.maxHeight + heightDelta);
            }

            that.el.val(that.getValue(that.suggestions[index].value));
            that.signalHint(null);
        },

        onSelect: function (index) {
            var that = this,
                onSelectCallback = that.options.onSelect,
                suggestion = that.suggestions[index];

            that.currentValue = that.getValue(suggestion.value);
            that.el.val(that.currentValue);
            that.signalHint(null);
            that.suggestions = [];
            that.selection = suggestion;

            if ($.isFunction(onSelectCallback)) {
                onSelectCallback.call(that.element, suggestion);
            }
        },

        getValue: function (value) {
            var that = this,
                delimiter = that.options.delimiter,
                currentValue,
                parts;

            if (!delimiter) {
                return value;
            }

            currentValue = that.currentValue;
            parts = currentValue.split(delimiter);

            if (parts.length === 1) {
                return value;
            }

            return currentValue.substr(0, currentValue.length - parts[parts.length - 1].length) + value;
        },

        dispose: function () {
            var that = this;
            that.el.off('.autocomplete').removeData('autocomplete');
            that.disableKillerFn();
            $(window).off('resize', that.fixPositionCapture);
            $(that.suggestionsContainer).remove();
        }
    };

    // Create chainable jQuery plugin:
    $.fn.autocompleter = function (options, args) {
        var dataKey = 'autocomplete';
        // If function invoked without argument return
        // instance of the first matched element:
        if (arguments.length === 0) {
            return this.first().data(dataKey);
        }

        return this.each(function () {
            var inputElement = $(this),
                instance = inputElement.data(dataKey);

            if (typeof options === 'string') {
                if (instance && typeof instance[options] === 'function') {
                    instance[options](args);
                }
            } else {
                // If instance already exists, destroy it:
                if (instance && instance.dispose) {
                    instance.dispose();
                }
                instance = new Autocomplete(this, options);
                inputElement.data(dataKey, instance);
            }
        });
    };
}));

// Avoid `console` errors in browsers that lack a console.
(function() {
    var method;
    var noop = function () {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());


// Place any jQuery/helper plugins above.

// -------------------------------------
// Front-end Code | START


// -------------------------------------
// -------------------------------------

var RC = RC || {};
RC.utils = RC.utils || {};

RC.Config = {
    isiPad: navigator.userAgent.match(/ipad/i) != null,
    breakpoint: 768,
    winWidth : $(window).width(),
    winHeight : $(window).height()
};

if (RC.Config.isiPad) {
    $("html").addClass("ipad");
}

var SpotlightPopupWidth = (RC.Config.isiPad ? "750" : "905");

    RC.utils.getParameterByName = function (name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }

//////////// Input Suggestion ///////////////
$(function() {
  /*
  var substringMatcher = function(strs) {
    return function findMatches(q, cb) {
      var matches, substringRegex;

      // an array that will be populated with substring matches
      matches = [];

      // regex used to determine if a string contains the substring `q`
      substrRegex = new RegExp(q, 'i');

      // iterate through the pool of strings and for any string that
      // contains the substring `q`, add it to the `matches` array
      $.each(strs, function(i, str) {
        if (substrRegex.test(str)) {
          matches.push(str);
        }
      });

      cb(matches);
    };
  };

  var states = ['Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California',
    'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii',
    'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana',
    'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota',
    'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire',
    'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota',
    'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island',
    'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont',
    'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming'
  ];

  $('#shipping_city_isr.typeahead').typeahead({
    hint: true,
    highlight: false,
    minLength: 1
  },
  {
    name: 'states',
    source: substringMatcher(states)
  });
*/
});

/////////// Input suggestion End ///////////////


// -------------------------------------
// -------------------------------------
// Front-end Code | END
// -------------------------------------


$(window).load(function(){
    $("#search_term").autocompleter({
        serviceUrl: "",
        deferRequestBy : 100,
        minChars : 2,
        maxHeight: 145,
        width: 240,
        offsetX: -98,
        onSelect: function(value){
            window.location.href = '?search='+encodeURIComponent(value.value)+'&d='+encodeURIComponent(searchDepartmentScript);
        }
    });
});

function resetNewsletter(defMsg, blur, id){
    var value = $('#'+id).val();
    if (blur){
        if (value.length == 0)
            $('#'+id).val(defMsg);
    } else {
        if (value === defMsg)
            $('#'+id).val('');
        else if ($('#'+id).hasClass('error') || $('#'+id).hasClass('success')){
            if ($('#'+id).hasClass('error'))
                $('#'+id).val('');
            $('#'+id).removeClass('error success');
        }
    }
}



function signInWithRecaptcha(email, pw, captcharesp, remember, successCallback, isCheckoutAction, saveForLater, isSecurityCheck, checkedPage, sectionURL){
    var isCheckoutSignin = $('#checkout_signin_email').is(':visible');
    var emailVisible = isCheckoutSignin ? $('#checkout_signin_email') : $('#signin_email');
    var pwVisible = isCheckoutSignin ? $('#checkout_signin_pw') : $('#signin_pw');
    var errorMessageOutput = $('#login_error_message');
	var rememberVisible =  $('#rememberMeCustomer');

	var favCode = $('#merge-heart-products').data('favcode');
    var favBrand = $('#merge-heart-products').data('favbrand');

    var email = email ? email : emailVisible.val();
    var pw = pw ? pw : pwVisible.val();
    var isBuyNow = location.href.indexOf('bn=true') != -1;
	if(typeof remember === 'undefined')
		remember = rememberVisible.prop('checked');

    var data = $.param({
        email: email,
        pw: pw,
        d: $('#merge-heart-products').data('favDept'),
		favcode: favCode,
        favbrand: favBrand,
		g_recaptcha_response : captcharesp,
		karmir_luys : true,
        rememberMe: remember,
        isCheckout: isCheckoutAction,
        saveForLater: saveForLater,
        isSecurityCheck: isSecurityCheck,
        checkedPage: checkedPage,
        isBuyNow : isBuyNow,
		sectionURL : sectionURL
    });
    $.ajax({
        type: 'POST',
        url: '/r/ajax/SignIn.jsp',
        data: data,
        success: function(data){
            var obj = parseRtn(data);
            pwVisible.removeClass('errortextbox');
            emailVisible.removeClass('errortextbox');
            if (obj.success){
                iPinYouSignIn();
                if(obj.msg0){
                    <!-- Convertro tracking function for login user-->
                    if (typeof convertroLogin === 'function'){
                        convertroLogin(obj.msg0);
                    }
                }

                if (typeof successCallback != 'function')
                    location.reload();
            } else {
                if (obj.msg0 && obj.msg0.length > 0){
                    emailVisible.addClass('errortextbox').val(obj.msg0).one('focus', function(){ $(this).removeClass('errortextbox').val(''); });
                    pwVisible.val('');
                }
                if (obj.msg1 && obj.msg1.length > 0){
                    var turnOffErrorBox = function(){$(this).removeClass('errortextbox');};
                    emailVisible.addClass('errortextbox').one('focus', turnOffErrorBox);
                    pwVisible.addClass('errortextbox').one('focus', turnOffErrorBox);
                    errorMessageOutput.html(obj.msg1).slideDown();
                    setTimeout(function(){ errorMessageOutput.slideUp(); }, 6000);
                }
                // unverified captcha , error code 3
                if(obj.msg2 && obj.msg2.length > 0){
                    errorMessageOutput.html(obj.msg2).slideDown();
                    setTimeout(function(){ errorMessageOutput.slideUp(); }, 6000);
                }
            }
            if (typeof successCallback === 'function')
                successCallback(obj);
        }
    });
}

function signIn(email, pw, remember, successCallback, isCheckoutAction, saveForLater, isSecurityCheck, checkedPage, sectionURL){
	var isCheckoutSignin = $('#checkout_signin_email').is(':visible');
	var emailVisible = isCheckoutSignin ? $('#checkout_signin_email') : $('#signin_email');
	var pwVisible = isCheckoutSignin ? $('#checkout_signin_pw') : $('#signin_pw');
	var errorMessageOutput = $('#login_error_message');
	var rememberVisible = $('#rememberMeCustomer');

	var email = email ? email : emailVisible.val();
	var pw = pw ? pw : pwVisible.val();
	if(typeof remember === 'undefined')
		remember = rememberVisible.prop('checked');
	var data = $.param({
		email: email,
		pw: pw,
		rememberMe: remember,
		isCheckout: isCheckoutAction,
		saveForLater: saveForLater,
		isSecurityCheck: isSecurityCheck,
		checkedPage: checkedPage,
		sectionURL : sectionURL
	});
	$.ajax({
		type: 'POST',
		url: '/r/ajax/SignIn.jsp',
		data: data,
		success: function(data){
			var obj = parseRtn(data);
			pwVisible.removeClass('errortextbox');
			emailVisible.removeClass('errortextbox');
			if (obj.success){

				if(obj.msg0){
					<!-- Convertro tracking function for login user-->
					if (typeof convertroLogin === 'function'){
						convertroLogin(obj.msg0);
					}
				}

				if (typeof successCallback != 'function')
					location.reload();
			} else {
				if (obj.msg0 && obj.msg0.length > 0){
					emailVisible.addClass('errortextbox').val(obj.msg0).one('focus', function(){ $(this).removeClass('errortextbox').val(''); });
					pwVisible.val('');
				}
				if (obj.msg1 && obj.msg1.length > 0){
					var turnOffErrorBox = function(){$(this).removeClass('errortextbox');};
					emailVisible.addClass('errortextbox').one('focus', turnOffErrorBox);
					pwVisible.addClass('errortextbox').one('focus', turnOffErrorBox);
					errorMessageOutput.html(obj.msg1).slideDown();
					setTimeout(function(){ errorMessageOutput.slideUp(); }, 6000);
				}
			}
			if (typeof successCallback === 'function')
				successCallback(obj);
      }
	});
}

function signOut(){
	try {
        if(typeof(cordial) != 'undefined' && cordial != null) {
            cordial.forget();
        }
	} catch(e) {
		console.log(e);
	}
    $.ajax({
        type: 'POST',
        url: '/r/ajax/SignOut.jsp',
        success: function(data){
            var obj = parseRtn(data);
            if (obj.success){
                location.reload();
            } else {
                alert ("Error logging out");
            }
        }
    });
}

function createAccountWithRecaptcha(email, pw, captcharesp, verify, saveCurrent, callback, subscribe, isFemale, saveForLater, invoice, optInLoyalty) {
    if (typeof email === 'undefined')
        email = $('#create_acct_email').val();
    if (typeof pw === 'undefined')
        pw = $('#create_acct_pw').val();
    if (typeof verify === 'undefined')
        verify = $('#create_acct_pw_verify').val();
    if (typeof subscribe === 'undefined')
        subscribe = false;
    if (typeof isFemale === 'undefined')
        isFemale = true;

    optInLoyalty = Boolean(optInLoyalty) || $("#loyalty").is(":checked");

    var favcode = $('#merge-heart-products').data('favcode');
    var favBrand = $('#merge-heart-products').data('favbrand');
    var data = $.param({
        email: email,
        pw: pw,
        d: $('#merge-heart-products').data('favDept'),
		favcode: favcode,
        favbrand: favBrand,
		g_recaptcha_response : captcharesp,
		karmir_luys : true,
        verifypw: verify,
        saveCurrent: saveCurrent,
        subscribe: subscribe,
        optInLoyalty: optInLoyalty,
        isFemale: isFemale,
        saveForLater: saveForLater,
        invoice: invoice
    });
    $.ajax({
        type: 'POST',
        url: '/r/ajax/CreateAccount.jsp',
        data: data,
        success: function(data){
            var obj = parseRtn(data);

            if (typeof callback === 'function')
                callback(obj);
            else {
                $('#create_acct_pw, #create_acct_pw_verify').val('');
                if (obj.success){
                    $.modal({
                        url:'/r/dialog/Welcome.jsp',
                        onLoad: closeSignInCreate,
                        triggerOpen: true
                    });
                    if(obj.msg0){
                        if (typeof convertroNewAccount === 'function')
                            convertroNewAccount(obj.msg0);
                    }
                } else{
                    var outputLocation = $('#create_account_error_message');
                    switch(obj.code){
                        case 1:
                            outputLocation = $('#create_acct_email');
                            outputLocation.addClass('errortextbox');
                            break;
                        case 3:
                            $('#create_acct_pw, #create_acct_pw_verify').addClass('errortextbox');
                            break;
                        case 2:
                        case 4:
                            $('#create_acct_pw').addClass('errortextbox');
                            break;
						case 13:
							outputLocation = $('#create_account_captcha_error_msg');
							break;
                    }
                    outputLocation.html(obj.msg0);
                }
            }
        }
    });
}

function createAccount(email, pw, verify, saveCurrent, callback, subscribe, isFemale, saveForLater, invoice, optInLoyalty, loyaltyOrderIds) {
	if (typeof email === 'undefined')
		email = $('#create_acct_email').val();
	if (typeof pw === 'undefined')
		pw = $('#create_acct_pw').val();
	if (typeof verify === 'undefined')
		verify = $('#create_acct_pw_verify').val();
	if (typeof subscribe === 'undefined')
		subscribe = false;
	if (typeof isFemale === 'undefined')
		isFemale = true;

	var data = $.param({
		email: email,
		pw: pw,
		verifypw: verify,
		saveCurrent: saveCurrent,
		subscribe: subscribe,
		isFemale: isFemale,
		saveForLater: saveForLater,
		invoice: invoice,
        optInLoyalty: Boolean(optInLoyalty) || $("#loyalty").is(":checked"),
        loyaltyOrderIds: loyaltyOrderIds || ""
	});
	$.ajax({
		type: 'POST',
		url: '/r/ajax/CreateAccount.jsp',
		data: data,
		success: function(data){
			var obj = parseRtn(data);

			if (typeof callback === 'function')
				callback(obj);
			else {
				$('#create_acct_pw, #create_acct_pw_verify').val('');
				if (obj.success){
					$.modal({
						url:'/r/dialog/Welcome.jsp',
						onLoad: closeSignInCreate,
            triggerOpen: true
					});
					if(obj.msg0){
						if (typeof convertroNewAccount === 'function')
							convertroNewAccount(obj.msg0);
					}
				} else{
					var outputLocation = $('#create_account_error_message');
					switch(obj.code){
						case 1:
							outputLocation = $('#create_acct_email');
							outputLocation.addClass('errortextbox');
							break;
						case 3:
							$('#create_acct_pw, #create_acct_pw_verify').addClass('errortextbox');
							break;
						case 2:
						case 4:
							$('#create_acct_pw').addClass('errortextbox');
							break;
					}
					outputLocation.html(obj.msg0);
				}
			}
		}
	});
}

var BASIC_EMAIL_VALIDATION_REGEX = /.+\@.+\..+/;
function sendPassword(param, callback){
    var emailField = $('#login_lostpw_email');
    var emailText = emailField.val();
	if (emailText.length == 0 || !BASIC_EMAIL_VALIDATION_REGEX.test(emailText)) {
        emailField.addClass('errortextbox');
        emailField.closest('.field').addClass('is-error');
		return;
	}
    emailField.closest('.field').removeClass('is-error');
    emailField.removeClass('errortextbox');
    var data = 'email=' + $('#login_lostpw_email').val();
    $.ajax({
        type: 'POST',
        url: '/r/ajax/EmailPassword.jsp',
        data: data,
        success: function(data){
            var obj = parseRtn(data);

            if (typeof callback !== 'undefined'){
                callback(obj);
            } else {
                if (obj.success){
                    if (param==null){
                        $.modal({
                            url: '/r/dialog/PasswordSent.jsp',
                            triggerOpen: true
                        });
                    }
                    else {
                        $.modal({
                            url: '/r/dialog/PasswordSent2.jsp',
                            triggerOpen: true
                        });
                    }
                } else {
                    $('#login_lostpw_email').addClass('errortextbox').val(obj.msg0);
                }
            }
        }
    });
}



function sendPasswordCustomerCare(input, param, callback){
	var email = input.val();
	if (email.length == 0) {
		input.addClass('errortextbox');
		return;
	}
	var data = 'email=' + email;
	$.ajax({
		type: 'POST',
		url: '/r/ajax/EmailPassword.jsp',
		data: data,
		success: function(data){
			var obj = parseRtn(data);

			if (typeof callback !== 'undefined'){
				callback(obj);
			} else {
				if (obj.success){
					if (param==null){
						$.modal({
							url: '/r/dialog/PasswordSent.jsp',
              triggerOpen: true
						});
					}
					else {
						$.modal({
							url: '/r/dialog/PasswordSent2.jsp',
              triggerOpen: true
						});
					}
				} else {
					input.addClass('errortextbox').val(obj.msg0);
				}
			}
		}
	});
}


function closeSignInCreate(){
    $('#rev_login_popup').hide();
}

function replaceField(hiddenField, plainField){
    $(plainField).focus(function(){
        $(plainField).hide();
        $(hiddenField).show().focus();
    });
    $(hiddenField).blur(function(){
        if($(hiddenField).val() == ''){
            $(plainField).show();
            $(hiddenField).hide();
        }
    });
}

$(document).ready(function() {
    // Sign In / Create Account

    $('#mini_cart .submit_checkout').click(function(){
        window.location = '/r/SignInCheckout.jsp';
    });

    // Currency
    $('.js-currency-select .js-currency-item').click(function(){
		var element = $(this);
		var currency = element.data('currency');
		if (!currency)
			currency = element.parent('li').attr('id').substring(0, 3)

		setCurrency(currency);
    });

    // Language
    $('#active_languages a').click(function(){
		var element = $(this);
		var language = element.data('language');
		if (!language)
			language = $(this).parent('li').attr('id').substring(0, 2);
        setLanguage(language);
    });
});

function closeLightBox(){
    $.modal.close();
}

function removeError(obj){
    if($(obj).val().length > 0)
        $(obj).removeClass('errortextbox');

}

function showMoreCurrencies(){
    $('#js-currency-small-menu').hide();
    $('#js-currency-big-menu').show();
    $(".js-currency-select").addClass("u-static");
}


// Display Product JS


function el(name) {
	return document.getElementById(name);
}
/* Common methods to write a review */
function ReviewSubmit(prodCode, elem, emailReview) {
	var reviewDiv = $("#review-container-" + prodCode);
	var ratingWrap = reviewDiv.find(".modal-review__rating-wrap");
	var commentField = reviewDiv.find('.reviewer-comment');
	var valid = true;
	resetErrors();

	var emailDiv = reviewDiv.find('.js-review__email');
	var emailField = reviewDiv.find('.reviewer-email');
	var data = {
		code: prodCode,
		overallAddRevValueField: ratingWrap.find(".is-active").attr("data-rating-value"),
		comments: commentField.val(),
		screenName: reviewDiv.find('.reviewer-name').val(),
		cityState: reviewDiv.find('.reviewer-location').val(),
		emailAddRev: emailField.val(),
        sweepstakesOptin: reviewDiv.find('#sweepstakes-checkbox-'+ prodCode).is(':checked')
	};

	if (data.comments == commentField.attr('placeholder')) {
		data.comments = "";
	}

	reviewDiv.find(".js-review-question").each(function() {
		var paramName = $(this).data("name");
		var value = $(this).find("input:radio:checked").val();
		var required = $(this).data("required");
		data[paramName] = value;

		if (required && !value) {
			valid = false;
			$(this).find('.js-review-error').removeClass('u-hide');
		}
	});

	// Validation
	// Note that some validation is done while looping through the questions, above.
	// Check that we have a valid rating.
	if (!data.overallAddRevValueField) {
		valid = false;
		reviewDiv.find('.js-review-error__rating').removeClass('u-hide');
	}

	// Check that a valid email has been entered.
	if (!isEmail(data.emailAddRev)) {
		valid = false;
		emailDiv.addClass('is-error');
	}

	if (!valid) return;

	// Validation has passed.
	$.ajax({
		url: '/r/ajax/ReviewProduct.jsp',
		data: data,
		success: function (data) {
      var $productReviewContaioner = $('#product-review-container');
      if ($productReviewContaioner.length) {
       reviewDiv.hide();
       var remaining = $("div[id*='review-container-']").not(':hidden').length;
       if (remaining == 0) {
         $productReviewContaioner.hide();
         if ($("#reviewConfirmFinal").length) {
  					$("#reviewConfirmFinal").removeClass("modal");
            $('.js-carousel').slickSlider('checkPosition');
  			 }
       }
      } else {
        $.modal({
          url: '/r/dialog/ProductMessages.jsp' + (emailReview ? "?emailreview=yes" : ""),
          triggerOpen: true,
          type: 'small',
		  onComplete : function () {
          	$('#modal .js-modal-close').attr('href', 'javascript:void(0);');
		    $('#modal .js-modal-close').attr('onclick', 'location.reload();');
		  }
        });
      }
		},
		error: function (jqXHR, textStatus) {
			if (textStatus == 'timeout') {
				getErrorMessage('An error has occured connecting to the server. You may need to resubmit your request and try again.');
			}
		}
	});

	function resetErrors() {
		reviewDiv.find('.js-review-error').addClass('u-hide');
		reviewDiv.find('.js-review-error__rating').addClass('u-hide');
		reviewDiv.find(".js-review__email").removeClass("is-error");
	}

	// Check if email
	function isEmail(email) {
		var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		return regex.test(email);
	}
}

/* Common Revolve review methods to write a review */
function ReviewRevolveSubmit(prodCode, emailReview) {
    var reviewDiv = $("#review-container-" + prodCode);
    var commentField = reviewDiv.find('.reviewer-comment');
    var valid = true;
    var fromReviewIpadApps = $('#fromReviewIpadApp').val();
    resetErrors();

    var emailDiv = reviewDiv.find('.js-review__email');
    var emailField = reviewDiv.find('.reviewer-email');
    var data = {
        code: prodCode,
        overallAddRevValueField: $('input[name=rating-scale-'+prodCode+']:checked').val(),
        comments: commentField.val(),
        screenName: reviewDiv.find('.reviewer-name').val(),
        cityState: reviewDiv.find('.reviewer-location').val(),
        emailAddRev: emailField.val(),
        sweepstakesOptin: reviewDiv.find('#sweepstakes-checkbox-'+ prodCode).is(':checked')
    };

    if (data.comments == commentField.attr('placeholder')) {
        data.comments = "";
    }

    reviewDiv.find(".js-review-question").each(function() {
        var paramName = $(this).data("name");
        var value = $(this).find("input:radio:checked").val();
        var required = $(this).data("required");
        data[paramName] = value;

        if (required && !value) {
            valid = false;
            $(this).find('.js-review-error').removeClass('u-hide');
        }
    });

    // Validation
    // Note that some validation is done while looping through the questions, above.
    // Check that we have a valid rating.

    // Check that a valid email has been entered.
    if (!isEmail(data.emailAddRev)) {
        valid = false;
        emailDiv.addClass('is-error');
    }

    if (!valid) return;

    // Validation has passed.
    $.ajax({
        url: '/r/ajax/ReviewProduct.jsp',
        data: data,
        success: function (data) {
            var $productReviewContaioner = $('#product-review-container');
            if ($productReviewContaioner.length) {
                reviewDiv.hide();
                var remaining = $("div[id*='review-container-']").not(':hidden').length;
                if (remaining == 0) {
                    $productReviewContaioner.hide();
                    if ($("#reviewConfirmFinal").length) {
                        $("#reviewConfirmFinal").removeClass("modal");
                        $('.js-carousel').slickSlider('checkPosition');
                    }
                }
            } else {
                if (fromReviewIpadApps) {
                    showNotification(true);
                } else {
                    $.modal({
                        url: '/r/dialog/ProductMessages.jsp' + (emailReview ? "?emailreview=yes" : ""),
                        triggerOpen: true,
                        type: 'small',
                        onComplete: function () {
                            $('#modal .js-modal-close').attr('href', 'javascript:void(0);');
                            $('#modal .js-modal-close').attr('onclick', 'location.reload();');
                        }
                    });
                }
            }
        },
        error: function (jqXHR, textStatus) {
            if (textStatus == 'timeout') {
                getErrorMessage('An error has occured connecting to the server. You may need to resubmit your request and try again.');
            }
        }
    });

    function resetErrors() {
        reviewDiv.find('.js-review-error').addClass('u-hide');
        reviewDiv.find('.js-review-error__rating').addClass('u-hide');
        reviewDiv.find(".js-review__email").removeClass("is-error");
    }

    // Check if email
    function isEmail(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    }
}

function addProductWithData(data) {
    var productCode = data.productCode;
    var size = data.size;
    var sectionURL = data.sectionURL;
    var sessionID = data.sessionID;
    var count = data.count;
    var successCallback = data.successCallback;
    var csrfHash = data.csrfHash;
    var closeWindowAfterAddingToCart = data.closeWindowAfterAddingToCart;
    var isMens = data.isMens;
    var src = data.src;
    var srcType = data.srcType;
    var useReferrer = data.useReferrer;
    var isPopup = data.isPopup;
    var qvclick = data.qvclick;
    var hotlistQuickView = data.hotlistQuickView;
    var focusID = data.focusID;
    var fitItemSizes = data.fitItemSizes;
    var serial = data.serial;

    addProduct(productCode, size, sectionURL, sessionID, count, successCallback, csrfHash, closeWindowAfterAddingToCart, isMens, src, srcType, useReferrer, isPopup, qvclick, hotlistQuickView, focusID, fitItemSizes, serial, data);
}

// THERE IS NO FUNCTION OVERLOADING IN JAVASCRIPT, DO NOT TRY TO JUST CREATE NEW METHODS WITH MORE PARAMS
// use addProductWithData if wanting to add more params, since that function will now just pass "moreData" as last param,
// no more adding specific params
function addProduct(productCode, size, sectionURL, sessionID, count, successCallback, csrfHash, closeWindowAfterAddingToCart, isMens, src, srcType, useReferrer, isPopup, qvclick, hotlistQuickView, focusID, fitItemSizes, serial, moreData) {
    moreData = moreData || {};

    return new Promise(function (resolve, reject) {
        var favorited = false;
        if (window.location.href.indexOf("MyFavorite.jsp") > -1) {
            favorited = true;
        }
        var dataObject = {
            colorSelect: productCode,
            serialNumber: serial,
            sizeSelect: size,
            sectionURL: sectionURL,
            sessionID: sessionID,
            count: count,
            csrfHash: csrfHash,
            isMens: isMens,
            d: $('#header-jsp').data('department'),
            src: src,
            srcType: srcType,
            qvclick: qvclick,
            contextReferrer: useReferrer ? document.referrer : window.location.href,
            addedFromFavorite: favorited,
            fitItemSizes : fitItemSizes,
            dateAdded: moreData.dateAdded
        };

        $.ajax({
            type: 'POST',
            url: '/r/ajax/AddItemToBag.jsp',
            data: $.param(dataObject),
            success: function(rtn){
                if(typeof(isPopup) != 'undefined' && isPopup) {
                    insertPageTrackForQuickView(null, true, productCode);
                } else {
                    insertPageTrack(true, productCode);
                }

                var obj = parseRtn(rtn);
                if (obj.success) {
                    if (obj.msg0)
                        updateGender(obj.msg0);

                    // incrementBagCount(count);
                    reloadCartRail();

                    // No callback defined -> default behavior (show small "Just
                    // Added" drop down)
                    if (typeof successCallback === 'function') {
                        successCallback(dataObject, obj); // Originally passed dataObject.
                        if (closeWindowAfterAddingToCart) {
                            closeLightBox();
                        }
                        updateMiniBag();
                    } else {
                        updateMiniBag();
                        // Create JS object
                        var itemInformation = {
                            code: productCode,
                            name: obj.msg1,
                            src: obj.msg2,
                            designer: obj.msg3,
                            designerHref: obj.msg4,
                            price: obj.msg5,
                            origPrice: obj.msg6,
                            disclaimer: obj.msg9,
                            invAlert: obj.msg12,
                            color: obj.msg10,
                            cartTotal: obj.msg11,
                            cartSize: obj.msg13,
                            addlItemsText: obj.msg14,
                            cartItemsCount: obj.msg22,
                            size: obj.msg23,
                            itemQtyInCart: obj.msg24
                        };
                        if (hotlistQuickView) {
                            $('.quickview_add_to_bag').prop('disabled', true);
                            $('.quickview_add_to_bag').text($('.quickview_add_to_bag').data('textItemAdded'));
                            setTimeout(function () {
                                $('.quickview_add_to_bag').prop('disabled', false);
                                if ($('.quickview_add_to_bag').closest('.atb-buy--preorder-qv').length > 0) {
                                    $('.quickview_add_to_bag').text($('.quickview_add_to_bag').data('textPreorder'));
                                } else {
                                    $('.quickview_add_to_bag').text($('.quickview_add_to_bag').data('textAddItem'));
                                }
                            }, 3000);
                        } else {
                            //no mini cart on shopping bag page
                            if (location.href.indexOf("/r/ShoppingBag.jsp") == -1) {
                                if (obj.msg16 == "Gift Certificates") {
                                    itemInformation.isGiftCert = true;
                                    itemInformation.delivery = "Email Delivery";

                                    showGiftCertInfo(itemInformation, focusID);
                                } else if (src !== 'CartRail') {
                                    showJustAdded(itemInformation, focusID, fitItemSizes);
                                }
                            }
                        }
                        gaAddToCartAction('add', productCode, obj.msg1, obj.msg16, obj.msg3, size, obj.msg15, 1);
                        if (typeof iPinYouAddtoCart === 'function') {
                            iPinYouAddtoCart(productCode);
                        }
                        if (obj.msg7) {
                            if (typeof convertroAddItem === 'function')
                                convertroAddItem(obj.msg7);
                        }
                        //close the lightbox if closeWindowAfterAddingToCart is true
                        if (closeWindowAfterAddingToCart)
                            closeLightBox();

                        if (typeof insertSnapchatAddToCart === 'function') {
                            insertSnapchatAddToCart(productCode);
                        }
                        if (typeof insertCriteoAddToCart === 'function') {
                            insertCriteoAddToCart(productCode);
                        }
                        if (typeof addToCartYahoo === 'function') {
                            addToCartYahoo(productCode);
                        }
                        if (typeof addToCartKenshoo === 'function') {
                            addToCartKenshoo(productCode);
                        }
                        if (typeof insertFacebookAddToCart === 'function') {
                            insertFacebookAddToCart(0, productCode, obj.msg5, size);
                        }
                        if (typeof addToCartPinterest === 'function') {
                            addToCartPinterest(productCode, obj.msg5.substring(1), 1);
                        }
                        if (typeof insertCordial === 'function') {
                            var cartItems = [];
                            var cartItem = [];
                            cartItem["productCode"] = productCode;

                            cartItem["productCategory"] = dataObject.d;
                            cartItem["productName"] = itemInformation.name;
                            cartItem["images"] = null;
                            cartItem["description"] = "";
                            cartItem["qty"] = 1;
                            cartItem["url"] = "";
                            cartItem["attr"] = [];
                            cartItem["attr"]["manufacturer"] = itemInformation.designer;
                            cartItem["attr"]["size"] = size;
                            cartItems.push(cartItem);

                            insertCordialAddToCart(cartItems);
                        }
                    }

                    if (obj.reCAPTCHA) {
                        showBagLimitReCAPTCHA();
                    } else if (obj.reachedBagLimit) {
                        showBagLimitPopup(productCode, size, favorited);
                    }
                    resolve();
                } else if (obj.reCAPTCHA){
                    showBagLimitReCAPTCHA();
                    reject();
                } else if (obj.reachedBagLimit) {
                    showBagLimitPopup(productCode, size, favorited);
                    reject();
                } else {
                    reject();
                }
            },
            error: function () {
                reject();
            }
        });
    });
}

function showBagLimitReCAPTCHA(){
    $.modal({
        url: "/r/dialog/VerifyHuman.jsp",
        type: "small",
        open: null,
        cache: false,
        overlayClose: true,
        triggerOpen: true,
		onClose : function(){
        	if(window.rcProps && typeof window.rcProps.ReCAPTCHARestore =='function'){
                window.rcProps.ReCAPTCHARestore();
                window.rcProps.ReCAPTCHARestore = "";
			}
		}
    });
}

function showBagLimitPopup(code, size, hideMyFav){
	var data = $.param({
		code : code,
		size : size,
        hideMyFav : hideMyFav
	});
    $.modal({
        url: "/r/dialog/ShoppingBagLimit.jsp?" + data,
        type: "small",
        open: null,
        cache: false,
        overlayClose: true,
        triggerOpen: true,
        onComplete : function () {}
    });
}

function addSavedProduct(productCode, size, sectionURL, sessionID, count, successCallback, csrfHash, closeWindowAfterAddingToCart, isMens, src, srcType, useReferrer, isPopup, qvclick){

	var favorited = false;
	if(window.location.href.indexOf("MyFavorite.jsp") > -1) {
		favorited = true;
	}
	var dataObject = {
		colorSelect: productCode,
		sizeSelect: size,
		sectionURL: sectionURL,
		sessionID: sessionID,
		count: count,
		csrfHash: csrfHash,
		isMens: isMens,
		d: isMens ? "Mens" : "Womens",
		src: src,
		srcType: srcType,
		qvclick: qvclick,
		contextReferrer: useReferrer ? document.referrer : window.location.href,
		addedFromFavorite: favorited,
		savedForLater: true
	};
	var data = $.param(dataObject);
	$.ajax({
		type: 'POST',
		url: '/r/ajax/AddItemToBag.jsp',
		data: data,
		success: function(rtn){
			if(typeof(isPopup) != 'undefined' && isPopup) {
				insertPageTrackForQuickView(null, true, productCode);
			} else {
				insertPageTrack(true, productCode);
			}
			var obj = parseRtn(rtn);
			if (obj.success) {
				if (obj.msg0)
					updateGender(obj.msg0);

				incrementBagCount(count);

				// No callback defined -> default behavior (show small "Just
				// Added" drop down)
				if (typeof successCallback === 'function') {
					successCallback(dataObject, obj);
					if (closeWindowAfterAddingToCart) {
						closeLightBox();
					}

				} else {
					updateMiniBag();
					// Create JS object
					var itemInformation = {
                        code: productCode,
                        name: obj.msg1,
                        src: obj.msg2,
                        designer: obj.msg3,
                        designerHref: obj.msg4,
                        price: obj.msg5,
                        origPrice: obj.msg6,
                        disclaimer: obj.msg9,
                        invAlert: obj.msg12,
                        color: obj.msg10,
                        cartTotal: obj.msg11,
                        cartSize: obj.msg13,
                        addlItemsText: obj.msg14,
                        cartItemsCount: obj.msg22,
                        size: obj.msg23,
                        itemQtyInCart: obj.msg24
					};

					showJustAdded(itemInformation);

					if(obj.msg7){
						if (typeof convertroAddItem === 'function')
							convertroAddItem(obj.msg7);
					}
					//close the lightbox if closeWindowAfterAddingToCart is true
					if (closeWindowAfterAddingToCart)
						closeLightBox();
				}
			} else {
                if (obj.reCAPTCHA){
                    showBagLimitReCAPTCHA();
                } else if(obj.reachedBagLimit){
                    showBagLimitPopup(productCode, size, false);
                }
				console.log('Unable to add product');
			}
		}
	});

}

/**
 * @param item - Information about object; should have:
 *        name, src, designer, designerHref, price, origPrice
 */
function showJustAdded(item, focusID, fitItemSizes){

	if (!item || $('#js-item-added-trigger').length !== 0)
		return;

    var addToBagMarkupLoadFn = function () {
        $('#add-to-bag-with-ctl-modal').removeClass('modal');
        $('#addToBagDisplayPrice').html(item.price);
        $('#addToBagDisplayRetailPrice').html(item.price);
        $('.js-minicart-size-display .js-size').html(item.size);
        if (fitItemSizes && fitItemSizes.trim().length) {
            $('.js-minicart-size-display .js-tryon-size').html(fitItemSizes);
            $('.js-minicart-size-display .js-tryon-size-wrap').show();
        } else {
            $('.js-minicart-size-display .js-tryon-size-wrap').hide();
            $('.js-minicart-size-display .js-tryon-size').html('');
        }
        $('.js-qty').html(item.itemQtyInCart);
        if (item.disclaimer && item.disclaimer != '') {
            $('.js-mini-cart-added__disclaimer').show().html(item.disclaimer);
        } else {
            $('.js-mini-cart-added__disclaimer').hide();
        }
        if (item.invAlert && item.invAlert != '') {
            $('.js-mini-cart-added__alert').show().html(item.invAlert);
        } else {
            $('.js-mini-cart-added__alert').hide();
        }
        $('#js-miniBagMarkup').show();

        if(!$('.js-carousel-ctl-atb').hasClass('slick-initialized')) {
            var $slickNextBtn = $('.js-carousel-ctl-atb').parent().find('.js-carousel__next');
            var $slickPrevBtn = $('.js-carousel-ctl-atb').parent().find('.js-carousel__prev');
            $('.js-carousel-ctl-atb').slick({
                slidesToShow: 3,
                infinite: false,
                slidesToScoll: 1,
                nextArrow: $slickNextBtn,
                prevArrow: $slickPrevBtn
            });
        }
    }
    if($('div.pdp').length) { // called from pdp page
        $.modal({
            "url": "/content/product/addToBagConfirmPopupMarkup/" + item.code,
            type: 'small',
            triggerOpen: true,
            onOpen: addToBagMarkupLoadFn,
            onClose: function () {
                $('.js-carousel-ctl-atb').slick('unslick');
            },
        });
        return;
    }

    var container = $('#js-mini-cart-added');
    if (item.name)
        $('#js-mini-cart-added__product-name', container).html(item.name);
    if (item.src)
        $('#js-mini-cart-added__image', container).attr('src', item.src);
    if (item.designer)
        $('#js-mini-cart-added__designer-link', container).html(item.designer);
    if (item.designerHref)
        $('#js-mini-cart-added__designer-link', container).attr('href', item.designerHref);
	if (item.color){
        $('#js-mini-cart-added__color', container).html(item.color);
        $('#js-mini-cart-added__color', container).parent('p').show();
    }else{
        $('#js-mini-cart-added__color', container).parent('p').hide();
    }

    if(item.cartItemsCount) {
        $('#addedItemMiniBagCount', container).html(item.cartItemsCount);
    }

    if(item.size) {
        $('#js-mini-cart-added__size', container).html(item.size);
    }

    if(item.itemQtyInCart) {
        $('#js-mini-cart-added__qty', container).html(item.itemQtyInCart);
    }

    if($('#js-mini-cart').hasClass('mini-bag--empty')) {
        $('#js-mini-cart').removeClass('mini-bag--empty');
    }

    $('#js-miniBagMarkup').show();

    if (item.price)
        $('#js-mini-cart-added__price, #js-mini-cart-added__markdown', container).html(item.price);
	if (item.cartTotal)
		$('#js-mini-cart-added__cartTotal', container).html(item.cartTotal);
	else
		$('#js-mini-cart-added__cartTotal', container).hide();
    if (item.origPrice && item.origPrice!='hideOriginalPrice'){
        $('#js-mini-cart-added__markdown-original', container).show().html(item.origPrice);
        $('#js-mini-cart-added__markdown', container).show();
        $('#js-mini-cart-added__price', container).hide();
    } else {
        $('#js-mini-cart-added__price', container).show();
        $('#js-mini-cart-added__markdown, #js-mini-cart-added__markdown-original', container).hide();
    }
    if (item.disclaimer && item.disclaimer != '') {
		$('#js-mini-cart-added__disclaimer').show().html(item.disclaimer);
	}
	else {
		$('#js-mini-cart-added__disclaimer').hide();
	}
	if (item.invAlert && item.invAlert != '') {
		$('#js-mini-cart-added__alert').show().html(item.invAlert);
	}
	else {
		$('#js-mini-cart-added__alert').hide();
	}
	if (item.addlItemsText && item.addlItemsText != '') {
		$('#js-mini-cart-added__addl_msg').show().html(item.addlItemsText);
	}
	else {
		$('#js-mini-cart-added__addl_msg').hide();
	}
    $('#js-mini-cart-items').hide();
    container.show()
    var atbTimeout;
    $('#tr-mybag').dropdown('open');
    if($('.no-focus')[0]){
      atbTimeout = setTimeout(closeJustAdded, 7000);
      // Cancel Timeout if user mouseover mini bag
      $('#tr-mybag').on('mouseenter', function() {
        clearTimeout(atbTimeout);
        $('#tr-mybag').off('mouseenter');
      });
    } else {
  		$('#tr-mybag').dropdown('openAndTrap', $('#'+focusID));
      $('#continue-shopping-link').on("click",function(){
        $('#'+focusID).focus();
      });
    }

    // Force Header into view when mini bag is visible
    var $FixedHeader = $('.js-fixed-header');
    if($FixedHeader.hasClass('is-fixed-scroll')) {
      $FixedHeader.css({'transform': 'translate3d(0px, 0px, 0px)'});
    }
}

function showGiftCertInfo(item, focusID){
    if (!item)
        return;

    $('.mini-bag__product-brand').addClass('u-hide');
    $('#js-mini-cart-added__size').parent().parent().addClass('u-hide');
    $('#js-mini-cart-added__disclaimer').addClass('u-hide');
    $('#js-mini-cart-added__alert').addClass('u-hide');

    var container = $('#js-mini-cart-added');
    $('#js-mini-cart-added__product-name', container).html(item.name);
    $('#js-mini-cart-added__image', container).attr('src', item.src);
    $('#delivery_for_certificates', container).removeClass('u-hide');
    $('#delivery_for_certificates', container).html(item.delivery);
    $('#recipient', container).removeClass('u-hide');
    $('#recipient', container).html(item.recipient);
    $('#message', container).removeClass('u-hide');
    $('#message', container).html(item.message);

    if(item.cartItemsCount) {
        $('#addedItemMiniBagCount', container).html(item.cartItemsCount);
    }

    if($('#js-mini-cart').hasClass('mini-bag--empty')) {
        $('#js-mini-cart').removeClass('mini-bag--empty');
    }

    $('#js-miniBagMarkup').show();

    if (item.price)
        $('#js-mini-cart-added__price, #js-mini-cart-added__markdown', container).html(item.price);
    if (item.cartTotal)
        $('#js-mini-cart-added__cartTotal', container).html(item.cartTotal);
    else
        $('#js-mini-cart-added__cartTotal', container).hide();
    if (item.origPrice && item.origPrice!='hideOriginalPrice'){
        $('#js-mini-cart-added__markdown-original', container).show().html(item.origPrice);
        $('#js-mini-cart-added__markdown', container).show();
        $('#js-mini-cart-added__price', container).hide();
    } else {
        $('#js-mini-cart-added__price', container).show();
        $('#js-mini-cart-added__markdown, #js-mini-cart-added__markdown-original', container).hide();
    }
    if (item.addlItemsText && item.addlItemsText != '') {
        $('#js-mini-cart-added__addl_msg').show().html(item.addlItemsText);
    }
    else {
        $('#js-mini-cart-added__addl_msg').hide();
    }
    $('#js-mini-cart-items').hide();
    container.show()
    var atbTimeout;
    $('#tr-mybag').dropdown('open');
    if($('.no-focus')[0]){
        atbTimeout = setTimeout(closeJustAdded, 7000);
        // Cancel Timeout if user mouseover mini bag
        $('#tr-mybag').on('mouseenter', function() {
            clearTimeout(atbTimeout);
            $('#tr-mybag').off('mouseenter');
        });
    } else {
        $('#tr-mybag').dropdown('openAndTrap', $('#'+focusID));
        $('#continue-shopping-link').on("click",function(){
            $('#'+focusID).focus();
        });
    }

    // Force Header into view when mini bag is visible
    var $FixedHeader = $('.js-fixed-header');
    if($FixedHeader.hasClass('is-fixed-scroll')) {
        $FixedHeader.css({'transform': 'translate3d(0px, 0px, 0px)'});
    }
}

function addBuyNowProduct(code, size, sectionURL, source, qvclick, fittingRoomSize){
    var itemSource = source ? 'itemsrc=' + source : '';

    $.ajax({
        type: 'POST',
        url: '/r/ajax/BuyNow.jsp?' + itemSource,
        data: $.param({
            bnCode: code,
            bnSize: size,
			d: $('#header-jsp').data('department'),
            sectionUrl: sectionURL,
            qvclick: qvclick,
            fittingRoomSize: fittingRoomSize
        }),
        success: function(obj){
            var rtn = parseRtn(obj);
            if (rtn && rtn.success) {
                if (typeof iPinYouBuyNow === 'function'){
                    iPinYouBuyNow(code);
                }
				gaAddToCartActionWithAjax('add',code,size,1);
				gaCheckout(1,'SIGNIN');
				if (typeof insertCriteoAddToCart === 'function'){
					insertCriteoAddToCart(code);
				}
                var callback = function () {window.location = '/r/SignInCheckout.jsp?bn=true';}
                insertPageTrack(true, code, false, callback, callback, undefined, true);  // Continue
				// even if there is a bad response code

            }
        }
    });
}

function pushAddProductToGA(productName, productCode, price, brand, category, color, currencyCode, count) {
    pushProductOnGAHelper('addToCart', productName, productCode, price, brand, category, color, currencyCode, count);
}

function pushRemoveProductFromGA(productName, productCode, price, brand, category, color, currencyCode, count) {
    pushProductOnGAHelper('removeFromCart', productName, productCode, price, brand, category, color, currencyCode, count);
}

function pushProductOnGAHelper(action, productName, productCode, price, brand, category, color, currencyCode, count) {
    // Measure adding a product to a shopping cart by using an 'add' actionFieldObject and a list of productFieldObjects.
    if(typeof(count) == 'undefined') {
        count = 1;
    }
    try {
        dataLayer.push({
                'event': action,
                'ecommerce': {
                    'currencyCode': currencyCode,
                    'add': { // 'add' actionFieldObject measures.
                        'products': [
                            { // adding a product to a shopping cart.
                                'name': productName,
                                'id': productCode,
                                'price': price,
                                'brand': brand,
                                'category': category,
                                'variant': color,
                                'quantity': count
                            }
                        ]
                    }
                }
            }
        );

    } catch(err) {
        console.log(err.message);
    }
    // console.log("pushed to dataLayer event[" + action + "], productCode[" + productCode + "], name[" + productName + "] price[" + price + "], brand[" + brand + "], color[" + color + "], currencyCode[" + currencyCode + "], count[" + count + "]");
    // console.dir(dataLayer);
}

window.get_cordial_cID = function(accountKey){
    if(typeof accountKey === 'undefined'){
        try{
            accountKey = document.querySelectorAll('script[data-cordial-track-key]')[0].getAttribute('data-cordial-track-key');
        }catch(e){}
    }
    return getCookie('crdl_'+(accountKey || '')+'cID');
}

function pushAddProductToCordial(productName, productCode, price, brand, category, color, currencyCode, count) {
    pushProductOnCordialHelper('add', productName, productCode, price, brand, category, color, currencyCode, count);
}

function pushRemoveProductFromCordial(productName, productCode, price, brand, category, color, currencyCode, count) {
    pushProductOnCordialHelper('remove', productName, productCode, price, brand, category, color, currencyCode, count);
}

function pushProductOnCordialHelper(action, productName, productCode, price, brand, category, color, currencyCode, count) {
    // Measure adding a product to a shopping cart by using an 'add' actionFieldObject and a list of productFieldObjects.
    if(typeof(cordial) == 'undefined' || cordial == null) {
        return;
    }
    if(typeof(count) == 'undefined') {
        count = 1;
    }
    try {
        price = price.replace("$", "");
	} catch(err) {
    	console.log(err.message);
	}
    try {
        if(get_cordial_cID()) {
            cordial.cartitem(action, {
                    'productID': productCode,
                    'sku': productCode,
                    'category': category,
                    'name': productName,
                    'images': [],
                    'description': '',
                    'qty': count,
                    'itemPrice': price,
                    'url': '',
                    'attr': {
                        'manufacturer': brand,
                        'size': null
                    }
                }
            );
        }
        cordial.event('cartitem',{
            'action': action,
            'productID': productCode,
            'qty' : count
        });


    } catch(err) {
        console.log(err.message);
    }
    // console.log("pushed to dataLayer event[" + action + "], productCode[" + productCode + "], name[" + productName + "] price[" + price + "], brand[" + brand + "], color[" + color + "], currencyCode[" + currencyCode + "], count[" + count + "]");
    // console.dir(dataLayer);
}



function trackAddToBagFromFavorite(code, size, sectionUrl, sessionId){
    var data2 = $.param({
        code: code,
        action: 'addToCart'
    });

    $.ajax({
        type: 'POST',
        url: '/r/ajax/TrackHeartItem.jsp',
        data: data2,
        success: function(data2){
            var obj = parseRtn(data2);
            if (obj.success){
                ;
            }
        }
    });
}

function moveProduct(productCode, size, count){

    if (size == 'one size') size = 'all';
		submitHeart("product", productCode,"add");
		var data = $.param({
        code: productCode,
        size: size,
        count: count
    });
    $.ajax({
        type: 'POST',
        url: '/r/ajax/RemoveFromBag.jsp',
        data: data,
        success: function(data){
            var obj = parseRtn(data);
            if (obj.success){
               location.reload();

            }
        }
    });






}

function removeProductSerial(productCode, size, count, serial, successCallback){
    if (size == 'one size') size = 'all';
    var data = $.param({
        code: productCode,
        size: size,
        count: count,
        serial: serial
    });
    $.ajax({
        type: 'POST',
        url: '/r/ajax/RemoveFromBag.jsp',
        data: data,
        success: function(data){
            var obj = parseRtn(data);
            if (obj.success){
                if (obj.msg0)
                    updateGender(obj.msg0);

                if (typeof successCallback === 'function')
                    successCallback(obj);
                else {
                    decrementBagCount(count);
                    updateMiniBag();
                }
            }
        }
    });
}

function removeProduct(productCode, size, count, successCallback){
    var data = {
        code: productCode,
        size: size,
        count: count
    };
    removeProductWithData(data, successCallback);
}

function removeProductWithData(removeData, successCallback) {
    if(removeData.size == 'one size') {
        removeData.size = 'all';
    }
    $.ajax({
        type: 'POST',
        url: '/r/ajax/RemoveFromBag.jsp',
        data: removeData,
        success: function(data){
            var obj = parseRtn(data);
            if (obj.success){
                if (obj.msg0)
                    updateGender(obj.msg0);

                if (typeof successCallback === 'function')
                    successCallback(obj);
                else {
                    decrementBagCount(count);
                    updateMiniBag();
                }
            }
        }
    });
}

function incrementBagCount(incrementAmount){
    var bagCountElement = $('#my_bag_item_count');
    var bagCount = parseInt(bagCountElement.text(), 10);
    if (!incrementAmount)
        incrementAmount = 1;
    bagCountElement.text(bagCount+incrementAmount);
}

function reloadCartRail() {
	var cartRailComponent = $('cart-rail-component-id');
	if(cartRailComponent && (typeof loadRailContent === "function") ) {
		loadRailContent();
	}
}

function decrementBagCount(decrementAmount){
    var bagCountElement = $('#my_bag_item_count');
    var bagCount = parseInt(bagCountElement.text(), 10);
    if (!decrementAmount)
        decrementAmount = 1;
    bagCountElement.text(bagCount-decrementAmount);
}

function closeJustAdded(closeQuickView,focusID){
    var $minicart = $('#tr-mybag');
    var $minicartAdded = $('#js-mini-cart-added');
    var $minicartItems = $('#js-mini-cart-items');

    if ($minicartAdded.is(':visible')) {
        $('#tr-mybag').dropdown('close');
        $minicartAdded.fadeOut(300, function() {
            $minicartItems.fadeIn();
        });
    }
    Keyboard.releaseFocus($minicartAdded, $('#'+focusID));

}

function closeAndReload(){
    location.reload();

}

function deleteBillingOption(id, successCallback){
    var data = $.param({
        id: id,
        action: 'delete'
    });
    $.ajax({
        type: 'POST',
        url: '/r/ajax/SaveBillingOption.jsp',
        data: data,
        success: function(data){
            var obj = parseRtn(data);
            if (obj.success)
                if (typeof successCallback === 'function')
                    successCallback();
        }
    });
}


// this function is not used for MyBillingSettings.jsp
function createBillingOption(name, payType, num, code, expDate, tel, usrAddID, dateofbirth, successCallback){
    saveBillingOption(name, payType, num, code, expDate, tel, usrAddID, dateofbirth,

        function(rtn){
            var newRtn = {};
            newRtn.success = rtn.success;
            if (rtn.success){
                if (rtn.msg0)
                    newRtn.msg = rtn.msg0
                if (rtn.msg1)
                    newRtn.id = rtn.msg1;
                if (rtn.msg2)
                    newRtn.img = rtn.msg2;
                if (rtn.msg3)
                    newRtn.summary = rtn.msg3;
            }
            successCallback(newRtn);
        },
        -1
    );
}

function saveBillingOption(name, payType, num, code, expDate, tel, usrAddID, dateofbirth, successCallback, id){
    id = (typeof id === 'undefined') ? -1 : id;
    var data = $.param({
        name: name,
        paymentType: payType,
        number: num,
        code: code,
        expirationDate: expDate,
        telephone: tel,
        userAddressID: usrAddID,
		dateofbirth: dateofbirth,
        id: id
    });
    $.ajax({
        type: 'POST',
        url: '/r/ajax/SaveBillingOption.jsp',
        data: data,
        success: function(data){
            var obj = parseRtn(data);
            if (obj.success)
                if (typeof successCallback === 'function')
                    successCallback(obj);
        }
    });
}

function deleteAddress(id, successCallback, isBilling, isShipping){
    var data = $.param({
        id: id,
        action: 'delete',
        bill: isBilling,
        ship: isShipping
    });
    $.ajax({
        type: 'POST',
        url: '/r/ajax/SaveAddress.jsp',
        data: data,
        success: function(data){
            var obj = parseRtn(data);
            if (obj.success)
                if (typeof successCallback === 'function')
                    successCallback(obj);
        }
    });
}

function createAddress(name, street, street2, city, state, zip, country, tel, internationalID, successCallback, bill, ship){
    saveAddress(name, street, street2, city, state, zip, country, tel, internationalID,
        function(rtn){
            var newRtn = {};
            newRtn.success = rtn.success;
            if (rtn.success){
                if (rtn.msg0)
                    newRtn.msg = rtn.msg0
                if (rtn.msg1)
                    newRtn.id = rtn.msg1;
            }
            successCallback(newRtn);
        },
        bill, ship, -1);
}

function saveAddress(name, street, street2, city, state, zip, country, tel, internationalID, successCallback, bill, ship, id, securitycode) {
    id = (typeof id === 'undefined') ? -1 : id;
    bill = (typeof bill === 'undefined') ? false : bill;
    var data = $.param({
        name: name,
        street: street,
        street2: street2,
        city: city,
        state: state,
        zip: zip,
        country: country,
        telephone: tel,
		internationalID: internationalID,
        bill: bill,
        ship: ship,
        id: id,
        securitycode: securitycode
    });
    $.ajax({
        type: 'POST',
        url: '/r/ajax/SaveAddress.jsp',
        data: data,
        success: function(data){
            var obj = parseRtn(data);
            if (obj.success)
                if (typeof successCallback === 'function')
                    successCallback(obj);
        }
    });
}

function createBillingOptionAfterSaveAddress(obj) {
	var newUserAddressID = obj.msg1;
// alert("newUserAddressID: " + newUserAddressID);
	var name = $("#name").val();

	var num = $("#card_num").val();

	var expDate = $("#expire_month").val() + "/" + $("#expire_year").val();
	var tel = $("#telephone").val();
	var security_code=$("#security_code").val();

	saveBillingOption(name, null, num, security_code, expDate, tel, newUserAddressID,
			closeAndReload, -1);

}
function saveEmailChange(newEmail, confirmEmail, currentEmail) {
	var data = $.param({
		newEmail : newEmail,
		confirmEmail : confirmEmail,
		currentEmail : currentEmail,
		site : "r"
	});
	$("#save_email_change").val("SAVING");
	$.ajax({
		type : 'POST',
		url : '/r/ajax/SaveEmail.jsp',
		data : data,
		success : function(data) {
			var obj = parseRtn(data);
			if (obj.success) {
				$("#save_email_success_trigger").click();
			} else {
		// alert(obj.msg0);
				$("#save_email_change").val("SAVE CHANGES") ;
				if(obj.code==1){
					$("#new_email").addClass("errortextbox");
					$("#new_email_tag").css("color", "#C9383F");
					$("#invalid_new_email").css("display", "inline");
				}
				else if (obj.code == 3){
					$("#new_email").addClass("errortextbox");
					$("#new_email_tag").css("color", "#C9383F");
					$("#invalid_new_email_2").css("display", "inline");

				}
				else{
					$("#new_email").addClass("errortextbox");
					$("#new_email").val(obj.msg0);
				}

			}
		}
	});

}

function applyPromo(promo, callback){
    var data = $.param({
        promo: promo,
		scope: $('#employee-checkout').data('scope')
    });
    $.ajax({
        type: 'POST',
        url: '/r/ajax/ApplyPromoCode.jsp',
        data: data,
        success: function(data){
            var obj = parseRtn(data);
            if (typeof callback === 'function')
                callback(obj);
            else if (obj.success){
                location.reload();
            }
        }
    });
}

function applyCredit(credit, callback){
    var data = $.param({
        credit: credit
    });
    $.ajax({
        type: 'POST',
        url: '/r/ajax/ApplyStoreGiftCredit.jsp',
        data: data,
        success: function(data){
            var obj = parseRtn(data);
            if (typeof callback === 'function')
                callback(obj);
            else if (obj.success){
                location.reload();
            }
        }
    });
}

function applyGiftCertificate(giftCode, callback, isRemove) {
    var data = $.param({
        code: giftCode,
        action: isRemove ? "remove" : "add"
    });
    $.ajax({
        type: 'POST',
        url: '/r/ajax/ApplyGiftCode.jsp',
        data: data,
        success: function(data){
            var obj = parseRtn(data);
            if (typeof callback === 'function')
                callback(obj);
            else if (obj.success){
                location.reload();
            }
        }
    });
}

function setBillingOption(number, code, exp, name, street, street2, city, state, zipCode, country, telephone, dateofbirth, internationalID,  successCallback, id, type, csrfHash){

    var data = $.param({
        number: number,
        code: code,
        exp: exp,
        name: name,
        street: street,
        street2: street2,
        city: city,
        state: state,
        zip: zipCode,
        country: country,
        telephone: telephone,
		dateofbirth: dateofbirth,
		internationalID: internationalID,
        id: id,
        type: type,
        csrfHash: csrfHash
    });

    return new Promise(function(resolve, reject) {
        $.ajax({
            type: 'POST',
            data: data,
            url: '/r/ajax/SetBillingOption.jsp',
            success: function(rtn){
                var obj = parseRtn(rtn);
                if (obj.success){
                    if (typeof successCallback == 'function')
                        resolve(successCallback(obj));
                } else {
                    reject(obj);
                }
            }
        });
    });
}
function setShippingAddress(
    name,
    street,
    street2,
    city,
    state,
    zipCode,
    country,
    telephone,
    internationalID,
    successCallback,
    id,
    isEmployeeShipping,
    deliveryCode)
{
    var data = $.param({
        name: name,
        street: street,
        street2: street2,
        city: city,
        state: state,
        zip: zipCode,
        country: country,
        telephone: telephone,
        id: (typeof(id) != 'undefined' ? id : -1),
        internationalID: internationalID,
        isEmployeeShipping: isEmployeeShipping,
        deliveryCode: deliveryCode
    });
    $.ajax({
        type: 'POST',
        data: data,
        url: '/r/ajax/SetShippingAddress.jsp',
        success: function(rtn){
            var obj = parseRtn(rtn);
            if (obj.success){
                if (typeof successCallback == 'function')
                    successCallback(obj);
            }
        }
    });
}

function openCouponRestrictions(){
    $.modal({
        url:'/r/dialog/CouponRestrictions.jsp',
        triggerOpen: true
    });
}
function expandCouponRestrictionBrands(){
	$('.brands-in-cart').hide();
	$('.all-brands').slideDown();
}

function checkPaymentType(number, callback){
    var data = $.param({
        number: number
    });
    $.ajax({
        type: 'POST',
        url: '/r/ajax/CheckValidCard.jsp',
        data: data,
        success: function(rtn){
            var obj = parseRtn(rtn);
            if (typeof callback === 'function')
                callback(obj);
        }
    });
}
function getInstalment(countryCode, currencyCode, billingType, callback){
	var data = $.param({
		currencyCode: currencyCode,
		countryCode: countryCode,
		billingType: billingType
	});
	$.ajax({
		type: 'POST',
		url: '/r/ajax/GetInstallments.jsp',
		data: data,
		success: function(obj){
			if (typeof callback === 'function') {
                callback(obj);
            }
		}
	});
}
function getBagSyncNotifications(notificationsHandled, callback){
    var data = $.param({
        notificationsHandled: notificationsHandled
    });
    $.ajax({
        type: 'POST',
        url: '/r/ajax/GetBagSyncNotifications.jsp',
        data: data,
        success: function(rtn){
            var obj = parseRtn(rtn);
            if (typeof callback === 'function')
                callback(obj);
        }
    });
}
function saveCustomerSatisfactionSurvey(callback, orderNum, satisfaction, isFullSurvey, selection, sizeAvail, websiteNav, prices,
    shipRtnPolicy, custService, packaging, suggestions, custSource, other){
    var data = $.param({
        isFullSurvey: isFullSurvey,
        orderNum: orderNum,
        satisfaction: satisfaction,
        selection: selection,
        sizeAvail: sizeAvail,
        websiteNav: websiteNav,
        prices: prices,
        shipRtn: shipRtnPolicy,
        custService: custService,
        pkgPresent: packaging,
        suggest: suggestions,
        custSrc: custSource,
        other: other
    });
    $.ajax({
        type: 'POST',
        url: '/r/ajax/SaveSatisfactionSurvey.jsp',
        data: data,
        success: function(rtn){
            var obj = parseRtn(rtn);
            if (typeof callback === 'function')
                callback(obj);
        }
    });
}

function areCookiesEnabled() {
    var cookieEnabled = navigator.cookieEnabled;
    // When cookieEnabled flag is present and false then cookies are disabled.
    if (cookieEnabled === false) {
        return false;
    }
    // try to set a test cookie if we can't see any cookies and we're using
    // either a browser that doesn't support navigator.cookieEnabled
    // or IE (which always returns true for navigator.cookieEnabled)
    if (!document.cookie && (cookieEnabled === null || /*@cc_on!@*/false))
    {
        setCookie('testcookie', '1');

        if (!getCookie('testcookie')) {
            return false;
        } else {
            removeCookie('testcookie');
        }
    }
    return true;
}

attachEventListener(window, 'popstate', function(e) {
    if(  location.href.indexOf("revolvemeajaxid=")==-1){
        //unloadQuickView("xxx");
        if(window.history && typeof(window.history.pushState)==="function" && areCookiesEnabled()){
            // $.modal.close();
        }
		/*
    }else{
        if(window.history && typeof(window.history.pushState)==="function" && areCookiesEnabled()){
            var id = getCookie('revolvemeajaxid');
            openSpotlight(id,'browserButton');
        }
        */
    }
});

function attachEventListener(element, type, handler) {
    if (element.addEventListener) {
        element.addEventListener(type, handler, false);
    }else if (element.attachEvent) {
        element.attachEvent('on' + type, handler)
    } else {
        element['on' + type] = handler;
    }
}

function refreshCartItemSummary() {
    $.ajax({
        type: 'POST',
        url: '/r/ipadApp/checkout/CartItemSummary.jsp',
        success: function(obj) {
            if (obj.total)
                $('.cart-summary-total').html(obj.total);
            if (obj.storeCredit) {
                $('.cart-summary-store-credit-amount').html(obj.storeCredit);
                $('.cart-summary-store-credit').show();
            } else {
                $('.cart-summary-store-credit-amount').html('$0.00');
                $('.cart-summary-store-credit').hide();
            }
            if (obj.storeCreditSummary) {
                $('.cart-summary-store-credit-balance').html(obj.storeCreditSummary);
                if ('None' == obj.storeCreditSummary) {
                    $('.cart-summary-store-credit-balance').hide();

                    $('#StoreCredit').val('');
                    $('#store_credit_applied').text('');
                    $('#store_credit_balance').text('');
                    $('.js-store-credit-applied').addClass("u-hide");
                } else {
                    $('.cart-summary-store-credit-balance').show();

                    $('#StoreCredit').val(obj.storeCreditAmount);
                    $('#store_credit_applied').text(obj.storeCredit);
                    $('#store_credit_balance').text(obj.storeCreditBalance);
                    $('.js-store-credit-applied').removeClass("u-hide");
                }
            }
            if (obj.giftCredit) {
                $('.cart-summary-gift-credit-amount').html(obj.giftCredit);
                $('.cart-summary-gift-credit').show();
            } else {
                $('.cart-summary-gift-credit-amount').html('$0.00');
                $('.cart-summary-gift-credit').hide();
            }
            if (obj.giftCreditSummary) {
                $('.cart-summary-gift-credit-balance').html(obj.giftCreditSummary);
                if ('None' == obj.giftCreditSummary) {
                    $('.cart-summary-gift-credit-balance').hide();

                    $('#gift_credit_applied').text('');
                    $('#gift_credit_balance').text('');
                    $('.js-gift-code-applied').addClass("u-hide");
                } else {
                    $('.cart-summary-gift-credit-balance').show();

                    $('#gift_credit_applied').text(obj.giftCredit);
                    $('#gift_credit_balance').text(obj.giftCreditBalance);
                    $('.js-gift-code-applied').removeClass("u-hide");
                }
            }
            if (obj.duties){
                $('#dutiesAmount').html(obj.duties);
                $('.dutiesAmountShippingOption').html(obj.duties);
            }
			if (obj.loyaltyRewardCredit){
				$('.cart-summary-loyalty-reward-amount').html(obj.loyaltyRewardCredit);
				if ('None' === obj.loyaltyRewardSummary){
					$('.cart-summary-loyalty-rewards').hide();
				} else {
					$('.cart-summary-loyalty-rewards').show();
				}
			}

            if (obj.couponCode) {
                $('#summary_coupon_code').html(obj.couponCodeSummary);
                $('#summary_coupon_code').show();
                $('#summary_coupon_amount').html(obj.couponDiscount);
                $('#summary_coupon_amount').show();
            } else {
                $('#summary_coupon_code').html('');
                $('#summary_coupon_code').hide();
                $('#summary_coupon_amount').html('');
                $('#summary_coupon_amount').hide();
            }

			var _shippingCost = $("#selectedShippingOptionPriceDisplay");
			if(_shippingCost!==null){
                var cost = "FREE";
                if(obj.string_free) cost = obj.string_free;
                if(obj.shippingCostAmount>0) cost = obj.shippingCostDisplay;
                _shippingCost.text(cost);
			}
        }
    });
}

/**
 *
 * @param url
 * @param module
 * @param action
 * @param params - appends key-value pairs to URL as query parameters for tracking purposes.
 * @returns {string}
 */
function logEngagement(url, module, action, params){
	if (params) {
		url += ((url.indexOf('?') >= 0) ? '&' : '?') + $.param(params);
	}
	$.ajax({
		type: 'POST',
		data: $.param({
			url: url,
			module: module,
			action: action
		}),
		url: '/r/ajax/LogEngagement.jsp'
	});
}

/**
 * Logs engagement to engagement_tracking with details column data
 * @param url - string
 * @param module - string
 * @param action - string
 * @param details - string
 */
function logEngagementWithDetails(url, module, action, details) {
    $.ajax({
        type: 'POST',
        data: $.param({
            url: url,
            module: module,
            action: action,
            details: details
        }),
        url: '/r/ajax/LogEngagement.jsp'
    });
}

function nameInputRestrict(event) {
	  var key = event.keyCode || event.charCode || 0;
	  //return ((key >= 65 && key <= 90) || key == 8 || key == 32 || key == 37 || key == 39 || key == 46 || key == 9);
	  return true;
};

 //address form validation
function addressFormHasErrors(country, isEmployeeShipping) {
    var hasError = false;
    var errorClass = "is-error";
    var ariaIvalid = "aria-invalid";
    var selectErrorClass = "errortextbox";
    // Add required keydown handler once the user has had a chance to input values
    var isFieldVisible = function (field) {
        if(field.length === 0 || !field.is(':visible') || field.css('visibility') === 'hidden'){
            return false;
        }else{
            return true;
        }
    };

    $('[required]:visible').each(function(index){
        if(!isFieldVisible($(this))) {
            return;
        }

        var fieldValue = $(this).val();
        var isSelect = $(this).is('select');
        if (fieldValue == null || ($.trim(fieldValue)).length == 0){
            $(this).closest('.field').addClass(errorClass);
            $(this).attr(ariaIvalid, true);
			if(isSelect){
                $(this).closest('.field').addClass(errorClass);
                $(this).attr(ariaIvalid, true);
			}
            hasError = true;
        }else{
            $(this).closest('.field').removeClass(errorClass);
            $(this).attr(ariaIvalid, false);
            if(isSelect){
                $(this).closest('.field').removeClass(errorClass);
                $(this).attr(ariaIvalid, false);
            }
		}

        if ($(this).attr("name") === "shippingName" || $(this).attr("name") === "billingName"){
            var element = $(this);
            var nameInputText = element.val();
            if(nameInputText) element.val(nameInputText.trim().replace(/\s\s+/g, ' ') );
        }

        if ($(this).attr("name") === "shippingName" &&
            country != 'China' &&
//				country != 'Japan' &&
            country != 'South Korea') {
            var fullName = $(this).val().split(" ");
            if(!fullName[1] || fieldValue == null || ($.trim(fieldValue)).length == 0 ) {
                $(this).closest('.field').addClass(errorClass);
                $(this).attr(ariaIvalid, true);
                hasError = true;
            } else {
                $(this).closest('.field').removeClass(errorClass);
                $(this).attr(ariaIvalid, false);
            }
        }

        if ($(this).attr("name") === "shippingName" || $(this).attr("name") === "billingName"){
            $(this).on("blur", function() {
                var element = $(this);
                var nameInputText = element.val();
                if(nameInputText) element.val(nameInputText.trim().replace(/\s\s+/g, ' ') );

                if (element.attr("name") === "shippingName" &&
                    country != 'China' &&
                    country != 'South Korea') {
                    var fullName2 = element.val().split(" ");
                    if(!fullName2[1]) {
                        element.closest('.field').addClass(errorClass);
                        $(this).attr(ariaIvalid, true);
                    } else {
                        element.closest('.field').removeClass(errorClass);
                        $(this).attr(ariaIvalid, false);
                    }
                }
            });
        }

        if (country == 'China' && ($(this).attr("name") === "shippingName" || $(this).attr("name") === "shippingStreet"  || $(this).attr("name") === "shippingCity" )){
            if (!/[\u4E00-\uFA6D]/.test($(this).val())){
                $(this).closest('.field').addClass(errorClass);
                $(this).attr(ariaIvalid, true);
                hasError = true;
            }
        }

        if (country == 'China' && $(this).attr("name") === "shippingTelephone" ){
            if ($(this).val().length != 11){
                $(this).closest('.field').addClass(errorClass);
                $(this).attr(ariaIvalid, true);
                hasError = true;
            }
        }

        $(this).keyup(function (){
            var element = $(this);
            if (element.val()){
                element.closest('.field').removeClass(errorClass);
                $(this).attr(ariaIvalid, false);
            }

            // if (element.attr("name") === "shippingName" &&
            //     country != 'China' &&
            //     country != 'South Korea') {
            //     var fullName2 = element.val().split(" ");
            //     if(!fullName2[1]) {
            //         element.closest('.field').addClass(errorClass);
            //         $(this).attr(ariaIvalid, true);
            //     } else {
            //         element.closest('.field').removeClass(errorClass);
            //         $(this).attr(ariaIvalid, false);
            //     }
            // }
        });
        if(isSelect){
            $(this).change(function(){
                var element = $(this);
                if (element.val()){
                    element.closest('.field').removeClass(errorClass);
                    $(this).attr(ariaIvalid, false);
                }
			})
		}

    });
    //us zipcode mapping
    var zipcodeVisible = false;
    $('.js-us-zipcode-mapping').each(function(){
      if (isFieldVisible($(this))){
        zipcodeVisible = true;
        return false;  // breaks out of .each()
      }
    });
    if(zipcodeVisible){
      var usZipField = $('.js-us-zipcode-mapping:visible');
      var zipcode = usZipField.val();

      var invalidZip = !/^[0-9]{5}(?:-[0-9]{4})?$/.test(zipcode);
      if (isEmployeeShipping) {
          invalidZip = invalidZip || !/^(9[0-3][0-9]{3})$/.test(zipcode);
      }
      if (invalidZip) {
        usZipField.closest('.field').addClass(errorClass);
        $(this).attr(ariaIvalid, true);
        hasError = true;
      } else {
        usZipField.closest('.field').removeClass(errorClass);
        $(this).attr(ariaIvalid, false);
      }
    }

    if($('.zipcheck').length && $('.zipcheck').is(":visible")){
    	var hasZipError = false;
        $('.zipcheck:visible').each(function() {
            if(!isFieldVisible($(this))){
                return;
            }
            var testzipcode =$(this).val();
            var maxlength = $(this).attr('maxlength');
            if(maxlength != testzipcode.length || /\D/.test(testzipcode) || hasZipError){
                $('.ziperrormsg').show();
                $(this).closest('.field').addClass(errorClass);
                $(this).attr(ariaIvalid, true);
                hasError = true;
                hasZipError = true;
            }else{
                $('.ziperrormsg').hide();
                $(this).closest('.field').removeClass(errorClass);
                $(this).attr(ariaIvalid, false);
            }
        });
    }

    if (typeof zipCheckEU == 'function'){
        hasError = zipCheckEU(country, errorClass, ariaIvalid) || hasError;
    }

    if($('.zipcheck_jp').length && $('.zipcheck_jp').is(":visible")){
        var pattern = /(^\d{3}-\d{4,}$)/;
        $('.zipcheck_jp:visible').each(function() {
            if(!isFieldVisible($(this))){
                return;
            }
            var testzipcode =$(this).val();
            if(!pattern.test(testzipcode)){
                $(this).closest('.field').addClass(errorClass);
                $(this).attr(ariaIvalid, true);
                hasError = true;
            }else{
                $(this).closest('.field').removeClass(errorClass);
                $(this).attr(ariaIvalid, false);
            }
        });
    }


    if($('.zipcheck_bz').length && $('.zipcheck_bz').is(":visible")){
    	var pattern = /(^\d{5}$)|(^\d{5}-\d{3}$)/;
        var hasZipError = false;
        $('.zipcheck_bz:visible').each(function() {
            if(!isFieldVisible($(this))){
                return;
            }
            var testzipcode =$(this).val();
            if(testzipcode == '' || pattern.test(testzipcode)) {
                $(this).closest('.field').removeClass(errorClass);
			}else{
                $(this).closest('.field').addClass(errorClass);
                hasError = true;
			}
            $(this).keyup(function(){
                var testzipcode =$(this).val();
                if(testzipcode == '' || pattern.test(testzipcode)) {
                    $(this).closest('.field').removeClass(errorClass);
                    $(this).attr(ariaIvalid, false);
                }else{
                    $(this).closest('.field').addClass(errorClass);
                    $(this).attr(ariaIvalid, true);
                }
			})
        });
    }

    if ($('.dateofbirth').length && $('.dateofbirth').is(":visible")){
        $('.dateofbirth:visible').each(function() {
            if(!isFieldVisible($(this))){
                return;
            }
            var pattern = /^(0[1-9]|[1-9]|1[0-9]|2[0-9]|3[0-1])\/(1[0-2]|[1-9]|0[1-9])\/((20|19)([0-9]{2}))/;
            var testdob =$(this).val();
            var maxlength = $(this).attr('maxlength');
            if(maxlength < testdob.length || !pattern.test(testdob)){
                $('.dobBrazil').show();
                $(this).closest('.field').addClass(errorClass);
                $(this).attr(ariaIvalid, true);
                hasError = true;
            }else{
                $('.dobBrazil').hide();
                $(this).closest('.field').removeClass(errorClass);
                $(this).attr(ariaIvalid, false);
            }
        });
    }
    if ($('.phone_bz').length && $('.phone_bz').is(":visible")){
        $('.phone_bz:visible').each(function() {
            if(!isFieldVisible($(this))){
                return;
            }
            var pattern = /^\+?[0-9]{8,15}$/;
            var testdob =$(this).val();
            var maxlength = $(this).attr('maxlength');
            if(maxlength < testdob.length || !pattern.test(testdob)){
                $('.phoneBrazil').show();
                $(this).closest('.field').addClass(errorClass);
                $(this).attr(ariaIvalid, true);
                hasError = true;
            }else{
                $('.phoneBrazil').hide();
                $(this).closest('.field').removeClass(errorClass);
                $(this).attr(ariaIvalid, false);
            }
        });
    }
    if ($('.cpf').length && $('.cpf').is(":visible")){
        $('.cpf:visible').each(function() {
            if(!isFieldVisible($(this))){
                return;
            }
            var cpf = $.trim($(this).val());
            invalid = false;
            if (cpf.length != 11 ||
                cpf == "00000000000" ||
                cpf == "11111111111" ||
                cpf == "22222222222" ||
                cpf == "33333333333" ||
                cpf == "44444444444" ||
                cpf == "55555555555" ||
                cpf == "66666666666" ||
                cpf == "77777777777" ||
                cpf == "88888888888" ||
                cpf == "99999999999") {
                invalid = true;
            }
            if (!invalid) {
                add = 0;
                for (i = 0; i < 9; i++)
                    add += parseInt(cpf.charAt(i)) * (10 - i);
                rev = 11 - (add % 11);
                if (rev == 10 || rev == 11)
                    rev = 0;
                if (rev != parseInt(cpf.charAt(9)))
                    invalid = true;
            } else if (!invalid) {
                // Valida 2o digito
                add = 0;
                for (i = 0; i < 10; i++)
                    add += parseInt(cpf.charAt(i)) * (11 - i);
                rev = 11 - (add % 11);
                if (rev == 10 || rev == 11)
                    rev = 0;
                if (rev != parseInt(cpf.charAt(10)))
                    invalid = true;
            }

            if (invalid) {
                $('.cpfBrazil').show();
                $(this).closest('.field').addClass(errorClass);
                $(this).attr(ariaIvalid, true);
                hasError = true;
            } else  {
                $('.cpfBrazil').hide();
                $(this).closest('.field').removeClass(errorClass);
                $(this).attr(ariaIvalid, false);
            }
        });
    }
    if ($('.dniField').length && $('.dniField').is(":visible")){
        $('.dniField:visible').each(function() {
            if(!isFieldVisible($(this))){
                return;
            }
            var testdni = $.trim($(this).val());
            var pattern = /^\s*\d+\s*$/; // all numeric, clean spaces in Java
            var countrycode = $(this).data('countrycode');
            var error = false;
            if (countrycode == 'ar') {
                if((testdni.length > 11 || testdni.length < 7) || !pattern.test(testdni)) {
                    error = true;
                }
            } else if ( countrycode == 'pe') {
                if ((testdni.length > 9 || testdni.length < 8) || !pattern.test(testdni)) {
                    error = true;
                }
            } else if (countrycode == 'cl') {
                pattern = /^\w+$/; // alphanumeric for Chili
                if ((testdni.length > 9 || testdni.length < 8) || !pattern.test(testdni)) {
                    error = true;
                }
            } else if (countrycode == 'co') {
                if ((testdni.length > 10 || testdni.length < 6) || !pattern.test(testdni)) {
                    error = true;
                }
            } else if (countrycode == 'uy') {
                if ((testdni.length > 8 || testdni.length < 6) || !pattern.test(testdni)) {
                    error = true;
                }
            }
            if (error) {
                $('.dni').show();
                $(this).closest('.field').addClass(errorClass);
                $(this).attr(ariaIvalid, true);
                hasError = true;
            } else {
                $('.dni').hide();
                $(this).closest('.field').removeClass(errorClass);
                $(this).attr(ariaIvalid, false);
            }
        });
    }


    if($('#create-checkbox').is(':checked')){
        var pw = $('#create_password').val();
        var verifypw = $('#create_password2').val();
        if(pw != verifypw){
            $('#create_password2').closest('.field').addClass(errorClass);
            $('#create_password2').attr(ariaIvalid, true);
            hasError = true;
		}
	}

    focusOnError();
    return hasError;
}

function focusOnError() {
    var firstError = $(".is-error:visible").eq(0);
    if(firstError.length) {
        // $('html,body').animate({
        //     scrollTop: firstError.offset().top - 40
        // });
        var inputField = firstError.find("input:visible").eq(0);
        if(inputField.length){
            inputField.focus();
        }else{
            firstError.find("select").eq(0).focus();
        }
    }

}

// https://stackoverflow.com/a/5298684
function removeHash (url) {
    var scrollV, scrollH, loc = window.location;
    if ("pushState" in history) {
        var urlWithoutHash = url ? url.replace('#', '') : loc.pathname + loc.search; // Last ditch effort to remove hash
        history.pushState("", document.title, urlWithoutHash);
    } else {
        // Prevent scrolling by storing the page's current scroll offset
        scrollV = document.body.scrollTop;
        scrollH = document.body.scrollLeft;

        loc.hash = "";

        // Restore the scroll offset, should be flicker free
        document.body.scrollTop = scrollV;
        document.body.scrollLeft = scrollH;
    }
}
function loadMiniBag(){
    var $miniBag = $("#tr-mybag");

    if ($miniBag.length) {
        $.ajax({
            type: 'GET',
            url: '/content/shoppingbag/getMinibagMarkup',
            success: function (data) {
                if (data.success) {
                    $miniBag.empty().append(data.markup);
                    var element = $('#screen_reader_mybag_count');
                    element.text(element.text().replace('{0}', data.itemCount));
                }
            },
            failure: function () {
            }
        });
    }
}
function loadFavouriteCount(){
    var $favoriteCount = $("#heart_total_count");

    if ($favoriteCount.length) {
        $.ajax({
            type: 'GET',
            url: '/content/favorites/fav-max-popup-markup',
            success: function (data) {
                $favoriteCount.empty().text(data.total);
                $('#fav-max-popup-div').empty().append(data.favMaxPopupMarkup);
            },
            failure: function () {
            }
        });
    }
}
function loadDesignerMenu(){
    var $heartBrands = $("#heart-brands");

    if ($heartBrands.length) {
        $.ajax({
            type: 'GET',
            url: '/content/nav/personalised-designer-list',
            success: function (data) {
                if (data.success) {
                    $heartBrands.empty().append(data.markup);
                }
            },
            failure: function () {
            }
        });
    }
}
function updateHeader(){
    loadMiniBag();
    loadFavouriteCount();
    loadDesignerMenu();
}
