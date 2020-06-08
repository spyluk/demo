require('./bootstrap');

window.Vue = require('vue');
window.axios = require('axios');
const stringify = require('stringify-object');

import moment from 'moment';
import $ from 'jquery';
import ElementUI from 'element-ui';
import locale from 'element-ui/lib/locale/lang/en';
Vue.use(ElementUI, { locale });
Vue.use(ElementUI);
var momentDurationFormatSetup = require("moment-duration-format");

momentDurationFormatSetup(moment);

import * as EditorUI from 'tiptap';
import * as EditorExtensions from 'tiptap-extensions';
import FullCalendar from '@fullcalendar/vue';
import FullCalendarDayGridPlugin from '@fullcalendar/daygrid';
import FullCalendarTimeGridPlugin from '@fullcalendar/timegrid';
import FullCalendarInteractionPlugin from '@fullcalendar/interaction';
// import FullCalendarCore from '@fullcalendar/core';

Vue.component("from-source", {
    props:["source"],
    template: '<span><slot></slot></span>'
});

window.ElementUI = ElementUI;
window.EditorUI = EditorUI;
window.EditorExtensions = EditorExtensions;
window.FullCalendar = FullCalendar;
window.FullCalendarDayGridPlugin = FullCalendarDayGridPlugin;
window.FullCalendarTimeGridPlugin = FullCalendarTimeGridPlugin;
// window.FullCalendarCore = FullCalendarCore;
window.FullCalendarInteractionPlugin = FullCalendarInteractionPlugin;
window.$ = $;

moment.updateLocale('en', {
    longDateFormat : {
        LT: "h:mm A",
        LTS: "h:mm:ss A",
        L: "MMM DD",
        LL: "MM/DD/YY",
        LLL: "LL LT",
        LLLL: "L, YYYY, LT"
    }
});

moment.updateLocale('ru', {
    longDateFormat : {
        LT: "HH:mm",
        LTS: "HH:mm:ss",
        L: "DD MMM",
        LL: "DD.MM.YY",
        LLL: "LL LT",
        LLLL: "L, YYYY, LT"
    }
});

Vue.filter('formatDate', function(utc_timestamp, formatName) {
    if (utc_timestamp) {
        if(formatName) {
            var checkFormat;
            formatName = formatName.split("|");
            for(var key in formatName) {
                checkFormat = formatName[key].split(":");
                if(checkFormat.length > 1) {
                    if(moment().isSame(moment(utc_timestamp * 1000), checkFormat[1])) {
                        formatName = checkFormat[0];
                        break;
                    }
                } else {
                    formatName = checkFormat[0];
                    break;
                }
            }
        } else {
            formatName = 'LLL';
        }
        return moment(utc_timestamp * 1000).format(formatName);
    }
});

Vue.filter('formatTime', function(timesec, format) {
    if (timesec) {
        format = (!format ? "d [days] hh:mm" : format);
        return moment.duration(parseInt(timesec), 'seconds').format(format);
    }
});

var templateParse = require('./vue-parser');
var apiUrl = document.currentScript.src.replace(/\/js.*$/, '');
var nameAtCookie = '__wdmat';
var nameEnvCookie = '__wdmenv';

