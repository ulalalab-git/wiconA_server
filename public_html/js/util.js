/**
 * JavaScript Utility
 * Author: 서명환 <mhseo@ulalalab.com>
 */
var util = util || {};

(function() {
    var global = this;
    var objectPrototype = Object.prototype;
    var toString = objectPrototype.toString;
    var enumerables = [/*'hasOwnProperty', 'isPrototypeOf', 'propertyIsEnumerable',*/ 'valueOf', 'toLocaleString', 'toString', 'constructor'];
    var emptyFn = function() {};
    var identityFn = function(o) {
        return o;
    };
    var iterableRe = /\[object\s*(?:Array|Arguments|\w*Collection|\w*List|HTML\s+document\.all\s+class)\]/;
    var MSDateRe = /^\\?\/Date\(([-+])?(\d+)(?:[+-]\d{4})?\)\\?\/$/;

    util.global = global;

    util.now = Date.now || (Date.now = function() {
        return +new Date();
    });

    util.ticks = global.performance && global.performance.now ? function() {
        return performance.now();
    } : util.now;

    emptyFn.$nullFn = identityFn.$nullFn = emptyFn.$emptyFn = identityFn.$identityFn = true;

    emptyFn.$noClearOnDestroy = identityFn.$noClearOnDestroy = true;

    for(var i in {toString: 1}) {
        enumerables = null;
    }

    util.enumerables = enumerables;

    util.apply = function(object, config, defaults) {
        if(object) {
            if(defaults) {
                util.apply(object, defaults);
            }

            if(config && typeof config === 'object') {
                for(var i in config) {
                    object[i] = config[i];
                }

                if(enumerables) {
                    for(var j = enumerables.length; j--;) {
                        var k = enumerables[j];
                        if(config.hasOwnProperty(k)) {
                            object[k] = config[k];
                        }
                    }
                }
            }
        }

        return object;
    };

    util.apply(util, {
        emptyFn: emptyFn,
        identityFn: identityFn,
        validIdRe: /^[a-z_][a-z0-9\-_]*$/i,
        makeIdSelector: function(id) {
            if(!util.validIdRe.test(id)) {
                return id;
            }
            return '#' + id;
        },
        returnTrue: function() {
            return true;
        },
        emptyArray: Object.freeze ? Object.freeze([]) : [],
        applyIf: function(object, config) {
            if(object && config && typeof config === 'object') {
                for(var property in config) {
                    if(object[property] === undefined) {
                        object[property] = config[property];
                    }
                }
            }

            return object;
        },
        destroy: function() {
            for(var ln = arguments.length, i = 0; i < ln; i++) {
                var arg = arguments[i];
                if(arg) {
                    if(util.isArray(arg)) {
                        this.destroy.apply(this, arg);
                    }
                    else if(util.isFunction(arg.destroy) && !arg.destroyed) {
                        arg.destroy();
                    }
                }
            }
            return null;
        },
        destroyMembers: function(object) {
            for(var name, i = 1, a = arguments, len = a.length; i < len; i++) {
                var ref = object[name = a[i]];

                if(ref != null) {
                    object[name] = util.destroy(ref);
                }
            }
        },
        valueFrom: function(value, defaultValue, allowBlank) {
            return util.isEmpty(value, allowBlank) ? defaultValue : value;
        },
        isEmpty: function(value, allowEmptyString) {
            return value == null || (!allowEmptyString ? value === '' : false) || (util.isArray(value) && value.length === 0) || (util.isObject(value) && util.Object.isEmpty(value));
        },
        isArray: 'isArray' in Array ? Array.isArray : function(value) {
            return toString.call(value) === '[object Array]';
        },
        isDate: function(obj) {
            return toString.call(obj) === '[object Date]';
        },
        isMSDate: function(value) {
            if(!util.isString(value)) {
                return false;
            }
            return MSDateRe.test(value);
        },
        isObject: toString.call(null) === '[object Object]' ? function(value) {
            return value != null && toString.call(value) === '[object Object]' && value.ownerDocument === undefined;
        } : function(value) {
            return toString.call(value) === '[object Object]';
        },
        isSimpleObject: function(value) {
            return value instanceof Object && value.constructor === Object;
        },
        isPrimitive: function(value) {
            var type = typeof value;

            return type === 'string' || type === 'number' || type === 'boolean';
        },
        isFunction: typeof document !== 'undefined' && typeof document.getElementsByTagName('body') === 'function' ? function(value) {
            return !!value && toString.call(value) === '[object Function]';
        } : function(value) {
            return !!value && typeof value === 'function';
        },
        isNumber: function(value) {
            return typeof value === 'number' && isFinite(value);
        },
        isNumeric: function(value) {
            return !isNaN(parseFloat(value)) && isFinite(value);
        },
        isString: function(value) {
            return typeof value === 'string';
        },
        isBoolean: function(value) {
            return typeof value === 'boolean';
        },
        isElement: function(value) {
            return value ? value.nodeType === 1 : false;
        },
        isTextNode: function(value) {
            return value ? value.nodeName === '#text' : false;
        },
        isDefined: function(value) {
            return typeof value !== 'undefined';
        },
        isIterable: function(value) {
            if(!value || typeof value.length !== 'number' || typeof value === 'string' || util.isFunction(value)) {
                return false;
            }

            if(!value.propertyIsEnumerable) {
                return !!value.item;
            }

            if(value.hasOwnProperty('length') && !value.propertyIsEnumerable('length')) {
                return true;
            }

            return iterableRe.test(toString.call(value));
        },
        clone: function(item, cloneDom) {
            if(item == null) {
                return item;
            }

            if(cloneDom !== false && item.nodeType && item.cloneNode) {
                return item.cloneNode(true);
            }

            var type = toString.call(item);
            var clone;

            if(type === '[object Date]') {
                return new Date(item.getTime());
            }

            if(type === '[object Array]') {
                var i = item.length;

                clone = [];

                while(i--) {
                    clone[i] = util.clone(item[i], cloneDom);
                }
            }
            else if(type === '[object Object]' && item.constructor === Object) {
                clone = {};

                for(var key in item) {
                    clone[key] = util.clone(item[key], cloneDom);
                }

                if(enumerables) {
                    for(var j = enumerables.length; j--;) {
                        var k = enumerables[j];
                        if(item.hasOwnProperty(k)) {
                            clone[k] = item[k];
                        }
                    }
                }
            }

            return clone || item;
        },
        functionFactory: function() {
            var args = Array.prototype.slice.call(arguments);

            return Function.prototype.constructor.apply(Function.prototype, args);
        },
        getElementById: function(id) {
            return document.getElementById(id);
        },
        coerce: function(from, to) {
            var fromType = util.typeOf(from);
            var toType = util.typeOf(to);
            var isString = typeof from === 'string';

            if(fromType !== toType) {
                switch(toType) {
                    case 'string':
                        return String(from);
                    case 'number':
                        return Number(from);
                    case 'boolean':
                        return isString && (!from || from === 'false' || from === '0') ? false : Boolean(from);
                    case 'null':
                        return isString && (!from || from === 'null') ? null : false;
                    case 'undefined':
                        return isString && (!from || from === 'undefined') ? undefined : false;
                    case 'date':
                        return isString && isNaN(from) ? util.Date.parse(from, util.Date.defaultFormat) : Date(Number(from));
                }
            }
            return from;
        },
        copyTo: function(dest, source, names, usePrototypeKeys) {
            if(typeof names === 'string') {
                names = names.split(util.propertyNameSplitRe);
            }

            for(var i = 0, n = names ? names.length : 0; i < n; i++) {
                var name = names[i];

                if(usePrototypeKeys || source.hasOwnProperty(name)) {
                    dest[name] = source[name];
                }
            }

            return dest;
        },
        copy: function(dest, source, names, usePrototypeKeys) {
            if(typeof names === 'string') {
                names = names.split(util.propertyNameSplitRe);
            }

            for(var i = 0, n = names ? names.length : 0; i < n; i++) {
                var name = names[i];

                if(source.hasOwnProperty(name) || (usePrototypeKeys && name in source)) {
                    dest[name] = source[name];
                }
            }

            return dest;
        },
        propertyNameSplitRe: /[,;\s]+/,
        copyToIf: function(destination, source, names) {
            if(typeof names === 'string') {
                names = names.split(util.propertyNameSplitRe);
            }

            for(var i = 0, n = names ? names.length : 0; i < n; i++) {
                var name = names[i];

                if(destination[name] === undefined) {
                    destination[name] = source[name];
                }
            }

            return destination;
        },
        copyIf: function(destination, source, names) {
            if(typeof names === 'string') {
                names = names.split(util.propertyNameSplitRe);
            }

            for(var i = 0, n = names ? names.length : 0; i < n; i++) {
                var name = names[i];

                if(!(name in destination) && name in source) {
                    destination[name] = source[name];
                }
            }

            return destination;
        },
        iterate: function(object, fn, scope) {
            if(util.isEmpty(object)) {
                return;
            }

            if(scope === undefined) {
                scope = object;
            }

            if(util.isIterable(object)) {
                util.Array.each.call(util.Array, object, fn, scope);
            }
            else {
                util.Object.each.call(util.Object, object, fn, scope);
            }
        },
        urlEncode: function() {
            var args = util.Array.from(arguments);
            var prefix = '';

            if(util.isString(args[1])) {
                prefix = args[1] + '&';
                args[1] = false;
            }

            return prefix + util.Object.toQueryString.apply(util.Object, args);
        },
        urlDecode: function() {
            return util.Object.fromQueryString.apply(util.Object, arguments);
        },
        getScrollbarSize: function(force) {
            var scrollbarSize = util._scrollbarSize;

            if(force || !scrollbarSize) {
                var db = document.body;
                var div = document.createElement('div');
                var h;
                var w;

                div.style.width = div.style.height = '100px';
                div.style.overflow = 'scroll';
                div.style.position = 'absolute';

                db.appendChild(div);

                util._scrollbarSize = scrollbarSize = {
                    width: w = div.offsetWidth - div.clientWidth,
                    height: h = div.offsetHeight - div.clientHeignt
                };

                scrollbarSize.reservedWidth = w ? 'calc(100% - ' + w + 'px)' : '';
                scrollbarSize.reservedHeight = h ? 'calc(100% - ' + h + 'px)' : '';

                db.removeChild(div);
            }

            return scrollbarSize;
        },
        typeOf: function() {
            var nonWhitespaceRe = /\S/;
            var toString = Object.prototype.toString;
            var typeofTypes = {
                number: 1,
                string: 1,
                boolean: 1,
                undefined: 1
            };
            var toStringTypes = {
                '[object Array]': 'array',
                '[object Date]': 'date',
                '[object Boolean]': 'boolean',
                '[object Number]': 'number',
                '[object RegExp]': 'regexp'
            };

            return function(value) {
                if(value === null) {
                    return 'null';
                }

                var type = typeof value;
                var typeToString;

                if(typeofTypes[type]) {
                    return type;
                }

                var ret = toStringTypes[typeToString = toString.call(value)];
                if(ret) {
                    return ret;
                }

                if(type === 'function') {
                    return 'function';
                }

                if(type === 'object') {
                    if(value.nodeType !== undefined) {
                        if(value.nodeType === 3) {
                            return nonWhitespaceRe.test(value.nodeValue) ? 'textnode' : 'whitespace';
                        }
                        return 'element';
                    }

                    return 'object';
                }

                return typeToString;
            };
        }(),
        weightSortFn: function(lhs, rhs) {
            return (lhs.weight || 0) - (rhs.weight || 0);
        },
        concat: function(a, b) {
            var noB = b == null;
            var E = util.emptyArray;

            return a == null ? (noB ? a : E.concat(b)) : (noB ? E.concat(a) : E.concat(a, b));
        },
        sha256: function(string) {
            var chrsz = 8;
            var hexcase = 0;

            function safeAdd(x, y) {
                var lsw = (x & 0xffff) + (y & 0xffff);
                var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
                return (msw << 16) | (lsw & 0xffff);
            }

            function s(x, n) {
                return (x >>> n) | (x << (32 - n));
            }
            function r(x, n) {
                return x >>> n;
            }
            function ch(x, y, z) {
                return (x & y) ^ ((~x) & z);
            }
            function maj(x, y, z) {
                return (x & y) ^ (x & z) ^ (y & z);
            }
            function sigma0256(x) {
                return s(x, 2) ^ s(x, 13) ^ s(x, 22);
            }
            function sigma1256(x) {
                return s(x, 6) ^ s(x, 11) ^ s(x, 25);
            }
            function gamma0256(x) {
                return s(x, 7) ^ s(x, 18) ^ r(x, 3);
            }
            function gamma1256(x) {
                return s(x, 17) ^ s(x, 19) ^ r(x, 10);
            }

            function coreSha256(m, l) {
                var k = [0x428a2f98, 0x71374491, 0xb5c0fbcf, 0xe9b5dba5, 0x3956c25b, 0x59f111f1, 0x923f82a4, 0xab1c5ed5, 0xd807aa98, 0x12835b01, 0x243185be, 0x550c7dc3, 0x72be5d74, 0x80deb1fe, 0x9bdc06a7, 0xc19bf174, 0xe49b69c1, 0xefbe4786, 0xfc19dc6, 0x240ca1cc, 0x2de92c6f, 0x4a7484aa, 0x5cb0a9dc, 0x76f988da, 0x983e5152, 0xa831c66d, 0xb00327c8, 0xbf597fc7, 0xc6e00bf3, 0xd5a79147, 0x6ca6351, 0x14292967, 0x27b70a85, 0x2e1b2138, 0x4d2c6dfc, 0x53380d13, 0x650a7354, 0x766a0abb, 0x81c2c92e, 0x92722c85, 0xa2bfe8a1, 0xa81a664b, 0xc24b8b70, 0xc76c51a3, 0xd192e819, 0xd6990624, 0xf40e3585, 0x106aa070, 0x19a4c116, 0x1e376c08, 0x2748774c, 0x34b0bcb5, 0x391c0cb3, 0x4ed8aa4a, 0x5b9cca4f, 0x682e6ff3, 0x748f82ee, 0x78a5636f, 0x84c87814, 0x8cc70208, 0x90befffa, 0xa4506ceb, 0xbef9a3f7, 0xc67178f2];

                var hash = [0x6a09e667, 0xbb67ae85, 0x3c6ef372, 0xa54ff53a, 0x510e527f, 0x9b05688c, 0x1f83d9ab, 0x5be0cd19];

                var w = new Array(64);

                m[l >> 5] |= 0x80 << (24 - l % 32);
                m[((l + 64 >> 9) << 4) + 15] = l;

                for(var i = 0, ln = m.length; i < ln; i += 16) {
                    var a = hash[0];
                    var b = hash[1];
                    var c = hash[2];
                    var d = hash[3];
                    var e = hash[4];
                    var f = hash[5];
                    var g = hash[6];
                    var h = hash[7];

                    for(var j = 0; j < 64; j++) {
                        if(j < 16) {
                            w[j] = m[j + i];
                        }
                        else {
                            w[j] = safeAdd(safeAdd(safeAdd(gamma1256(w[j - 2]), w[j - 7]), gamma0256(w[j - 15])), w[j - 16]);
                        }

                        var t1 = safeAdd(safeAdd(safeAdd(safeAdd(h, sigma1256(e)), ch(e, f, g)), k[j]), w[j]);
                        var t2 = safeAdd(sigma0256(a), maj(a, b, c));

                        h = g;
                        g = f;
                        f = e;
                        e = safeAdd(d, t1);
                        d = c;
                        c = b;
                        b = a;
                        a = safeAdd(t1, t2);
                    }

                    hash[0] = safeAdd(a, hash[0]);
                    hash[1] = safeAdd(b, hash[1]);
                    hash[2] = safeAdd(c, hash[2]);
                    hash[3] = safeAdd(d, hash[3]);
                    hash[4] = safeAdd(e, hash[4]);
                    hash[5] = safeAdd(f, hash[5]);
                    hash[6] = safeAdd(g, hash[6]);
                    hash[7] = safeAdd(h, hash[7]);
                }
                return hash;
            }

            function str2binb(str) {
                var bin = [];
                var mask = (1 << chrsz) - 1;
                for(var i = 0, ln = str.length; i < ln * chrsz; i += chrsz) {
                    bin[i >> 5] |= (str.charCodeAt(i / chrsz) & mask) << (24 - i % 32);
                }
                return bin;
            }

            function binb2hex(binarray) {
                var hexTab = hexcase ? '0123456789ABCDEF' : '0123456789abcdef';
                var str = '';
                for(var i = 0, ln = binarray.length; i < ln * 4; i++) {
                    str += hexTab.charAt((binarray[i >> 2] >> ((3 - i % 4) * 8 + 4)) & 0xf) + hexTab.charAt((binarray[i >> 2] >> ((3 - i % 4) * 8)) & 0xf);
                }
                return str;
            }

            string = util.Base64._utf8_encode(string);
            return binb2hex(coreSha256(str2binb(string), string.length * chrsz));
        }
    });

    util.returnTrue.$nullFn = true;

    var TemplateClass = function() {};
    var queryRe = /^\?/;
    var keyRe = /(\[):?([^\]]*)\]/g;
    var nameRe = /^([^\[]+)/;
    var plusRe = /\+/g;
    var utilObject = util.Object = {
        chain: Object.create || function(object) {
            TemplateClass.prototype = object;
            var result = new TemplateClass();
            TemplateClass.prototype = null;
            return result;
        },
        clear: function(object) {
            for(var key in object) {
                delete object[key];
            }

            return object;
        },
        freeze: Object.freeze ? function(obj, deep) {
            if(obj && typeof obj === 'object' && !Object.isFrozen(obj)) {
                Object.freeze(obj);

                if(deep) {
                    for(var name in obj) {
                        utilObject.freeze(obj[name], deep);
                    }
                }
            }
            return obj;
        } : util.identityFn,
        toQueryObjects: function(name, value, recursive) {
            var self = utilObject.toQueryObjects;
            var objects = [];
            var i;
            var ln;

            if(util.isArray(value)) {
                for(i = 0, ln = value.length; i < ln; i++) {
                    if(recursive) {
                        objects = objects.concat(self(name + '[' + i + ']', value[i], true));
                    }
                    else {
                        objects.push({
                            name: name,
                            value: value[i]
                        });
                    }
                }
            }
            else if(util.isObject(value)) {
                for(i in value) {
                    if(value.hasOwnProperty(i)) {
                        if(recursive) {
                            objects = objects.concat(self(name + '[' + i + ']', value[i], true));
                        }
                        else {
                            objects.push({
                                name: name,
                                value: value[i]
                            });
                        }
                    }
                }
            }
            else {
                objects.push({
                    name: name,
                    value: value
                });
            }

            return objects;
        },
        toQueryString: function(object, recursive) {
            var paramObjects = [];
            var params = [];

            for(var i in object) {
                if(object.hasOwnProperty(i)) {
                    paramObjects = paramObjects.concat(utilObject.toQueryObjects(i, object[i], recursive));
                }
            }

            for(var j = 0, ln = paramObjects.length; j < ln; j++) {
                var paramObject = paramObjects[j];
                var value = paramObject.value;

                if(util.isEmpty(value)) {
                    value = '';
                }
                else if(util.isDate(value)) {
                    value = util.Date.toString(value);
                }

                params.push(encodeURIComponent(paramObject.name) + '=' + encodeURIComponent(String(value)));
            }

            return params.join('&');
        },
        fromQueryString: function(queryString, recursive) {
            var parts = queryString.replace(queryRe, '').split('&');
            var object = {};
            var j;
            var subLn;
            var key;

            for(var i = 0, ln = parts.length; i < ln; i++) {
                var part = parts[i];

                if(part.length > 0) {
                    var components = part.split('=');
                    var name = components[0];
                    name = name.replace(plusRe, '%20');
                    name = decodeURIComponent(name);

                    var value = components[1];
                    if(value !== undefined) {
                        value = value.replace(plusRe, '%20');
                        value = decodeURIComponent(value);
                    }
                    else {
                        value = '';
                    }

                    if(!recursive) {
                        if(object.hasOwnProperty(name)) {
                            if(!util.isArray(object[name])) {
                                object[name] = [object[name]];
                            }

                            object[name].push(value);
                        }
                        else {
                            object[name] = value;
                        }
                    }
                    else {
                        var matchedKeys = name.match(keyRe);
                        var matchedName = name.match(nameRe);

                        name = matchedName[0];
                        var keys = [];

                        if(matchedKeys === null) {
                            object[name] = value;
                            continue;
                        }

                        for(j = 0, subLn = matchedKeys.length; j < subLn; j++) {
                            key = matchedKeys[j];
                            key = key.length === 2 ? '' : key.substring(1, key.length - 1);
                            keys.push(key);
                        }

                        keys.unshift(name);

                        var temp = object;

                        for(j = 0, subLn = keys.length; j < subLn; j++) {
                            key = keys[j];

                            if(j === subLn - 1) {
                                if(util.isArray(temp) && key === '') {
                                    temp.push(value);
                                }
                                else {
                                    temp[key] = value;
                                }
                            }
                            else {
                                if(temp[key] === undefined || typeof temp[key] === 'string') {
                                    var nextKey = keys[j + 1];

                                    temp[key] = util.isNumeric(nextKey) || nextKey === '' ? [] : {};
                                }

                                temp = temp[key];
                            }
                        }
                    }
                }
            }

            return object;
        },
        each: function(object, fn, scope) {
            var enumerables = util.enumerables;
            var property;

            if(object) {
                scope = scope || object;

                for(property in object) {
                    if(object.hasOwnProperty(property)) {
                        if(fn.call(scope, property, object[property], object) === false) {
                            return;
                        }
                    }
                }

                if(enumerables) {
                    for(var i = enumerables.length; i--;) {
                        if(object.hasOwnProperty(property = enumerables[i])) {
                            if(fn.call(scope, property, object[property], object) === false) {
                                return;
                            }
                        }
                    }
                }
            }
        },
        eachValue: function(object, fn, scope) {
            var enumerables = util.enumerables;
            var property;

            scope = scope || object;

            for(property in object) {
                if(object.hasOwnProperty(property)) {
                    if(fn.call(scope, object[property]) === false) {
                        return;
                    }
                }
            }

            if(enumerables) {
                for(var i = enumerables.length; i--;) {
                    if(object.hasOwnProperty(property = enumerables[i])) {
                        if(fn.call(scope, object[property]) === false) {
                            return;
                        }
                    }
                }
            }
        },
        merge: function(destination) {
            var args = arguments;
            var mergeFn = utilObject.merge;
            var cloneFn = util.clone;

            for(var i = 1, ln = args.length; i < ln; i++) {
                var object = args[i];

                for(var key in object) {
                    var value = object[key];
                    if(value && value.constructor === Object) {
                        var sourceKey = destination[key];
                        if(sourceKey && sourceKey.constructor === Object) {
                            mergeFn(sourceKey, value);
                        }
                        else {
                            destination[key] = cloneFn(value);
                        }
                    }
                    else {
                        destination[key] = value;
                    }
                }
            }

            return destination;
        },
        mergeIf: function(destination) {
            var cloneFn = util.clone;

            for(var i = 1, ln = arguments.length; i < ln; i++) {
                var object = arguments[i];

                for(var key in object) {
                    if(!(key in destination)) {
                        var value = object[key];

                        if(value && value.constructor === Object) {
                            destination[key] = cloneFn(value);
                        }
                        else {
                            destination[key] = value;
                        }
                    }
                }
            }

            return destination;
        },
        getAllKeys: function(object) {
            var keys = [];

            for(var property in object) {
                keys.push(property);
            }

            return keys;
        },
        getKey: function(object, value) {
            for(var property in object) {
                if(object.hasOwnProperty(property) && object[property] === value) {
                    return property;
                }
            }

            return null;
        },
        getValues: function(object) {
            var values = [];

            for(var property in object) {
                if(object.hasOwnProperty(property)) {
                    values.push(object[property]);
                }
            }

            return values;
        },
        getKeys: typeof Object.keys == 'function' ? function(object) {
            if(!object) {
                return [];
            }
            return Object.keys(object);
        } : function(object) {
            var keys = [];

            for(var property in object) {
                if(object.hasOwnProperty(property)) {
                    keys.push(property);
                }
            }

            return keys;
        },
        getSize: function(object) {
            var size = 0;

            for(var property in object) {
                if(object.hasOwnProperty(property)) {
                    size++;
                }
            }

            return size;
        },
        isEmpty: function(object) {
            for(var key in object) {
                if(object.hasOwnProperty(key)) {
                    return false;
                }
            }
            return true;
        },
        equals: function() {
            var check = function(o1, o2) {
                for(var key in o1) {
                    if(o1.hasOwnProperty(key)) {
                        if(o1[key] !== o2[key]) {
                            return false;
                        }
                    }
                }
                return true;
            };

            return function(object1, object2) {
                if(object1 === object2) {
                    return true;
                }
                if(object1 && object2) {
                    return check(object1, object2) && check(object2, object1);
                }
                if(!object1 && !object2) {
                    return object1 === object2;
                }
                return false;
            };
        }(),
        fork: function(obj) {
            var ret;

            if(obj && obj.constructor === Object) {
                ret = utilObject.chain(obj);

                for(var key in obj) {
                    var value = obj[key];

                    if(value) {
                        if(value.constructor === Object) {
                            ret[key] = utilObject.fork(value);
                        }
                        else if(value instanceof Array) {
                            ret[key] = util.Array.clone(value);
                        }
                    }
                }
            }
            else {
                ret = obj;
            }

            return ret;
        },
        defineProperty: 'defineProperty' in Object ? Object.defineProperty : function(object, name, descriptor) {
            if(!Object.prototype.__defineGetter__) {
                return;
            }
            if(descriptor.get) {
                object.__defineGetter__(name, descriptor.get);
            }

            if(descriptor.set) {
                object.__defineSetter__(name, descriptor.set);
            }
        },
        classify: function(object) {
            var prototype = object;
            var objectProperties = [];
            var propertyClassesMap = {};
            var objectClass = function() {
                for(var i = 0, ln = objectProperties.length; i < ln; i++) {
                    var property = objectProperties[i];
                    this[property] = new propertyClassesMap[property]();
                }
            };

            for(var key in object) {
                if(object.hasOwnProperty(key)) {
                    var value = object[key];

                    if(value && value.constructor === Object) {
                        objectProperties.push(key);
                        propertyClassesMap[key] = utilObject.classify(value);
                    }
                }
            }

            objectClass.prototype = prototype;

            return objectClass;
        }
    };

    util.merge = util.Object.merge;
    util.mergeIf = util.Object.mergeIf;
}());

