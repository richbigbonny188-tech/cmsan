'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/*
 * File: iframeResizer.js
 * Desc: Force iframes to size to content.
 * Requires: iframeResizer.contentWindow.js to be loaded into the target frame.
 * Doc: https://github.com/davidjbradshaw/iframe-resizer
 * Author: David J. Bradshaw - dave@bradshaw.net
 * Contributor: Jure Mav - jure.mav@gmail.com
 * Contributor: Reed Dadoune - reed@dadoune.com
 */

;(function (undefined) {
    'use strict';

    if (typeof window === 'undefined') return; // don't run for server side render

    var count = 0,
        logEnabled = false,
        hiddenCheckEnabled = false,
        msgHeader = 'message',
        msgHeaderLen = msgHeader.length,
        msgId = '[iFrameSizer]',
        //Must match iframe msg ID
    msgIdLen = msgId.length,
        pagePosition = null,
        requestAnimationFrame = window.requestAnimationFrame,
        resetRequiredMethods = { max: 1, scroll: 1, bodyScroll: 1, documentElementScroll: 1 },
        settings = {},
        timer = null,
        logId = 'Host Page',
        defaults = {
        autoResize: true,
        bodyBackground: null,
        bodyMargin: null,
        bodyMarginV1: 8,
        bodyPadding: null,
        checkOrigin: true,
        inPageLinks: false,
        enablePublicMethods: true,
        heightCalculationMethod: 'bodyOffset',
        id: 'iFrameResizer',
        interval: 32,
        log: false,
        maxHeight: Infinity,
        maxWidth: Infinity,
        minHeight: 0,
        minWidth: 0,
        resizeFrom: 'parent',
        scrolling: false,
        sizeHeight: true,
        sizeWidth: false,
        warningTimeout: 5000,
        tolerance: 0,
        widthCalculationMethod: 'scroll',
        closedCallback: function closedCallback() {},
        initCallback: function initCallback() {},
        messageCallback: function messageCallback() {
            warn('MessageCallback function not defined');
        },
        resizedCallback: function resizedCallback() {},
        scrollCallback: function scrollCallback() {
            return true;
        }
    };

    function addEventListener(obj, evt, func) {
        /* istanbul ignore else */ // Not testable in PhantonJS
        if ('addEventListener' in window) {
            obj.addEventListener(evt, func, false);
        } else if ('attachEvent' in window) {
            //IE
            obj.attachEvent('on' + evt, func);
        }
    }

    function removeEventListener(el, evt, func) {
        /* istanbul ignore else */ // Not testable in phantonJS
        if ('removeEventListener' in window) {
            el.removeEventListener(evt, func, false);
        } else if ('detachEvent' in window) {
            //IE
            el.detachEvent('on' + evt, func);
        }
    }

    function setupRequestAnimationFrame() {
        var vendors = ['moz', 'webkit', 'o', 'ms'],
            x;

        // Remove vendor prefixing if prefixed and break early if not
        for (x = 0; x < vendors.length && !requestAnimationFrame; x += 1) {
            requestAnimationFrame = window[vendors[x] + 'RequestAnimationFrame'];
        }

        if (!requestAnimationFrame) {
            log('setup', 'RequestAnimationFrame not supported');
        }
    }

    function getMyID(iframeId) {
        var retStr = 'Host page: ' + iframeId;

        if (window.top !== window.self) {
            if (window.parentIFrame && window.parentIFrame.getId) {
                retStr = window.parentIFrame.getId() + ': ' + iframeId;
            } else {
                retStr = 'Nested host page: ' + iframeId;
            }
        }

        return retStr;
    }

    function formatLogHeader(iframeId) {
        return msgId + '[' + getMyID(iframeId) + ']';
    }

    function isLogEnabled(iframeId) {
        return settings[iframeId] ? settings[iframeId].log : logEnabled;
    }

    function log(iframeId, msg) {
        output('log', iframeId, msg, isLogEnabled(iframeId));
    }

    function info(iframeId, msg) {
        output('info', iframeId, msg, isLogEnabled(iframeId));
    }

    function warn(iframeId, msg) {
        output('warn', iframeId, msg, true);
    }

    function output(type, iframeId, msg, enabled) {
        if (true === enabled && 'object' === _typeof(window.console)) {
            console[type](formatLogHeader(iframeId), msg);
        }
    }

    function iFrameListener(event) {
        function resizeIFrame() {
            function resize() {
                setSize(messageData);
                setPagePosition(iframeId);
                callback('resizedCallback', messageData);
            }

            ensureInRange('Height');
            ensureInRange('Width');

            syncResize(resize, messageData, 'init');
        }

        function processMsg() {
            var data = msg.substr(msgIdLen).split(':');

            return {
                iframe: settings[data[0]] && settings[data[0]].iframe,
                id: data[0],
                height: data[1],
                width: data[2],
                type: data[3]
            };
        }

        function ensureInRange(Dimension) {
            var max = Number(settings[iframeId]['max' + Dimension]),
                min = Number(settings[iframeId]['min' + Dimension]),
                dimension = Dimension.toLowerCase(),
                size = Number(messageData[dimension]);

            log(iframeId, 'Checking ' + dimension + ' is in range ' + min + '-' + max);

            if (size < min) {
                size = min;
                log(iframeId, 'Set ' + dimension + ' to min value');
            }

            if (size > max) {
                size = max;
                log(iframeId, 'Set ' + dimension + ' to max value');
            }

            messageData[dimension] = '' + size;
        }

        function isMessageFromIFrame() {
            function checkAllowedOrigin() {
                function checkList() {
                    var i = 0,
                        retCode = false;

                    log(iframeId, 'Checking connection is from allowed list of origins: ' + checkOrigin);

                    for (; i < checkOrigin.length; i++) {
                        if (checkOrigin[i] === origin) {
                            retCode = true;
                            break;
                        }
                    }
                    return retCode;
                }

                function checkSingle() {
                    var remoteHost = settings[iframeId] && settings[iframeId].remoteHost;
                    log(iframeId, 'Checking connection is from: ' + remoteHost);
                    return origin === remoteHost;
                }

                return checkOrigin.constructor === Array ? checkList() : checkSingle();
            }

            var origin = event.origin,
                checkOrigin = settings[iframeId] && settings[iframeId].checkOrigin;

            if (checkOrigin && '' + origin !== 'null' && !checkAllowedOrigin()) {
                throw new Error('Unexpected message received from: ' + origin + ' for ' + messageData.iframe.id + '. Message was: ' + event.data + '. This error can be disabled by setting the checkOrigin: false option or by providing of array of trusted domains.');
            }

            return true;
        }

        function isMessageForUs() {
            return msgId === ('' + msg).substr(0, msgIdLen) && msg.substr(msgIdLen).split(':')[0] in settings; //''+Protects against non-string msg
        }

        function isMessageFromMetaParent() {
            //Test if this message is from a parent above us. This is an ugly test, however, updating
            //the message format would break backwards compatibity.
            var retCode = messageData.type in { 'true': 1, 'false': 1, 'undefined': 1 };

            if (retCode) {
                log(iframeId, 'Ignoring init message from meta parent page');
            }

            return retCode;
        }

        function getMsgBody(offset) {
            return msg.substr(msg.indexOf(':') + msgHeaderLen + offset);
        }

        function forwardMsgFromIFrame(msgBody) {
            log(iframeId, 'MessageCallback passed: {iframe: ' + messageData.iframe.id + ', message: ' + msgBody + '}');
            callback('messageCallback', {
                iframe: messageData.iframe,
                message: JSON.parse(msgBody)
            });
            log(iframeId, '--');
        }

        function getPageInfo() {
            var bodyPosition = document.body.getBoundingClientRect(),
                iFramePosition = messageData.iframe.getBoundingClientRect();

            return JSON.stringify({
                iframeHeight: iFramePosition.height,
                iframeWidth: iFramePosition.width,
                clientHeight: Math.max(document.documentElement.clientHeight, window.innerHeight || 0),
                clientWidth: Math.max(document.documentElement.clientWidth, window.innerWidth || 0),
                offsetTop: parseInt(iFramePosition.top - bodyPosition.top, 10),
                offsetLeft: parseInt(iFramePosition.left - bodyPosition.left, 10),
                scrollTop: window.pageYOffset,
                scrollLeft: window.pageXOffset
            });
        }

        function sendPageInfoToIframe(iframe, iframeId) {
            function debouncedTrigger() {
                trigger('Send Page Info', 'pageInfo:' + getPageInfo(), iframe, iframeId);
            }
            debounceFrameEvents(debouncedTrigger, 32, iframeId);
        }

        function startPageInfoMonitor() {
            function setListener(type, func) {
                function sendPageInfo() {
                    if (settings[id]) {
                        sendPageInfoToIframe(settings[id].iframe, id);
                    } else {
                        stop();
                    }
                }

                ['scroll', 'resize'].forEach(function (evt) {
                    log(id, type + evt + ' listener for sendPageInfo');
                    func(window, evt, sendPageInfo);
                });
            }

            function stop() {
                setListener('Remove ', removeEventListener);
            }

            function start() {
                setListener('Add ', addEventListener);
            }

            var id = iframeId; //Create locally scoped copy of iFrame ID

            start();

            if (settings[id]) {
                settings[id].stopPageInfo = stop;
            }
        }

        function stopPageInfoMonitor() {
            if (settings[iframeId] && settings[iframeId].stopPageInfo) {
                settings[iframeId].stopPageInfo();
                delete settings[iframeId].stopPageInfo;
            }
        }

        function checkIFrameExists() {
            var retBool = true;

            if (null === messageData.iframe) {
                warn(iframeId, 'IFrame (' + messageData.id + ') not found');
                retBool = false;
            }
            return retBool;
        }

        function getElementPosition(target) {
            var iFramePosition = target.getBoundingClientRect();

            getPagePosition(iframeId);

            return {
                x: Math.floor(Number(iFramePosition.left) + Number(pagePosition.x)),
                y: Math.floor(Number(iFramePosition.top) + Number(pagePosition.y))
            };
        }

        function scrollRequestFromChild(addOffset) {
            /* istanbul ignore next */ //Not testable in Karma
            function reposition() {
                pagePosition = newPosition;
                scrollTo();
                log(iframeId, '--');
            }

            function calcOffset() {
                return {
                    x: Number(messageData.width) + offset.x,
                    y: Number(messageData.height) + offset.y
                };
            }

            function scrollParent() {
                if (window.parentIFrame) {
                    window.parentIFrame['scrollTo' + (addOffset ? 'Offset' : '')](newPosition.x, newPosition.y);
                } else {
                    warn(iframeId, 'Unable to scroll to requested position, window.parentIFrame not found');
                }
            }

            var offset = addOffset ? getElementPosition(messageData.iframe) : { x: 0, y: 0 },
                newPosition = calcOffset();

            log(iframeId, 'Reposition requested from iFrame (offset x:' + offset.x + ' y:' + offset.y + ')');

            if (window.top !== window.self) {
                scrollParent();
            } else {
                reposition();
            }
        }

        function scrollTo() {
            if (false !== callback('scrollCallback', pagePosition)) {
                setPagePosition(iframeId);
            } else {
                unsetPagePosition();
            }
        }

        function findTarget(location) {
            function jumpToTarget() {
                var jumpPosition = getElementPosition(target);

                log(iframeId, 'Moving to in page link (#' + hash + ') at x: ' + jumpPosition.x + ' y: ' + jumpPosition.y);
                pagePosition = {
                    x: jumpPosition.x,
                    y: jumpPosition.y
                };

                scrollTo();
                log(iframeId, '--');
            }

            function jumpToParent() {
                if (window.parentIFrame) {
                    window.parentIFrame.moveToAnchor(hash);
                } else {
                    log(iframeId, 'In page link #' + hash + ' not found and window.parentIFrame not found');
                }
            }

            var hash = location.split('#')[1] || '',
                hashData = decodeURIComponent(hash),
                target = document.getElementById(hashData) || document.getElementsByName(hashData)[0];

            if (target) {
                jumpToTarget();
            } else if (window.top !== window.self) {
                jumpToParent();
            } else {
                log(iframeId, 'In page link #' + hash + ' not found');
            }
        }

        function callback(funcName, val) {
            return chkCallback(iframeId, funcName, val);
        }

        function actionMsg() {

            if (settings[iframeId] && settings[iframeId].firstRun) firstRun();

            switch (messageData.type) {
                case 'close':
                    if (settings[iframeId].closeRequestCallback) chkCallback(iframeId, 'closeRequestCallback', settings[iframeId].iframe);else closeIFrame(messageData.iframe);
                    break;
                case 'message':
                    forwardMsgFromIFrame(getMsgBody(6));
                    break;
                case 'scrollTo':
                    scrollRequestFromChild(false);
                    break;
                case 'scrollToOffset':
                    scrollRequestFromChild(true);
                    break;
                case 'pageInfo':
                    sendPageInfoToIframe(settings[iframeId] && settings[iframeId].iframe, iframeId);
                    startPageInfoMonitor();
                    break;
                case 'pageInfoStop':
                    stopPageInfoMonitor();
                    break;
                case 'inPageLink':
                    findTarget(getMsgBody(9));
                    break;
                case 'reset':
                    resetIFrame(messageData);
                    break;
                case 'init':
                    resizeIFrame();
                    callback('initCallback', messageData.iframe);
                    break;
                default:
                    resizeIFrame();
            }
        }

        function hasSettings(iframeId) {
            var retBool = true;

            if (!settings[iframeId]) {
                retBool = false;
                warn(messageData.type + ' No settings for ' + iframeId + '. Message was: ' + msg);
            }

            return retBool;
        }

        function iFrameReadyMsgReceived() {
            for (var iframeId in settings) {
                trigger('iFrame requested init', createOutgoingMsg(iframeId), document.getElementById(iframeId), iframeId);
            }
        }

        function firstRun() {
            if (settings[iframeId]) {
                settings[iframeId].firstRun = false;
            }
        }

        function clearWarningTimeout() {
            if (settings[iframeId]) {
                clearTimeout(settings[iframeId].msgTimeout);
                settings[iframeId].warningTimeout = 0;
            }
        }

        var msg = event.data,
            messageData = {},
            iframeId = null;

        if ('[iFrameResizerChild]Ready' === msg) {
            iFrameReadyMsgReceived();
        } else if (isMessageForUs()) {
            messageData = processMsg();
            iframeId = logId = messageData.id;
            if (settings[iframeId]) {
                settings[iframeId].loaded = true;
            }

            if (!isMessageFromMetaParent() && hasSettings(iframeId)) {
                log(iframeId, 'Received: ' + msg);

                if (checkIFrameExists() && isMessageFromIFrame()) {
                    actionMsg();
                }
            }
        } else {
            info(iframeId, 'Ignored: ' + msg);
        }
    }

    function chkCallback(iframeId, funcName, val) {
        var func = null,
            retVal = null;

        if (settings[iframeId]) {
            func = settings[iframeId][funcName];

            if ('function' === typeof func) {
                retVal = func(val);
            } else {
                throw new TypeError(funcName + ' on iFrame[' + iframeId + '] is not a function');
            }
        }

        return retVal;
    }

    function closeIFrame(iframe) {
        var iframeId = iframe.id;

        log(iframeId, 'Removing iFrame: ' + iframeId);
        if (iframe.parentNode) {
            iframe.parentNode.removeChild(iframe);
        }
        chkCallback(iframeId, 'closedCallback', iframeId);
        log(iframeId, '--');
        delete settings[iframeId];
    }

    function getPagePosition(iframeId) {
        if (null === pagePosition) {
            pagePosition = {
                x: window.pageXOffset !== undefined ? window.pageXOffset : document.documentElement.scrollLeft,
                y: window.pageYOffset !== undefined ? window.pageYOffset : document.documentElement.scrollTop
            };
            log(iframeId, 'Get page position: ' + pagePosition.x + ',' + pagePosition.y);
        }
    }

    function setPagePosition(iframeId) {
        if (null !== pagePosition) {
            window.scrollTo(pagePosition.x, pagePosition.y);
            log(iframeId, 'Set page position: ' + pagePosition.x + ',' + pagePosition.y);
            unsetPagePosition();
        }
    }

    function unsetPagePosition() {
        pagePosition = null;
    }

    function resetIFrame(messageData) {
        function reset() {
            setSize(messageData);
            trigger('reset', 'reset', messageData.iframe, messageData.id);
        }

        log(messageData.id, 'Size reset requested by ' + ('init' === messageData.type ? 'host page' : 'iFrame'));
        getPagePosition(messageData.id);
        syncResize(reset, messageData, 'reset');
    }

    function setSize(messageData) {
        function setDimension(dimension) {
            messageData.iframe.style[dimension] = messageData[dimension] + 'px';
            log(messageData.id, 'IFrame (' + iframeId + ') ' + dimension + ' set to ' + messageData[dimension] + 'px');
        }

        function chkZero(dimension) {
            //FireFox sets dimension of hidden iFrames to zero.
            //So if we detect that set up an event to check for
            //when iFrame becomes visible.

            /* istanbul ignore next */ //Not testable in PhantomJS
            if (!hiddenCheckEnabled && '0' === messageData[dimension]) {
                hiddenCheckEnabled = true;
                log(iframeId, 'Hidden iFrame detected, creating visibility listener');
                fixHiddenIFrames();
            }
        }

        function processDimension(dimension) {
            setDimension(dimension);
            chkZero(dimension);
        }

        var iframeId = messageData.iframe.id;

        if (settings[iframeId]) {
            if (settings[iframeId].sizeHeight) {
                processDimension('height');
            }
            if (settings[iframeId].sizeWidth) {
                processDimension('width');
            }
        }
    }

    function syncResize(func, messageData, doNotSync) {
        /* istanbul ignore if */ //Not testable in PhantomJS
        if (doNotSync !== messageData.type && requestAnimationFrame) {
            log(messageData.id, 'Requesting animation frame');
            requestAnimationFrame(func);
        } else {
            func();
        }
    }

    function trigger(calleeMsg, msg, iframe, id, noResponseWarning) {
        function postMessageToIFrame() {
            var target = settings[id] && settings[id].targetOrigin;
            log(id, '[' + calleeMsg + '] Sending msg to iframe[' + id + '] (' + msg + ') targetOrigin: ' + target);
            iframe.contentWindow.postMessage(msgId + msg, target);
        }

        function iFrameNotFound() {
            warn(id, '[' + calleeMsg + '] IFrame(' + id + ') not found');
        }

        function chkAndSend() {
            if (iframe && 'contentWindow' in iframe && null !== iframe.contentWindow) {
                //Null test for PhantomJS
                postMessageToIFrame();
            } else {
                iFrameNotFound();
            }
        }

        function warnOnNoResponse() {
            function warning() {
                if (settings[id] && !settings[id].loaded && !errorShown) {
                    errorShown = true;
                    warn(id, 'IFrame has not responded within ' + settings[id].warningTimeout / 1000 + ' seconds. Check iFrameResizer.contentWindow.js has been loaded in iFrame. This message can be ingored if everything is working, or you can set the warningTimeout option to a higher value or zero to suppress this warning.');
                }
            }

            if (!!noResponseWarning && settings[id] && !!settings[id].warningTimeout) {
                settings[id].msgTimeout = setTimeout(warning, settings[id].warningTimeout);
            }
        }

        var errorShown = false;

        id = id || iframe.id;

        if (settings[id]) {
            chkAndSend();
            warnOnNoResponse();
        }
    }

    function createOutgoingMsg(iframeId) {
        return iframeId + ':' + settings[iframeId].bodyMarginV1 + ':' + settings[iframeId].sizeWidth + ':' + settings[iframeId].log + ':' + settings[iframeId].interval + ':' + settings[iframeId].enablePublicMethods + ':' + settings[iframeId].autoResize + ':' + settings[iframeId].bodyMargin + ':' + settings[iframeId].heightCalculationMethod + ':' + settings[iframeId].bodyBackground + ':' + settings[iframeId].bodyPadding + ':' + settings[iframeId].tolerance + ':' + settings[iframeId].inPageLinks + ':' + settings[iframeId].resizeFrom + ':' + settings[iframeId].widthCalculationMethod;
    }

    function setupIFrame(iframe, options) {
        function setLimits() {
            function addStyle(style) {
                if (Infinity !== settings[iframeId][style] && 0 !== settings[iframeId][style]) {
                    iframe.style[style] = settings[iframeId][style] + 'px';
                    log(iframeId, 'Set ' + style + ' = ' + settings[iframeId][style] + 'px');
                }
            }

            function chkMinMax(dimension) {
                if (settings[iframeId]['min' + dimension] > settings[iframeId]['max' + dimension]) {
                    throw new Error('Value for min' + dimension + ' can not be greater than max' + dimension);
                }
            }

            chkMinMax('Height');
            chkMinMax('Width');

            addStyle('maxHeight');
            addStyle('minHeight');
            addStyle('maxWidth');
            addStyle('minWidth');
        }

        function newId() {
            var id = options && options.id || defaults.id + count++;
            if (null !== document.getElementById(id)) {
                id = id + count++;
            }
            return id;
        }

        function ensureHasId(iframeId) {
            logId = iframeId;
            if ('' === iframeId) {
                iframe.id = iframeId = newId();
                logEnabled = (options || {}).log;
                logId = iframeId;
                log(iframeId, 'Added missing iframe ID: ' + iframeId + ' (' + iframe.src + ')');
            }

            return iframeId;
        }

        function setScrolling() {
            log(iframeId, 'IFrame scrolling ' + (settings[iframeId] && settings[iframeId].scrolling ? 'enabled' : 'disabled') + ' for ' + iframeId);
            iframe.style.overflow = false === (settings[iframeId] && settings[iframeId].scrolling) ? 'hidden' : 'auto';
            switch (settings[iframeId] && settings[iframeId].scrolling) {
                case true:
                    iframe.scrolling = 'yes';
                    break;
                case false:
                    iframe.scrolling = 'no';
                    break;
                default:
                    iframe.scrolling = settings[iframeId] ? settings[iframeId].scrolling : 'no';
            }
        }

        //The V1 iFrame script expects an int, where as in V2 expects a CSS
        //string value such as '1px 3em', so if we have an int for V2, set V1=V2
        //and then convert V2 to a string PX value.
        function setupBodyMarginValues() {
            if ('number' === typeof (settings[iframeId] && settings[iframeId].bodyMargin) || '0' === (settings[iframeId] && settings[iframeId].bodyMargin)) {
                settings[iframeId].bodyMarginV1 = settings[iframeId].bodyMargin;
                settings[iframeId].bodyMargin = '' + settings[iframeId].bodyMargin + 'px';
            }
        }

        function checkReset() {
            // Reduce scope of firstRun to function, because IE8's JS execution
            // context stack is borked and this value gets externally
            // changed midway through running this function!!!
            var firstRun = settings[iframeId] && settings[iframeId].firstRun,
                resetRequertMethod = settings[iframeId] && settings[iframeId].heightCalculationMethod in resetRequiredMethods;

            if (!firstRun && resetRequertMethod) {
                resetIFrame({ iframe: iframe, height: 0, width: 0, type: 'init' });
            }
        }

        function setupIFrameObject() {
            if (Function.prototype.bind && settings[iframeId]) {
                //Ignore unpolyfilled IE8.
                settings[iframeId].iframe.iFrameResizer = {

                    close: closeIFrame.bind(null, settings[iframeId].iframe),

                    resize: trigger.bind(null, 'Window resize', 'resize', settings[iframeId].iframe),

                    moveToAnchor: function moveToAnchor(anchor) {
                        trigger('Move to anchor', 'moveToAnchor:' + anchor, settings[iframeId].iframe, iframeId);
                    },

                    sendMessage: function sendMessage(message) {
                        message = JSON.stringify(message);
                        trigger('Send Message', 'message:' + message, settings[iframeId].iframe, iframeId);
                    }
                };
            }
        }

        //We have to call trigger twice, as we can not be sure if all
        //iframes have completed loading when this code runs. The
        //event listener also catches the page changing in the iFrame.
        function init(msg) {
            function iFrameLoaded() {
                trigger('iFrame.onload', msg, iframe, undefined, true);
                checkReset();
            }

            addEventListener(iframe, 'load', iFrameLoaded);
            trigger('init', msg, iframe, undefined, true);
        }

        function checkOptions(options) {
            if ('object' !== (typeof options === 'undefined' ? 'undefined' : _typeof(options))) {
                throw new TypeError('Options is not an object');
            }
        }

        function copyOptions(options) {
            for (var option in defaults) {
                if (defaults.hasOwnProperty(option)) {
                    settings[iframeId][option] = options.hasOwnProperty(option) ? options[option] : defaults[option];
                }
            }
        }

        function getTargetOrigin(remoteHost) {
            return '' === remoteHost || 'file://' === remoteHost ? '*' : remoteHost;
        }

        function processOptions(options) {
            options = options || {};
            settings[iframeId] = {
                firstRun: true,
                iframe: iframe,
                remoteHost: iframe.src.split('/').slice(0, 3).join('/')
            };

            checkOptions(options);
            copyOptions(options);

            if (settings[iframeId]) {
                settings[iframeId].targetOrigin = true === settings[iframeId].checkOrigin ? getTargetOrigin(settings[iframeId].remoteHost) : '*';
            }
        }

        function beenHere() {
            return iframeId in settings && 'iFrameResizer' in iframe;
        }

        var iframeId = ensureHasId(iframe.id);

        if (!beenHere()) {
            processOptions(options);
            setScrolling();
            setLimits();
            setupBodyMarginValues();
            init(createOutgoingMsg(iframeId));
            setupIFrameObject();
        } else {
            warn(iframeId, 'Ignored iFrame, already setup.');
        }
    }

    function debouce(fn, time) {
        if (null === timer) {
            timer = setTimeout(function () {
                timer = null;
                fn();
            }, time);
        }
    }

    var frameTimer = {};
    function debounceFrameEvents(fn, time, frameId) {
        if (!frameTimer[frameId]) {
            frameTimer[frameId] = setTimeout(function () {
                frameTimer[frameId] = null;
                fn();
            }, time);
        }
    }

    /* istanbul ignore next */ //Not testable in PhantomJS
    function fixHiddenIFrames() {
        function checkIFrames() {
            function checkIFrame(settingId) {
                function chkDimension(dimension) {
                    return '0px' === (settings[settingId] && settings[settingId].iframe.style[dimension]);
                }

                function isVisible(el) {
                    return null !== el.offsetParent;
                }

                if (settings[settingId] && isVisible(settings[settingId].iframe) && (chkDimension('height') || chkDimension('width'))) {
                    trigger('Visibility change', 'resize', settings[settingId].iframe, settingId);
                }
            }

            for (var settingId in settings) {
                checkIFrame(settingId);
            }
        }

        function mutationObserved(mutations) {
            log('window', 'Mutation observed: ' + mutations[0].target + ' ' + mutations[0].type);
            debouce(checkIFrames, 16);
        }

        function createMutationObserver() {
            var target = document.querySelector('body'),
                config = {
                attributes: true,
                attributeOldValue: false,
                characterData: true,
                characterDataOldValue: false,
                childList: true,
                subtree: true
            },
                observer = new MutationObserver(mutationObserved);

            observer.observe(target, config);
        }

        var MutationObserver = window.MutationObserver || window.WebKitMutationObserver;

        if (MutationObserver) createMutationObserver();
    }

    function resizeIFrames(event) {
        function resize() {
            sendTriggerMsg('Window ' + event, 'resize');
        }

        log('window', 'Trigger event: ' + event);
        debouce(resize, 16);
    }

    /* istanbul ignore next */ //Not testable in PhantomJS
    function tabVisible() {
        function resize() {
            sendTriggerMsg('Tab Visable', 'resize');
        }

        if ('hidden' !== document.visibilityState) {
            log('document', 'Trigger event: Visiblity change');
            debouce(resize, 16);
        }
    }

    function sendTriggerMsg(eventName, event) {
        function isIFrameResizeEnabled(iframeId) {
            return settings[iframeId] && 'parent' === settings[iframeId].resizeFrom && settings[iframeId].autoResize && !settings[iframeId].firstRun;
        }

        for (var iframeId in settings) {
            if (isIFrameResizeEnabled(iframeId)) {
                trigger(eventName, event, document.getElementById(iframeId), iframeId);
            }
        }
    }

    function setupEventListeners() {
        addEventListener(window, 'message', iFrameListener);

        addEventListener(window, 'resize', function () {
            resizeIFrames('resize');
        });

        addEventListener(document, 'visibilitychange', tabVisible);
        addEventListener(document, '-webkit-visibilitychange', tabVisible); //Andriod 4.4
        addEventListener(window, 'focusin', function () {
            resizeIFrames('focus');
        }); //IE8-9
        addEventListener(window, 'focus', function () {
            resizeIFrames('focus');
        });
    }

    function factory() {
        function init(options, element) {
            function chkType() {
                if (!element.tagName) {
                    throw new TypeError('Object is not a valid DOM element');
                } else if ('IFRAME' !== element.tagName.toUpperCase()) {
                    throw new TypeError('Expected <IFRAME> tag, found <' + element.tagName + '>');
                }
            }

            if (element) {
                chkType();
                setupIFrame(element, options);
                iFrames.push(element);
            }
        }

        function warnDeprecatedOptions(options) {
            if (options && options.enablePublicMethods) {
                warn('enablePublicMethods option has been removed, public methods are now always available in the iFrame');
            }
        }

        var iFrames;

        setupRequestAnimationFrame();
        setupEventListeners();

        return function iFrameResizeF(options, target) {
            iFrames = []; //Only return iFrames past in on this call

            warnDeprecatedOptions(options);

            switch (typeof target === 'undefined' ? 'undefined' : _typeof(target)) {
                case 'undefined':
                case 'string':
                    Array.prototype.forEach.call(document.querySelectorAll(target || 'iframe'), init.bind(undefined, options));
                    break;
                case 'object':
                    init(options, target);
                    break;
                default:
                    throw new TypeError('Unexpected data type (' + (typeof target === 'undefined' ? 'undefined' : _typeof(target)) + ')');
            }

            return iFrames;
        };
    }

    function createJQueryPublicMethod($) {
        if (!$.fn) {
            info('', 'Unable to bind to jQuery, it is not fully loaded.');
        } else if (!$.fn.iFrameResize) {
            $.fn.iFrameResize = function $iFrameResizeF(options) {
                function init(index, element) {
                    setupIFrame(element, options);
                }

                return this.filter('iframe').each(init).end();
            };
        }
    }

    if (window.jQuery) {
        createJQueryPublicMethod(window.jQuery);
    }

    if (typeof define === 'function' && define.amd) {
        define([], factory);
    } else if ((typeof module === 'undefined' ? 'undefined' : _typeof(module)) === 'object' && _typeof(module.exports) === 'object') {
        //Node for browserfy
        module.exports = factory();
    } else {
        window.iFrameResize = window.iFrameResize || factory();
    }
})();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkFkbWluL0phdmFzY3JpcHQvdmVuZG9yL2lmcmFtZV9yZXNpemVyLmpzIl0sIm5hbWVzIjpbInVuZGVmaW5lZCIsIndpbmRvdyIsImNvdW50IiwibG9nRW5hYmxlZCIsImhpZGRlbkNoZWNrRW5hYmxlZCIsIm1zZ0hlYWRlciIsIm1zZ0hlYWRlckxlbiIsImxlbmd0aCIsIm1zZ0lkIiwibXNnSWRMZW4iLCJwYWdlUG9zaXRpb24iLCJyZXF1ZXN0QW5pbWF0aW9uRnJhbWUiLCJyZXNldFJlcXVpcmVkTWV0aG9kcyIsIm1heCIsInNjcm9sbCIsImJvZHlTY3JvbGwiLCJkb2N1bWVudEVsZW1lbnRTY3JvbGwiLCJzZXR0aW5ncyIsInRpbWVyIiwibG9nSWQiLCJkZWZhdWx0cyIsImF1dG9SZXNpemUiLCJib2R5QmFja2dyb3VuZCIsImJvZHlNYXJnaW4iLCJib2R5TWFyZ2luVjEiLCJib2R5UGFkZGluZyIsImNoZWNrT3JpZ2luIiwiaW5QYWdlTGlua3MiLCJlbmFibGVQdWJsaWNNZXRob2RzIiwiaGVpZ2h0Q2FsY3VsYXRpb25NZXRob2QiLCJpZCIsImludGVydmFsIiwibG9nIiwibWF4SGVpZ2h0IiwiSW5maW5pdHkiLCJtYXhXaWR0aCIsIm1pbkhlaWdodCIsIm1pbldpZHRoIiwicmVzaXplRnJvbSIsInNjcm9sbGluZyIsInNpemVIZWlnaHQiLCJzaXplV2lkdGgiLCJ3YXJuaW5nVGltZW91dCIsInRvbGVyYW5jZSIsIndpZHRoQ2FsY3VsYXRpb25NZXRob2QiLCJjbG9zZWRDYWxsYmFjayIsImluaXRDYWxsYmFjayIsIm1lc3NhZ2VDYWxsYmFjayIsIndhcm4iLCJyZXNpemVkQ2FsbGJhY2siLCJzY3JvbGxDYWxsYmFjayIsImFkZEV2ZW50TGlzdGVuZXIiLCJvYmoiLCJldnQiLCJmdW5jIiwiYXR0YWNoRXZlbnQiLCJyZW1vdmVFdmVudExpc3RlbmVyIiwiZWwiLCJkZXRhY2hFdmVudCIsInNldHVwUmVxdWVzdEFuaW1hdGlvbkZyYW1lIiwidmVuZG9ycyIsIngiLCJnZXRNeUlEIiwiaWZyYW1lSWQiLCJyZXRTdHIiLCJ0b3AiLCJzZWxmIiwicGFyZW50SUZyYW1lIiwiZ2V0SWQiLCJmb3JtYXRMb2dIZWFkZXIiLCJpc0xvZ0VuYWJsZWQiLCJtc2ciLCJvdXRwdXQiLCJpbmZvIiwidHlwZSIsImVuYWJsZWQiLCJjb25zb2xlIiwiaUZyYW1lTGlzdGVuZXIiLCJldmVudCIsInJlc2l6ZUlGcmFtZSIsInJlc2l6ZSIsInNldFNpemUiLCJtZXNzYWdlRGF0YSIsInNldFBhZ2VQb3NpdGlvbiIsImNhbGxiYWNrIiwiZW5zdXJlSW5SYW5nZSIsInN5bmNSZXNpemUiLCJwcm9jZXNzTXNnIiwiZGF0YSIsInN1YnN0ciIsInNwbGl0IiwiaWZyYW1lIiwiaGVpZ2h0Iiwid2lkdGgiLCJEaW1lbnNpb24iLCJOdW1iZXIiLCJtaW4iLCJkaW1lbnNpb24iLCJ0b0xvd2VyQ2FzZSIsInNpemUiLCJpc01lc3NhZ2VGcm9tSUZyYW1lIiwiY2hlY2tBbGxvd2VkT3JpZ2luIiwiY2hlY2tMaXN0IiwiaSIsInJldENvZGUiLCJvcmlnaW4iLCJjaGVja1NpbmdsZSIsInJlbW90ZUhvc3QiLCJjb25zdHJ1Y3RvciIsIkFycmF5IiwiRXJyb3IiLCJpc01lc3NhZ2VGb3JVcyIsImlzTWVzc2FnZUZyb21NZXRhUGFyZW50IiwiZ2V0TXNnQm9keSIsIm9mZnNldCIsImluZGV4T2YiLCJmb3J3YXJkTXNnRnJvbUlGcmFtZSIsIm1zZ0JvZHkiLCJtZXNzYWdlIiwiSlNPTiIsInBhcnNlIiwiZ2V0UGFnZUluZm8iLCJib2R5UG9zaXRpb24iLCJkb2N1bWVudCIsImJvZHkiLCJnZXRCb3VuZGluZ0NsaWVudFJlY3QiLCJpRnJhbWVQb3NpdGlvbiIsInN0cmluZ2lmeSIsImlmcmFtZUhlaWdodCIsImlmcmFtZVdpZHRoIiwiY2xpZW50SGVpZ2h0IiwiTWF0aCIsImRvY3VtZW50RWxlbWVudCIsImlubmVySGVpZ2h0IiwiY2xpZW50V2lkdGgiLCJpbm5lcldpZHRoIiwib2Zmc2V0VG9wIiwicGFyc2VJbnQiLCJvZmZzZXRMZWZ0IiwibGVmdCIsInNjcm9sbFRvcCIsInBhZ2VZT2Zmc2V0Iiwic2Nyb2xsTGVmdCIsInBhZ2VYT2Zmc2V0Iiwic2VuZFBhZ2VJbmZvVG9JZnJhbWUiLCJkZWJvdW5jZWRUcmlnZ2VyIiwidHJpZ2dlciIsImRlYm91bmNlRnJhbWVFdmVudHMiLCJzdGFydFBhZ2VJbmZvTW9uaXRvciIsInNldExpc3RlbmVyIiwic2VuZFBhZ2VJbmZvIiwic3RvcCIsImZvckVhY2giLCJzdGFydCIsInN0b3BQYWdlSW5mbyIsInN0b3BQYWdlSW5mb01vbml0b3IiLCJjaGVja0lGcmFtZUV4aXN0cyIsInJldEJvb2wiLCJnZXRFbGVtZW50UG9zaXRpb24iLCJ0YXJnZXQiLCJnZXRQYWdlUG9zaXRpb24iLCJmbG9vciIsInkiLCJzY3JvbGxSZXF1ZXN0RnJvbUNoaWxkIiwiYWRkT2Zmc2V0IiwicmVwb3NpdGlvbiIsIm5ld1Bvc2l0aW9uIiwic2Nyb2xsVG8iLCJjYWxjT2Zmc2V0Iiwic2Nyb2xsUGFyZW50IiwidW5zZXRQYWdlUG9zaXRpb24iLCJmaW5kVGFyZ2V0IiwibG9jYXRpb24iLCJqdW1wVG9UYXJnZXQiLCJqdW1wUG9zaXRpb24iLCJoYXNoIiwianVtcFRvUGFyZW50IiwibW92ZVRvQW5jaG9yIiwiaGFzaERhdGEiLCJkZWNvZGVVUklDb21wb25lbnQiLCJnZXRFbGVtZW50QnlJZCIsImdldEVsZW1lbnRzQnlOYW1lIiwiZnVuY05hbWUiLCJ2YWwiLCJjaGtDYWxsYmFjayIsImFjdGlvbk1zZyIsImZpcnN0UnVuIiwiY2xvc2VSZXF1ZXN0Q2FsbGJhY2siLCJjbG9zZUlGcmFtZSIsInJlc2V0SUZyYW1lIiwiaGFzU2V0dGluZ3MiLCJpRnJhbWVSZWFkeU1zZ1JlY2VpdmVkIiwiY3JlYXRlT3V0Z29pbmdNc2ciLCJjbGVhcldhcm5pbmdUaW1lb3V0IiwiY2xlYXJUaW1lb3V0IiwibXNnVGltZW91dCIsImxvYWRlZCIsInJldFZhbCIsIlR5cGVFcnJvciIsInBhcmVudE5vZGUiLCJyZW1vdmVDaGlsZCIsInJlc2V0Iiwic2V0RGltZW5zaW9uIiwic3R5bGUiLCJjaGtaZXJvIiwiZml4SGlkZGVuSUZyYW1lcyIsInByb2Nlc3NEaW1lbnNpb24iLCJkb05vdFN5bmMiLCJjYWxsZWVNc2ciLCJub1Jlc3BvbnNlV2FybmluZyIsInBvc3RNZXNzYWdlVG9JRnJhbWUiLCJ0YXJnZXRPcmlnaW4iLCJjb250ZW50V2luZG93IiwicG9zdE1lc3NhZ2UiLCJpRnJhbWVOb3RGb3VuZCIsImNoa0FuZFNlbmQiLCJ3YXJuT25Ob1Jlc3BvbnNlIiwid2FybmluZyIsImVycm9yU2hvd24iLCJzZXRUaW1lb3V0Iiwic2V0dXBJRnJhbWUiLCJvcHRpb25zIiwic2V0TGltaXRzIiwiYWRkU3R5bGUiLCJjaGtNaW5NYXgiLCJuZXdJZCIsImVuc3VyZUhhc0lkIiwic3JjIiwic2V0U2Nyb2xsaW5nIiwib3ZlcmZsb3ciLCJzZXR1cEJvZHlNYXJnaW5WYWx1ZXMiLCJjaGVja1Jlc2V0IiwicmVzZXRSZXF1ZXJ0TWV0aG9kIiwic2V0dXBJRnJhbWVPYmplY3QiLCJGdW5jdGlvbiIsInByb3RvdHlwZSIsImJpbmQiLCJpRnJhbWVSZXNpemVyIiwiY2xvc2UiLCJhbmNob3IiLCJzZW5kTWVzc2FnZSIsImluaXQiLCJpRnJhbWVMb2FkZWQiLCJjaGVja09wdGlvbnMiLCJjb3B5T3B0aW9ucyIsIm9wdGlvbiIsImhhc093blByb3BlcnR5IiwiZ2V0VGFyZ2V0T3JpZ2luIiwicHJvY2Vzc09wdGlvbnMiLCJzbGljZSIsImpvaW4iLCJiZWVuSGVyZSIsImRlYm91Y2UiLCJmbiIsInRpbWUiLCJmcmFtZVRpbWVyIiwiZnJhbWVJZCIsImNoZWNrSUZyYW1lcyIsImNoZWNrSUZyYW1lIiwic2V0dGluZ0lkIiwiY2hrRGltZW5zaW9uIiwiaXNWaXNpYmxlIiwib2Zmc2V0UGFyZW50IiwibXV0YXRpb25PYnNlcnZlZCIsIm11dGF0aW9ucyIsImNyZWF0ZU11dGF0aW9uT2JzZXJ2ZXIiLCJxdWVyeVNlbGVjdG9yIiwiY29uZmlnIiwiYXR0cmlidXRlcyIsImF0dHJpYnV0ZU9sZFZhbHVlIiwiY2hhcmFjdGVyRGF0YSIsImNoYXJhY3RlckRhdGFPbGRWYWx1ZSIsImNoaWxkTGlzdCIsInN1YnRyZWUiLCJvYnNlcnZlciIsIk11dGF0aW9uT2JzZXJ2ZXIiLCJvYnNlcnZlIiwiV2ViS2l0TXV0YXRpb25PYnNlcnZlciIsInJlc2l6ZUlGcmFtZXMiLCJzZW5kVHJpZ2dlck1zZyIsInRhYlZpc2libGUiLCJ2aXNpYmlsaXR5U3RhdGUiLCJldmVudE5hbWUiLCJpc0lGcmFtZVJlc2l6ZUVuYWJsZWQiLCJzZXR1cEV2ZW50TGlzdGVuZXJzIiwiZmFjdG9yeSIsImVsZW1lbnQiLCJjaGtUeXBlIiwidGFnTmFtZSIsInRvVXBwZXJDYXNlIiwiaUZyYW1lcyIsInB1c2giLCJ3YXJuRGVwcmVjYXRlZE9wdGlvbnMiLCJpRnJhbWVSZXNpemVGIiwiY2FsbCIsInF1ZXJ5U2VsZWN0b3JBbGwiLCJjcmVhdGVKUXVlcnlQdWJsaWNNZXRob2QiLCIkIiwiaUZyYW1lUmVzaXplIiwiJGlGcmFtZVJlc2l6ZUYiLCJpbmRleCIsImZpbHRlciIsImVhY2giLCJlbmQiLCJqUXVlcnkiLCJkZWZpbmUiLCJhbWQiLCJtb2R1bGUiLCJleHBvcnRzIl0sIm1hcHBpbmdzIjoiOzs7O0FBQUE7Ozs7Ozs7Ozs7QUFXQSxDQUFDLENBQUMsVUFBU0EsU0FBVCxFQUFvQjtBQUNsQjs7QUFFQSxRQUFHLE9BQU9DLE1BQVAsS0FBa0IsV0FBckIsRUFBa0MsT0FIaEIsQ0FHd0I7O0FBRTFDLFFBQ0lDLFFBQXdCLENBRDVCO0FBQUEsUUFFSUMsYUFBd0IsS0FGNUI7QUFBQSxRQUdJQyxxQkFBd0IsS0FINUI7QUFBQSxRQUlJQyxZQUF3QixTQUo1QjtBQUFBLFFBS0lDLGVBQXdCRCxVQUFVRSxNQUx0QztBQUFBLFFBTUlDLFFBQXdCLGVBTjVCO0FBQUEsUUFNNkM7QUFDekNDLGVBQXdCRCxNQUFNRCxNQVBsQztBQUFBLFFBUUlHLGVBQXdCLElBUjVCO0FBQUEsUUFTSUMs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QixVQUFsQixJQUFnQ0EsT0FBT0MsR0FBM0MsRUFBZ0Q7QUFDNUNELGVBQU8sRUFBUCxFQUFVcEIsT0FBVjtBQUNILEtBRkQsTUFFTyxJQUFJLFFBQU9zQixNQUFQLHlDQUFPQSxNQUFQLE9BQWtCLFFBQWxCLElBQThCLFFBQU9BLE9BQU9DLE9BQWQsTUFBMEIsUUFBNUQsRUFBc0U7QUFBRTtBQUMzRUQsZUFBT0MsT0FBUCxHQUFpQnZCLFNBQWpCO0FBQ0gsS0FGTSxNQUVBO0FBQ0g1UixlQUFPeVMsWUFBUCxHQUFzQnpTLE9BQU95UyxZQUFQLElBQXVCYixTQUE3QztBQUNIO0FBRUosQ0F6aENBIiwiZmlsZSI6IkFkbWluL0phdmFzY3JpcHQvdmVuZG9yL2lmcmFtZV9yZXNpemVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLypcbiAqIEZpbGU6IGlmcmFtZVJlc2l6ZXIuanNcbiAqIERlc2M6IEZvcmNlIGlmcmFtZXMgdG8gc2l6ZSB0byBjb250ZW50LlxuICogUmVxdWlyZXM6IGlmcmFtZVJlc2l6ZXIuY29udGVudFdpbmRvdy5qcyB0byBiZSBsb2FkZWQgaW50byB0aGUgdGFyZ2V0IGZyYW1lLlxuICogRG9jOiBodHRwczovL2dpdGh1Yi5jb20vZGF2aWRqYnJhZHNoYXcvaWZyYW1lLXJlc2l6ZXJcbiAqIEF1dGhvcjogRGF2aWQgSi4gQnJhZHNoYXcgLSBkYXZlQGJyYWRzaGF3Lm5ldFxuICogQ29udHJpYnV0b3I6IEp1cmUgTWF2IC0ganVyZS5tYXZAZ21haWwuY29tXG4gKiBDb250cmlidXRvcjogUmVlZCBEYWRvdW5lIC0gcmVlZEBkYWRvdW5lLmNvbVxuICovXG5cblxuOyhmdW5jdGlvbih1bmRlZmluZWQpIHtcbiAgICAndXNlIHN0cmljdCc7XG5cbiAgICBpZih0eXBlb2Ygd2luZG93ID09PSAndW5kZWZpbmVkJykgcmV0dXJuOyAvLyBkb24ndCBydW4gZm9yIHNlcnZlciBzaWRlIHJlbmRlclxuXG4gICAgdmFyXG4gICAgICAgIGNvdW50ICAgICAgICAgICAgICAgICA9IDAsXG4gICAgICAgIGxvZ0VuYWJsZWQgICAgICAgICAgICA9IGZhbHNlLFxuICAgICAgICBoaWRkZW5DaGVja0VuYWJsZWQgICAgPSBmYWxzZSxcbiAgICAgICAgbXNnSGVhZGVyICAgICAgICAgICAgID0gJ21lc3NhZ2UnLFxuICAgICAgICBtc2dIZWFkZXJMZW4gICAgICAgICAgPSBtc2dIZWFkZXIubGVuZ3RoLFxuICAgICAgICBtc2dJZCAgICAgICAgICAgICAgICAgPSAnW2lGcmFtZVNpemVyXScsIC8vTXVzdCBtYXRjaCBpZnJhbWUgbXNnIElEXG4gICAgICAgIG1zZ0lkTGVuICAgICAgICAgICAgICA9IG1zZ0lkLmxlbmd0aCxcbiAgICAgICAgcGFnZVBvc2l0aW9uICAgICAgICAgID0gbnVsbCxcbiAgICAgICAgcmVxdWVzdEFuaW1hdGlvbkZyYW1lID0gd2luZG93LnJlcXVlc3RBbmltYXRpb25GcmFtZSxcbiAgICAgICAgcmVzZXRSZXF1aXJlZE1ldGhvZHMgID0ge21heDoxLHNjcm9sbDoxLGJvZHlTY3JvbGw6MSxkb2N1bWVudEVsZW1lbnRTY3JvbGw6MX0sXG4gICAgICAgIHNldHRpbmdzICAgICAgICAgICAgICA9IHt9LFxuICAgICAgICB0aW1lciAgICAgICAgICAgICAgICAgPSBudWxsLFxuICAgICAgICBsb2dJZCAgICAgICAgICAgICAgICAgPSAnSG9zdCBQYWdlJyxcblxuICAgICAgICBkZWZhdWx0cyAgICAgICAgICAgICAgPSB7XG4gICAgICAgICAgICBhdXRvUmVzaXplICAgICAgICAgICAgICAgIDogdHJ1ZSxcbiAgICAgICAgICAgIGJvZHlCYWNrZ3JvdW5kICAgICAgICAgICAgOiBudWxsLFxuICAgICAgICAgICAgYm9keU1hcmdpbiAgICAgICAgICAgICAgICA6IG51bGwsXG4gICAgICAgICAgICBib2R5TWFyZ2luVjEgICAgICAgICAgICAgIDogOCxcbiAgICAgICAgICAgIGJvZHlQYWRkaW5nICAgICAgICAgICAgICAgOiBudWxsLFxuICAgICAgICAgICAgY2hlY2tPcmlnaW4gICAgICAgICAgICAgICA6IHRydWUsXG4gICAgICAgICAgICBpblBhZ2VMaW5rcyAgICAgICAgICAgICAgIDogZmFsc2UsXG4gICAgICAgICAgICBlbmFibGVQdWJsaWNNZXRob2RzICAgICAgIDogdHJ1ZSxcbiAgICAgICAgICAgIGhlaWdodENhbGN1bGF0aW9uTWV0aG9kICAgOiAnYm9keU9mZnNldCcsXG4gICAgICAgICAgICBpZCAgICAgICAgICAgICAgICAgICAgICAgIDogJ2lGcmFtZVJlc2l6ZXInLFxuICAgICAgICAgICAgaW50ZXJ2YWwgICAgICAgICAgICAgICAgICA6IDMyLFxuICAgICAgICAgICAgbG9nICAgICAgICAgICAgICAgICAgICAgICA6IGZhbHNlLFxuICAgICAgICAgICAgbWF4SGVpZ2h0ICAgICAgICAgICAgICAgICA6IEluZmluaXR5LFxuICAgICAgICAgICAgbWF4V2lkdGggICAgICAgICAgICAgICAgICA6IEluZmluaXR5LFxuICAgICAgICAgICAgbWluSGVpZ2h0ICAgICAgICAgICAgICAgICA6IDAsXG4gICAgICAgICAgICBtaW5XaWR0aCAgICAgICAgICAgICAgICAgIDogMCxcbiAgICAgICAgICAgIHJlc2l6ZUZyb20gICAgICAgICAgICAgICAgOiAncGFyZW50JyxcbiAgICAgICAgICAgIHNjcm9sbGluZyAgICAgICAgICAgICAgICAgOiBmYWxzZSxcbiAgICAgICAgICAgIHNpemVIZWlnaHQgICAgICAgICAgICAgICAgOiB0cnVlLFxuICAgICAgICAgICAgc2l6ZVdpZHRoICAgICAgICAgICAgICAgICA6IGZhbHNlLFxuICAgICAgICAgICAgd2FybmluZ1RpbWVvdXQgICAgICAgICAgICA6IDUwMDAsXG4gICAgICAgICAgICB0b2xlcmFuY2UgICAgICAgICAgICAgICAgIDogMCxcbiAgICAgICAgICAgIHdpZHRoQ2FsY3VsYXRpb25NZXRob2QgICAgOiAnc2Nyb2xsJyxcbiAgICAgICAgICAgIGNsb3NlZENhbGxiYWNrICAgICAgICAgICAgOiBmdW5jdGlvbigpIHt9LFxuICAgICAgICAgICAgaW5pdENhbGxiYWNrICAgICAgICAgICAgICA6IGZ1bmN0aW9uKCkge30sXG4gICAgICAgICAgICBtZXNzYWdlQ2FsbGJhY2sgICAgICAgICAgIDogZnVuY3Rpb24oKSB7d2FybignTWVzc2FnZUNhbGxiYWNrIGZ1bmN0aW9uIG5vdCBkZWZpbmVkJyk7fSxcbiAgICAgICAgICAgIHJlc2l6ZWRDYWxsYmFjayAgICAgICAgICAgOiBmdW5jdGlvbigpIHt9LFxuICAgICAgICAgICAgc2Nyb2xsQ2FsbGJhY2sgICAgICAgICAgICA6IGZ1bmN0aW9uKCkge3JldHVybiB0cnVlO31cbiAgICAgICAgfTtcblxuICAgIGZ1bmN0aW9uIGFkZEV2ZW50TGlzdGVuZXIob2JqLGV2dCxmdW5jKSB7XG5cdFx0LyogaXN0YW5idWwgaWdub3JlIGVsc2UgKi8gLy8gTm90IHRlc3RhYmxlIGluIFBoYW50b25KU1xuICAgICAgICBpZiAoJ2FkZEV2ZW50TGlzdGVuZXInIGluIHdpbmRvdykge1xuICAgICAgICAgICAgb2JqLmFkZEV2ZW50TGlzdGVuZXIoZXZ0LGZ1bmMsIGZhbHNlKTtcbiAgICAgICAgfSBlbHNlIGlmICgnYXR0YWNoRXZlbnQnIGluIHdpbmRvdykgey8vSUVcbiAgICAgICAgICAgIG9iai5hdHRhY2hFdmVudCgnb24nK2V2dCxmdW5jKTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIGZ1bmN0aW9uIHJlbW92ZUV2ZW50TGlzdGVuZXIoZWwsZXZ0LGZ1bmMpIHtcblx0XHQvKiBpc3RhbmJ1bCBpZ25vcmUgZWxzZSAqLyAvLyBOb3QgdGVzdGFibGUgaW4gcGhhbnRvbkpTXG4gICAgICAgIGlmICgncmVtb3ZlRXZlbnRMaXN0ZW5lcicgaW4gd2luZG93KSB7XG4gICAgICAgICAgICBlbC5yZW1vdmVFdmVudExpc3RlbmVyKGV2dCxmdW5jLCBmYWxzZSk7XG4gICAgICAgIH0gZWxzZSBpZiAoJ2RldGFjaEV2ZW50JyBpbiB3aW5kb3cpIHsgLy9JRVxuICAgICAgICAgICAgZWwuZGV0YWNoRXZlbnQoJ29uJytldnQsZnVuYyk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICBmdW5jdGlvbiBzZXR1cFJlcXVlc3RBbmltYXRpb25GcmFtZSgpIHtcbiAgICAgICAgdmFyXG4gICAgICAgICAgICB2ZW5kb3JzID0gWydtb3onLCAnd2Via2l0JywgJ28nLCAnbXMnXSxcbiAgICAgICAgICAgIHg7XG5cbiAgICAgICAgLy8gUmVtb3ZlIHZlbmRvciBwcmVmaXhpbmcgaWYgcHJlZml4ZWQgYW5kIGJyZWFrIGVhcmx5IGlmIG5vdFxuICAgICAgICBmb3IgKHggPSAwOyB4IDwgdmVuZG9ycy5sZW5ndGggJiYgIXJlcXVlc3RBbmltYXRpb25GcmFtZTsgeCArPSAxKSB7XG4gICAgICAgICAgICByZXF1ZXN0QW5pbWF0aW9uRnJhbWUgPSB3aW5kb3dbdmVuZG9yc1t4XSArICdSZXF1ZXN0QW5pbWF0aW9uRnJhbWUnXTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmICghKHJlcXVlc3RBbmltYXRpb25GcmFtZSkpIHtcbiAgICAgICAgICAgIGxvZygnc2V0dXAnLCdSZXF1ZXN0QW5pbWF0aW9uRnJhbWUgbm90IHN1cHBvcnRlZCcpO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gZ2V0TXlJRChpZnJhbWVJZCkge1xuICAgICAgICB2YXIgcmV0U3RyID0gJ0hvc3QgcGFnZTogJytpZnJhbWVJZDtcblxuICAgICAgICBpZiAod2luZG93LnRvcCAhPT0gd2luZG93LnNlbGYpIHtcbiAgICAgICAgICAgIGlmICh3aW5kb3cucGFyZW50SUZyYW1lICYmIHdpbmRvdy5wYXJlbnRJRnJhbWUuZ2V0SWQpIHtcbiAgICAgICAgICAgICAgICByZXRTdHIgPSB3aW5kb3cucGFyZW50SUZyYW1lLmdldElkKCkrJzogJytpZnJhbWVJZDtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgcmV0U3RyID0gJ05lc3RlZCBob3N0IHBhZ2U6ICcraWZyYW1lSWQ7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gcmV0U3RyO1xuICAgIH1cblxuICAgIGZ1bmN0aW9uIGZvcm1hdExvZ0hlYWRlcihpZnJhbWVJZCkge1xuICAgICAgICByZXR1cm4gbXNnSWQgKyAnWycgKyBnZXRNeUlEKGlmcmFtZUlkKSArICddJztcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBpc0xvZ0VuYWJsZWQoaWZyYW1lSWQpIHtcbiAgICAgICAgcmV0dXJuIHNldHRpbmdzW2lmcmFtZUlkXSA/IHNldHRpbmdzW2lmcmFtZUlkXS5sb2cgOiBsb2dFbmFibGVkO1xuICAgIH1cblxuICAgIGZ1bmN0aW9uIGxvZyhpZnJhbWVJZCxtc2cpIHtcbiAgICAgICAgb3V0cHV0KCdsb2cnLGlmcmFtZUlkLG1zZyxpc0xvZ0VuYWJsZWQoaWZyYW1lSWQpKTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBpbmZvKGlmcmFtZUlkLG1zZykge1xuICAgICAgICBvdXRwdXQoJ2luZm8nLGlmcmFtZUlkLG1zZyxpc0xvZ0VuYWJsZWQoaWZyYW1lSWQpKTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiB3YXJuKGlmcmFtZUlkLG1zZykge1xuICAgICAgICBvdXRwdXQoJ3dhcm4nLGlmcmFtZUlkLG1zZyx0cnVlKTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBvdXRwdXQodHlwZSxpZnJhbWVJZCxtc2csZW5hYmxlZCkge1xuICAgICAgICBpZiAodHJ1ZSA9PT0gZW5hYmxlZCAmJiAnb2JqZWN0JyA9PT0gdHlwZW9mIHdpbmRvdy5jb25zb2xlKSB7XG4gICAgICAgICAgICBjb25zb2xlW3R5cGVdKGZvcm1hdExvZ0hlYWRlcihpZnJhbWVJZCksbXNnKTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIGZ1bmN0aW9uIGlGcmFtZUxpc3RlbmVyKGV2ZW50KSB7XG4gICAgICAgIGZ1bmN0aW9uIHJlc2l6ZUlGcmFtZSgpIHtcbiAgICAgICAgICAgIGZ1bmN0aW9uIHJlc2l6ZSgpIHtcbiAgICAgICAgICAgICAgICBzZXRTaXplKG1lc3NhZ2VEYXRhKTtcbiAgICAgICAgICAgICAgICBzZXRQYWdlUG9zaXRpb24oaWZyYW1lSWQpO1xuICAgICAgICAgICAgICAgIGNhbGxiYWNrKCdyZXNpemVkQ2FsbGJhY2snLG1lc3NhZ2VEYXRhKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgZW5zdXJlSW5SYW5nZSgnSGVpZ2h0Jyk7XG4gICAgICAgICAgICBlbnN1cmVJblJhbmdlKCdXaWR0aCcpO1xuXG4gICAgICAgICAgICBzeW5jUmVzaXplKHJlc2l6ZSxtZXNzYWdlRGF0YSwnaW5pdCcpO1xuICAgICAgICB9XG5cbiAgICAgICAgZnVuY3Rpb24gcHJvY2Vzc01zZygpIHtcbiAgICAgICAgICAgIHZhciBkYXRhID0gbXNnLnN1YnN0cihtc2dJZExlbikuc3BsaXQoJzonKTtcblxuICAgICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgICAgICBpZnJhbWU6IHNldHRpbmdzW2RhdGFbMF1dICYmIHNldHRpbmdzW2RhdGFbMF1dLmlmcmFtZSxcbiAgICAgICAgICAgICAgICBpZDogICAgIGRhdGFbMF0sXG4gICAgICAgICAgICAgICAgaGVpZ2h0OiBkYXRhWzFdLFxuICAgICAgICAgICAgICAgIHdpZHRoOiAgZGF0YVsyXSxcbiAgICAgICAgICAgICAgICB0eXBlOiAgIGRhdGFbM11cbiAgICAgICAgICAgIH07XG4gICAgICAgIH1cblxuICAgICAgICBmdW5jdGlvbiBlbnN1cmVJblJhbmdlKERpbWVuc2lvbikge1xuICAgICAgICAgICAgdmFyXG4gICAgICAgICAgICAgICAgbWF4ICA9IE51bWJlcihzZXR0aW5nc1tpZnJhbWVJZF1bJ21heCcgKyBEaW1lbnNpb25dKSxcbiAgICAgICAgICAgICAgICBtaW4gID0gTnVtYmVyKHNldHRpbmdzW2lmcmFtZUlkXVsnbWluJyArIERpbWVuc2lvbl0pLFxuICAgICAgICAgICAgICAgIGRpbWVuc2lvbiA9IERpbWVuc2lvbi50b0xvd2VyQ2FzZSgpLFxuICAgICAgICAgICAgICAgIHNpemUgPSBOdW1iZXIobWVzc2FnZURhdGFbZGltZW5zaW9uXSk7XG5cbiAgICAgICAgICAgIGxvZyhpZnJhbWVJZCwnQ2hlY2tpbmcgJyArIGRpbWVuc2lvbiArICcgaXMgaW4gcmFuZ2UgJyArIG1pbiArICctJyArIG1heCk7XG5cbiAgICAgICAgICAgIGlmIChzaXplPG1pbikge1xuICAgICAgICAgICAgICAgIHNpemU9bWluO1xuICAgICAgICAgICAgICAgIGxvZyhpZnJhbWVJZCwnU2V0ICcgKyBkaW1lbnNpb24gKyAnIHRvIG1pbiB2YWx1ZScpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAoc2l6ZT5tYXgpIHtcbiAgICAgICAgICAgICAgICBzaXplPW1heDtcbiAgICAgICAgICAgICAgICBsb2coaWZyYW1lSWQsJ1NldCAnICsgZGltZW5zaW9uICsgJyB0byBtYXggdmFsdWUnKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgbWVzc2FnZURhdGFbZGltZW5zaW9uXSA9ICcnICsgc2l6ZTtcbiAgICAgICAgfVxuXG5cbiAgICAgICAgZnVuY3Rpb24gaXNNZXNzYWdlRnJvbUlGcmFtZSgpIHtcbiAgICAgICAgICAgIGZ1bmN0aW9uIGNoZWNrQWxsb3dlZE9yaWdpbigpIHtcbiAgICAgICAgICAgICAgICBmdW5jdGlvbiBjaGVja0xpc3QoKSB7XG4gICAgICAgICAgICAgICAgICAgIHZhclxuICAgICAgICAgICAgICAgICAgICAgICAgaSA9IDAsXG4gICAgICAgICAgICAgICAgICAgICAgICByZXRDb2RlID0gZmFsc2U7XG5cbiAgICAgICAgICAgICAgICAgICAgbG9nKGlmcmFtZUlkLCdDaGVja2luZyBjb25uZWN0aW9uIGlzIGZyb20gYWxsb3dlZCBsaXN0IG9mIG9yaWdpbnM6ICcgKyBjaGVja09yaWdpbik7XG5cbiAgICAgICAgICAgICAgICAgICAgZm9yICg7IGkgPCBjaGVja09yaWdpbi5sZW5ndGg7IGkrKykge1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGNoZWNrT3JpZ2luW2ldID09PSBvcmlnaW4pIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXRDb2RlID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gcmV0Q29kZTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBmdW5jdGlvbiBjaGVja1NpbmdsZSgpIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyIHJlbW90ZUhvc3QgID0gc2V0dGluZ3NbaWZyYW1lSWRdICYmIHNldHRpbmdzW2lmcmFtZUlkXS5yZW1vdGVIb3N0O1xuICAgICAgICAgICAgICAgICAgICBsb2coaWZyYW1lSWQsJ0NoZWNraW5nIGNvbm5lY3Rpb24gaXMgZnJvbTogJytyZW1vdGVIb3N0KTtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIG9yaWdpbiA9PT0gcmVtb3RlSG9zdDtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICByZXR1cm4gY2hlY2tPcmlnaW4uY29uc3RydWN0b3IgPT09IEFycmF5ID8gY2hlY2tMaXN0KCkgOiBjaGVja1NpbmdsZSgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB2YXJcbiAgICAgICAgICAgICAgICBvcmlnaW4gICAgICA9IGV2ZW50Lm9yaWdpbixcbiAgICAgICAgICAgICAgICBjaGVja09yaWdpbiA9IHNldHRpbmdzW2lmcmFtZUlkXSAmJiBzZXR0aW5nc1tpZnJhbWVJZF0uY2hlY2tPcmlnaW47XG5cbiAgICAgICAgICAgIGlmIChjaGVja09yaWdpbiAmJiAoJycrb3JpZ2luICE9PSAnbnVsbCcpICYmICFjaGVja0FsbG93ZWRPcmlnaW4oKSkge1xuICAgICAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcihcbiAgICAgICAgICAgICAgICAgICAgJ1VuZXhwZWN0ZWQgbWVzc2FnZSByZWNlaXZlZCBmcm9tOiAnICsgb3JpZ2luICtcbiAgICAgICAgICAgICAgICAgICAgJyBmb3IgJyArIG1lc3NhZ2VEYXRhLmlmcmFtZS5pZCArXG4gICAgICAgICAgICAgICAgICAgICcuIE1lc3NhZ2Ugd2FzOiAnICsgZXZlbnQuZGF0YSArXG4gICAgICAgICAgICAgICAgICAgICcuIFRoaXMgZXJyb3IgY2FuIGJlIGRpc2FibGVkIGJ5IHNldHRpbmcgdGhlIGNoZWNrT3JpZ2luOiBmYWxzZSBvcHRpb24gb3IgYnkgcHJvdmlkaW5nIG9mIGFycmF5IG9mIHRydXN0ZWQgZG9tYWlucy4nXG4gICAgICAgICAgICAgICAgKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgIH1cblxuICAgICAgICBmdW5jdGlvbiBpc01lc3NhZ2VGb3JVcygpIHtcbiAgICAgICAgICAgIHJldHVybiBtc2dJZCA9PT0gKCgnJyArIG1zZykuc3Vic3RyKDAsbXNnSWRMZW4pKSAmJiAobXNnLnN1YnN0cihtc2dJZExlbikuc3BsaXQoJzonKVswXSBpbiBzZXR0aW5ncyk7IC8vJycrUHJvdGVjdHMgYWdhaW5zdCBub24tc3RyaW5nIG1zZ1xuICAgICAgICB9XG5cbiAgICAgICAgZnVuY3Rpb24gaXNNZXNzYWdlRnJvbU1ldGFQYXJlbnQoKSB7XG4gICAgICAgICAgICAvL1Rlc3QgaWYgdGhpcyBtZXNzYWdlIGlzIGZyb20gYSBwYXJlbnQgYWJvdmUgdXMuIFRoaXMgaXMgYW4gdWdseSB0ZXN0LCBob3dldmVyLCB1cGRhdGluZ1xuICAgICAgICAgICAgLy90aGUgbWVzc2FnZSBmb3JtYXQgd291bGQgYnJlYWsgYmFja3dhcmRzIGNvbXBhdGliaXR5LlxuICAgICAgICAgICAgdmFyIHJldENvZGUgPSBtZXNzYWdlRGF0YS50eXBlIGluIHsndHJ1ZSc6MSwnZmFsc2UnOjEsJ3VuZGVmaW5lZCc6MX07XG5cbiAgICAgICAgICAgIGlmIChyZXRDb2RlKSB7XG4gICAgICAgICAgICAgICAgbG9nKGlmcmFtZUlkLCdJZ25vcmluZyBpbml0IG1lc3NhZ2UgZnJvbSBtZXRhIHBhcmVudCBwYWdlJyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHJldHVybiByZXRDb2RlO1xuICAgICAgICB9XG5cbiAgICAgICAgZnVuY3Rpb24gZ2V0TXNnQm9keShvZmZzZXQpIHtcbiAgICAgICAgICAgIHJldHVybiBtc2cuc3Vic3RyKG1zZy5pbmRleE9mKCc6JykrbXNnSGVhZGVyTGVuK29mZnNldCk7XG4gICAgICAgIH1cblxuICAgICAgICBmdW5jdGlvbiBmb3J3YXJkTXNnRnJvbUlGcmFtZShtc2dCb2R5KSB7XG4gICAgICAgICAgICBsb2coaWZyYW1lSWQsJ01lc3NhZ2VDYWxsYmFjayBwYXNzZWQ6IHtpZnJhbWU6ICcrIG1lc3NhZ2VEYXRhLmlmcmFtZS5pZCArICcsIG1lc3NhZ2U6ICcgKyBtc2dCb2R5ICsgJ30nKTtcbiAgICAgICAgICAgIGNhbGxiYWNrKCdtZXNzYWdlQ2FsbGJhY2snLHtcbiAgICAgICAgICAgICAgICBpZnJhbWU6IG1lc3NhZ2VEYXRhLmlmcmFtZSxcbiAgICAgICAgICAgICAgICBtZXNzYWdlOiBKU09OLnBhcnNlKG1zZ0JvZHkpXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIGxvZyhpZnJhbWVJZCwnLS0nKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGZ1bmN0aW9uIGdldFBhZ2VJbmZvKCkge1xuICAgICAgICAgICAgdmFyXG4gICAgICAgICAgICAgICAgYm9keVBvc2l0aW9uICAgPSBkb2N1bWVudC5ib2R5LmdldEJvdW5kaW5nQ2xpZW50UmVjdCgpLFxuICAgICAgICAgICAgICAgIGlGcmFtZVBvc2l0aW9uID0gbWVzc2FnZURhdGEuaWZyYW1lLmdldEJvdW5kaW5nQ2xpZW50UmVjdCgpO1xuXG4gICAgICAgICAgICByZXR1cm4gSlNPTi5zdHJpbmdpZnkoe1xuICAgICAgICAgICAgICAgIGlmcmFtZUhlaWdodDogaUZyYW1lUG9zaXRpb24uaGVpZ2h0LFxuICAgICAgICAgICAgICAgIGlmcmFtZVdpZHRoOiAgaUZyYW1lUG9zaXRpb24ud2lkdGgsXG4gICAgICAgICAgICAgICAgY2xpZW50SGVpZ2h0OiBNYXRoLm1heChkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQuY2xpZW50SGVpZ2h0LCB3aW5kb3cuaW5uZXJIZWlnaHQgfHwgMCksXG4gICAgICAgICAgICAgICAgY2xpZW50V2lkdGg6ICBNYXRoLm1heChkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQuY2xpZW50V2lkdGgsICB3aW5kb3cuaW5uZXJXaWR0aCAgfHwgMCksXG4gICAgICAgICAgICAgICAgb2Zmc2V0VG9wOiAgICBwYXJzZUludChpRnJhbWVQb3NpdGlvbi50b3AgIC0gYm9keVBvc2l0aW9uLnRvcCwgIDEwKSxcbiAgICAgICAgICAgICAgICBvZmZzZXRMZWZ0OiAgIHBhcnNlSW50KGlGcmFtZVBvc2l0aW9uLmxlZnQgLSBib2R5UG9zaXRpb24ubGVmdCwgMTApLFxuICAgICAgICAgICAgICAgIHNjcm9sbFRvcDogICAgd2luZG93LnBhZ2VZT2Zmc2V0LFxuICAgICAgICAgICAgICAgIHNjcm9sbExlZnQ6ICAgd2luZG93LnBhZ2VYT2Zmc2V0XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuXG4gICAgICAgIGZ1bmN0aW9uIHNlbmRQYWdlSW5mb1RvSWZyYW1lKGlmcmFtZSxpZnJhbWVJZCkge1xuICAgICAgICAgICAgZnVuY3Rpb24gZGVib3VuY2VkVHJpZ2dlcigpIHtcbiAgICAgICAgICAgICAgICB0cmlnZ2VyKFxuICAgICAgICAgICAgICAgICAgICAnU2VuZCBQYWdlIEluZm8nLFxuICAgICAgICAgICAgICAgICAgICAncGFnZUluZm86JyArIGdldFBhZ2VJbmZvKCksXG4gICAgICAgICAgICAgICAgICAgIGlmcmFtZSxcbiAgICAgICAgICAgICAgICAgICAgaWZyYW1lSWRcbiAgICAgICAgICAgICAgICApO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgZGVib3VuY2VGcmFtZUV2ZW50cyhkZWJvdW5jZWRUcmlnZ2VyLDMyLGlmcmFtZUlkKTtcbiAgICAgICAgfVxuXG5cbiAgICAgICAgZnVuY3Rpb24gc3RhcnRQYWdlSW5mb01vbml0b3IoKSB7XG4gICAgICAgICAgICBmdW5jdGlvbiBzZXRMaXN0ZW5lcih0eXBlLGZ1bmMpIHtcbiAgICAgICAgICAgICAgICBmdW5jdGlvbiBzZW5kUGFnZUluZm8oKSB7XG4gICAgICAgICAgICAgICAgICAgIGlmIChzZXR0aW5nc1tpZF0pIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHNlbmRQYWdlSW5mb1RvSWZyYW1lKHNldHRpbmdzW2lkXS5pZnJhbWUsaWQpO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgc3RvcCgpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgWydzY3JvbGwnLCdyZXNpemUnXS5mb3JFYWNoKGZ1bmN0aW9uKGV2dCkge1xuICAgICAgICAgICAgICAgICAgICBsb2coaWQsIHR5cGUgKyAgZXZ0ICsgJyBsaXN0ZW5lciBmb3Igc2VuZFBhZ2VJbmZvJyk7XG4gICAgICAgICAgICAgICAgICAgIGZ1bmMod2luZG93LGV2dCxzZW5kUGFnZUluZm8pO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBmdW5jdGlvbiBzdG9wKCkge1xuICAgICAgICAgICAgICAgIHNldExpc3RlbmVyKCdSZW1vdmUgJywgcmVtb3ZlRXZlbnRMaXN0ZW5lcik7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGZ1bmN0aW9uIHN0YXJ0KCkge1xuICAgICAgICAgICAgICAgIHNldExpc3RlbmVyKCdBZGQgJywgYWRkRXZlbnRMaXN0ZW5lcik7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHZhciBpZCA9IGlmcmFtZUlkOyAvL0NyZWF0ZSBsb2NhbGx5IHNjb3BlZCBjb3B5IG9mIGlGcmFtZSBJRFxuXG4gICAgICAgICAgICBzdGFydCgpO1xuXG4gICAgICAgICAgICBpZiAoc2V0dGluZ3NbaWRdKSB7XG4gICAgICAgICAgICAgICAgc2V0dGluZ3NbaWRdLnN0b3BQYWdlSW5mbyA9IHN0b3A7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBmdW5jdGlvbiBzdG9wUGFnZUluZm9Nb25pdG9yKCkge1xuICAgICAgICAgICAgaWYgKHNldHRpbmdzW2lmcmFtZUlkXSAmJiBzZXR0aW5nc1tpZnJhbWVJZF0uc3RvcFBhZ2VJbmZvKSB7XG4gICAgICAgICAgICAgICAgc2V0dGluZ3NbaWZyYW1lSWRdLnN0b3BQYWdlSW5mbygpO1xuICAgICAgICAgICAgICAgIGRlbGV0ZSBzZXR0aW5nc1tpZnJhbWVJZF0uc3RvcFBhZ2VJbmZvO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgZnVuY3Rpb24gY2hlY2tJRnJhbWVFeGlzdHMoKSB7XG4gICAgICAgICAgICB2YXIgcmV0Qm9vbCA9IHRydWU7XG5cbiAgICAgICAgICAgIGlmIChudWxsID09PSBtZXNzYWdlRGF0YS5pZnJhbWUpIHtcbiAgICAgICAgICAgICAgICB3YXJuKGlmcmFtZUlkLCdJRnJhbWUgKCcrbWVzc2FnZURhdGEuaWQrJykgbm90IGZvdW5kJyk7XG4gICAgICAgICAgICAgICAgcmV0Qm9vbCA9IGZhbHNlO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgcmV0dXJuIHJldEJvb2w7XG4gICAgICAgIH1cblxuICAgICAgICBmdW5jdGlvbiBnZXRFbGVtZW50UG9zaXRpb24odGFyZ2V0KSB7XG4gICAgICAgICAgICB2YXIgaUZyYW1lUG9zaXRpb24gPSB0YXJnZXQuZ2V0Qm91bmRpbmdDbGllbnRSZWN0KCk7XG5cbiAgICAgICAgICAgIGdldFBhZ2VQb3NpdGlvbihpZnJhbWVJZCk7XG5cbiAgICAgICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICAgICAgeDogTWF0aC5mbG9vciggTnVtYmVyKGlGcmFtZVBvc2l0aW9uLmxlZnQpICsgTnVtYmVyKHBhZ2VQb3NpdGlvbi54KSApLFxuICAgICAgICAgICAgICAgIHk6IE1hdGguZmxvb3IoIE51bWJlcihpRnJhbWVQb3NpdGlvbi50b3ApICArIE51bWJlcihwYWdlUG9zaXRpb24ueSkgKVxuICAgICAgICAgICAgfTtcbiAgICAgICAgfVxuXG4gICAgICAgIGZ1bmN0aW9uIHNjcm9sbFJlcXVlc3RGcm9tQ2hpbGQoYWRkT2Zmc2V0KSB7XG5cdFx0XHQvKiBpc3RhbmJ1bCBpZ25vcmUgbmV4dCAqLyAgLy9Ob3QgdGVzdGFibGUgaW4gS2FybWFcbiAgICAgICAgICAgIGZ1bmN0aW9uIHJlcG9zaXRpb24oKSB7XG4gICAgICAgICAgICAgICAgcGFnZVBvc2l0aW9uID0gbmV3UG9zaXRpb247XG4gICAgICAgICAgICAgICAgc2Nyb2xsVG8oKTtcbiAgICAgICAgICAgICAgICBsb2coaWZyYW1lSWQsJy0tJyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGZ1bmN0aW9uIGNhbGNPZmZzZXQoKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgICAgICAgICAgeDogTnVtYmVyKG1lc3NhZ2VEYXRhLndpZHRoKSArIG9mZnNldC54LFxuICAgICAgICAgICAgICAgICAgICB5OiBOdW1iZXIobWVzc2FnZURhdGEuaGVpZ2h0KSArIG9mZnNldC55XG4gICAgICAgICAgICAgICAgfTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgZnVuY3Rpb24gc2Nyb2xsUGFyZW50KCkge1xuICAgICAgICAgICAgICAgIGlmICh3aW5kb3cucGFyZW50SUZyYW1lKSB7XG4gICAgICAgICAgICAgICAgICAgIHdpbmRvdy5wYXJlbnRJRnJhbWVbJ3Njcm9sbFRvJysoYWRkT2Zmc2V0PydPZmZzZXQnOicnKV0obmV3UG9zaXRpb24ueCxuZXdQb3NpdGlvbi55KTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICB3YXJuKGlmcmFtZUlkLCdVbmFibGUgdG8gc2Nyb2xsIHRvIHJlcXVlc3RlZCBwb3NpdGlvbiwgd2luZG93LnBhcmVudElGcmFtZSBub3QgZm91bmQnKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHZhclxuICAgICAgICAgICAgICAgIG9mZnNldCA9IGFkZE9mZnNldCA/IGdldEVsZW1lbnRQb3NpdGlvbihtZXNzYWdlRGF0YS5pZnJhbWUpIDoge3g6MCx5OjB9LFxuICAgICAgICAgICAgICAgIG5ld1Bvc2l0aW9uID0gY2FsY09mZnNldCgpO1xuXG4gICAgICAgICAgICBsb2coaWZyYW1lSWQsJ1JlcG9zaXRpb24gcmVxdWVzdGVkIGZyb20gaUZyYW1lIChvZmZzZXQgeDonK29mZnNldC54KycgeTonK29mZnNldC55KycpJyk7XG5cbiAgICAgICAgICAgIGlmKHdpbmRvdy50b3AgIT09IHdpbmRvdy5zZWxmKSB7XG4gICAgICAgICAgICAgICAgc2Nyb2xsUGFyZW50KCk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIHJlcG9zaXRpb24oKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIGZ1bmN0aW9uIHNjcm9sbFRvKCkge1xuICAgICAgICAgICAgaWYgKGZhbHNlICE9PSBjYWxsYmFjaygnc2Nyb2xsQ2FsbGJhY2snLHBhZ2VQb3NpdGlvbikpIHtcbiAgICAgICAgICAgICAgICBzZXRQYWdlUG9zaXRpb24oaWZyYW1lSWQpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICB1bnNldFBhZ2VQb3NpdGlvbigpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgZnVuY3Rpb24gZmluZFRhcmdldChsb2NhdGlvbikge1xuICAgICAgICAgICAgZnVuY3Rpb24ganVtcFRvVGFyZ2V0KCkge1xuICAgICAgICAgICAgICAgIHZhciBqdW1wUG9zaXRpb24gPSBnZXRFbGVtZW50UG9zaXRpb24odGFyZ2V0KTtcblxuICAgICAgICAgICAgICAgIGxvZyhpZnJhbWVJZCwnTW92aW5nIHRvIGluIHBhZ2UgbGluayAoIycraGFzaCsnKSBhdCB4OiAnK2p1bXBQb3NpdGlvbi54KycgeTogJytqdW1wUG9zaXRpb24ueSk7XG4gICAgICAgICAgICAgICAgcGFnZVBvc2l0aW9uID0ge1xuICAgICAgICAgICAgICAgICAgICB4OiBqdW1wUG9zaXRpb24ueCxcbiAgICAgICAgICAgICAgICAgICAgeToganVtcFBvc2l0aW9uLnlcbiAgICAgICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAgICAgc2Nyb2xsVG8oKTtcbiAgICAgICAgICAgICAgICBsb2coaWZyYW1lSWQsJy0tJyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGZ1bmN0aW9uIGp1bXBUb1BhcmVudCgpIHtcbiAgICAgICAgICAgICAgICBpZiAod2luZG93LnBhcmVudElGcmFtZSkge1xuICAgICAgICAgICAgICAgICAgICB3aW5kb3cucGFyZW50SUZyYW1lLm1vdmVUb0FuY2hvcihoYXNoKTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBsb2coaWZyYW1lSWQsJ0luIHBhZ2UgbGluayAjJytoYXNoKycgbm90IGZvdW5kIGFuZCB3aW5kb3cucGFyZW50SUZyYW1lIG5vdCBmb3VuZCcpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdmFyXG4gICAgICAgICAgICAgICAgaGFzaCAgICAgPSBsb2NhdGlvbi5zcGxpdCgnIycpWzFdIHx8ICcnLFxuICAgICAgICAgICAgICAgIGhhc2hEYXRhID0gZGVjb2RlVVJJQ29tcG9uZW50KGhhc2gpLFxuICAgICAgICAgICAgICAgIHRhcmdldCAgID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoaGFzaERhdGEpIHx8IGRvY3VtZW50LmdldEVsZW1lbnRzQnlOYW1lKGhhc2hEYXRhKVswXTtcblxuICAgICAgICAgICAgaWYgKHRhcmdldCkge1xuICAgICAgICAgICAgICAgIGp1bXBUb1RhcmdldCgpO1xuICAgICAgICAgICAgfSBlbHNlIGlmKHdpbmRvdy50b3AhPT13aW5kb3cuc2VsZikge1xuICAgICAgICAgICAgICAgIGp1bXBUb1BhcmVudCgpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICBsb2coaWZyYW1lSWQsJ0luIHBhZ2UgbGluayAjJytoYXNoKycgbm90IGZvdW5kJyk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBmdW5jdGlvbiBjYWxsYmFjayhmdW5jTmFtZSx2YWwpIHtcbiAgICAgICAgICAgIHJldHVybiBjaGtDYWxsYmFjayhpZnJhbWVJZCxmdW5jTmFtZSx2YWwpO1xuICAgICAgICB9XG5cbiAgICAgICAgZnVuY3Rpb24gYWN0aW9uTXNnKCkge1xuXG4gICAgICAgICAgICBpZihzZXR0aW5nc1tpZnJhbWVJZF0gJiYgc2V0dGluZ3NbaWZyYW1lSWRdLmZpcnN0UnVuKSBmaXJzdFJ1bigpO1xuXG4gICAgICAgICAgICBzd2l0Y2gobWVzc2FnZURhdGEudHlwZSkge1xuICAgICAgICAgICAgICAgIGNhc2UgJ2Nsb3NlJzpcbiAgICAgICAgICAgICAgICAgICAgaWYoc2V0dGluZ3NbaWZyYW1lSWRdLmNsb3NlUmVxdWVzdENhbGxiYWNrKSBjaGtDYWxsYmFjayhpZnJhbWVJZCwgJ2Nsb3NlUmVxdWVzdENhbGxiYWNrJywgc2V0dGluZ3NbaWZyYW1lSWRdLmlmcmFtZSk7XG4gICAgICAgICAgICAgICAgICAgIGVsc2UgY2xvc2VJRnJhbWUobWVzc2FnZURhdGEuaWZyYW1lKTtcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgY2FzZSAnbWVzc2FnZSc6XG4gICAgICAgICAgICAgICAgICAgIGZvcndhcmRNc2dGcm9tSUZyYW1lKGdldE1zZ0JvZHkoNikpO1xuICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICBjYXNlICdzY3JvbGxUbyc6XG4gICAgICAgICAgICAgICAgICAgIHNjcm9sbFJlcXVlc3RGcm9tQ2hpbGQoZmFsc2UpO1xuICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICBjYXNlICdzY3JvbGxUb09mZnNldCc6XG4gICAgICAgICAgICAgICAgICAgIHNjcm9sbFJlcXVlc3RGcm9tQ2hpbGQodHJ1ZSk7XG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgIGNhc2UgJ3BhZ2VJbmZvJzpcbiAgICAgICAgICAgICAgICAgICAgc2VuZFBhZ2VJbmZvVG9JZnJhbWUoc2V0dGluZ3NbaWZyYW1lSWRdICYmIHNldHRpbmdzW2lmcmFtZUlkXS5pZnJhbWUsaWZyYW1lSWQpO1xuICAgICAgICAgICAgICAgICAgICBzdGFydFBhZ2VJbmZvTW9uaXRvcigpO1xuICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICBjYXNlICdwYWdlSW5mb1N0b3AnOlxuICAgICAgICAgICAgICAgICAgICBzdG9wUGFnZUluZm9Nb25pdG9yKCk7XG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgIGNhc2UgJ2luUGFnZUxpbmsnOlxuICAgICAgICAgICAgICAgICAgICBmaW5kVGFyZ2V0KGdldE1zZ0JvZHkoOSkpO1xuICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICBjYXNlICdyZXNldCc6XG4gICAgICAgICAgICAgICAgICAgIHJlc2V0SUZyYW1lKG1lc3NhZ2VEYXRhKTtcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgY2FzZSAnaW5pdCc6XG4gICAgICAgICAgICAgICAgICAgIHJlc2l6ZUlGcmFtZSgpO1xuICAgICAgICAgICAgICAgICAgICBjYWxsYmFjaygnaW5pdENhbGxiYWNrJyxtZXNzYWdlRGF0YS5pZnJhbWUpO1xuICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICBkZWZhdWx0OlxuICAgICAgICAgICAgICAgICAgICByZXNpemVJRnJhbWUoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIGZ1bmN0aW9uIGhhc1NldHRpbmdzKGlmcmFtZUlkKSB7XG4gICAgICAgICAgICB2YXIgcmV0Qm9vbCA9IHRydWU7XG5cbiAgICAgICAgICAgIGlmICghc2V0dGluZ3NbaWZyYW1lSWRdKSB7XG4gICAgICAgICAgICAgICAgcmV0Qm9vbCA9IGZhbHNlO1xuICAgICAgICAgICAgICAgIHdhcm4obWVzc2FnZURhdGEudHlwZSArICcgTm8gc2V0dGluZ3MgZm9yICcgKyBpZnJhbWVJZCArICcuIE1lc3NhZ2Ugd2FzOiAnICsgbXNnKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuIHJldEJvb2w7XG4gICAgICAgIH1cblxuICAgICAgICBmdW5jdGlvbiBpRnJhbWVSZWFkeU1zZ1JlY2VpdmVkKCkge1xuICAgICAgICAgICAgZm9yICh2YXIgaWZyYW1lSWQgaW4gc2V0dGluZ3MpIHtcbiAgICAgICAgICAgICAgICB0cmlnZ2VyKCdpRnJhbWUgcmVxdWVzdGVkIGluaXQnLGNyZWF0ZU91dGdvaW5nTXNnKGlmcmFtZUlkKSxkb2N1bWVudC5nZXRFbGVtZW50QnlJZChpZnJhbWVJZCksaWZyYW1lSWQpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgZnVuY3Rpb24gZmlyc3RSdW4oKSB7XG4gICAgICAgICAgICBpZiAoc2V0dGluZ3NbaWZyYW1lSWRdKSB7XG4gICAgICAgICAgICAgICAgc2V0dGluZ3NbaWZyYW1lSWRdLmZpcnN0UnVuID0gZmFsc2U7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBmdW5jdGlvbiBjbGVhcldhcm5pbmdUaW1lb3V0KCkge1xuICAgICAgICAgICAgaWYgKHNldHRpbmdzW2lmcmFtZUlkXSkge1xuICAgICAgICAgICAgICAgIGNsZWFyVGltZW91dChzZXR0aW5nc1tpZnJhbWVJZF0ubXNnVGltZW91dCk7XG4gICAgICAgICAgICAgICAgc2V0dGluZ3NbaWZyYW1lSWRdLndhcm5pbmdUaW1lb3V0ID0gMDtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIHZhclxuICAgICAgICAgICAgbXNnID0gZXZlbnQuZGF0YSxcbiAgICAgICAgICAgIG1lc3NhZ2VEYXRhID0ge30sXG4gICAgICAgICAgICBpZnJhbWVJZCA9IG51bGw7XG5cbiAgICAgICAgaWYoJ1tpRnJhbWVSZXNpemVyQ2hpbGRdUmVhZHknID09PSBtc2cpIHtcbiAgICAgICAgICAgIGlGcmFtZVJlYWR5TXNnUmVjZWl2ZWQoKTtcbiAgICAgICAgfSBlbHNlIGlmIChpc01lc3NhZ2VGb3JVcygpKSB7XG4gICAgICAgICAgICBtZXNzYWdlRGF0YSA9IHByb2Nlc3NNc2coKTtcbiAgICAgICAgICAgIGlmcmFtZUlkICAgID0gbG9nSWQgPSBtZXNzYWdlRGF0YS5pZDtcbiAgICAgICAgICAgIGlmIChzZXR0aW5nc1tpZnJhbWVJZF0pIHtcbiAgICAgICAgICAgICAgICBzZXR0aW5nc1tpZnJhbWVJZF0ubG9hZGVkID0gdHJ1ZTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKCFpc01lc3NhZ2VGcm9tTWV0YVBhcmVudCgpICYmIGhhc1NldHRpbmdzKGlmcmFtZUlkKSkge1xuICAgICAgICAgICAgICAgIGxvZyhpZnJhbWVJZCwnUmVjZWl2ZWQ6ICcrbXNnKTtcblxuICAgICAgICAgICAgICAgIGlmICggY2hlY2tJRnJhbWVFeGlzdHMoKSAmJiBpc01lc3NhZ2VGcm9tSUZyYW1lKCkgKSB7XG4gICAgICAgICAgICAgICAgICAgIGFjdGlvbk1zZygpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIGluZm8oaWZyYW1lSWQsJ0lnbm9yZWQ6ICcrbXNnKTtcbiAgICAgICAgfVxuXG4gICAgfVxuXG5cbiAgICBmdW5jdGlvbiBjaGtDYWxsYmFjayhpZnJhbWVJZCxmdW5jTmFtZSx2YWwpIHtcbiAgICAgICAgdmFyXG4gICAgICAgICAgICBmdW5jID0gbnVsbCxcbiAgICAgICAgICAgIHJldFZhbCA9IG51bGw7XG5cbiAgICAgICAgaWYoc2V0dGluZ3NbaWZyYW1lSWRdKSB7XG4gICAgICAgICAgICBmdW5jID0gc2V0dGluZ3NbaWZyYW1lSWRdW2Z1bmNOYW1lXTtcblxuICAgICAgICAgICAgaWYoICdmdW5jdGlvbicgPT09IHR5cGVvZiBmdW5jKSB7XG4gICAgICAgICAgICAgICAgcmV0VmFsID0gZnVuYyh2YWwpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICB0aHJvdyBuZXcgVHlwZUVycm9yKGZ1bmNOYW1lKycgb24gaUZyYW1lWycraWZyYW1lSWQrJ10gaXMgbm90IGEgZnVuY3Rpb24nKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiByZXRWYWw7XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gY2xvc2VJRnJhbWUoaWZyYW1lKSB7XG4gICAgICAgIHZhciBpZnJhbWVJZCA9IGlmcmFtZS5pZDtcblxuICAgICAgICBsb2coaWZyYW1lSWQsJ1JlbW92aW5nIGlGcmFtZTogJytpZnJhbWVJZCk7XG4gICAgICAgIGlmIChpZnJhbWUucGFyZW50Tm9kZSkgeyBpZnJhbWUucGFyZW50Tm9kZS5yZW1vdmVDaGlsZChpZnJhbWUpOyB9XG4gICAgICAgIGNoa0NhbGxiYWNrKGlmcmFtZUlkLCdjbG9zZWRDYWxsYmFjaycsaWZyYW1lSWQpO1xuICAgICAgICBsb2coaWZyYW1lSWQsJy0tJyk7XG4gICAgICAgIGRlbGV0ZSBzZXR0aW5nc1tpZnJhbWVJZF07XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gZ2V0UGFnZVBvc2l0aW9uKGlmcmFtZUlkKSB7XG4gICAgICAgIGlmKG51bGwgPT09IHBhZ2VQb3NpdGlvbikge1xuICAgICAgICAgICAgcGFnZVBvc2l0aW9uID0ge1xuICAgICAgICAgICAgICAgIHg6ICh3aW5kb3cucGFnZVhPZmZzZXQgIT09IHVuZGVmaW5lZCkgPyB3aW5kb3cucGFnZVhPZmZzZXQgOiBkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQuc2Nyb2xsTGVmdCxcbiAgICAgICAgICAgICAgICB5OiAod2luZG93LnBhZ2VZT2Zmc2V0ICE9PSB1bmRlZmluZWQpID8gd2luZG93LnBhZ2VZT2Zmc2V0IDogZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50LnNjcm9sbFRvcFxuICAgICAgICAgICAgfTtcbiAgICAgICAgICAgIGxvZyhpZnJhbWVJZCwnR2V0IHBhZ2UgcG9zaXRpb246ICcrcGFnZVBvc2l0aW9uLngrJywnK3BhZ2VQb3NpdGlvbi55KTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIGZ1bmN0aW9uIHNldFBhZ2VQb3NpdGlvbihpZnJhbWVJZCkge1xuICAgICAgICBpZihudWxsICE9PSBwYWdlUG9zaXRpb24pIHtcbiAgICAgICAgICAgIHdpbmRvdy5zY3JvbGxUbyhwYWdlUG9zaXRpb24ueCxwYWdlUG9zaXRpb24ueSk7XG4gICAgICAgICAgICBsb2coaWZyYW1lSWQsJ1NldCBwYWdlIHBvc2l0aW9uOiAnK3BhZ2VQb3NpdGlvbi54KycsJytwYWdlUG9zaXRpb24ueSk7XG4gICAgICAgICAgICB1bnNldFBhZ2VQb3NpdGlvbigpO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gdW5zZXRQYWdlUG9zaXRpb24oKSB7XG4gICAgICAgIHBhZ2VQb3NpdGlvbiA9IG51bGw7XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gcmVzZXRJRnJhbWUobWVzc2FnZURhdGEpIHtcbiAgICAgICAgZnVuY3Rpb24gcmVzZXQoKSB7XG4gICAgICAgICAgICBzZXRTaXplKG1lc3NhZ2VEYXRhKTtcbiAgICAgICAgICAgIHRyaWdnZXIoJ3Jlc2V0JywncmVzZXQnLG1lc3NhZ2VEYXRhLmlmcmFtZSxtZXNzYWdlRGF0YS5pZCk7XG4gICAgICAgIH1cblxuICAgICAgICBsb2cobWVzc2FnZURhdGEuaWQsJ1NpemUgcmVzZXQgcmVxdWVzdGVkIGJ5ICcrKCdpbml0Jz09PW1lc3NhZ2VEYXRhLnR5cGU/J2hvc3QgcGFnZSc6J2lGcmFtZScpKTtcbiAgICAgICAgZ2V0UGFnZVBvc2l0aW9uKG1lc3NhZ2VEYXRhLmlkKTtcbiAgICAgICAgc3luY1Jlc2l6ZShyZXNldCxtZXNzYWdlRGF0YSwncmVzZXQnKTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBzZXRTaXplKG1lc3NhZ2VEYXRhKSB7XG4gICAgICAgIGZ1bmN0aW9uIHNldERpbWVuc2lvbihkaW1lbnNpb24pIHtcbiAgICAgICAgICAgIG1lc3NhZ2VEYXRhLmlmcmFtZS5zdHlsZVtkaW1lbnNpb25dID0gbWVzc2FnZURhdGFbZGltZW5zaW9uXSArICdweCc7XG4gICAgICAgICAgICBsb2coXG4gICAgICAgICAgICAgICAgbWVzc2FnZURhdGEuaWQsXG4gICAgICAgICAgICAgICAgJ0lGcmFtZSAoJyArIGlmcmFtZUlkICtcbiAgICAgICAgICAgICAgICAnKSAnICsgZGltZW5zaW9uICtcbiAgICAgICAgICAgICAgICAnIHNldCB0byAnICsgbWVzc2FnZURhdGFbZGltZW5zaW9uXSArICdweCdcbiAgICAgICAgICAgICk7XG4gICAgICAgIH1cblxuICAgICAgICBmdW5jdGlvbiBjaGtaZXJvKGRpbWVuc2lvbikge1xuICAgICAgICAgICAgLy9GaXJlRm94IHNldHMgZGltZW5zaW9uIG9mIGhpZGRlbiBpRnJhbWVzIHRvIHplcm8uXG4gICAgICAgICAgICAvL1NvIGlmIHdlIGRldGVjdCB0aGF0IHNldCB1cCBhbiBldmVudCB0byBjaGVjayBmb3JcbiAgICAgICAgICAgIC8vd2hlbiBpRnJhbWUgYmVjb21lcyB2aXNpYmxlLlxuXG5cdFx0XHQvKiBpc3RhbmJ1bCBpZ25vcmUgbmV4dCAqLyAgLy9Ob3QgdGVzdGFibGUgaW4gUGhhbnRvbUpTXG4gICAgICAgICAgICBpZiAoIWhpZGRlbkNoZWNrRW5hYmxlZCAmJiAnMCcgPT09IG1lc3NhZ2VEYXRhW2RpbWVuc2lvbl0pIHtcbiAgICAgICAgICAgICAgICBoaWRkZW5DaGVja0VuYWJsZWQgPSB0cnVlO1xuICAgICAgICAgICAgICAgIGxvZyhpZnJhbWVJZCwnSGlkZGVuIGlGcmFtZSBkZXRlY3RlZCwgY3JlYXRpbmcgdmlzaWJpbGl0eSBsaXN0ZW5lcicpO1xuICAgICAgICAgICAgICAgIGZpeEhpZGRlbklGcmFtZXMoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIGZ1bmN0aW9uIHByb2Nlc3NEaW1lbnNpb24oZGltZW5zaW9uKSB7XG4gICAgICAgICAgICBzZXREaW1lbnNpb24oZGltZW5zaW9uKTtcbiAgICAgICAgICAgIGNoa1plcm8oZGltZW5zaW9uKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHZhciBpZnJhbWVJZCA9IG1lc3NhZ2VEYXRhLmlmcmFtZS5pZDtcblxuICAgICAgICBpZihzZXR0aW5nc1tpZnJhbWVJZF0pIHtcbiAgICAgICAgICAgIGlmKCBzZXR0aW5nc1tpZnJhbWVJZF0uc2l6ZUhlaWdodCkgeyBwcm9jZXNzRGltZW5zaW9uKCdoZWlnaHQnKTsgfVxuICAgICAgICAgICAgaWYoIHNldHRpbmdzW2lmcmFtZUlkXS5zaXplV2lkdGggKSB7IHByb2Nlc3NEaW1lbnNpb24oJ3dpZHRoJyk7IH1cbiAgICAgICAgfVxuICAgIH1cblxuICAgIGZ1bmN0aW9uIHN5bmNSZXNpemUoZnVuYyxtZXNzYWdlRGF0YSxkb05vdFN5bmMpIHtcblx0XHQvKiBpc3RhbmJ1bCBpZ25vcmUgaWYgKi8gIC8vTm90IHRlc3RhYmxlIGluIFBoYW50b21KU1xuICAgICAgICBpZihkb05vdFN5bmMhPT1tZXNzYWdlRGF0YS50eXBlICYmIHJlcXVlc3RBbmltYXRpb25GcmFtZSkge1xuICAgICAgICAgICAgbG9nKG1lc3NhZ2VEYXRhLmlkLCdSZXF1ZXN0aW5nIGFuaW1hdGlvbiBmcmFtZScpO1xuICAgICAgICAgICAgcmVxdWVzdEFuaW1hdGlvbkZyYW1lKGZ1bmMpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgZnVuYygpO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gdHJpZ2dlcihjYWxsZWVNc2csIG1zZywgaWZyYW1lLCBpZCwgbm9SZXNwb25zZVdhcm5pbmcpIHtcbiAgICAgICAgZnVuY3Rpb24gcG9zdE1lc3NhZ2VUb0lGcmFtZSgpIHtcbiAgICAgICAgICAgIHZhciB0YXJnZXQgPSBzZXR0aW5nc1tpZF0gJiYgc2V0dGluZ3NbaWRdLnRhcmdldE9yaWdpbjtcbiAgICAgICAgICAgIGxvZyhpZCwnWycgKyBjYWxsZWVNc2cgKyAnXSBTZW5kaW5nIG1zZyB0byBpZnJhbWVbJytpZCsnXSAoJyttc2crJykgdGFyZ2V0T3JpZ2luOiAnK3RhcmdldCk7XG4gICAgICAgICAgICBpZnJhbWUuY29udGVudFdpbmRvdy5wb3N0TWVzc2FnZSggbXNnSWQgKyBtc2csIHRhcmdldCApO1xuICAgICAgICB9XG5cbiAgICAgICAgZnVuY3Rpb24gaUZyYW1lTm90Rm91bmQoKSB7XG4gICAgICAgICAgICB3YXJuKGlkLCdbJyArIGNhbGxlZU1zZyArICddIElGcmFtZSgnK2lkKycpIG5vdCBmb3VuZCcpO1xuICAgICAgICB9XG5cbiAgICAgICAgZnVuY3Rpb24gY2hrQW5kU2VuZCgpIHtcbiAgICAgICAgICAgIGlmKGlmcmFtZSAmJiAnY29udGVudFdpbmRvdycgaW4gaWZyYW1lICYmIChudWxsICE9PSBpZnJhbWUuY29udGVudFdpbmRvdykpIHsgLy9OdWxsIHRlc3QgZm9yIFBoYW50b21KU1xuICAgICAgICAgICAgICAgIHBvc3RNZXNzYWdlVG9JRnJhbWUoKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgaUZyYW1lTm90Rm91bmQoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIGZ1bmN0aW9uIHdhcm5Pbk5vUmVzcG9uc2UoKSB7XG4gICAgICAgICAgICBmdW5jdGlvbiB3YXJuaW5nKCkge1xuICAgICAgICAgICAgICAgIGlmIChzZXR0aW5nc1tpZF0gJiYgIXNldHRpbmdzW2lkXS5sb2FkZWQgJiYgIWVycm9yU2hvd24pIHtcbiAgICAgICAgICAgICAgICAgICAgZXJyb3JTaG93biA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgIHdhcm4oaWQsICdJRnJhbWUgaGFzIG5vdCByZXNwb25kZWQgd2l0aGluICcrIHNldHRpbmdzW2lkXS53YXJuaW5nVGltZW91dC8xMDAwICsnIHNlY29uZHMuIENoZWNrIGlGcmFtZVJlc2l6ZXIuY29udGVudFdpbmRvdy5qcyBoYXMgYmVlbiBsb2FkZWQgaW4gaUZyYW1lLiBUaGlzIG1lc3NhZ2UgY2FuIGJlIGluZ29yZWQgaWYgZXZlcnl0aGluZyBpcyB3b3JraW5nLCBvciB5b3UgY2FuIHNldCB0aGUgd2FybmluZ1RpbWVvdXQgb3B0aW9uIHRvIGEgaGlnaGVyIHZhbHVlIG9yIHplcm8gdG8gc3VwcHJlc3MgdGhpcyB3YXJuaW5nLicpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKCEhbm9SZXNwb25zZVdhcm5pbmcgJiYgc2V0dGluZ3NbaWRdICYmICEhc2V0dGluZ3NbaWRdLndhcm5pbmdUaW1lb3V0KSB7XG4gICAgICAgICAgICAgICAgc2V0dGluZ3NbaWRdLm1zZ1RpbWVvdXQgPSBzZXRUaW1lb3V0KHdhcm5pbmcsIHNldHRpbmdzW2lkXS53YXJuaW5nVGltZW91dCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICB2YXIgZXJyb3JTaG93biA9IGZhbHNlO1xuXG4gICAgICAgIGlkID0gaWQgfHwgaWZyYW1lLmlkO1xuXG4gICAgICAgIGlmKHNldHRpbmdzW2lkXSkge1xuICAgICAgICAgICAgY2hrQW5kU2VuZCgpO1xuICAgICAgICAgICAgd2Fybk9uTm9SZXNwb25zZSgpO1xuICAgICAgICB9XG5cbiAgICB9XG5cbiAgICBmdW5jdGlvbiBjcmVhdGVPdXRnb2luZ01zZyhpZnJhbWVJZCkge1xuICAgICAgICByZXR1cm4gaWZyYW1lSWQgK1xuICAgICAgICAgICAgJzonICsgc2V0dGluZ3NbaWZyYW1lSWRdLmJvZHlNYXJnaW5WMSArXG4gICAgICAgICAgICAnOicgKyBzZXR0aW5nc1tpZnJhbWVJZF0uc2l6ZVdpZHRoICtcbiAgICAgICAgICAgICc6JyArIHNldHRpbmdzW2lmcmFtZUlkXS5sb2cgK1xuICAgICAgICAgICAgJzonICsgc2V0dGluZ3NbaWZyYW1lSWRdLmludGVydmFsICtcbiAgICAgICAgICAgICc6JyArIHNldHRpbmdzW2lmcmFtZUlkXS5lbmFibGVQdWJsaWNNZXRob2RzICtcbiAgICAgICAgICAgICc6JyArIHNldHRpbmdzW2lmcmFtZUlkXS5hdXRvUmVzaXplICtcbiAgICAgICAgICAgICc6JyArIHNldHRpbmdzW2lmcmFtZUlkXS5ib2R5TWFyZ2luICtcbiAgICAgICAgICAgICc6JyArIHNldHRpbmdzW2lmcmFtZUlkXS5oZWlnaHRDYWxjdWxhdGlvbk1ldGhvZCArXG4gICAgICAgICAgICAnOicgKyBzZXR0aW5nc1tpZnJhbWVJZF0uYm9keUJhY2tncm91bmQgK1xuICAgICAgICAgICAgJzonICsgc2V0dGluZ3NbaWZyYW1lSWRdLmJvZHlQYWRkaW5nICtcbiAgICAgICAgICAgICc6JyArIHNldHRpbmdzW2lmcmFtZUlkXS50b2xlcmFuY2UgK1xuICAgICAgICAgICAgJzonICsgc2V0dGluZ3NbaWZyYW1lSWRdLmluUGFnZUxpbmtzICtcbiAgICAgICAgICAgICc6JyArIHNldHRpbmdzW2lmcmFtZUlkXS5yZXNpemVGcm9tICtcbiAgICAgICAgICAgICc6JyArIHNldHRpbmdzW2lmcmFtZUlkXS53aWR0aENhbGN1bGF0aW9uTWV0aG9kO1xuICAgIH1cblxuICAgIGZ1bmN0aW9uIHNldHVwSUZyYW1lKGlmcmFtZSxvcHRpb25zKSB7XG4gICAgICAgIGZ1bmN0aW9uIHNldExpbWl0cygpIHtcbiAgICAgICAgICAgIGZ1bmN0aW9uIGFkZFN0eWxlKHN0eWxlKSB7XG4gICAgICAgICAgICAgICAgaWYgKChJbmZpbml0eSAhPT0gc2V0dGluZ3NbaWZyYW1lSWRdW3N0eWxlXSkgJiYgKDAgIT09IHNldHRpbmdzW2lmcmFtZUlkXVtzdHlsZV0pKSB7XG4gICAgICAgICAgICAgICAgICAgIGlmcmFtZS5zdHlsZVtzdHlsZV0gPSBzZXR0aW5nc1tpZnJhbWVJZF1bc3R5bGVdICsgJ3B4JztcbiAgICAgICAgICAgICAgICAgICAgbG9nKGlmcmFtZUlkLCdTZXQgJytzdHlsZSsnID0gJytzZXR0aW5nc1tpZnJhbWVJZF1bc3R5bGVdKydweCcpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgZnVuY3Rpb24gY2hrTWluTWF4KGRpbWVuc2lvbikge1xuICAgICAgICAgICAgICAgIGlmIChzZXR0aW5nc1tpZnJhbWVJZF1bJ21pbicrZGltZW5zaW9uXT5zZXR0aW5nc1tpZnJhbWVJZF1bJ21heCcrZGltZW5zaW9uXSkge1xuICAgICAgICAgICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoJ1ZhbHVlIGZvciBtaW4nK2RpbWVuc2lvbisnIGNhbiBub3QgYmUgZ3JlYXRlciB0aGFuIG1heCcrZGltZW5zaW9uKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGNoa01pbk1heCgnSGVpZ2h0Jyk7XG4gICAgICAgICAgICBjaGtNaW5NYXgoJ1dpZHRoJyk7XG5cbiAgICAgICAgICAgIGFkZFN0eWxlKCdtYXhIZWlnaHQnKTtcbiAgICAgICAgICAgIGFkZFN0eWxlKCdtaW5IZWlnaHQnKTtcbiAgICAgICAgICAgIGFkZFN0eWxlKCdtYXhXaWR0aCcpO1xuICAgICAgICAgICAgYWRkU3R5bGUoJ21pbldpZHRoJyk7XG4gICAgICAgIH1cblxuICAgICAgICBmdW5jdGlvbiBuZXdJZCgpIHtcbiAgICAgICAgICAgIHZhciBpZCA9ICgob3B0aW9ucyAmJiBvcHRpb25zLmlkKSB8fCBkZWZhdWx0cy5pZCArIGNvdW50KyspO1xuICAgICAgICAgICAgaWYgIChudWxsICE9PSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZChpZCkpIHtcbiAgICAgICAgICAgICAgICBpZCA9IGlkICsgY291bnQrKztcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHJldHVybiBpZDtcbiAgICAgICAgfVxuXG4gICAgICAgIGZ1bmN0aW9uIGVuc3VyZUhhc0lkKGlmcmFtZUlkKSB7XG4gICAgICAgICAgICBsb2dJZD1pZnJhbWVJZDtcbiAgICAgICAgICAgIGlmICgnJz09PWlmcmFtZUlkKSB7XG4gICAgICAgICAgICAgICAgaWZyYW1lLmlkID0gaWZyYW1lSWQgPSAgbmV3SWQoKTtcbiAgICAgICAgICAgICAgICBsb2dFbmFibGVkID0gKG9wdGlvbnMgfHwge30pLmxvZztcbiAgICAgICAgICAgICAgICBsb2dJZD1pZnJhbWVJZDtcbiAgICAgICAgICAgICAgICBsb2coaWZyYW1lSWQsJ0FkZGVkIG1pc3NpbmcgaWZyYW1lIElEOiAnKyBpZnJhbWVJZCArJyAoJyArIGlmcmFtZS5zcmMgKyAnKScpO1xuICAgICAgICAgICAgfVxuXG5cbiAgICAgICAgICAgIHJldHVybiBpZnJhbWVJZDtcbiAgICAgICAgfVxuXG4gICAgICAgIGZ1bmN0aW9uIHNldFNjcm9sbGluZygpIHtcbiAgICAgICAgICAgIGxvZyhpZnJhbWVJZCwnSUZyYW1lIHNjcm9sbGluZyAnICsgKHNldHRpbmdzW2lmcmFtZUlkXSAmJiBzZXR0aW5nc1tpZnJhbWVJZF0uc2Nyb2xsaW5nID8gJ2VuYWJsZWQnIDogJ2Rpc2FibGVkJykgKyAnIGZvciAnICsgaWZyYW1lSWQpO1xuICAgICAgICAgICAgaWZyYW1lLnN0eWxlLm92ZXJmbG93ID0gZmFsc2UgPT09IChzZXR0aW5nc1tpZnJhbWVJZF0gJiYgc2V0dGluZ3NbaWZyYW1lSWRdLnNjcm9sbGluZykgPyAnaGlkZGVuJyA6ICdhdXRvJztcbiAgICAgICAgICAgIHN3aXRjaChzZXR0aW5nc1tpZnJhbWVJZF0gJiYgc2V0dGluZ3NbaWZyYW1lSWRdLnNjcm9sbGluZykge1xuICAgICAgICAgICAgICAgIGNhc2UgdHJ1ZTpcbiAgICAgICAgICAgICAgICAgICAgaWZyYW1lLnNjcm9sbGluZyA9ICd5ZXMnO1xuICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICBjYXNlIGZhbHNlOlxuICAgICAgICAgICAgICAgICAgICBpZnJhbWUuc2Nyb2xsaW5nID0gJ25vJztcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgZGVmYXVsdDpcbiAgICAgICAgICAgICAgICAgICAgaWZyYW1lLnNjcm9sbGluZyA9IHNldHRpbmdzW2lmcmFtZUlkXSA/IHNldHRpbmdzW2lmcmFtZUlkXS5zY3JvbGxpbmcgOiAnbm8nO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgLy9UaGUgVjEgaUZyYW1lIHNjcmlwdCBleHBlY3RzIGFuIGludCwgd2hlcmUgYXMgaW4gVjIgZXhwZWN0cyBhIENTU1xuICAgICAgICAvL3N0cmluZyB2YWx1ZSBzdWNoIGFzICcxcHggM2VtJywgc28gaWYgd2UgaGF2ZSBhbiBpbnQgZm9yIFYyLCBzZXQgVjE9VjJcbiAgICAgICAgLy9hbmQgdGhlbiBjb252ZXJ0IFYyIHRvIGEgc3RyaW5nIFBYIHZhbHVlLlxuICAgICAgICBmdW5jdGlvbiBzZXR1cEJvZHlNYXJnaW5WYWx1ZXMoKSB7XG4gICAgICAgICAgICBpZiAoKCdudW1iZXInPT09dHlwZW9mKHNldHRpbmdzW2lmcmFtZUlkXSAmJiBzZXR0aW5nc1tpZnJhbWVJZF0uYm9keU1hcmdpbikpIHx8ICgnMCc9PT0oc2V0dGluZ3NbaWZyYW1lSWRdICYmIHNldHRpbmdzW2lmcmFtZUlkXS5ib2R5TWFyZ2luKSkpIHtcbiAgICAgICAgICAgICAgICBzZXR0aW5nc1tpZnJhbWVJZF0uYm9keU1hcmdpblYxID0gc2V0dGluZ3NbaWZyYW1lSWRdLmJvZHlNYXJnaW47XG4gICAgICAgICAgICAgICAgc2V0dGluZ3NbaWZyYW1lSWRdLmJvZHlNYXJnaW4gICA9ICcnICsgc2V0dGluZ3NbaWZyYW1lSWRdLmJvZHlNYXJnaW4gKyAncHgnO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgZnVuY3Rpb24gY2hlY2tSZXNldCgpIHtcbiAgICAgICAgICAgIC8vIFJlZHVjZSBzY29wZSBvZiBmaXJzdFJ1biB0byBmdW5jdGlvbiwgYmVjYXVzZSBJRTgncyBKUyBleGVjdXRpb25cbiAgICAgICAgICAgIC8vIGNvbnRleHQgc3RhY2sgaXMgYm9ya2VkIGFuZCB0aGlzIHZhbHVlIGdldHMgZXh0ZXJuYWxseVxuICAgICAgICAgICAgLy8gY2hhbmdlZCBtaWR3YXkgdGhyb3VnaCBydW5uaW5nIHRoaXMgZnVuY3Rpb24hISFcbiAgICAgICAgICAgIHZhclxuICAgICAgICAgICAgICAgIGZpcnN0UnVuICAgICAgICAgICA9IHNldHRpbmdzW2lmcmFtZUlkXSAmJiBzZXR0aW5nc1tpZnJhbWVJZF0uZmlyc3RSdW4sXG4gICAgICAgICAgICAgICAgcmVzZXRSZXF1ZXJ0TWV0aG9kID0gc2V0dGluZ3NbaWZyYW1lSWRdICYmIHNldHRpbmdzW2lmcmFtZUlkXS5oZWlnaHRDYWxjdWxhdGlvbk1ldGhvZCBpbiByZXNldFJlcXVpcmVkTWV0aG9kcztcblxuICAgICAgICAgICAgaWYgKCFmaXJzdFJ1biAmJiByZXNldFJlcXVlcnRNZXRob2QpIHtcbiAgICAgICAgICAgICAgICByZXNldElGcmFtZSh7aWZyYW1lOmlmcmFtZSwgaGVpZ2h0OjAsIHdpZHRoOjAsIHR5cGU6J2luaXQnfSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBmdW5jdGlvbiBzZXR1cElGcmFtZU9iamVjdCgpIHtcbiAgICAgICAgICAgIGlmKEZ1bmN0aW9uLnByb3RvdHlwZS5iaW5kICYmIHNldHRpbmdzW2lmcmFtZUlkXSkgeyAvL0lnbm9yZSB1bnBvbHlmaWxsZWQgSUU4LlxuICAgICAgICAgICAgICAgIHNldHRpbmdzW2lmcmFtZUlkXS5pZnJhbWUuaUZyYW1lUmVzaXplciA9IHtcblxuICAgICAgICAgICAgICAgICAgICBjbG9zZSAgICAgICAgOiBjbG9zZUlGcmFtZS5iaW5kKG51bGwsc2V0dGluZ3NbaWZyYW1lSWRdLmlmcmFtZSksXG5cbiAgICAgICAgICAgICAgICAgICAgcmVzaXplICAgICAgIDogdHJpZ2dlci5iaW5kKG51bGwsJ1dpbmRvdyByZXNpemUnLCAncmVzaXplJywgc2V0dGluZ3NbaWZyYW1lSWRdLmlmcmFtZSksXG5cbiAgICAgICAgICAgICAgICAgICAgbW92ZVRvQW5jaG9yIDogZnVuY3Rpb24oYW5jaG9yKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICB0cmlnZ2VyKCdNb3ZlIHRvIGFuY2hvcicsJ21vdmVUb0FuY2hvcjonK2FuY2hvciwgc2V0dGluZ3NbaWZyYW1lSWRdLmlmcmFtZSxpZnJhbWVJZCk7XG4gICAgICAgICAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgICAgICAgICAgc2VuZE1lc3NhZ2UgIDogZnVuY3Rpb24obWVzc2FnZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgbWVzc2FnZSA9IEpTT04uc3RyaW5naWZ5KG1lc3NhZ2UpO1xuICAgICAgICAgICAgICAgICAgICAgICAgdHJpZ2dlcignU2VuZCBNZXNzYWdlJywnbWVzc2FnZTonK21lc3NhZ2UsIHNldHRpbmdzW2lmcmFtZUlkXS5pZnJhbWUsIGlmcmFtZUlkKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH07XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICAvL1dlIGhhdmUgdG8gY2FsbCB0cmlnZ2VyIHR3aWNlLCBhcyB3ZSBjYW4gbm90IGJlIHN1cmUgaWYgYWxsXG4gICAgICAgIC8vaWZyYW1lcyBoYXZlIGNvbXBsZXRlZCBsb2FkaW5nIHdoZW4gdGhpcyBjb2RlIHJ1bnMuIFRoZVxuICAgICAgICAvL2V2ZW50IGxpc3RlbmVyIGFsc28gY2F0Y2hlcyB0aGUgcGFnZSBjaGFuZ2luZyBpbiB0aGUgaUZyYW1lLlxuICAgICAgICBmdW5jdGlvbiBpbml0KG1zZykge1xuICAgICAgICAgICAgZnVuY3Rpb24gaUZyYW1lTG9hZGVkKCkge1xuICAgICAgICAgICAgICAgIHRyaWdnZXIoJ2lGcmFtZS5vbmxvYWQnLCBtc2csIGlmcmFtZSwgdW5kZWZpbmVkICwgdHJ1ZSk7XG4gICAgICAgICAgICAgICAgY2hlY2tSZXNldCgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBhZGRFdmVudExpc3RlbmVyKGlmcmFtZSwnbG9hZCcsaUZyYW1lTG9hZGVkKTtcbiAgICAgICAgICAgIHRyaWdnZXIoJ2luaXQnLCBtc2csIGlmcmFtZSwgdW5kZWZpbmVkLCB0cnVlKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGZ1bmN0aW9uIGNoZWNrT3B0aW9ucyhvcHRpb25zKSB7XG4gICAgICAgICAgICBpZiAoJ29iamVjdCcgIT09IHR5cGVvZiBvcHRpb25zKSB7XG4gICAgICAgICAgICAgICAgdGhyb3cgbmV3IFR5cGVFcnJvcignT3B0aW9ucyBpcyBub3QgYW4gb2JqZWN0Jyk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBmdW5jdGlvbiBjb3B5T3B0aW9ucyhvcHRpb25zKSB7XG4gICAgICAgICAgICBmb3IgKHZhciBvcHRpb24gaW4gZGVmYXVsdHMpIHtcbiAgICAgICAgICAgICAgICBpZiAoZGVmYXVsdHMuaGFzT3duUHJvcGVydHkob3B0aW9uKSkge1xuICAgICAgICAgICAgICAgICAgICBzZXR0aW5nc1tpZnJhbWVJZF1bb3B0aW9uXSA9IG9wdGlvbnMuaGFzT3duUHJvcGVydHkob3B0aW9uKSA/IG9wdGlvbnNbb3B0aW9uXSA6IGRlZmF1bHRzW29wdGlvbl07XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgZnVuY3Rpb24gZ2V0VGFyZ2V0T3JpZ2luIChyZW1vdGVIb3N0KSB7XG4gICAgICAgICAgICByZXR1cm4gKCcnID09PSByZW1vdGVIb3N0IHx8ICdmaWxlOi8vJyA9PT0gcmVtb3RlSG9zdCkgPyAnKicgOiByZW1vdGVIb3N0O1xuICAgICAgICB9XG5cbiAgICAgICAgZnVuY3Rpb24gcHJvY2Vzc09wdGlvbnMob3B0aW9ucykge1xuICAgICAgICAgICAgb3B0aW9ucyA9IG9wdGlvbnMgfHwge307XG4gICAgICAgICAgICBzZXR0aW5nc1tpZnJhbWVJZF0gPSB7XG4gICAgICAgICAgICAgICAgZmlyc3RSdW5cdDogdHJ1ZSxcbiAgICAgICAgICAgICAgICBpZnJhbWVcdFx0OiBpZnJhbWUsXG4gICAgICAgICAgICAgICAgcmVtb3RlSG9zdFx0OiBpZnJhbWUuc3JjLnNwbGl0KCcvJykuc2xpY2UoMCwzKS5qb2luKCcvJylcbiAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgIGNoZWNrT3B0aW9ucyhvcHRpb25zKTtcbiAgICAgICAgICAgIGNvcHlPcHRpb25zKG9wdGlvbnMpO1xuXG4gICAgICAgICAgICBpZiAoc2V0dGluZ3NbaWZyYW1lSWRdKSB7XG4gICAgICAgICAgICAgICAgc2V0dGluZ3NbaWZyYW1lSWRdLnRhcmdldE9yaWdpbiA9IHRydWUgPT09IHNldHRpbmdzW2lmcmFtZUlkXS5jaGVja09yaWdpbiA/IGdldFRhcmdldE9yaWdpbihzZXR0aW5nc1tpZnJhbWVJZF0ucmVtb3RlSG9zdCkgOiAnKic7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBmdW5jdGlvbiBiZWVuSGVyZSgpIHtcbiAgICAgICAgICAgIHJldHVybiAoaWZyYW1lSWQgaW4gc2V0dGluZ3MgJiYgJ2lGcmFtZVJlc2l6ZXInIGluIGlmcmFtZSk7XG4gICAgICAgIH1cblxuICAgICAgICB2YXIgaWZyYW1lSWQgPSBlbnN1cmVIYXNJZChpZnJhbWUuaWQpO1xuXG4gICAgICAgIGlmICghYmVlbkhlcmUoKSkge1xuICAgICAgICAgICAgcHJvY2Vzc09wdGlvbnMob3B0aW9ucyk7XG4gICAgICAgICAgICBzZXRTY3JvbGxpbmcoKTtcbiAgICAgICAgICAgIHNldExpbWl0cygpO1xuICAgICAgICAgICAgc2V0dXBCb2R5TWFyZ2luVmFsdWVzKCk7XG4gICAgICAgICAgICBpbml0KGNyZWF0ZU91dGdvaW5nTXNnKGlmcmFtZUlkKSk7XG4gICAgICAgICAgICBzZXR1cElGcmFtZU9iamVjdCgpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgd2FybihpZnJhbWVJZCwnSWdub3JlZCBpRnJhbWUsIGFscmVhZHkgc2V0dXAuJyk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICBmdW5jdGlvbiBkZWJvdWNlKGZuLHRpbWUpIHtcbiAgICAgICAgaWYgKG51bGwgPT09IHRpbWVyKSB7XG4gICAgICAgICAgICB0aW1lciA9IHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgdGltZXIgPSBudWxsO1xuICAgICAgICAgICAgICAgIGZuKCk7XG4gICAgICAgICAgICB9LCB0aW1lKTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIHZhciBmcmFtZVRpbWVyID0ge307XG4gICAgZnVuY3Rpb24gZGVib3VuY2VGcmFtZUV2ZW50cyhmbix0aW1lLGZyYW1lSWQpIHtcbiAgICAgICAgaWYgKCFmcmFtZVRpbWVyW2ZyYW1lSWRdKSB7XG4gICAgICAgICAgICBmcmFtZVRpbWVyW2ZyYW1lSWRdID0gc2V0VGltZW91dChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBmcmFtZVRpbWVyW2ZyYW1lSWRdID0gbnVsbDtcbiAgICAgICAgICAgICAgICBmbigpO1xuICAgICAgICAgICAgfSwgdGltZSk7XG4gICAgICAgIH1cbiAgICB9XG5cblx0LyogaXN0YW5idWwgaWdub3JlIG5leHQgKi8gIC8vTm90IHRlc3RhYmxlIGluIFBoYW50b21KU1xuICAgIGZ1bmN0aW9uIGZpeEhpZGRlbklGcmFtZXMoKSB7XG4gICAgICAgIGZ1bmN0aW9uIGNoZWNrSUZyYW1lcygpIHtcbiAgICAgICAgICAgIGZ1bmN0aW9uIGNoZWNrSUZyYW1lKHNldHRpbmdJZCkge1xuICAgICAgICAgICAgICAgIGZ1bmN0aW9uIGNoa0RpbWVuc2lvbihkaW1lbnNpb24pIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuICcwcHgnID09PSAoc2V0dGluZ3Nbc2V0dGluZ0lkXSAmJiBzZXR0aW5nc1tzZXR0aW5nSWRdLmlmcmFtZS5zdHlsZVtkaW1lbnNpb25dKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBmdW5jdGlvbiBpc1Zpc2libGUoZWwpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIChudWxsICE9PSBlbC5vZmZzZXRQYXJlbnQpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGlmIChzZXR0aW5nc1tzZXR0aW5nSWRdICYmIGlzVmlzaWJsZShzZXR0aW5nc1tzZXR0aW5nSWRdLmlmcmFtZSkgJiYgKGNoa0RpbWVuc2lvbignaGVpZ2h0JykgfHwgY2hrRGltZW5zaW9uKCd3aWR0aCcpKSkge1xuICAgICAgICAgICAgICAgICAgICB0cmlnZ2VyKCdWaXNpYmlsaXR5IGNoYW5nZScsICdyZXNpemUnLCBzZXR0aW5nc1tzZXR0aW5nSWRdLmlmcmFtZSwgc2V0dGluZ0lkKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGZvciAodmFyIHNldHRpbmdJZCBpbiBzZXR0aW5ncykge1xuICAgICAgICAgICAgICAgIGNoZWNrSUZyYW1lKHNldHRpbmdJZCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBmdW5jdGlvbiBtdXRhdGlvbk9ic2VydmVkKG11dGF0aW9ucykge1xuICAgICAgICAgICAgbG9nKCd3aW5kb3cnLCdNdXRhdGlvbiBvYnNlcnZlZDogJyArIG11dGF0aW9uc1swXS50YXJnZXQgKyAnICcgKyBtdXRhdGlvbnNbMF0udHlwZSk7XG4gICAgICAgICAgICBkZWJvdWNlKGNoZWNrSUZyYW1lcywxNik7XG4gICAgICAgIH1cblxuICAgICAgICBmdW5jdGlvbiBjcmVhdGVNdXRhdGlvbk9ic2VydmVyKCkge1xuICAgICAgICAgICAgdmFyXG4gICAgICAgICAgICAgICAgdGFyZ2V0ID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignYm9keScpLFxuXG4gICAgICAgICAgICAgICAgY29uZmlnID0ge1xuICAgICAgICAgICAgICAgICAgICBhdHRyaWJ1dGVzICAgICAgICAgICAgOiB0cnVlLFxuICAgICAgICAgICAgICAgICAgICBhdHRyaWJ1dGVPbGRWYWx1ZSAgICAgOiBmYWxzZSxcbiAgICAgICAgICAgICAgICAgICAgY2hhcmFjdGVyRGF0YSAgICAgICAgIDogdHJ1ZSxcbiAgICAgICAgICAgICAgICAgICAgY2hhcmFjdGVyRGF0YU9sZFZhbHVlIDogZmFsc2UsXG4gICAgICAgICAgICAgICAgICAgIGNoaWxkTGlzdCAgICAgICAgICAgICA6IHRydWUsXG4gICAgICAgICAgICAgICAgICAgIHN1YnRyZWUgICAgICAgICAgICAgICA6IHRydWVcbiAgICAgICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICAgICAgb2JzZXJ2ZXIgPSBuZXcgTXV0YXRpb25PYnNlcnZlcihtdXRhdGlvbk9ic2VydmVkKTtcblxuICAgICAgICAgICAgb2JzZXJ2ZXIub2JzZXJ2ZSh0YXJnZXQsIGNvbmZpZyk7XG4gICAgICAgIH1cblxuICAgICAgICB2YXIgTXV0YXRpb25PYnNlcnZlciA9IHdpbmRvdy5NdXRhdGlvbk9ic2VydmVyIHx8IHdpbmRvdy5XZWJLaXRNdXRhdGlvbk9ic2VydmVyO1xuXG4gICAgICAgIGlmIChNdXRhdGlvbk9ic2VydmVyKSBjcmVhdGVNdXRhdGlvbk9ic2VydmVyKCk7XG4gICAgfVxuXG5cbiAgICBmdW5jdGlvbiByZXNpemVJRnJhbWVzKGV2ZW50KSB7XG4gICAgICAgIGZ1bmN0aW9uIHJlc2l6ZSgpIHtcbiAgICAgICAgICAgIHNlbmRUcmlnZ2VyTXNnKCdXaW5kb3cgJytldmVudCwncmVzaXplJyk7XG4gICAgICAgIH1cblxuICAgICAgICBsb2coJ3dpbmRvdycsJ1RyaWdnZXIgZXZlbnQ6ICcrZXZlbnQpO1xuICAgICAgICBkZWJvdWNlKHJlc2l6ZSwxNik7XG4gICAgfVxuXG5cdC8qIGlzdGFuYnVsIGlnbm9yZSBuZXh0ICovICAvL05vdCB0ZXN0YWJsZSBpbiBQaGFudG9tSlNcbiAgICBmdW5jdGlvbiB0YWJWaXNpYmxlKCkge1xuICAgICAgICBmdW5jdGlvbiByZXNpemUoKSB7XG4gICAgICAgICAgICBzZW5kVHJpZ2dlck1zZygnVGFiIFZpc2FibGUnLCdyZXNpemUnKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmKCdoaWRkZW4nICE9PSBkb2N1bWVudC52aXNpYmlsaXR5U3RhdGUpIHtcbiAgICAgICAgICAgIGxvZygnZG9jdW1lbnQnLCdUcmlnZ2VyIGV2ZW50OiBWaXNpYmxpdHkgY2hhbmdlJyk7XG4gICAgICAgICAgICBkZWJvdWNlKHJlc2l6ZSwxNik7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICBmdW5jdGlvbiBzZW5kVHJpZ2dlck1zZyhldmVudE5hbWUsZXZlbnQpIHtcbiAgICAgICAgZnVuY3Rpb24gaXNJRnJhbWVSZXNpemVFbmFibGVkKGlmcmFtZUlkKSB7XG4gICAgICAgICAgICByZXR1cm5cdHNldHRpbmdzW2lmcmFtZUlkXSAmJlxuICAgICAgICAgICAgICAgICdwYXJlbnQnID09PSBzZXR0aW5nc1tpZnJhbWVJZF0ucmVzaXplRnJvbSAmJlxuICAgICAgICAgICAgICAgIHNldHRpbmdzW2lmcmFtZUlkXS5hdXRvUmVzaXplICYmXG4gICAgICAgICAgICAgICAgIXNldHRpbmdzW2lmcmFtZUlkXS5maXJzdFJ1bjtcbiAgICAgICAgfVxuXG4gICAgICAgIGZvciAodmFyIGlmcmFtZUlkIGluIHNldHRpbmdzKSB7XG4gICAgICAgICAgICBpZihpc0lGcmFtZVJlc2l6ZUVuYWJsZWQoaWZyYW1lSWQpKSB7XG4gICAgICAgICAgICAgICAgdHJpZ2dlcihldmVudE5hbWUsIGV2ZW50LCBkb2N1bWVudC5nZXRFbGVtZW50QnlJZChpZnJhbWVJZCksIGlmcmFtZUlkKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH1cblxuICAgIGZ1bmN0aW9uIHNldHVwRXZlbnRMaXN0ZW5lcnMoKSB7XG4gICAgICAgIGFkZEV2ZW50TGlzdGVuZXIod2luZG93LCdtZXNzYWdlJyxpRnJhbWVMaXN0ZW5lcik7XG5cbiAgICAgICAgYWRkRXZlbnRMaXN0ZW5lcih3aW5kb3csJ3Jlc2l6ZScsIGZ1bmN0aW9uKCkge3Jlc2l6ZUlGcmFtZXMoJ3Jlc2l6ZScpO30pO1xuXG4gICAgICAgIGFkZEV2ZW50TGlzdGVuZXIoZG9jdW1lbnQsJ3Zpc2liaWxpdHljaGFuZ2UnLHRhYlZpc2libGUpO1xuICAgICAgICBhZGRFdmVudExpc3RlbmVyKGRvY3VtZW50LCctd2Via2l0LXZpc2liaWxpdHljaGFuZ2UnLHRhYlZpc2libGUpOyAvL0FuZHJpb2QgNC40XG4gICAgICAgIGFkZEV2ZW50TGlzdGVuZXIod2luZG93LCdmb2N1c2luJyxmdW5jdGlvbigpIHtyZXNpemVJRnJhbWVzKCdmb2N1cycpO30pOyAvL0lFOC05XG4gICAgICAgIGFkZEV2ZW50TGlzdGVuZXIod2luZG93LCdmb2N1cycsZnVuY3Rpb24oKSB7cmVzaXplSUZyYW1lcygnZm9jdXMnKTt9KTtcbiAgICB9XG5cblxuICAgIGZ1bmN0aW9uIGZhY3RvcnkoKSB7XG4gICAgICAgIGZ1bmN0aW9uIGluaXQob3B0aW9ucyxlbGVtZW50KSB7XG4gICAgICAgICAgICBmdW5jdGlvbiBjaGtUeXBlKCkge1xuICAgICAgICAgICAgICAgIGlmKCFlbGVtZW50LnRhZ05hbWUpIHtcbiAgICAgICAgICAgICAgICAgICAgdGhyb3cgbmV3IFR5cGVFcnJvcignT2JqZWN0IGlzIG5vdCBhIHZhbGlkIERPTSBlbGVtZW50Jyk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIGlmICgnSUZSQU1FJyAhPT0gZWxlbWVudC50YWdOYW1lLnRvVXBwZXJDYXNlKCkpIHtcbiAgICAgICAgICAgICAgICAgICAgdGhyb3cgbmV3IFR5cGVFcnJvcignRXhwZWN0ZWQgPElGUkFNRT4gdGFnLCBmb3VuZCA8JytlbGVtZW50LnRhZ05hbWUrJz4nKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmKGVsZW1lbnQpIHtcbiAgICAgICAgICAgICAgICBjaGtUeXBlKCk7XG4gICAgICAgICAgICAgICAgc2V0dXBJRnJhbWUoZWxlbWVudCwgb3B0aW9ucyk7XG4gICAgICAgICAgICAgICAgaUZyYW1lcy5wdXNoKGVsZW1lbnQpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgZnVuY3Rpb24gd2FybkRlcHJlY2F0ZWRPcHRpb25zKG9wdGlvbnMpIHtcbiAgICAgICAgICAgIGlmIChvcHRpb25zICYmIG9wdGlvbnMuZW5hYmxlUHVibGljTWV0aG9kcykge1xuICAgICAgICAgICAgICAgIHdhcm4oJ2VuYWJsZVB1YmxpY01ldGhvZHMgb3B0aW9uIGhhcyBiZWVuIHJlbW92ZWQsIHB1YmxpYyBtZXRob2RzIGFyZSBub3cgYWx3YXlzIGF2YWlsYWJsZSBpbiB0aGUgaUZyYW1lJyk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICB2YXIgaUZyYW1lcztcblxuICAgICAgICBzZXR1cFJlcXVlc3RBbmltYXRpb25GcmFtZSgpO1xuICAgICAgICBzZXR1cEV2ZW50TGlzdGVuZXJzKCk7XG5cbiAgICAgICAgcmV0dXJuIGZ1bmN0aW9uIGlGcmFtZVJlc2l6ZUYob3B0aW9ucyx0YXJnZXQpIHtcbiAgICAgICAgICAgIGlGcmFtZXMgPSBbXTsgLy9Pbmx5IHJldHVybiBpRnJhbWVzIHBhc3QgaW4gb24gdGhpcyBjYWxsXG5cbiAgICAgICAgICAgIHdhcm5EZXByZWNhdGVkT3B0aW9ucyhvcHRpb25zKTtcblxuICAgICAgICAgICAgc3dpdGNoICh0eXBlb2YodGFyZ2V0KSkge1xuICAgICAgICAgICAgICAgIGNhc2UgJ3VuZGVmaW5lZCc6XG4gICAgICAgICAgICAgICAgY2FzZSAnc3RyaW5nJzpcbiAgICAgICAgICAgICAgICAgICAgQXJyYXkucHJvdG90eXBlLmZvckVhY2guY2FsbChcbiAgICAgICAgICAgICAgICAgICAgICAgIGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoIHRhcmdldCB8fCAnaWZyYW1lJyApLFxuICAgICAgICAgICAgICAgICAgICAgICAgaW5pdC5iaW5kKHVuZGVmaW5lZCwgb3B0aW9ucylcbiAgICAgICAgICAgICAgICAgICAgKTtcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgY2FzZSAnb2JqZWN0JzpcbiAgICAgICAgICAgICAgICAgICAgaW5pdChvcHRpb25zLHRhcmdldCk7XG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgIGRlZmF1bHQ6XG4gICAgICAgICAgICAgICAgICAgIHRocm93IG5ldyBUeXBlRXJyb3IoJ1VuZXhwZWN0ZWQgZGF0YSB0eXBlICgnK3R5cGVvZih0YXJnZXQpKycpJyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHJldHVybiBpRnJhbWVzO1xuICAgICAgICB9O1xuICAgIH1cblxuICAgIGZ1bmN0aW9uIGNyZWF0ZUpRdWVyeVB1YmxpY01ldGhvZCgkKSB7XG4gICAgICAgIGlmICghJC5mbikge1xuICAgICAgICAgICAgaW5mbygnJywnVW5hYmxlIHRvIGJpbmQgdG8galF1ZXJ5LCBpdCBpcyBub3QgZnVsbHkgbG9hZGVkLicpO1xuICAgICAgICB9IGVsc2UgaWYgKCEkLmZuLmlGcmFtZVJlc2l6ZSkge1xuICAgICAgICAgICAgJC5mbi5pRnJhbWVSZXNpemUgPSBmdW5jdGlvbiAkaUZyYW1lUmVzaXplRihvcHRpb25zKSB7XG4gICAgICAgICAgICAgICAgZnVuY3Rpb24gaW5pdChpbmRleCwgZWxlbWVudCkge1xuICAgICAgICAgICAgICAgICAgICBzZXR1cElGcmFtZShlbGVtZW50LCBvcHRpb25zKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICByZXR1cm4gdGhpcy5maWx0ZXIoJ2lmcmFtZScpLmVhY2goaW5pdCkuZW5kKCk7XG4gICAgICAgICAgICB9O1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgaWYgKHdpbmRvdy5qUXVlcnkpIHsgY3JlYXRlSlF1ZXJ5UHVibGljTWV0aG9kKHdpbmRvdy5qUXVlcnkpOyB9XG5cbiAgICBpZiAodHlwZW9mIGRlZmluZSA9PT0gJ2Z1bmN0aW9uJyAmJiBkZWZpbmUuYW1kKSB7XG4gICAgICAgIGRlZmluZShbXSxmYWN0b3J5KTtcbiAgICB9IGVsc2UgaWYgKHR5cGVvZiBtb2R1bGUgPT09ICdvYmplY3QnICYmIHR5cGVvZiBtb2R1bGUuZXhwb3J0cyA9PT0gJ29iamVjdCcpIHsgLy9Ob2RlIGZvciBicm93c2VyZnlcbiAgICAgICAgbW9kdWxlLmV4cG9ydHMgPSBmYWN0b3J5KCk7XG4gICAgfSBlbHNlIHtcbiAgICAgICAgd2luZG93LmlGcmFtZVJlc2l6ZSA9IHdpbmRvdy5pRnJhbWVSZXNpemUgfHwgZmFjdG9yeSgpO1xuICAgIH1cblxufSkoKTsiXX0=