window.WidgetManager = {
    class: 'widget-edu',
    classMain: 'widget-main',
    params_key: 'variables',
    locationPrefix: '/',
    listeners: {},
    components: {},
    componentsStructure: {ids: {}},
    roots: {},
    env: {id: null},
    moduleInitData: {},
    moduleQueue: {},
    echo: {},
    debugPosition: "widget-edu-debug",
    _initEcho: function() {
        this.echo = new Echo({
            authEndpoint: apiUrl + '/api/broadcasting/auth',
            broadcaster: 'pusher',
            key: process.env.MIX_PUSHER_APP_KEY,
            cluster: process.env.MIX_PUSHER_APP_CLUSTER,
            encrypted: true,
            auth: {
                headers: {
                    Authorization: 'Bearer ' + this.__getCookie(nameAtCookie)
                }
            }
        });

        if (this.getEnv().id) {
            this.echo.private('App.User.' + this.getEnv().id)
                .notification((notification) => {
                var title = notification.title ? notification.title : '';
                var message = notification.message ? notification.message : '';
                ElementUI.Notification.info({title:title, message:message});
            });
        }
    },
    checkAppComponent: function(component) {
        if(!component instanceof Vue) {
            throw new Error('Wrong component type');
        }

        if(!component.id) {
            throw new Error('Сomponent has no id');
        }
    },
    addPrivateListener: function(component, events, handler) {

        this.checkAppComponent(component);
        events = Array.isArray(events) ? events : [events];

        var self = this;

        for(var key in events) {
            var event = events[key];
            if (!this.listeners[event] && this.getEnv().id) {
                this.listeners[event] = {};
                this.echo.private('App.User.' + this.getEnv().id)
                    .listen(event, function (data) {
                        for (var key in self.listeners[event]) {
                            var listener = self.listeners[event][key];
                            listener(data);
                        }
                    });
            }

            this.listeners[event][component.id] = handler;
        }
    },
    init: function () {
        var token, env;

        if((token = this.__getCookie(nameAtCookie))) {
            this.setToken(token, false);
        }

        if((env = this.__getCookie(nameEnvCookie))) {
            this.setEnv(JSON.parse(env, false));
        }

        this.__initByUrl();
        $('.'+this.class).each(function () {
            WidgetManager.moduleQueue[$(this).attr('id')] = $(this);
        });

        this._initEcho();

        this.handleModuleQueue();
    },
    __initByUrl: function (url) {
        var current_location = window.location.href;
        url = (url === undefined ? window.location.href : url);
        if(url.indexOf('#' + WidgetManager.locationPrefix) > -1) {
            var regular = '.*#'+WidgetManager.locationPrefix;
            regular = new RegExp('[^'+regular+']*'+regular, "g");
            var moduleAndPlace= url.replace(regular, '');
            $("."+this.classMain).data('module', moduleAndPlace);
        }
    },
    /**
     * convert uri параметры to obj
     * @param str
     * @returns {{}}
     */
    uriParamsToObject: function(str) {
        if(str.indexOf("?") !== -1) {
            var search = str.replace(/.*\?/, '');
            return JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g, '":"') + '"}', function (key, value) {
                return key === "" ? value : decodeURIComponent(value)
            });
        }

        return {};
    },
    handleModuleQueue: function(moduleQueue, params, handleResult) {
        var self = this;
        var fullRefresh = !!moduleQueue;
        moduleQueue = moduleQueue ? moduleQueue : this.moduleQueue;
        if(Object.keys(moduleQueue).length) {
            var modules = [];
            for(var key in moduleQueue) {
                modules.push(
                    this.getRequestData(moduleQueue[key].data('module'), key, params)
                );
            }

            if(fullRefresh) {
                this.moduleQueue = {};
            }
            self.requestModules(modules, null, handleResult);
        }
    },
    getRequestData: function(moduleName, position, params) {
        var code = moduleName.replace(/\?.*/,'');
        var allParams = this.uriParamsToObject(moduleName);
        allParams = params ? Object.assign({}, params, allParams) : allParams;
        return {code: code, params: allParams, position: position};
    },
    setToken: function(token, reset) {
        axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
        if(reset || reset === undefined) {
            this.__resetCookie(nameAtCookie, token);
        }
    },
    setEnv: function(env, reset) {
        this.env = env;
        if(reset || reset === undefined) {
            this.__resetCookie(nameEnvCookie, JSON.stringify(env));
        }
    },
    getEnv: function() {
        return this.env;
    },
    getParamsExclude: function(object, exclude) {
        var result = Object.keys(object).reduce((objectReduce, key) => {
            if (exclude.indexOf(key) === -1) {
                objectReduce[key] = object[key]
            }
            return objectReduce;
        }, {});

        return result;
    },
    requestModules: function(modules, postParams, handleResult, handleError) {
        var self = this;
        if(modules.length) {
            var query = '{vsm(codes: ' + stringify(modules, {singleQuotes: false})+') {structure}}';
        }

        var loadingInstance = ElementUI.Loading.service({ fullscreen: true });

        handleResult = handleResult ? handleResult : this;
        handleError = handleError ? handleError : this;

        this.request(query, postParams, handleResult, handleError);
    },
    requestAndReplaceModule: function(module, params, handleResult, handleError, show_in_url) {
        params = !params ? {} : params;
        if(show_in_url) {
            var paramsToUrl = this.getParamsExclude(params, ['caller']);
            paramsToUrl = Object.keys(paramsToUrl).length ? '?' + $.param(paramsToUrl) : '';
            window.location.href = '#' + WidgetManager.locationPrefix + module + paramsToUrl;
        }
        var self = this;
        self.requestModules(
            [this.getRequestData(module, null, params)],
            null,
            function(result) {
                self.handleResult(result, handleResult);
            },
            function(result) {
                self.handleError(result, handleError);
            }
        );
    },
    request: function(query, postParams, handleResult, handleError) {
        var self = this;
        handleError = handleError ? handleError : this;
        var loadingInstance = ElementUI.Loading.service({ fullscreen: true });
        var headres = {};
        var postParamsAdapteToFormData = {};
        var params = {};

        var getFormDataOnFiles = function(params, field) {
            var result = {map: null, mapFiles: null, params: {}};
            var currentField = '';
            field = field ? field : '';

            for(var key in params) {
                currentField = field ? field + '[' + key + ']' : key;
                if(typeof params[key] === 'object' && !(params[key] instanceof File)) {
                    result = Object.assign({}, result, getFormDataOnFiles(params[key], currentField));
                } else {
                    if(params[key] instanceof File) {
                        result.map = !result.map ? {} : result.map;
                        var fileOrder = Object.keys(result.map).length;
                        result.mapFiles = !result.mapFiles ? {} : result.mapFiles;
                        result.map[fileOrder] = [self.params_key + '.' + key];
                        result.mapFiles[fileOrder] = params[key];
                    } else {
                        result.params[currentField] = (params[key] instanceof File) ? null : params[key];
                    }
                }
            }
            return result;
        };

        params.query = query;

        if(postParams) {
            postParamsAdapteToFormData = getFormDataOnFiles(postParams);
            if(postParamsAdapteToFormData.map) {
                headres = {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                };
                var paramsWithFiles = postParamsAdapteToFormData.mapFiles;
                paramsWithFiles.map = postParamsAdapteToFormData.map;
                paramsWithFiles.operations = {query: query};
                paramsWithFiles.operations[this.params_key] = postParamsAdapteToFormData.params;
                params = new FormData();
                for (var key in paramsWithFiles) {
                    params.append(key, (typeof paramsWithFiles[key] === 'object' && !(paramsWithFiles[key] instanceof File)) ?
                        JSON.stringify(paramsWithFiles[key]) : paramsWithFiles[key]);
                }
            } else {
                params[this.params_key] = postParams;
            }
        }

        axios.post(apiUrl + "/graphql", params, headres)
            .then(function (resp) {
                var result = (resp.data && resp.data.data) ? resp.data.data : null;
                handleError = handleError ? handleError : null;
                var errors = (resp.data && resp.data.errors) ?
                    resp.data.errors : (
                        (result && result.vsm && result.vsm.structure && result.vsm.structure.errors) ?
                            result.vsm.structure.errors : false
                    );

                if(result && !errors) {
                    result = (result.vsm && result.vsm.structure) ? result.vsm.structure : result;
                    if(handleResult instanceof Function) {
                        handleResult(result);
                    } else if (handleResult instanceof Object) {
                        handleResult.handleResult(result);
                    }
                } else if(handleError) {
                    resp = errors ? errors : resp.data;
                    if(handleError instanceof Function) {
                        handleError(resp);
                    } else if (handleError instanceof Object) {
                        handleError.handleError(resp);
                    }
                }

                loadingInstance.close();
            })
            .catch(function (resp) {
                self.handleError(resp);
            });

    },
    handleError: function(result, callback) {
        if(callback) {
            callback(result);
        } else if(Array.isArray(result) && typeof result[0] === 'object' && result[0].message) {
            ElementUI.Loading.service().close();
            ElementUI.Notification.warning({title:'Warning', message:result[0].message});
        } else {
            ElementUI.Loading.service().close();
            ElementUI.Notification.warning({title:'Warning', message:'Unknown error!'});
        }
    },
    handleResult: function(result, callback) {
        var self = this;
        /**
         * clear data
         * @type {{}}
         */
        self.moduleInitData = {};
        /**
         *
         * @param modules
         * @param templates
         * @returns {{componentsString: string}}
         */
        var modulesHandler = function (modules, templates, root) {
            root = (root === undefined ? true : root);
            var result = [];
            var positions = [];
            var sub_result = {};
            var id, templateObj, position, componentName, relations;
            for(var key in modules) {
                id = `f${(~~(Math.random() * 1e8)).toString(16)}`;
                position = modules[key].position ? modules[key].position : '';
                componentName = modules[key].template;
                templateObj = templates[componentName] ? templates[componentName] : '';
                componentName = self.getNormalizeModuleName(componentName);
                relations = modules[key]['relations'];

                self.moduleInitData[id] = {
                    data: modules[key].data ? modules[key].data : {},
                    component: componentName,
                    id: id
                };

                result.push({component: componentName, id: id, position: position});

                if(relations) {
                    for(var placeholder in relations) { //module placeholder
                        sub_result = modulesHandler(relations[placeholder], templates, false);
                        if(sub_result) {
                            for(var key in sub_result) {
                                if(!self.moduleInitData[id].data.pholders) {
                                    self.moduleInitData[id].data.pholders = {};
                                    self.moduleInitData[id].data.pholders[placeholder] = {};
                                }

                                self.moduleInitData[id].data.pholders[placeholder][key] = {
                                    component: sub_result[key].component,
                                    id: sub_result[key].id
                                };
                            }
                        }
                    }
                }

                /**
                 * initialize the component if not already done
                 */
                if(!self.components[componentName]) {
                    self.components[componentName] = true;
                    self.__initVueComponent(componentName, templateObj);
                }
            }

            if(root && Object.keys(result).length) {/*handle root component*/
                for(var key in result) {
                    if((position = result[key].position)) {
                        if (position == self.debugPosition) {
                            if (!$('#' + self.debugPosition).length) {
                                $('body').append('<div id="' + self.debugPosition + '" class="widget-edu"></div>');
                            }
                        }

                        $("#" + position).html('');
                        self.__initVue(position, {components: [result[key]]});

                        if (position == self.debugPosition) {
                            result.splice(key, 1);
                        }
                    }
                }
            }

            return result;
        };

        if(result.redirect) {
            window.location = result.redirect;
        }else if(result.modules) {
            result = modulesHandler(result.modules, result.templates);

            if(callback) {
                callback(result, this.moduleInitData);
            }

            this.__refreshComponentStructure();
        }
    },
    __refreshComponentStructure: function() {
        var walk = function(component, parent_id, root) {
            var result = {ids: {}};
            var response;

            if(root) {
                for (var rkey in component) {
                    if (component[rkey].$children.length) {
                        for (var key in component[rkey].$children) {
                            response = walk(component[rkey].$children[key], null);
                            result.ids = Object.assign({}, result.ids, response.ids);
                        }
                   }
                }
            } else if(component.id) {
                result.ids[component.id] = {id: component.id, parent_id: parent_id, name: component.$options._componentTag};
                if(component.$children.length) {
                    for(var key in component.$children) {
                        response = walk(component.$children[key], component.id);
                        result.ids = Object.assign({}, result.ids, response.ids);
                    }
                }
            }
            
            return result;
        };

        if(Object.keys(this.roots)) {
            this.componentsStructure = walk(this.roots, null, true);
            this.__refrashListeners();
        }
    },
    __refrashListeners: function() {
        for(var lkey in this.listeners) {
            for(var component_id in this.listeners[lkey]) {
                if(this.componentsStructure.ids[component_id] == undefined) {
                    delete this.listeners[lkey][component_id];
                }
            }
        }
    },
    toUTC: function(date) {
        return moment(date).utc().unix();
    },
    fromUtc: function(date, format) {
        return moment.unix(date).format(format);
    },
    getInitData: function(id) {
        var result = {};
        if(this.moduleInitData[id]) {
            result = this.moduleInitData[id];
            delete this.moduleInitData[id];
        }

        return result;
    },
    getNormalizeModuleName: function(module) {
        return module.replace(/\./g,'-');
    },
    addScript: function(srcs, call_onload) {
        srcs = (srcs instanceof Array) ? srcs : [srcs];
        var src;
        for(var key in srcs) {
            src = srcs[key].replace(/\{\{apiUrl\}\}/, apiUrl);
            if (!$("script[href='" + src + "']").length) {
                $.getScript(src, call_onload);
            }
        }
    },
    addStyle: function(srcs) {
        srcs = (srcs instanceof Array) ? srcs : [srcs];
        var src;
        for(var key in srcs) {
            src = srcs[key].replace(/\{\{apiUrl\}\}/, apiUrl);
            if (!$("script[href='"+src+"']").length) {
                $('<link href="'+src+'" rel="stylesheet">').appendTo("head");
            }
        }
    },
    convertListByKeyPairs: function(list, parent_id, key_pairs, filter_callback) {
        parent_id = !parent_id ? null : parent_id;
        key_pairs = key_pairs ? key_pairs :
            {value: 'value', id: 'id', children: 'children'};

        filter_callback = filter_callback ? filter_callback : function(item){return true;};

        var result = [];
        for (var key in list) {
            if(list[key].parent_id == parent_id && filter_callback(list[key])) {
                var item = {value: list[key].value, id: list[key].id};
                for(var i in key_pairs) {
                    if(i != 'children') {
                        item[key_pairs[i]] = list[key][i];
                    }
                }

                var children = null;
                if(key_pairs.children) {
                    children = this.convertListByKeyPairs(list, list[key].id, key_pairs);
                    if(children.length) {
                        item[key_pairs.children] = children;
                    }
                }

                result.push(item);
            }
        }

        return result;
    },
    __initVueComponent: function(template_code, templateObj) {
        var parse = {};
        parse = templateParse(templateObj.template);
        var data = parse.source.data instanceof Function ? Object.assign({}, parse.source.data()) : {data: {}};
        if(templateObj.attr && templateObj.attr.styles) {
            this.addStyle(templateObj.attr.styles);
        }

        if(templateObj.attr && templateObj.attr.scripts) {
            this.addScript(templateObj.attr.scripts);
        }

        data.attr = templateObj.attr;

        data.vars = templateObj.vars;
        data.WidgetManager = this;
        if(parse.source && parse.template) {
            return Vue.component(
                this.getNormalizeModuleName(template_code),
                Object.assign({template: parse.template}, Object.assign(parse.source, {data() { return data }}))
            );
        }

        return null;
    },
    __initVue: function(position, data) {

        var positionArea = position + '-area';
        data = data ? data : {components: {}};
        data.WidgetManager = this;
        var template = '<div><div v-for="item in components">' +
                '<component :is="item.component" :id="item.id" ></component>' +
            '</div></div>';

        $('#' + position).html('<div id="'+positionArea+'"></div>');
        this.roots[position] = new Vue(
            Object.assign({el: '#' + positionArea, template: template}, {data() { return data }},
                {mounted() {
                    let self = this;
                    this.$on('switch', function(data) {
                        self.components = data.data;
                    });
                }})
        );
    },
    __getCookie: function (name) {
        var matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    },
    __setCookie: function (name, value, days) {
        var date = new Date();
        days = days ? days : 30;
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toUTCString();
        document.cookie = encodeURIComponent(name)+"="+encodeURIComponent(value)+expires+"; path=/";
    },
    __resetCookie: function(name, value, days) {
        if(this.__getCookie(name)) {
            this.__setCookie(value, '', -1);
        }

        this.__setCookie(name, value, days);
    }
};

WidgetManager.init();