if(!util.form) {
    util.form = {};
}
if(!util.form.field) {
    util.form.field = {};
}

util.form.field.VTypes = util.form.VTypes = function() {
    var alpha = /^[a-zA-Z_]+$/;
    var alphanum = /^[a-zA-Z0-9_]+$/;
    var email = /^(")?(?:[^\."\s])(?:(?:[\.])?(?:[\w\-!#$%&'*+/=?^_`{|}~]))*\1@(\w[\-\w]*\.){1,5}([A-Za-z]){2,20}$/;
    var url = /(((^https?)|(^ftp)):\/\/((([\-\w]+\.)+\w{2,3}(\/[%\-\w]+(\.\w{2,})?)*(([\w\-\.\?\\\/+@&#;`~=%!]*)(\.\w{2,})?)*)|(localhost|LOCALHOST))\/?)/i;
    var ip = /^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/;

    return {
        email: function(value) {
            return email.test(value);
        },
        url: function(value) {
            return url.test(value);
        },
        alpha: function(value) {
            return alpha.test(value);
        },
        alphanum: function(value) {
            return alphanum.test(value);
        },
        ip: function(value) {
            return ip.test(value);
        }
    };
}();

util.Array = function() {
    var arrayPrototype = Array.prototype;
    var slice = arrayPrototype.slice;
    var supportsSplice = function() {
        var array = [];
        var j = 20;

        if(!array.splice) {
            return false;
        }

        while(j--) {
            array.push('A');
        }

        array.splice(15, 0, 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F');

        var lengthBefore = array.length;
        array.splice(13, 0, 'XXX');

        if(lengthBefore + 1 !== array.length) {
            return false;
        }

        return true;
    }();
    var supportsIndexOf = 'indexOf' in arrayPrototype;
    var supportsSliceOnNodeList = true;

    function stableSort(array, userComparator) {
        var len = array.length;
        var indices = new Array(len);
        var i;

        for(i = 0; i < len; i++) {
            indices[i] = i;
        }

        indices.sort(function(index1, index2) {
            return userComparator(array[index1], array[index2]) || index1 - index2;
        });

        for(i = 0; i < len; i++) {
            indices[i] = array[indices[i]];
        }

        for(i = 0; i < len; i++) {
            array[i] = indices[i];
        }

        return array;
    }

    try {
        if(typeof document !== 'undefined') {
            slice.call(document.getElementsByTagName('body'));
        }
    }
    catch(e) {
        supportsSliceOnNodeList = false;
    }

    var fixArrayIndex = function(array, index) {
        return index < 0 ? Math.max(0, array.length + index) : Math.min(array.length, index);
    };

    var replaceSim = function(array, index, removeCount, insert) {
        var add = insert ? insert.length : 0;
        var length = array.length;
        var pos = fixArrayIndex(array, index);

        if(pos === length && add) {
            array.push.apply(array, insert);
        }
        else {
            var remove = Math.min(removeCount, length - pos);
            var tailOldPos = pos + remove;
            var tailNewPos = tailOldPos + add - remove;
            var tailCount = length - tailOldPos;
            var lengthAfterRemove = length - remove;
            var i;

            if(tailNewPos < tailOldPos) {
                for(i = 0; i < tailCount; ++i) {
                    array[tailNewPos + i] = array[tailOldPos + i];
                }
            }
            else if(tailNewPos > tailOldPos) {
                for(i = tailCount; i--;) {
                    array[tailNewPos + i] = array[tailOldPos + i];
                }
            }

            if(add && pos === lengthAfterRemove) {
                array.length = lengthAfterRemove;
                array.push.apply(array, insert);
            }
            else {
                array.length = lengthAfterRemove + add;
                for(i = 0; i < add; ++i) {
                    array[pos + i] = insert[i];
                }
            }
        }

        return array;
    };

    var replaceNative = function(array, index, removeCount, insert) {
        if(insert && insert.length) {
            if(index === 0 && !removeCount) {
                array.unshift.apply(array, insert);
            }
            else if(index < array.length) {
                array.splice.apply(array, [index, removeCount].concat(insert));
            }
            else {
                array.push.apply(array, insert);
            }
        }
        else {
            array.splice(index, removeCount);
        }
        return array;
    };

    var eraseSim = function(array, index, removeCount) {
        return replaceSim(array, index, removeCount);
    };

    var eraseNative = function(array, index, removeCount) {
        array.splice(index, removeCount);
        return array;
    };

    var spliceSim = function(array, index, removeCount) {
        var len = arguments.length;
        var pos = fixArrayIndex(array, index);

        if(len < 3) {
            removeCount = array.length - pos;
        }

        var removed = array.slice(index, fixArrayIndex(array, pos + removeCount));

        if(len < 4) {
            replaceSim(array, pos, removeCount);
        }
        else {
            replaceSim(array, pos, removeCount, slice.call(arguments, 3));
        }

        return removed;
    };

    var spliceNative = function(array) {
        return array.splice.apply(array, slice.call(arguments, 1));
    };

    var erase = supportsSplice ? eraseNative : eraseSim;
    var replace = supportsSplice ? replaceNative : replaceSim;
    var splice = supportsSplice ? spliceNative : spliceSim;

    var utilArray = {
        binarySearch: function(array, item, begin, end, compareFn) {
            var length = array.length;

            if(begin instanceof Function) {
                compareFn = begin;
                begin = 0;
                end = length;
            }
            else if(end instanceof Function) {
                compareFn = end;
                end = length;
            }
            else {
                if(begin === undefined) {
                    begin = 0;
                }
                if(end === undefined) {
                    end = length;
                }
                compareFn = compareFn || utilArray.lexicalCompare;
            }

            --end;

            while(begin <= end) {
                var middle = begin + end >> 1;
                var comparison = compareFn(item, array[middle]);
                if(comparison >= 0) {
                    begin = middle + 1;
                }
                else if(comparison < 0) {
                    end = middle - 1;
                }
            }

            return begin;
        },
        defaultCompare: function(lhs, rhs) {
            return lhs < rhs ? -1 : (lhs > rhs ? 1 : 0);
        },
        lexicalCompare: function(lhs, rhs) {
            lhs = String(lhs);
            rhs = String(rhs);

            return lhs < rhs ? -1 : (lhs > rhs ? 1 : 0);
        },
        each: function(array, fn, scope, reverse) {
            array = utilArray.from(array);

            var i;
            var ln = array.length;

            if(reverse !== true) {
                for(i = 0; i < ln; i++) {
                    if(fn.call(scope || array[i], array[i], i, array) === false) {
                        return i;
                    }
                }
            }
            else {
                for(i = ln - 1; i > -1; i--) {
                    if(fn.call(scope || array[i], array[i], i, array) === false) {
                        return i;
                    }
                }
            }

            return true;
        },
        findInsertionIndex: function(item, items, comparatorFn, index) {
            var len = items.length;

            comparatorFn = comparatorFn || utilArray.lexicalCompare;

            if(index < len) {
                var beforeCheck = index > 0 ? comparatorFn(items[index - 1], item) : 0;
                var afterCheck = index < len - 1 ? comparatorFn(items, items[index]) : 0;
                if(beforeCheck < 1 && afterCheck < 1) {
                    return index;
                }
            }

            return utilArray.binarySearch(items, item, comparatorFn);
        },
        forEach: 'forEach' in arrayPrototype ? function(array, fn, scope) {
            array.forEach(fn, scope);
        } : function(array, fn, scope) {
            for(var i = 0, ln = array.length; i < ln; i++) {
                fn.call(scope, array[i], i, array);
            }
        },
        indexOf: supportsIndexOf ? function(array, item, from) {
            return array ? arrayPrototype.indexOf.call(array, item, from) : -1;
        } : function(array, item, from) {
            var length = array ? array.length : 0;

            for(var i = from < 0 ? Math.max(0, length + from) : from || 0; i < length; i++) {
                if(array[i] === item) {
                    return i;
                }
            }

            return -1;
        },
        contains: supportsIndexOf ? function(array, item) {
            return arrayPrototype.indexOf.call(array, item) !== -1;
        } : function(array, item) {
            for(var i = 0, ln = array.length; i < ln; i++) {
                if(array[i] === item) {
                    return true;
                }
            }

            return false;
        },
        toArray: function(iterable, start, end) {
            if(!iterable || !iterable.length) {
                return [];
            }

            if(typeof iterable === 'string') {
                iterable = iterable.split('');
            }

            if(supportsSliceOnNodeList) {
                return slice.call(iterable, start || 0, end || iterable.length);
            }

            var array = [];

            start = start || 0;
            end = end ? (end < 0 ? iterable.length + end : end) : iterable.length;

            for(var i = start; i < end; i++) {
                array.push(iterable[i]);
            }

            return array;
        },
        pluck: function(array, propertyName) {
            var ret = [];

            for(var i = 0, ln = array.length; i < ln; i++) {
                var item = array[i];

                ret.push(item[propertyName]);
            }

            return ret;
        },
        map: 'map' in arrayPrototype ? function(array, fn, scope) {
            return array.map(fn, scope);
        } : function(array, fn, scope) {
            var len = array.length;
            var results = new Array(len);

            for(var i = 0; i < len; i++) {
                results[i] = fn.call(scope, array[i], i, array);
            }

            return results;
        },
        every: 'every' in arrayPrototype ? function(array, fn, scope) {
            return array.every(fn, scope);
        } : function(array, fn, scope) {
            for(var i = 0, ln = array.length; i < ln; ++i) {
                if(!fn.call(scope, array[i], i, array)) {
                    return false;
                }
            }

            return true;
        },
        some: 'some' in arrayPrototype ? function(array, fn, scope) {
            return array.some(fn, scope);
        } : function(array, fn, scope) {
            for(var i = 0, ln = array.length; i < ln; ++i) {
                if(fn.call(scope, array[i], i, array)) {
                    return true;
                }
            }

            return false;
        },
        equals: function(array1, array2) {
            var len1 = array1.length;
            var len2 = array2.length;

            if(len1 !== len2) {
                return false;
            }

            for(var i = 0; i < len1; ++i) {
                if(array1[i] !== array2[i]) {
                    return false;
                }
            }

            return true;
        },
        clean: function(array) {
            var results = [];

            for(var i = 0, ln = array.length; i < ln; i++) {
                var item = array[i];

                if(!util.isEmpty(item)) {
                    results.push(item);
                }
            }

            return results;
        },
        unique: function(array) {
            var clone = [];

            for(var i = 0, ln = array.length; i < ln; i++) {
                var item = array[i];

                if(utilArray.indexOf(clone, item) === -1) {
                    clone.push(item);
                }
            }

            return clone;
        },
        filter: 'filter' in arrayPrototype ? function(array, fn, scope) {
            return array.filter(fn, scope);
        } : function(array, fn, scope) {
            var results = [];

            for(var i = 0, ln = array.length; i < ln; i++) {
                if(fn.call(scope, array[i], i, array)) {
                    results.push(array[i]);
                }
            }

            return results;
        },
        findBy: function(array, fn, scope) {
            for(var i = 0, len = array.length; i < len; i++) {
                if(fn.call(scope || array, array[i], i)) {
                    return array[i];
                }
            }
            return null;
        },
        from: function(value, newReference) {
            if(value === undefined || value === null) {
                return [];
            }

            if(util.isArray(value)) {
                return newReference ? slice.call(value) : value;
            }

            var type = typeof value;
            if(value && value.length !== undefined && type !== 'string' && (type !== 'function' || !value.apply)) {
                return utilArray.toArray(value);
            }

            return [value];
        },
        remove: function(array, item) {
            var index = utilArray.indexOf(array, item);

            if(index !== -1) {
                erase(array, index, 1);
            }

            return array;
        },
        removeAt: function(array, index, count) {
            var len = array.length;
            if(index >= 0 && index < len) {
                count = count || 1;
                count = Math.min(count, len - index);
                erase(array, index, count);
            }
            return array;
        },
        include: function(array, item) {
            if(!utilArray.contains(array, item)) {
                array.push(item);
            }
        },
        clone: function(array) {
            return slice.call(array);
        },
        merge: function() {
            var args = slice.call(arguments);
            var array = [];

            for(var i = 0, ln = args.length; i < ln; i++) {
                array = array.concat(args[i]);
            }

            return utilArray.unique(array);
        },
        intersect: function() {
            var intersection = [];
            var arrays = slice.call(arguments);
            var minArray;
            var minArrayIndex;
            var i;

            if(!arrays.length) {
                return intersection;
            }

            var arraysLength = arrays.length;
            for(i = minArrayIndex = 0; i < arraysLength; i++) {
                var minArrayCandidate = arrays[i];
                if(!minArray || minArrayCandidate.length < minArray.length) {
                    minArray = minArrayCandidate;
                    minArrayIndex = i;
                }
            }

            minArray = utilArray.unique(minArray);
            erase(arrays, minArrayIndex, 1);

            var minArrayLength = minArray.length;
            arraysLength = arrays.length;
            for(i = 0; i < minArrayLength; i++) {
                var element = minArray[i];
                var elementCount = 0;

                for(var j = 0; j < arraysLength; j++) {
                    var array = arrays[j];
                    for(var k = 0, arrayLength = array.length; k < arrayLength; k++) {
                        var elementCandidate = array[k];
                        if(element === elementCandidate) {
                            elementCount++;
                            break;
                        }
                    }
                }

                if(elementCount === arraysLength) {
                    intersection.push(element);
                }
            }

            return intersection;
        },
        difference: function(arrayA, arrayB) {
            var clone = slice.call(arrayA);
            var ln = clone.length;

            for(var i = 0, lnB = arrayB.length; i < lnB; i++) {
                for(var j = 0; j < ln; j++) {
                    if(clone[j] === arrayB[i]) {
                        erase(clone,j, 1);
                        j--;
                        ln--;
                    }
                }
            }

            return clone;
        },
        reduce: Array.prototype.reduce ? function(array, reduceFn, initialValue) {
            if(arguments.length === 3) {
                return Array.prototype.reduce.call(array, reduceFn, initialValue);
            }
            return Array.prototype.reduce.call(array, reduceFn);
        } : function(array, reduceFn, initialValue) {
            array = Object(array);

            var index = 0;
            var length = array.length >>> 0;
            var reduced = initialalue;

            if(arguments.length < 3) {
                while(true) {
                    if(index in array) {
                        reduced = array[index++];
                        break;
                    }
                    if(++index >= length) {
                        throw new TypeError('Reduce of empty array with no initial value');
                    }
                }
            }

            for(; index < length; ++index) {
                if(index in array) {
                    reduced = reduceFn(reduced, array[index], index, array);
                }
            }

            return reduced;
        },
        slice: [1, 2].slice(1, undefined).length ? function(array, begin, end) {
            return slice.call(array, begin, end);
        } : function(array, begin, end) {
            if(typeof begin === 'undefined') {
                return slice.call(array);
            }
            if(typeof end === 'undefined') {
                return slice.call(array, begin);
            }
            return slice.call(array, begin, end);
        },
        sort: function(array, sortFn) {
            return stableSort(array, sortFn || utilArray.lexicalCompare);
        },
        flatten: function(array) {
            var worker = [];

            function rFlatten(a) {
                for(var i = 0, ln = a.length; i < ln; i++) {
                    var v = a[i];

                    if(util.isArray(v)) {
                        rFlatten(v);
                    }
                    else {
                        worker.push(v);
                    }
                }

                return worker;
            }

            return rFlatten(array);
        },
        min: function(array, comparisonFn) {
            var min = array[0];

            for(var i = 0, ln = array.length; i < ln; i++) {
                var item = array[i];

                if(comparisonFn) {
                    if(comparisonFn(min, item) === 1) {
                        min = item;
                    }
                }
                else {
                    if(item < min) {
                        min = item;
                    }
                }
            }

            return min;
        },
        max: function(array, comparisonFn) {
            var max = array[0];

            for(var i = 0, ln = array.length; i < ln; i++) {
                var item = array[i];

                if(comparisonFn) {
                    if(comparisonFn(max, item) === -1) {
                        max = item;
                    }
                }
                else {
                    if(item > max) {
                        max = item;
                    }
                }
            }

            return max;
        },
        mean: function(array) {
            return array.length > 0 ? utilArray.sum(array) / array.length : undefined;
        },
        sum: function(array) {
            var sum = 0;
            for(var i = 0, ln = array.length; i < ln; i++) {
                var item = array[i];

                sum += item;
            }

            return sum;
        },
        toMap: function(strings, getKey, scope) {
            if(!strings) {
                return null;
            }

            var map = {};
            var i = strings.length;

            if(typeof strings === 'string') {
                map[strings] = 1;
            }
            else if(!getKey) {
                while(i--) {
                    map[strings[i]] = i + 1;
                }
            }
            else if(typeof getKey === 'string') {
                while(i--) {
                    map[strings[i][getKey]] = i + 1;
                }
            }
            else {
                while(i--) {
                    map[getKey.call(scope, strings[i])] = i + 1;
                }
            }

            return map;
        },
        toValueMap: function(array, getKey, scope, arrayify) {
            var map = {};
            var i = array.length;
            var entry;
            var fn;
            var value;

            if(!getKey) {
                while(i--) {
                    value = array[i];
                    map[value] = value;
                }
            }
            else {
                if(!(fn = typeof getKey !== 'string')) {
                    arrayify = scope;
                }

                var alwaysArray = arrayify === 1;
                var autoArray = arrayify === 2;

                while(i--) {
                    value = array[i];
                    var key = fn ? getKey.call(scope, value) : value[getKey];

                    if(alwaysArray) {
                        if(key in map) {
                            map[key].push(value);
                        }
                        else {
                            map[key] = [value];
                        }
                    }
                    else if(autoArray && key in map) {
                        if(entry = map[key] instanceof Array) {
                            entry.push(value);
                        }
                        else {
                            map[key] = [entry, value];
                        }
                    }
                    else {
                        map[key] = value;
                    }
                }
            }

            return map;
        },
        erase: erase,
        insert: function(array, index, items) {
            return replace(array, index, 0, items);
        },
        move: function(array, fromIdx, toIdx) {
            if(toIdx === fromIdx) {
                return;
            }

            var item = array[fromIdx];

            for(var incr = toIdx > fromIdx ? 1 : -1, i = fromIdx; i != toIdx; i += incr) {
                array[i] = array[i + incr];
            }
            array[toIdx] = item;
        },
        replace: replace,
        splice: splice,
        push: function(target) {
            var args = arguments;

            if(target === undefined) {
                target = [];
            }
            else if(!util.isArray(target)) {
                target = [target];
            }

            for(var len = args.length, i = 1; i < len; i++) {
                var newItem = args[i];
                Array.prototype.push[util.isIterable(newItem) ? 'apply' : 'call'](target, newItem);
            }

            return target;
        },
        numericSortFn: function(a, b) {
            return a - b;
        }
    };

    util.each = utilArray.each;
    utilArray.union = utilArray.merge;
    util.min = utilArray.min;
    util.max = utilArray.max;
    util.sum = utilArray.sum;
    util.mean = utilArray.mean;
    util.flatten = utilArray.flatten;
    util.clean = utilArray.clean;
    util.unique = utilArray.unique;
    util.pluck = utilArray.pluck;
    util.toArray = function() {
        return utilArray.toArray.apply(utilArray, arguments);
    };

    return utilArray;
}();

util.Base64 = {
    _str: 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=',
    encode: function(input) {
        var me = this;
        var output = '';
        var i = 0;

        input = me._utf8_encode(input);
        var len = input.length;

        while(i < len) {
            var chr1 = input.charCodeAt(i++);
            var chr2 = input.charCodeAt(i++);
            var chr3 = input.charCodeAt(i++);

            var enc1 = chr1 >> 2;
            var enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            var enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            var enc4 = chr3 & 63;

            if(isNaN(chr2)) {
                enc3 = enc4 = 64;
            }
            else if(isNaN(chr3)) {
                enc4 = 64;
            }

            output += me._str.charAt(enc1) + me._str.charAt(enc2) + me._str.charAt(enc3) + me._str.charAt(enc4);
        }

        return output;
    },
    decode: function(input) {
        var me = this;
        var output = '';
        var i = 0;

        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, '');

        var len = input.length;

        while(i < len) {
            var enc1 = me._str.indexOf(input.charAt(i++));
            var enc2 = me._str.indexOf(input.charAt(i++));
            var enc3 = me._str.indexOf(input.charAt(i++));
            var enc4 = me._str.indexOf(input.charAt(i++));

            var chr1 = (enc1 << 2) | (enc2 >> 4);
            var chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            var chr3 = ((enc3 & 3) << 6) | enc4;

            output += String.fromCharCode(chr1);

            if(enc3 !== 64) {
                output += String.fromCharCode(chr2);
            }
            if(enc4 !== 64) {
                output += String.fromCharCode(chr3);
            }
        }

        output = me._utf8_decode(output);

        return output;
    },
    _utf8_encode: function(string) {
        string = string.replace(/\r\n/g, '\n');
        var utftext = '';

        for(var n = 0, len = string.length; n < len; n++) {
            var c = string.charCodeAt(n);

            if(c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if(c > 127 && c < 2048) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) || 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }
        }

        return utftext;
    },
    _utf8_decode: function(utftext) {
        var string = '';
        var i = 0;
        var c = 0;
        var c3 = 0;
        var c2 = 0;
        var len = utftext.length;

        while(i < len) {
            c = utftext.charCodeAt(i);

            if(c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if(c > 191 && c < 224) {
                c2 = utftext.charCodeAt(i + 1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i + 1);
                c3 = utftext.charCodeAt(i + 2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }
        }

        return string;
    }
};

util.Cookies = {
    set: function(name, value) {
        var argv = arguments;
        var argc = argv.length;
        var expires = argc > 2 ? argv[2] : null;
        var path = argc > 3 ? argv[3] : '/';
        var domain = argc > 4 ? argv[4] : null;
        var secure = argc > 5 ? argv[5] : false;

        document.cookie = name + '=' + escape(value) + (expires === null ? '' : '; expires=' + expires.toUTCString()) + (path === null ? '' : '; path=' + path) + (domain === null ? '' : '; domain=' + domain) + (secure === true ? '; secure' : '');
    },
    get: function(name) {
        var parts = document.cookie.split('; ');

        for(var len = parts.length, i = 0; i < len; ++i) {
            var item = parts[i].split('=');
            if(item[0] === name) {
                var ret = item[1];
                return ret ? unescape(ret) : '';
            }
        }
        return null;
    },
    clear: function(name, path) {
        if(this.get(name)) {
            path = path || '/';
            document.cookie = name + '=; expires=Thu, 01-Jan-1970 00:00:01 GMT; path=' + path;
        }
    }
};

util.String = function() {
    var trimRegex = /^[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u2028\u2029\u202f\u205f\u3000]+|[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u2028\u2029\u202f\u205f\u3000]+$/g;
    var escapeRe = /('|\\)/g;
    var escapeRegexRe = /([-.*+?\^${}()|\[\]\/\\])/g;
    var basicTrimRe = /^\s+|\s+$/g;
    var whitespaceRe = /\s+/;
    var varReplace = /(^[^a-z]*|[^\w])/gi;
    var charToEntity;
    var entityToChar;
    var charToEntityRegex;
    var entityToCharRegex;
    var htmlEncodeReplaceFn = function(match, capture) {
        return charToEntity[capture];
    };
    var htmlDecodeReplaceFn = function(match, capture) {
        return capture in entityToChar ? entityToChar[capture] : String.fromCharCode(parseInt(capture.substr(2), 10));
    };
    var boundsCheck = function(s, other) {
        if(s === null || s === undefined || other === null || other === undefined) {
            return false;
        }

        return other.length <= s.length;
    };
    var fromCharCode = String.fromCharCode;
    var utilString;

    return utilString = {
        fromCodePoint: String.fromCodePoint || function() {
            var result = '';
            var codeUnits = [];
            var index = -1;
            var length = arguments.length;

            while(++index < length) {
                var codePoint = Number(arguments[index]);
                if(codePoint <= 0xffff) {
                    codeUnits.push(codePoint);
                }
                else {
                    codePoint -= 0x10000;
                    codeUnits.push((codePoint >> 10) + 0xd800, codePoint % 0x400 + 0xdc00);
                }
                if(index + 1 === length) {
                    result += fromCharCode(codeUnits);
                    codeUnits.length = 0;
                }
            }
            return result;
        },
        insert: function(s, value, index) {
            if(!s) {
                return value;
            }

            if(!value) {
                return s;
            }

            var len = s.length;

            if(!index && index !== 0) {
                index = len;
            }

            if(index < 0) {
                index *= -1;
                if(index >= len) {
                    index = 0;
                }
                else {
                    index = len - index;
                }
            }

            if(index === 0) {
                s = value + s;
            }
            else if(index >= s.length) {
                s += value;
            }
            else {
                s = s.substr(0, index) + value + s.substr(index);
            }
            return s;
        },
        startsWith: function(s, start, ignoreCase) {
            var result = boundsCheck(s, start);

            if(result) {
                if(ignoreCase) {
                    s = s.toLowerCase();
                    start = start.toLowerCase();
                }
                result = s.lastIndexOf(start, 0) === 0;
            }
            return result;
        },
        endsWith: function(s, end, ignoreCase) {
            var result = boundsCheck(s, end);

            if(result) {
                if(ignoreCase) {
                    s = s.toLowerCase();
                    end = end.toLowerCase();
                }
                result = s.indexOf(end, s.length - end.length) !== -1;
            }
            return result;
        },
        createVarName: function(s) {
            return s.replace(varReplace, '');
        },
        htmlEncode: function(value) {
            return !value ? value : String(value).replace(charToEntityRegex, htmlEncodeReplaceFn);
        },
        htmlDecode: function(value) {
            return !value ? value : String(value).replace(entityToCharRegex, htmlDecodeReplaceFn);
        },
        hasHtmlCharacters: function(s) {
            return charToEntityRegex.test(s);
        },
        addCharacterEntities: function(newEntities) {
            var charKeys = [];
            var entityKeys = [];
            for(var key in newEntities) {
                var echar = newEntities[key];
                entityToChar[key] = echar;
                charToEntity[echar] = key;
                charKeys.push(echar);
                entityKeys.push(key);
            }
            charToEntityRegex = new RegExp('(' + charKeys.join('|') + ')', 'g');
            entityToCharRegex = new RegExp('(' + entityKeys.join('|') + '|&#[0-9]{1,5};)', 'g');
        },
        resetCharacterEntities: function() {
            charToEntity = {};
            entityToChar = {};
            this.addCharacterEntities({
                '&amp;': '&',
                '&gt;': '>',
                '&lt;': '<',
                '&quot;': '"',
                '&#39;': '\''
            });
        },
        urlAppend: function(url, string) {
            if(!util.isEmpty(string)) {
                return url + (url.indexOf('?') === -1 ? '?' : '&') + string;
            }

            return url;
        },
        trim: function(string) {
            if(string) {
                string = string.replace(trimRegex, '');
            }
            return string || '';
        },
        capitalize: function(string) {
            if(string) {
                string = string.charAt(0).toUpperCase() + string.substr(1);
            }
            return string || '';
        },
        uncapitalize: function(string) {
            if(string) {
                string = string.charAt(0).toLowerCase() + string.substr(1);
            }
            return string || '';
        },
        ellipsis: function(value, length, word) {
            if(value && value.length > length) {
                if(word) {
                    var vs = value.substr(0, length - 2);
                    var index = Math.max(vs.lastIndexOf(' '), vs.lastIndexOf('.'), vs.lastIndexOf('!'), vs.lastIndexOf('?'));
                    if(index !== -1 && index >= length - 15) {
                        return vs.substr(0, index) + '...';
                    }
                }
                return value.substr(0, length - 3) + '...';
            }
            return value;
        },
        escapeRegex: function(string) {
            return string.replace(escapeRegexRe, '\\$1');
        },
        createRegex: function(value, startsWith, endsWith, ignoreCase) {
            var ret = value;

            if(value != null && !value.exec) {
                ret = utilString.escapeRegex(String(value));

                if(startsWith !== false) {
                    ret = '^' + ret;
                }
                if(endsWith !== false) {
                    ret += '$';
                }

                ret = new RegExp(ret, ignoreCase !== false ? 'i' : '');
            }

            return ret;
        },
        escape: function(string) {
            return string.replace(escapeRe, '\\$1');
        },
        toggle: function(string, value, other) {
            return string === value ? other : value;
        },
        leftPad: function(string, size, character) {
            var result = String(string);
            character = character || ' ';
            while(result.length < size) {
                result = character + result;
            }
            return result;
        },
        repeat: function(pattern, count, sep) {
            if(count < 1) {
                count = 0;
            }
            for(var buf = [], i = count; i--;) {
                buf.push(pattern);
            }
            return buf.join(sep || '');
        },
        splitWords: function(words) {
            if(words && typeof words == 'string') {
                return words.replace(basicTrimRe, '').split(whitespaceRe);
            }
            return words || [];
        }
    };
}();

util.String.resetCharacterEntities();

util.htmlEncode = util.String.htmlEncode;
util.htmlDecode = util.String.htmlDecode;
util.urlAppend = util.String.urlAppend;

util.Format = function() {
    return {
        thousandSeparator: ',',
        decimalSeparator: '.',
        currencyPrecision: 2,
        currencySign: '$',
        currencySpacer: '',
        percentSign: '%',
        currencyAtEnd: false,
        stripTagsRe: /<\/?[^>]+>/gi,
        stripScriptsRe: /(?:<script.*?>)((\n|\r|.)*?)(?:<\/script>)/ig,
        nl2brRe: /\r?\n/g,
        hashRe: /#+$/,
        formatPattern: /[\d,\.#]+/,
        formatCleanRe: /[^\d\.#]/g,
        I18NFormatCleanRe: null,
        formatFns: {},
        nbsp: function(value, strict) {
            strict = strict !== false;

            if(strict ? value === '' || value == null : !value) {
                value = '\xA0';
            }

            return value;
        },
        undef: function(value) {
            return value !== undefined ? value : '';
        },
        defaultValue: function(value, defaultValue) {
            return value !== undefined && value !== '' ? value : defaultValue;
        },
        substr: 'ab'.substr(-1) != 'b' ? function(value, start, length) {
            var str = String(value);
            return start < 0 ? str.substr(Math.max(str.length + start, 0), length) : str.substr(start, length);
        } : function(value, start, length) {
            return String(value).substr(start, length);
        },
        lowercase: function(value) {
            return String(value).toLowerCase();
        },
        uppercase: function(value) {
            return String(value).toUpperCase();
        },
        usMoney: function(value) {
            return this.currency(value, '$', 2);
        },
        currency: function(value, currencySign, decimals, end, currencySpacer) {
            var negativeSign = '';
            var format = ',0';
            value = value - 0;
            if(value < 0) {
                value = -value;
                negativeSign = '-';
            }
            decimals = util.isDefined(decimals) ? decimals : this.currencyPrecision;
            format += decimals > 0 ? '.' : '';
            for(var i = 0; i < decimals; i++) {
                format += '0';
            }
            value = this.number(value, format);

            if(currencySpacer == null) {
                currencySpacer = this.currencySpacer;
            }

            if((end || this.currencyAtEnd) === true) {
                return util.String.format('{0}{1}{2}{3}', negativeSign, value, currencySpacer, currencySign || this.currencySign);
            }
            return util.String.format('{0}{1}{2}{3}', negativeSign, currencySign || this.currencySign, currencySpacer, value);
        },
        date: function(value, format) {
            if(!value) {
                return '';
            }
            if(!util.isDate(value)) {
                value = new Date(Date.parse(value));
            }
            return util.Date.dateFormat(value, format || util.Date.defaultFormat);
        },
        dateRenderer: function(format) {
            return function(v) {
                return this.date(v, format);
            };
        },
        hex: function(value, digits) {
            var s = parseInt(value || 0, 10).toString(16);
            if(digits) {
                if(digits < 0) {
                    digits = -digits;
                    if(s.length > digits) {
                        s = s.substr(s.length - digits);
                    }
                }
                while(s.length < digits) {
                    s = '0' + s;
                }
            }
            return s;
        },
        or: function(value, orValue) {
            return value || orValue;
        },
        pick: function(value, firstValue, secondValue) {
            if(util.isNumber(value)) {
                var ret = arguments[value + 1];
                if(ret) {
                    return ret;
                }
            }
            return value ? secondValue : firstValue;
        },
        lessThanElse: function(value, threshold, below, above, equal) {
            var v = util.Number.from(value, 0);
            var t = util.Number.from(threshold, 0);
            var missing = !util.isDefined(equal);

            return v < t ? below : (v > t ? above : (missing ? above : equal));
        },
        sign: function(value, negative, positive, zero) {
            if(zero === undefined) {
                zero = positive;
            }
            return this.lessThanElse(value, 0, negative, positive, zero);
        },
        stripTags: function(value) {
            return !value ? value : String(value).replace(this.stripTagsRe, '');
        },
        stripScripts: function(value) {
            return !value ? value : String(value).replace(this.stripScriptsRe, '');
        },
        fileSize: function() {
            var byteLimit = 1024;
            var kbLimit = 1048576;
            var mbLimit = 1073741824;

            return function(size) {
                var out;
                if(size < byteLimit) {
                    if(size === 1) {
                        out = '1 byte';
                    }
                    else {
                        out = size + ' bytes';
                    }
                }
                else if(size < kbLimit) {
                    out = Math.round(size * 10 / byteLimit) / 10 + ' KB';
                }
                else if(size < mbLimit) {
                    out = Math.round(size * 10 / kbLimit) / 10 + ' MB';
                }
                else {
                    out = Math.round(size * 10 / mbLimit) / 10 + ' GB';
                }
                return out;
            };
        }(),
        math: function() {
            var fns = {};

            return function(v, a) {
                if(!fns[a]) {
                    fns[a] = util.functionFactory('v', 'return v ' + a + ';');
                }
                return fns[a](v);
            };
        }(),
        round: function(value, precision) {
            var result = Number(value);
            if(typeof precision === 'number') {
                precision = Math.pow(10, precision);
                result = Math.round(value * precision) / precision;
            }
            else if(precision === undefined) {
                result = Math.round(result);
            }
            return result;
        },
        number: function(v, formatString) {
            if(!formatString) {
                return v;
            }
            if(isNaN(v)) {
                return '';
            }

            var formatFn = this.formatFns[formatString];

            if(!formatFn) {
                var originalFormatString = formatString;
                var comma = this.thousandSeparator;
                var decimalSeparator = this.decimalSeparator;
                var precision = 0;
                var trimPart = '';
                var hasComma;
                var splitFormat;
                var trimTrailingZeroes;

                if(formatString.substr(formatString.length - 2) === '/i') {
                    if(!this.I18NFormatCleanRe || this.lastDecimalSeparator !== decimalSeparator) {
                        this.I18NFormatCleanRe = new RegExp('[^\\d\\' + decimalSeparator + '#]', 'g');
                        this.lastDecimalSeparator = decimalSeparator;
                    }
                    formatString = formatString.substr(0, formatString.length - 2);
                    hasComma = formatString.indexOf(comma) !== -1;
                    splitFormat = formatString.replace(this.I18NFormatCleanRe, '').split(decimalSeparator);
                }
                else {
                    hasComma = formatString.indexOf(',') !== -1;
                    splitFormat = formatString.replace(this.formatCleanRe, '').split('.');
                }
                var extraChars = formatString.replace(this.formatPattern, '');

                if(splitFormat.length === 2) {
                    precision = splitFormat[1].length;

                    trimTrailingZeroes = splitFormat[1].match(this.hashRe);
                    if(trimTrailingZeroes) {
                        var len = trimTrailingZeroes[0].length;
                        trimPart = ', trailingZeroes = new RegExp(util.String.escapeRegex(utilFormat.decimalSeparator) + \'*0{0,' + len + '}$\')';
                    }
                }

                var code = ['var utilFormat = util.Format, utilNumber = util.Number' + (hasComma ? ', thousands = []' : '') + (extraChars ? ', formatString = \'' + formatString + '\', formatPattern = /[\\d,\\.#]+/' : '') + '; return function(v) { if(typeof v !== \'number\' && isNaN(v = utilNumber.from(v, NaN))) { return \'\'; } var neg = v < 0', ', absVal = Math.abs(v)', ', fnum = util.Number.toFixed(absVal, ' + precision + ')', trimPart, ';'];

                if(hasComma) {
                    if(precision) {
                        code[code.length] = 'var parts = fnum.split(\'.\');';
                        code[code.length] = 'fnum = parts[0];';
                    }
                    code[code.length] = 'if(absVal >= 1000) {';
                    code[code.length] = 'var thousandSeparator = utilFormat.thousandSeparator; thousands.length = 0; for(var j = fnum.length, n = fnum.length % 3 || 3, i = 0; i < j; i += n) { if(i !== 0) { n = 3; } thousands[thousands.length] = fnum.substr(i, n); } fnum = thousands.join(thousandSeparator); }';
                    if(precision) {
                        code[code.length] = 'fnum += utilFormat.decimalSeparator + parts[1];';
                    }
                }
                else if(precision) {
                    code[code.length] = 'if(utilFormat.decimalSeparator !== \'.\') { var parts = fnum.split(\'.\'); fnum = parts[0] + utilFormat.decimalSeparator + parts[1]; }';
                }

                code[code.length] = 'if(neg && fnum !== \'' + (precision ? '0.' + util.String.repeat('0', precision) : '0') + '\') { fnum = \'-\' + fnum; }';

                if(trimTrailingZeroes) {
                    code[code.length] = 'fnum = fnum.replace(trailingZeroes, \'\');';
                }

                code[code.length] = 'return ';

                if(extraChars) {
                    code[code.length] = 'formatString.replace(formatPattern, fnum);';
                }
                else {
                    code[code.length] = 'fnum;';
                }
                code[code.length] = '};';

                formatFn = this.formatFns[originalFormatString] = util.functionFactory('util', code.join(''))(util);
            }
            return formatFn(v);
        },
        numberRenderer: function(format) {
            return function(v) {
                return this.number(v, format);
            };
        },
        percent: function(value, formatString) {
            return this.number(value * 100, formatString || '0') + this.percentSign;
        },
        repeat: function(value, text, sep) {
            return util.String.repeat(text, value, sep);
        },
        plural: function(value, singular, plural) {
            return value + ' ' + (value === 1 ? singular : (plural ? plural : singular + 's'));
        },
        nl2br: function(v) {
            return util.isEmpty(v) ? '' : v.replace(this.nl2brRe, '<br>');
        },
        capitalize: util.String.capitalize,
        uncapitalize: util.String.uncapitalize,
        ellipsis: util.String.ellipsis,
        escape: util.String.escape,
        escapeRegex: util.String.escapeRegex,
        htmlDecode: util.String.htmlDecode,
        htmlEncode: util.String.htmlEncode,
        leftPad: util.String.leftPad,
        toggle: util.String.toggle,
        trim: util.String.trim,
        parseBox: function(box) {
            box = box || 0;

            if(typeof box === 'number') {
                return {
                    top: box,
                    right: box,
                    bottom: box,
                    left: box
                };
            }

            var parts = box.split(' ');
            var ln = parts.length;

            if(ln === 1) {
                parts[1] = parts[2] = parts[3] = parts[0];
            }
            else if(ln === 2) {
                parts[2] = parts[0];
                parts[3] = parts[1];
            }
            else if(ln === 3) {
                parts[3] = parts[1];
            }

            return {
                top: parseInt(parts[0], 10) || 0,
                right: parseInt(parts[1], 10) || 0,
                bottom: parseInt(parts[2], 10) || 0,
                left: parseInt(parts[3], 10) || 0
            };
        },
        uri: function(value) {
            return encodeURI(value);
        },
        uriCmp: function(value) {
            return encodeURIComponent(value);
        },
        wordBreakRe: /[\W\s]+/,
        word: function(value, index, sep) {
            var re = sep ? (typeof sep === 'string' ? new RegExp(sep) : sep) : this.wordBreakRe;
            var parts = (value || '').split(re);

            return parts[index || 0] || '';
        }
    };
}();

(function() {
    var formatRe = /\{\d+\}/;
    var generateFormatFn = function(format) {
        if(formatRe.test(format)) {
            util.each(arguments, function(item, index, allItems) {
                format = format.replace(new RegExp('\\{' + index + '\\}', 'g'), allItems[index + 1]);
            });
        }
        return function() {
            return format;
        };
    };
    var formatFns = {};

    util.String.format = util.Format.format = function(format) {
        // var formatFn = formatFns[format] || (formatFns[format] = generateFormatFn.apply(this, arguments));
        var formatFn = formatFns[format] = generateFormatFn.apply(this, arguments);
        return formatFn.apply(this, arguments);
    };

    util.String.formatEncode = function() {
        return util.String.htmlEncode(util.String.format.apply(this, arguments));
    };
}());

util.Date = function() {
    var nativeDate = Date;
    var stripEscapeRe = /(\\.)/g;
    var hourInfoRe = /([gGhHisucUOPZ]|MS)/;
    var dateInfoRe = /([djzmnYycU]|MS)/;
    var slashRe = /\\/gi;
    var numberTokenRe = /\{(\d+)\}/g;
    var MSFormatRe = new RegExp('\\/Date\\(([-+])?(\\d+)(?:[+-]\\d{4})?\\)\\/');
    var pad = util.String.leftPad;

    var monthInfo = {
        F: true,
        m: true,
        M: true,
        n: true
    };

    var yearInfo = {
        o: true,
        Y: true,
        y: true
    };

    var code = ['var me = this;', 'var y;', 'var m;', 'var d;', 'var h;', 'var i;', 'var s;', 'var ms', 'var o;', 'var O;', 'var z;', 'var zz;', 'var u;', 'var v;', 'var W;', 'var def = me.defaults;', 'var from = util.Number.from;', 'var results = String(input).match(me.parseRegexes[{0}]);',
                'if(results) {', '{1}',
                    'if(u != null) {', 'v = new Date(u * 1000);',
                    '} else {', 'var dt = me.clearTime(new Date);', 'y = from(y, from(def.y, dt.getFullYear()));', 'm = from(m, from(def.m - 1, dt.getMonth()));', 'var dayMatched = d !== undefined;', 'd = from(d, from(def.d, dt.getDate()));',
                        'if(!dayMatched) {', 'dt.setDate(1);', 'dt.setMonth(m);', 'dt.setFullYear(y);', 'var daysInMonth = me.getDaysInMonth(dt);', 'if(d > daysInMonth) {', 'd = daysInMonth;', '}', '}',
                        'h = from(h, from(def.h, dt.getHours()));', 'i = from(i, from(def.i, dt.getMinutes()));', 's = from(s, from(def.s, dt.getSeconds()));', 'ms = from(ms, from(def.ms, dt.getMilliseconds()));',
                        'if(z >= 0 && y >= 0) {', 'v = me.add(new Date(y < 100 ? 100 : y, 0, 1, h, i, s, ms), me.YEAR, y < 100 ? y - 100 : 0);', 'v = !strict ? v : (strict === true && (z <= 364 || (me.isLeapYear(v) && z <= 365)) ? me.add(v, me.DAY, z) : null);',
                        '} else if(strict === true && !me.isValid(y, m + 1, d, h, i, s, ms)) {', 'v = null;',
                        '} else {',
                            'if(W) {', 'var year = y || new Date().getFullYear();', 'var jan4 = new Date(year, 0, 4, 0, 0, 0);', 'd = jan4.getDay();', 'var week1monday = new Date(jan4.getTime() - ((d === 0 ? 6 : d - 1) * 86400000));', 'v = util.Date.clearTime(new Date(week1monday.getTime() + ((W - 1) * 604800000 + 43200000)));',
                            '} else {', 'v = me.add(new Date(y < 100 ? 100 : y, m, d, h, i, s, ms), me.YEAR, y < 100 ? y - 100 : 0);', '}',
                        '}',
                    '}',
                '}',
                'if(v) {', 'if(zz != null) {', 'v = me.add(v, me.SECOND, -v.getTimezoneOffset() * 60 - zz);', '} else if(o) {', 'v = me.add(v, me.MINUTE, -v.getTimezoneOffset() + (sn == \'+\' ? -1 : 1) * (hr * 60 + mn));', '}', '}',
                'return v != null ? v : null;'].join('\n');

    if(!Date.prototype.toISOString) {
        Date.prototype.toISOString = function() {
            var me = this;
            return pad(me.getUTCFullYear(), 4, '0') + '-' + pad(me.getUTCMonth() + 1, 2, '0') + '-' + pad(me.getUTCDate(), 2, '0') + 'T' + pad(me.getUTCHours(), 2, '0') + ':' + pad(me.getUTCMinutes(), 2, '0') + ':' + pad(me.getUTCSeconds(), 2, '0') + '.' + pad(me.getUTCMilliseconds(), 3, '0') + 'Z';
        };
    }

    function xf(format) {
        var args = Array.prototype.slice.call(arguments, 1);
        return format.replace(numberTokenRe, function(m, i) {
            return args[i];
        });
    }

    var utilDate = {
        now: nativeDate.now,
        toString: function(date) {
            if(!date) {
                date = new nativeDate();
            }

            return date.getFullYear() + '-' + pad(date.getMonth() + 1, 2, '0') + '-' + pad(date.getDate(), 2, '0') + 'T' + pad(date.getHours(), 2, '0') + ':' + pad(date.getMinutes(), 2, '0') + ':' + pad(date.getSeconds(), 2, '0');
        },
        getElapsed: function(dateA, dateB) {
            return Math.abs(dateA - dateB || utilDate.now());
        },
        useStrict: false,
        formatCodeToRegex: function(character, currentGroup) {
            var p = utilDate.parseCodes[character];

            if(p) {
                p = typeof p === 'function' ? p() : p;
                utilDate.parseCodes[character] = p;
            }

            return p ? util.applyIf({
                c: p.c ? xf(p.c, currentGroup || '{0}') : p.c
            }, p) : {
                g: 0,
                c: null,
                s: util.String.escapeRegex(character)
            };
        },
        parseFunctions: {
            MS: function(input, strict) {
                var r = (input || '').match(MSFormatRe);
                return r ? new nativeDate(((r[1] || '') + r[2]) * 1) : null;
            },
            time: function(input, strict) {
                var num = parseInt(input, 10);
                if(num || num === 0) {
                    return new nativeDate(num);
                }
                return null;
            },
            timestamp: function(input, strict) {
                var num = parseInt(input, 10);
                if(num || num === 0) {
                    return new nativeDate(num * 1000);
                }
                return null;
            }
        },
        parseRegexes: [],
        formatFunctions: {
            MS: function() {
                return '\\/Date(' + this.getTime() + ')\\/';
            },
            time: function() {
                return this.getTime().toString();
            },
            timestamp: function() {
                return utilDate.format(this, 'U');
            }
        },
        y2kYear: 50,
        MILLI: 'ms',
        SECOND: 's',
        MINUTE: 'mi',
        HOUR: 'h',
        DAY: 'd',
        WEEK: 'w',
        MONTH: 'mo',
        YEAR: 'y',
        defaults: {},
        dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        monthNumbers: {
            January: 0,
            Jan: 0,
            February: 1,
            Feb: 1,
            March: 2,
            Mar: 2,
            April: 3,
            Apr: 3,
            May: 4,
            June: 5,
            Jun: 5,
            July: 6,
            Jul: 6,
            August: 7,
            Aug: 7,
            September: 8,
            Sep: 8,
            October: 9,
            Oct: 9,
            November: 10,
            Nov: 10,
            December: 11,
            Dec: 11
        },
        defaultFormat: 'm/d/Y',
        weekendDays: [0, 6],
        getShortMonthName: function(month) {
            return utilDate.monthNames[month].substr(0, 3);
        },
        getShortDayName: function(day) {
            return utilDate.dayNames[day].substr(0, 3);
        },
        getMonthNumber: function(name) {
            return utilDate.monthNumbers[name.substr(0, 1).toUpperCase() + name.substr(1, 2).toLowerCase()];
        },
        formatContainsHourInfo: function(format) {
            return hourInfoRe.test(format.replace(stripEscapeRe, ''));
        },
        formatContainsDateInfo: function(format) {
            return dateInfoRe.test(format.replace(stripEscapeRe, ''));
        },
        isMonthFormat: function(format) {
            return !!monthInfo[format];
        },
        isYearFormat: function(format) {
            return !!yearInfo[format];
        },
        unescapeFormat: function(format) {
            return format.replace(slashRe, '');
        },
        formatCodes: {
            d: 'util.String.leftPad(m.getDate(), 2, \'0\')',
            D: 'util.Date.getShortDayName(m.getDay())',
            j: 'm.getDate()',
            l: 'util.Date.dayNames[m.getDay()]',
            N: '(m.getDay() ? m.getDay() : 7)',
            S: 'util.Date.getSuffix(m)',
            w: 'm.getDay()',
            z: 'util.Date.getDayOfYear(m)',
            W: 'util.String.leftPad(util.Date.getWeekOfYear(m), 2, \'0\')',
            F: 'util.Date.monthNames[m.getMonth()]',
            m: 'util.String.leftPad(m.getMonth() + 1, 2, \'0\')',
            M: 'util.Date.getShortMonthName(m.getMonth())',
            n: '(m.getMonth() + 1)',
            t: 'util.Date.getDaysInMonth(m)',
            L: '(util.Date.isLeapYear(m) ? 1 : 0)',
            o: '(m.getFullYear() + (util.Date.getWeekOfYear(m) == 1 && m.getMonth() > 0 ? +1 : (util.Date.getWeekYear(m) >= 52 && m.getMonth() < 11 ? -1 : 0)))',
            Y: 'util.String.leftPad(m.getFullYear(), 4, \'0\')',
            y: '(\'\' + m.getFullYear()).substr(2, 2)',
            a: '(m.getHours() < 12 ? \'am\' : \'pm\')',
            A: '(m.getHours() < 12 ? \'AM\' : \'PM\')',
            g: '((m.getHours() % 12) ? m.getHours() % 12 : 12)',
            G: 'm.getHours()',
            h: 'util.String.leftPad((m.getHours() % 12) ? m.getHours() % 12 : 12, 2, \'0\')',
            H: 'util.String.leftPad(m.getHours(), 2, \'0\')',
            i: 'util.String.leftPad(m.getMinutes(), 2, \'0\')',
            s: 'util.String.leftPad(m.getSeconds(), 2, \'0\')',
            u: 'util.String.leftPad(m.getMilliseconds(), 3, \'0\')',
            O: 'util.Date.getGMTOffset(m)',
            P: 'util.Date.getGMTOffset(m, true)',
            T: 'util.Date.getTimezone(m)',
            Z: '(m.getTimezoneOffset() * -60)',
            c: function() {
                var c = 'Y-m-dTH:i:sP';
                var code = [];
                for(var i = 0, l = c.length; i < l; ++i) {
                    var e = c.charAt(i);
                    code.push(e === 'T' ? '\'T\'' : utilDate.getFormatCode(e));
                }
                return code.join(' + ');
            },
            C: function() {
                return 'm.toISOString()';
            },
            U: 'Math.round(m.getTime() / 1000)'
        },
        isValid: function(year, month, day, hour, minute, second, millisecond) {
            hour = hour || 0;
            minute = minute || 0;
            second = second || 0;
            millisecond = millisecond || 0;

            var dt = utilDate.add(new nativeDate(year < 100 ? 100 : year, month - 1, day, hour, minute, second, millisecond), utilDate.YEAR, year < 100 ? year - 100 : 0);

            return year === dt.getFullYear() && month === dt.getMonth() + 1 && day === dt.getDate() && hour === dt.getHours() && minute === dt.getMinutes() && second === dt.getSeconds() && millisecond === dt.getMilliseconds();
        },
        parse: function(input, format, strict) {
            var p = utilDate.parseFunctions;
            if(p[format] == null) {
                utilDate.createParser(format);
            }
            return p[format].call(utilDate, input, util.isDefined(strict) ? strict : utilDate.useStrict);
        },
        parseDate: function(input, format, strict) {
            return utilDate.parse(input, format, strict);
        },
        getFormatCode: function(character) {
            var f = utilDate.formatCodes[character];

            if(f) {
                f = typeof f === 'function' ? f() : f;
                utilDate.formatCodes[character] = f;
            }

            return f || ('\'' + util.String.escape(character) + '\'');
        },
        createFormat: function(format) {
            var code = [];
            var special = false;

            for(var i = 0, len = format.length; i < len; ++i) {
                var ch = format.charAt(i);
                if(!special && ch === '\\') {
                    special = true;
                }
                else if(special) {
                    special = false;
                    code.push('\'' + util.String.escape(ch) + '\'');
                }
                else if(ch === '\n') {
                    code.push('\'\\n\'');
                }
                else {
                    code.push(utilDate.getFormatCode(ch));
                }
            }
            utilDate.formatFunctions[format] = util.functionFactory('var m = this; return ' + code.join('+'));
        },
        createParser: function(format) {
            var regexNum = utilDate.parseRegexes.length;
            var currentGroup = 1;
            var calc = [];
            var regex = [];
            var special = false;
            var atEnd = [];

            for(var i = 0, len = format.length; i < len; ++i) {
                var ch = format.charAt(i);
                if(!special && ch === '\\') {
                    special = true;
                }
                else if(special) {
                    special = false;
                    regex.push(util.String.escape(ch));
                }
                else {
                    var obj = utilDate.formatCodeToRegex(ch, currentGroup);
                    currentGroup += obj.g;
                    regex.push(obj.s);
                    if(obj.g && obj.c) {
                        if(obj.calcAtEnd) {
                            atEnd.push(obj.c);
                        }
                        else {
                            calc.push(obj.c);
                        }
                    }
                }
            }

            calc = calc.concat(atEnd);

            utilDate.parseRegexes[regexNum] = new RegExp('^' + regex.join('') + '$', 'i');
            utilDate.parseFunctions[format] = util.functionFactory('input', 'strict', xf(code, regexNum, calc.join('')));
        },
        parseCodes: {
            d: {
                g: 1,
                c: 'd = parseInt(results[{0}], 10);\n',
                s: '(3[0-1]|[1-2][0-9]|0[1-9])'
            },
            j: {
                g: 1,
                c: 'd = parseInt(results[{0}], 10);\n',
                s: '(3[0-1]|[1-2][0-9]|[1-9])'
            },
            D: function() {
                for(var a = [], i = 0; i < 7; a.push(utilDate.getShortDayName(i)), ++i);
                return {
                    g: 0,
                    c: null,
                    s: '(?:' + a.join('|') + ')'
                };
            },
            l: function() {
                return {
                    g: 0,
                    c: null,
                    s: '(?:' + utilDate.dayNames.join('|') + ')'
                };
            },
            N: {
                g: 0,
                c: null,
                s: '[1-7]'
            },
            S: {
                g: 0,
                c: null,
                s: '(?:st|nd|rd|th)'
            },
            w: {
                g: 0,
                c: null,
                s: '[0-6]'
            },
            z: {
                g: 1,
                c: 'z = parseInt(results[{0}], 10);\n',
                s: '(\\d{1,3})'
            },
            W: {
                g: 1,
                c: 'W = parseInt(results[{0}], 10);\n',
                s: '(\\d{2})'
            },
            F: function() {
                return {
                    g: 1,
                    c: 'm = parseInt(me.getMonthNumber(results[{0}]), 10);\n',
                    s: '(' + utilDate.monthNames.join('|') + ')'
                };
            },
            M: function() {
                for(var a = [], i = 0; i < 12; a.push(utilDate.getShortMonthName(i)), ++i);
                return util.applyIf({
                    s: '(' + a.join('|') + ')'
                }, util.Date.formatCodeToRegex('F'));
            },
            m: {
                g: 1,
                c: 'm = parseInt(results[{0}], 10) - 1;\n',
                s: '(1[0-2]|0[1-9])'
            },
            n: {
                g: 1,
                c: 'm = parseInt(results[{0}], 10) - 1;\n',
                s: '(1[0-2]|[1-9])'
            },
            t: {
                g: 0,
                c: null,
                s: '(?:\\d{2})'
            },
            L: {
                g: 0,
                c: null,
                s: '(?:1|0)'
            },
            o: {
                g: 1,
                c: 'y = parseInt(results[{0}], 10);\n',
                s: '(\\d{4})'
            },
            Y: {
                g: 1,
                c: 'y = parseInt(results[{0}], 10);\n',
                s: '(\\d{4})'
            },
            y: {
                g: 1,
                c: 'var ty = parseInt(results[{0}], 10);\ny = ty > me.y2kYear ? 1900 + ty : 2000 + ty;\n',
                s: '(\\d{2})'
            },
            a: {
                g: 1,
                c: 'if(/(am)/i.test(results[{0}])) {\nif(!h || h == 12) { h = 0; }\n} else { if(!h || h < 12) { h = (h || 0) + 12; }}',
                s: '(am|pm|AM|PM)',
                calcAtEnd: true
            },
            A: {
                g: 1,
                c: 'if(/(am)/i.test(results[{0}])) {\nif(!h || h == 12) { h = 0; }\n} else { if(!h || h < 12) { h = (h || 0) + 12; }}',
                s: '(AM|PM|am|pm)',
                calcAtEnd: true
            },
            g: {
                g: 1,
                c: 'h = parseInt(results[{0}], 10);\n',
                s: '(1[0-2]|[0-9])'
            },
            G: {
                g: 1,
                c: 'h = parseInt(results[{0}], 10);\n',
                s: '(2[0-3]|1[0-9]|[0-9])'
            },
            h: {
                g: 1,
                c: 'h = parseInt(results[{0}], 10);\n',
                s: '(1[0-2]|0[1-9])'
            },
            H: {
                g: 1,
                c: 'h = parseInt(results[{0}], 10);\n',
                s: '(2[0-3]|[0-1][0-9])'
            },
            i: {
                g: 1,
                c: 'i = parseInt(results[{0}], 10);\n',
                s: '([0-5][0-9])'
            },
            s: {
                g: 1,
                c: 's = parseInt(results[{0}], 10);\n',
                s: '([0-5][0-9])'
            },
            u: {
                g: 1,
                c: 'ms = results[{0}]; ms = parseInt(ms, 10) / Math.pow(10, ms.length - 3);\n',
                s: '(\\d+)'
            },
            O: {
                g: 1,
                c: ['o = results[{0}];', 'var sn = o.substr(0, 1);', 'var hr = o.substr(1, 2) * 1 + Math.floor(o.substr(3, 2) / 60);', 'var mn = o.substr(3, 2) % 60;', 'o = -12 <= (hr * 60 + mn) / 60 && (hr * 60 + mn) / 60 <= 14 ? sn + util.String.leftPad(hr, 2, \'0\') + util.String.leftPad(mn, 2, \'0\') : null;\n'].join('\n'),
                s: '([+-]\\d{4})'
            },
            P: {
                g: 1,
                c: ['o = results[{0}];', 'var sn = o.substr(0, 1);', 'var hr = o.substr(1, 2) * 1 + Math.floor(o.substr(4, 2) / 60);', 'var mn = o.substr(4, 2) % 60;', 'o = -12 <= (hr * 60 + mn) / 60 && (hr * 60 + mn) / 60 <= 14 ? sn + util.String.leftPad(hr, 2, \'0\') + util.String.leftPad(mn, 2, \'0\') : null;\n'].join('\n'),
                s: '([+-]\\d{2}:\\d{2})'
            },
            T: {
                g: 0,
                c: null,
                s: '[A-Z]{1,5}'
            },
            Z: {
                g: 1,
                c: 'zz = results[{0}] * 1;\nzz = -43200 <= zz && zz <= 50400 ? zz : null;\n',
                s: '([+-]?\\d{1,5})'
            },
            c: function() {
                var calc = [];
                var arr = [util.Date.formatCodeToRegex('Y', 1), util.Date.formatCodeToRegex('m', 2), util.Date.formatCodeToRegex('d', 3), util.Date.formatCodeToRegex('H', 4), util.Date.formatCodeToRegex('i', 5), util.Date.formatCodeToRegex('s', 6), {
                    c: 'ms = results[7] || \'0\'; ms = parseInt(ms, 10) / Math.pow(10, ms.length - 3);\n'
                }, {
                    c: ['if(results[8]) {', 'if(results[8] == \'Z\') {', 'zz = 0;', '} else if(results[8].indexOf(\':\') > -1) {', utilDate.formatCodeToRegex('P', 8).c, '} else {', utilDate.formatCodeToRegex('O', 8).c, '}', '}'].join('\n')
                }];

                for(var i = 0, l = arr.length; i < l; ++i) {
                    calc.push(arr[i].c);
                }

                return {
                    g: 1,
                    c: calc.join(''),
                    s: [arr[0].s, '(?:', '-', arr[1].s, '(?:', '-', arr[2].s, '(?:', '(?:T| )?', arr[3].s, ':', arr[4].s, '(?::', arr[5].s, ')?', '(?:(?:\\.|,)(\\d+))?', '(Z|(?:[-+]\\d{2}(?::)?\\d{2}))?', ')?', ')?', ')?'].join('')
                };
            },
            U: {
                g: 1,
                c: 'u = parseInt(results[{0}], 10);\n',
                s: '(-?\\d+)'
            }
        },
        dateFormat: function(date, format) {
            return utilDate.format(date,format);
        },
        isEqual: function(date1, date2) {
            if(date1 && date2) {
                return date1.getTime() === date2.getTime();
            }
            return !(date1 || date2);
        },
        format: function(date, format) {
            var formatFunctions = utilDate.formatFunctions;

            if(!util.isDate(date)) {
                return '';
            }

            if(formatFunctions[format] == null) {
                utilDate.createFormat(format);
            }

            return formatFunctions[format].call(date) + '';
        },
        getTimezone: function(date) {
            return date.toString().replace(/^.* (?:\((.*)\)|([A-Z]{1,5})(?:[\-+][0-9]{4})?(?: -?\d+)?)$/, '$1$2').replace(/[^A-Z]/g, '');
        },
        getGMTOffset: function(date, colon) {
            var offset = date.getTimezoneOffset();
            return (offset > 0 ? '-' : '+') + util.String.leftPad(Math.floor(Math.abs(offset) / 60), 2, '0') + (colon ? ':' : '') + util.String.leftPad(Math.abs(offset % 60), 2, '0');
        },
        getDayOfYear: function(date) {
            var num = 0;
            var d = utilDate.clone(date);
            var m =date.getMonth();
            var i;

            for(i = 0, d.setDate(1), d.setMonth(0); i < m; d.setMonth(++i)) {
                num += utilDate.getDaysInMonth(d);
            }
            return num + date.getDate() - 1;
        },
        getWeekOfYear: function() {
            var ms1d = 864e5;
            var ms7d = 7 * ms1d;

            return function(date) {
                var DC3 = nativeDate.UTC(date.getFullYear(), date.getMonth(), date.getDate() + 3) / ms1d;
                var AWN = Math.floor(DC3 / 7);
                var Wyr = new nativeDate(AWN * ms7d).getUTCFullYear();

                return AWN - Math.floor(nativeDate.UTC(Wyr, 0, 7) / ms7d) + 1;
            };
        }(),
        isLeapYear: function(date) {
            var year = date.getFullYear();
            return !!(year & 3 === 0 && (year % 100 || (year % 400 === 0 && year)));
        },
        getFirstDayOfMonth: function(date) {
            var day = (date.getDay() - (date.getDate() - 1)) % 7;
            return day < 0 ? day + 7 : day;
        },
        getLastDayOfMonth: function(date) {
            return utilDate.getLastDateOfMonth(date).getDay();
        },
        getFirstDateOfMonth: function(date) {
            return new nativeDate(date.getFullYear(), date.getMonth(), 1);
        },
        getLastDateOfMonth: function(date) {
            return new nativeDate(date.getFullYear(), date.getMonth(), utilDate.getDaysInMonth(date));
        },
        getDaysInMonth: function() {
            var daysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

            return function(date) {
                var m = date.getMonth();

                return m === 1 && utilDate.isLeapYear(date) ? 29 : daysInMonth[m];
            };
        }(),
        getSuffix: function(date) {
            switch(date.getDate()) {
                case 1:
                case 21:
                case 31:
                    return 'st';
                case 2:
                case 22:
                    return 'nd';
                case 3:
                case 23:
                    return 'rd';
                default:
                    return 'th';
            }
        },
        clone: function(date) {
            return new nativeDate(date.getTime());
        },
        isDST: function(date) {
            return new nativeDate(date.getFullYear(), 0, 1).getTimezoneOffset() !== date.getTimezoneOffset();
        },
        clearTime: function(date, clone) {
            if(isNaN(date.getTime())) {
                return date;
            }

            if(clone) {
                return utilDate.clearTime(utilDate.clone(date));
            }

            var d = date.getDate();

            date.setHours(0);
            date.setMinutes(0);
            date.setSeconds(0);
            date.setMilliseconds(0);

            if(date.getDate() !== d) {
                for(var hr = 1, c = utilDate.add(date, utilDate.HOUR, hr); c.getDate() !== d; hr++, c = utilDate.add(date, utilDate.HOUR, hr));

                date.setDate(d);
                date.setHours(c.getHours());
            }

            return date;
        },
        add: function(date, interval, value, preventDstAdjust) {
            var d = utilDate.clone(date);
            var base = 0;
            var day;

            if(!interval || value === 0) {
                return d;
            }

            var decimalValue = value - parseInt(value, 10);
            value = parseInt(value, 10);

            if(value) {
                switch(interval.toLowerCase()) {
                    case utilDate.MILLI:
                        if(preventDstAdjust) {
                            d.setMilliseconds(d.getMilliseconds() + value);
                        }
                        else {
                            d.setTime(d.getTime() + value);
                        }
                        break;
                    case utilDate.SECOND:
                        if(preventDstAdjust) {
                            d.setSeconds(d.getSeconds() + value);
                        }
                        else {
                            d.setTime(d.getTime() + value * 1000);
                        }
                        break;
                    case utilDate.MINUTE:
                        if(preventDstAdjust) {
                            d.setMinutes(d.getMinutes() + value);
                        }
                        else {
                            d.setTime(d.getTime() + value * 60 * 1000);
                        }
                        break;
                    case utilDate.HOUR:
                        if(preventDstAdjust) {
                            d.setHours(d.getHours() + value);
                        }
                        else {
                            d.setTime(d.getTime() + value * 60 * 60 * 1000);
                        }
                        break;
                    case utilDate.DAY:
                        d.setDate(d.getDate() + value);
                        break;
                    case utilDate.MONTH:
                        day = date.getDate();
                        if(day > 28) {
                            day = Math.min(day, utilDate.getLastDateOfMonth(utilDate.add(utilDate.getFirstDateOfMonth(date), utilDate.MONTH, value)).getDate());
                        }
                        d.setDate(day);
                        d.setMonth(date.getMonth() + value);
                        break;
                    case utilDate.YEAR:
                        day = date.getDate();
                        if(day > 28) {
                            day = Math.min(day, utilDate.getLastDateOfMonth(utilDate.add(utilDate.getFirstDateOfMonth(date), utilDate.YEAR, value)).getDate());
                        }
                        d.setDate(day);
                        d.setFullYear(date.getFullYear() + value);
                        break;
                }
            }

            if(decimalValue) {
                switch(interval.toLowerCase()) {
                    case utilDate.MILLI:
                        base = 1;
                        break;
                    case utilDate.SECOND:
                        base = 1000;
                        break;
                    case utilDate.MINUTE:
                        base = 1000 * 60;
                        break;
                    case utilDate.HOUR:
                        base = 1000 * 60 * 60;
                        break;
                    case utilDate.DAY:
                        base = 1000 * 60 * 60 * 24;
                        break;
                    case utilDate.MONTH:
                        day = utilDate.getDaysInMonth(d);
                        base = 1000 * 60 * 60 * 24 * day;
                        break;
                    case utilDate.YEAR:
                        day = utilDate.isLeapYear(d) ? 366 : 365;
                        base = 1000 * 60 * 60 * 24 * day;
                        break;
                }
                if(base) {
                    d.setTime(d.getTime() + base * decimalValue);
                }
            }

            return d;
        },
        subtract: function(date, interval, value, preventDstAdjust) {
            return utilDate.add(date, interval, -value, preventDstAdjust);
        },
        between: function(date, start, end) {
            var t = date.getTime();
            return start.getTime() <= t && t <= end.getTime();
        },
        isWeekend: function(date) {
            return util.Array.indexOf(this.weekendDays, date.getDay()) > -1;
        },
        utcToLocal: function(d) {
            return new Date(d.getUTCFullYear(), d.getUTCMonth(), d.getUTCDate(), d.getUTCHours(), d.getUTCMinutes(), d.getUTCSeconds(), d.getUTCMilliseconds());
        },
        localToUtc: function(d) {
            return utilDate.utc(d.getFullYear(), d.getMonth(), d.getDate(), d.getHours(), d.getMinutes(), d.getSeconds(), d.getMilliseconds());
        },
        utc: function(year, month, day, hour, min, s, ms) {
            return new Date(Date.UTC(year, month, day, hour || 0, min || 0, s || 0, ms || 0));
        },
        compat: function() {
            var statics = ['useStrict', 'formatCodeToRegex', 'parseFunctions', 'parseRegexes', 'formatFunctions', 'y2kYear', 'MILLI', 'SECOND', 'MINUTE', 'HOUR', 'DAY', 'MONTH', 'YEAR', 'defaults', 'dayNames', 'monthNames', 'monthNumbers', 'getShortMonthName', 'getShortDayName', 'getMonthNumber', 'formatCodes', 'isValid', 'parseDate', 'getFormatDate', 'createFormat', 'createParser', 'parseCodes'];
            var proto = ['dateFormat', 'format', 'getTimezone', 'getGMTOffset', 'getDayOfYear', 'getWeekOfYear', 'isLeapYear', 'getFirstDayOfMonth', 'getLastDayOfMonth', 'getDaysInMonth', 'getSuffix', 'clone', 'isDST', 'clearTime', 'add', 'between'];

            for(var sLen = statics.length, s = 0; s < sLen; s++) {
                var stat = statics[s];
                nativeDate[stat] = utilDate[stat];
            }

            for(var p = 0, pLen = proto.length; p < pLen; p++) {
                var prot = proto[p];
                nativeDate.prototype[prot] = function() {
                    var args = Array.prototype.slice.call(arguments);
                    args.unshift(this);
                    return utilDate[prot].apply(utilDate, args);
                };
            }
        },
        diff: function(min, max, unit) {
            var est;
            var diff = +max - min;
            switch(unit) {
                case utilDate.MILLI:
                    return diff;
                case utilDate.SECOND:
                    return Math.floor(diff / 1000);
                case utilDate.MINUTE:
                    return Math.floor(diff / 60000);
                case utilDate.HOUR:
                    return Math.floor(diff / 3600000);
                case utilDate.DAY:
                    return Math.floor(diff / 86400000);
                case utilDate.WEEK:
                    return Math.floor(diff / 604800000);
                case utilDate.MONTH:
                    est = (max.getFullYear() * 12 + max.getMonth()) - (min.getFullYear() * 12 + min.getMonth());
                    if(utilDate.add(min, unit, est) > max) {
                        return est - 1;
                    }
                    return est;
                case utilDate.YEAR:
                    est = max.getFullYear() - min.getFullYear();
                    if(utilDate.add(min, unit, est) > max) {
                        return est - 1;
                    }
                    return est;
            }
        },
        align: function(date, unit, step) {
            var num = new nativeDate(+date);

            switch(unit.toLowerCase()) {
                case utilDate.MILLI:
                    return num;
                case utilDate.SECOND:
                    num.setUTCSeconds(num.getUTCSeconds() - num.getUTCSeconds() % step);
                    num.setUTCMilliseconds(0);
                    return num;
                case utilDate.MINUTE:
                    num.setUTCMinutes(num.getUTCMinutes() - num.getUTCMinutes() % step);
                    num.setUTCSeconds(0);
                    num.setUTCMilliseconds(0);
                    return num;
                case utilDate.HOUR:
                    num.setUTCHours(num.getUTCHours() - num.getUTCHours() % step);
                    num.setUTCMinutes(0);
                    num.setUTCSeconds(0);
                    num.setMilliseconds(0);
                    return num;
                case utilDate.DAY:
                    if(step === 7 || step === 14) {
                        num.setUTCDate(num.getUTCDate() - num.getUTCDay() + 1);
                    }
                    num.setUTCHours(0);
                    num.setUTCMinutes(0);
                    num.setUTCSeconds(0);
                    num.setMilliseconds(0);
                    return num;
                case utilDate.MONTH:
                    num.setUTCMonth(num.getUTCMonth() - (num.getUTCMonth() - 1) % step, 1);
                    num.setUTCHours(0);
                    num.setUTCMinutes(0);
                    num.setUTCSeconds(0);
                    num.setMilliseconds(0);
                    return num;
                case utilDate.YEAR:
                    num.setUTCFullYear(num.getUTCFullYear() - num.getUTCFullYear() % step, 1, 1);
                    num.setUTCHours(0);
                    num.setUTCMinutes(0);
                    num.setUTCSeconds(0);
                    num.setMilliseconds(0);
                    return date;
            }
        }
    };

    utilDate.parseCodes.C = utilDate.parseCodes.c;

    return utilDate;
}();

util.Function = function() {
    var global = util.global;

    var utilFunction = {
        flexSetter: function(setter) {
            var k;

            return function(name, value) {
                if(name !== null) {
                    if(typeof name !== 'string') {
                        for(k in name) {
                            if(name.hasOwnProperty(k)) {
                                setter.call(this, k, name[k]);
                            }
                        }

                        if(util.enumerables) {
                            for(var i = util.enumerables.length; i--;) {
                                k = util.enumerables[i];
                                if(name.hasOwnProperty(k)) {
                                    setter.call(this, k, name[k]);
                                }
                            }
                        }
                    }
                    else {
                        setter.call(this, name, value);
                    }
                }

                return this;
            };
        },
        bind: function(fn, scope, args, appendArgs) {
            if(arguments.length <= 2) {
                return fn.bind(scope);
            }

            var method = fn;

            return function() {
                var callArgs = args || arguments;

                if(appendArgs === true) {
                    callArgs = slice.call(arguments, 0);
                    callArgs = callArgs.concat(args);
                }
                else if(typeof appendArgs === 'number') {
                    callArgs = slice.call(arguments, 0);
                    util.Array.insert(callArgs, appendArgs, args);
                }

                return method.apply(scope || global, callArgs);
            };
        },
        pass: function(fn, args, scope) {
            if(!util.isArray(args)) {
                if(util.isIterables(args)) {
                    args = util.Array.clone(args);
                }
                else {
                    args = args !== undefined ? [args] : [];
                }
            }

            return function() {
                var fnArgs = args.slice();
                fnArgs.push.apply(fnArgs, arguments);
                return fn.apply(scope || this, fnArgs);
            };
        },
        alias: function(object, methodName) {
            return function() {
                return object[methodName].apply(object, arguments);
            };
        },
        clone: function(method) {
            var newMethod = function() {
                return method.apply(this, arguments);
            };

            for(var prop in method) {
                if(method.hasOwnProperty(prop)) {
                    newMethod[prop] = method[prop];
                }
            }

            return newMethod;
        },
        createInterceptor: function(origFn, newFn, scope, returnValue) {
            if(!util.isFunction(newFn)) {
                return origFn;
            }
            returnValue = util.isDefined(returnValue) ? returnValue : null;

            return function() {
                var me = this;
                var args = arguments;

                return newFn.apply(scope || me || global, args) !== false ? origFn.apply(me || global, args) : returnValue;
            };
        },
        createSequence: function(originalFn, newFn, scope) {
            if(!newFn) {
                return originalFn;
            }
            return function() {
                var result = originalFn.apply(this, arguments);
                newFn.apply(scope || this, arguments);
                return result;
            };
        },
        interceptBefore: function(object, methodName, fn, scope) {
            var method = object[methodName] || util.emptyFn;

            return (object[methodName] = function() {
                var ret = fn.apply(scope || this, arguments);
                method.apply(this, arguments);

                return ret;
            });
        },
        interceptAfter: function(object, methodName, fn, scope) {
            var method = object[methodName] || util.emptyFn;

            return (object[methodName] = function() {
                method.apply(this, arguments);
                return fn.apply(scope || this, arguments);
            });
        },
        interceptAfterOnce: function(object, methodName, fn, scope) {
            var origMethod = object[methodName];

            var newMethod = function() {
                if(origMethod) {
                    origMethod.apply(this, arguments);
                }

                var ret = fn.apply(scope || this, arguments);

                object[methodName] = origMethod;
                object = methodName = fn = scope = origMethod = newMethod = null;

                return ret;
            };

            object[methodName] = newMethod;

            return newMethod;
        },
        memoize: function(fn, scope, hashFn) {
            var memo = {};
            var isFunc = hashFn && util.isFunction(hashFn);

            return function(value) {
                var key = isFunc ? hashFn.apply(scope, arguments) : value;

                if(!(key in memo)) {
                    memo[key] = fn.apply(scope, arguments);
                }

                return memo[key];
            };
        },
        _stripCommentRe: /(\/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+\/)|(\/\/.*)/g,
        toCode: function(fn) {
            var s = fn ? fn.toString() : '';

            s = s.replace(utilFunction._stripCommentRe, '');

            return s;
        }
    };

    util.pass = utilFunction.pass;
    util.bind = utilFunction.bind;

    return utilFunction;
}();

util.USE_NATIVE_JSON = false;

util.JSON = new function() {
    var me = this;
    var hasNative = window.JSON && JSON.toString() === '[object JSON]';
    var useHasOwn = !!{}.hasOwnProperty;
    var pad = function(n) {
        return n < 10 ? '0' + n : n;
    };
    var doDecode = function(json) {
        return eval('(' + json + ')');
    };
    var doEncode = function(o, newline) {
        if(o === null || o === undefined || typeof o === 'function') {
            return 'null';
        }
        if(util.isDate(o)) {
            return me.encodeDate(o);
        }
        if(util.isString(o)) {
            if(util.isMSDate(o)) {
                return me.encodeMSDate(o);
            }
            return me.encodeString(o);
        }
        if(typeof o === 'number') {
            return isFinite(o) ? String(o) : 'null';
        }
        if(util.isBoolean(o)) {
            return String(o);
        }
        if(o.toJSON) {
            return o.toJSON();
        }
        if(util.isArray(o)) {
            return encodeArray(o, newline);
        }
        if(util.isObject(o)) {
            return encodeObject(o, newline);
        }
        return 'undefined';
    };
    var m = {
        '\b': '\\b',
        '\t': '\\t',
        '\n': '\\n',
        '\f': '\\f',
        '\r': '\\r',
        '"': '\\"',
        '\\': '\\\\',
        '\x0b': '\\u000b'
    };
    var charToReplace = /[\\\"\x00-\x1f\x7f-\uffff]/g;
    var encodeString = function(s) {
        return '"' + s.replace(charToReplace, function(a) {
            var c = m[a];
            return typeof c === 'string' ? c : '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
        }) + '"';
    };
    var encodeMSDate = function(o) {
        return '"' + o + '"';
    };

    var encodeArrayPretty = function(o, newline) {
        var cnewline = newline + '   ';
        var sep = ',' + cnewline;
        var a = ['[', cnewline];

        for(var len = o.length, i = 0; i < len; i++) {
            a.push(me.encodeValue(o[i], cnewline), sep);
        }

        a[a.length - 1] = newline + ']';

        return a.join('');
    };

    var encodeObjectPretty = function(o, newline) {
        var cnewline = newline + '   ';
        var sep = ',' + cnewline;
        var a = ['{', cnewline];

        for(var i in o) {
            var val = o[i];
            if(!useHasOwn || o.hasOwnProperty(i)) {
                if(typeof val === 'function' || val === undefined || val.isInstance) {
                    continue;
                }
                a.push(me.encodeValue(i) + ': ' + me.encodeValue(val, cnewline), sep);
            }
        }

        a[a.length - 1] = newline + '}';

        return a.join('');
    };

    var encodeArray = function(o, newline) {
        if(newline) {
            return encodeArrayPretty(o, newline);
        }

        var a = ['[', ''];
        for(var len = o.length, i = 0; i < len; i++) {
            a.push(me.encodeValue(o[i]), ',');
        }
        a[a.length - 1] = ']';
        return a.join('');
    };

    var encodeObject = function(o, newline) {
        if(newline) {
            return encodeObjectPretty(o, newline);
        }

        var a = ['{', ''];
        for(var i in o) {
            var val = o[i];
            if(!useHasOwn || o.hasOwnProperty(i)) {
                if(typeof val === 'function' || val === undefined) {
                    continue;
                }
                a.push(me.encodeValue(i), ':', me.encodeValue(val), ',');
            }
        }
        a[a.length - 1] = '}';
        return a.join('');
    };

    me.encodeString = encodeString;

    me.encodeValue = doEncode;

    me.encodeDate = function(o) {
        return '"' + o.getFullYear() + '-' + pad(o.getMonth() + 1) + '-' + pad(o.getDate()) + 'T' + pad(o.getHours()) + ':' + pad(o.getMinutes()) + ':' + pad(o.getSeconds()) + '"';
    };

    me.encode = function(o) {
        if(hasNative && util.USE_NATIVE_JSON) {
            return JSON.stringify(o);
        }
        return me.encodeValue(o);
    };

    me.decode = function(json, safe) {
        try {
            if(hasNative && util.USE_NATIVE_JSON) {
                return JSON.parse(json);
            }
            return doDecode(json);
        }
        catch(e) {
            if(safe) {
                return null;
            }
        }
    }

    me.encodeMSDate = encodeMSDate;

    util.encode = me.encode;
    util.decode = me.decode;
}();

util.LocalStorage = {
    getKeys: function(session) {
        var store = session ? window.sessionStorage : window.localStorage;
        var keys = [];

        for(var i = store.length; i--;) {
            var key = store.key(i);
            keys.push(key);
        }

        return keys;
    },
    clear: function(session) {
        var me = this;
        var store = session ? window.sessionStorage : window.localStorage;
        var keys = me.getKeys(session);

        for(var i = keys.length; i--;) {
            store.removeItem(keys[i]);
        }
    },
    key: function(index, session) {
        var keys = this.getKeys(session);

        return 0 <= index && index < keys.length ? keys[index] : null;
    },
    getItem: function(key, session, base64) {
        var k = base64 !== false ? util.Base64.encode(key) : key;
        var data = (session ? window.sessionStorage : window.localStorage).getItem(k);

        if(!util.isEmpty(data)) {
            if(base64 !== false) {
                data = util.Base64.decode(data);
            }

            data = util.valueFrom(util.decode(data, true), data);
        }

        return data;
    },
    removeItem: function(key, session, base64) {
        var k = base64 !== false ? util.Base64.encode(key) : key;
        var store = session ? window.sessionStorage : window.localStorage;

        store.removeItem(k);
    },
    setItem: function(key, value, session, base64) {
        var k = base64 !== false ? util.Base64.encode(key) : key;
        var store = session ? window.sessionStorage : window.localStorage;

        if(!util.isString(value)) {
            value = util.encode(value);
        }

        store.setItem(k, base64 !== false ? util.Base64.encode(value) : value);
    }
};

util.Number = new function() {
    var utilNumber = this;
    var isToFixedBroken = 0.9.toFixed() !== '1';
    var math = Math;
    var ClipDefault = {
        count: false,
        inclusive: false,
        wrap: true
    };

    Number.MIN_SAFE_INTEGER = Number.MIN_SAFE_INTEGER || -(math.pow(2, 53) - 1);
    Number.MAX_SAFE_INTEGER = Number.MAX_SAFE_INTEGER || math.pow(2, 53) - 1;

    util.apply(utilNumber, {
        floatRe: /^[-+]?(?:\d+|\d*\.\d*)(?:[Ee][+-]?\d+)?$/,
        intRe: /^[-+]?\d+(?:[Ee]\+?\d+)?$/,
        parseFloat: function(value) {
            if(value === undefined) {
                value = null;
            }

            if(value !== null && typeof value !== 'number') {
                value = String(value);
                value = utilNumber.floatRe.test(value) ? +value : null;
                if(isNaN(value)) {
                    value = null;
                }
            }

            return value;
        },
        parseInt: function(value) {
            if(value === undefined) {
                value = null;
            }

            if(typeof value === 'number') {
                value = Math.floor(value);
            }
            else if(value !== null) {
                value = String(value);
                value = utilNumber.intRe.test(value) ? +value : null;
            }

            return value;
        },
        binarySearch: function(array, value, begin, end) {
            if(begin === undefined) {
                begin = 0;
            }
            if(end === undefined) {
                end = array.length;
            }

            --end;

            while(begin <= end) {
                var middle = begin + end >>> 1;
                var midVal = array[middle];

                if(value === midVal) {
                    return middle;
                }
                if(midVal < value) {
                    begin = middle + 1;
                }
                else {
                    end = middle - 1;
                }
            }

            return begin;
        },
        bisectTuples: function(array, value, index, begin, end) {
            if(begin === undefined) {
                begin = 0;
            }
            if(end === undefined) {
                end = array.length;
            }

            --end;

            while(begin <= end) {
                var middle = begin + end >>> 1;
                var midVal = array[middle][index];

                if(value === midVal) {
                    return middle;
                }
                if(midVal < value) {
                    begin = middle + 1;
                }
                else {
                    end = middle - 1;
                }
            }

            return begin;
        },
        clipIndices: function(length, indices, options) {
            options = options || ClipDefault;

            var defaultValue = 0;
            var wrap = options.wrap;
            var begin;
            var end;

            for(var i = 0; i < 2; ++i) {
                begin = end;
                end = indices[i];
                if(end == null) {
                    end = defaultValue;
                }
                else if(i && options.count) {
                    end += begin;
                    end = end > length ? length : end;
                }
                else {
                    if(wrap) {
                        end = end < 0 ? length + end : end;
                    }
                    if(i && options.inclusive) {
                        ++end;
                    }
                    end = end < 0 ? 0 : (end > length ? length : end);
                }
                defaultValue = length;
            }

            indices[0] = begin;
            indices[1] = end < begin ? begin : end;
            return indices;
        },
        constrain: function(number, min, max) {
            var x = parseFloat(number);

            if(min === null) {
                min = number;
            }

            if(max === null) {
                max = number;
            }

            return x < min ? min : (x > max ? max : x);
        },
        snap: function(value, increment, minValue, maxValue) {
            if(value === undefined || value < minValue) {
                return minValue || 0;
            }

            if(increment) {
                var m = value % increment;
                if(m !== 0) {
                    value -= m;
                    if(m * 2 >= increment) {
                        value += increment;
                    }
                    else if(m * 2 < -increment) {
                        value -= increment;
                    }
                }
            }
            return utilNumber.constrain(value, minValue, maxValue);
        },
        snapInRange: function(value, increment, minValue, maxValue) {
            var tween;

            minValue = minValue || 0;

            if(value === undefined || value < minValue) {
                return minValue;
            }

            if(increment && (tween = (value - minValue) % increment)) {
                value -= tween;
                tween *= 2;
                if(tween >= increment) {
                    value += increment;
                }
            }

            if(maxValue !== undefined && value > (maxValue = utilNumber.snapInRange(maxValue, increment, minValue))) {
                value = maxValue;
            }

            return value;
        },
        roundToNearest: function(value, interval) {
            interval = interval || 1;
            return interval * math.round(value / interval);
        },
        roundToPrecision: function(value, precision) {
            var factor = math.pow(10, precision || 1);

            return math.round(value * factor) / factor;
        },
        truncateToPrecision: function(value, precision) {
            var factor = math.pow(10, precision || 1);

            return parseInt(value * factor, 10) / factor;
        },
        sign: math.sign || function(x) {
            x = +x;

            if(x === 0 || isNaN(x)) {
                return x;
            }

            return x > 0 ? 1 : -1;
        },
        log10: math.log10 || function(x) {
            return math.log(x) * math.LOG10E;
        },
        isEqual: function(n1, n2, epsilon) {
            return math.abs(n1 - n2) < epsilon;
        },
        isFinite: Number.isFinite || function(value) {
            return typeof value === 'number' && isFinite(value);
        },
        isInteger: Number.isInteger || function(value) {
            // return ~~(value + 0) === value;
            return utilNumber.isFinite(value) && Math.floor(value) === value;
        },
        toFixed: isToFixedBroken ? function(value, precision) {
            precision = precision || 0;
            var pow = math.pow(10, precision);
            return (math.round(value * pow) / pow).toFixed(precision);
        } : function(value, precision) {
            return value.toFixed(precision);
        },
        from: function(value, defaultValue) {
            if(isFinite(value)) {
                value = parseFloat(value);
            }

            return !isNaN(value) ? value : defaultValue;
        },
        randomInt: function(from, to) {
            return math.floor(math.random() * (to - from + 1) + from);
        },
        correctFloat: function(n) {
            return parseFloat(n.toPrecision(14));
        }
    });

    util.num = function() {
        return utilNumber.from.apply(this, arguments);
    };
}();

util.Helper = function() {
    var visitNode = function(node, hashMap, array) {
        if(!hashMap[node.group]) {
            hashMap[node.group] = true;
            array[node.group] = node;
        }
    };

    var utilHelper = {
        encode: function(json) {
            return !util.isEmpty(json) ? encodeURIComponent(JSON.stringify(json)) : '';
        },
        decode: function(string) {
            return !util.isEmpty(string) ? JSON.parse(decodeURIComponent(string)) : [];
        },
        getLoaded: function() {
            return util.LocalStorage.getItem('loaded', false, false);
        },
        setLoaded: function(loaded) {
            util.LocalStorage.setItem('loaded', loaded, false, false);
        },
        clearLoaded: function() {
            util.LocalStorage.removeItem('loaded', false, false);
        },
        getCategoryArray: function() {
            var loaded = utilHelper.getLoaded();

            return loaded.category;
        },
        getCategoryObject: function() {
            var category = utilHelper.getCategoryArray();

            var stack = [];
            var array = {};
            var hashMap = {};

            util.each(category, function(item, index, allItems) {
                util.Array.push(stack, item);
            });

            while(stack.length > 0) {
                var node = stack.pop();

                if(util.isEmpty(node.childs)) {
                    visitNode(node, hashMap, array);

                    continue;
                }

                util.each(node.childs, function(item, index, allItems) {
                    visitNode(node, hashMap, array);

                    util.Array.push(stack, item);
                }, null, true);
            }

            return array;
        },
        getCategoryByGroup: function(group) {
            var category = utilHelper.getCategoryObject();

            return category[util.Number.parseInt(group)];
        },
        getCategoryByDeviceCode: function(deviceCode) {
            var device = utilHelper.getDevice();
            var group = util.Array.findBy(device, function(item, index) {
                return deviceCode === item.device_code;
            }).device_group;

            return utilHelper.getCategoryByGroup(group);
        },
        getTreeByGroup: function(group) {
            var category = utilHelper.getCategoryByGroup(group);

            if(util.isEmpty(category)) {
                return;
            }

            var tree = {};

            tree[category.group] = category.comment;

            var parent = utilHelper.getCategoryByGroup(category.parent_group);

            while(!util.isEmpty(parent)) {
                tree[parent.group] = parent.comment;
                parent = utilHelper.getCategoryByGroup(parent.parent_group);
            }

            return tree;
        },
        getCompany: function() {
            var loaded = utilHelper.getLoaded();

            return loaded.companys;
        },
        getCompanyByIdx: function(idx) {
            var company = utilHelper.getCompany();

            return util.Array.findBy(company, function(item, index) {
                return item.company_idx === util.Number.parseInt(idx);
            });
        },
        getCompanyByCorporateNumber: function(corporateNumber) {
            var company = utilHelper.getCompany();

            return util.Array.findBy(company, function(item, index) {
                return corporateNumber === item.company_corporate_number;
            });
        },
        getCompanyByCountry: function(country) {
            var company = utilHelper.getCompany();

            return util.Array.filter(company, function(item, index, allItems) {
                return country === item.company_country;
            });
        },
        getFactory: function() {
            var loaded = utilHelper.getLoaded();

            return loaded.factory;
        },
        getFactoryByIdx: function(idx) {
            var factory = utilHelper.getFactory();

            return util.Array.findBy(factory, function(item, index) {
                return item.factory_idx === util.Number.parseInt(idx);
            });
        },
        getFactoryByCompany: function(company) {
            var factory = utilHelper.getFactory();

            return util.Array.filter(factory, function(item, index, allItems) {
                return item.factory_company_idx === util.Number.parseInt(company);
            });
        },
        getDevice: function() {
            var loaded = utilHelper.getLoaded();

            return loaded.devices;
        },
        getDeviceConvert: function() {
            var device = utilHelper.getDevice();

            return util.Array.map(device, function(item, index, allItems) {
                item.channel = !util.isEmpty(item.device_config) ? utilHelper.decode(item.device_config) : [];
                item.checked = false;
                item.fold = false;

                return item;
            });
        },
        getDeviceByIdx: function(idx) {
            var device = utilHelper.getDevice();

            return util.Array.findBy(device, function(item, index) {
                return item.device_idx === util.Number.parseInt(idx);
            });
        },
        getDeviceByCode: function(code) {
            var device = utilHelper.getDevice();

            return util.Array.findBy(device, function(item, index) {
                return code === item.device_code;
            });
        },
        getDeviceByGroup: function(group) {
            var device = utilHelper.getDevice();

            return util.Array.filter(device, function(item, index, allItems) {
                return item.device_group === util.Number.parseInt(group);
            });
        },
        getDeviceByFactory: function(factory) {
            var device = utilHelper.getDevice();

            return util.Array.filter(device, function(item, index, allItems) {
                return item.device_factory_idx === util.Number.parseInt(factory);
            });
        },
        getChannel: function(deviceCode, channel) {
            var device = utilHelper.getDeviceByCode(deviceCode);

            if(util.isEmpty(device) || util.isEmpty(device.device_config)) {
                return;
            }

            var deviceConfig = utilHelper.decode(device.device_config);

            return !util.isEmpty(channel) ? util.Array.findBy(deviceConfig, function(item, index) {
                return channel === item.channel;
            }) : deviceConfig;
        },
        getFilter: function() {
            var loaded = utilHelper.getLoaded();

            return loaded.filters;
        },
        getFilterByIdx: function(idx) {
            var filter = utilHelper.getFilter();

            return util.Array.findBy(filter, function(item, index) {
                return item.sensor_idx === util.Number.parseInt(idx);
            });
        }
    };

    return utilHelper;
}();
