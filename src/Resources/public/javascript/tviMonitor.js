/*! Copyright (c) 2018 Vladimir Turnaev turnaev@gmail.com
 * Licensed under the MIT License (LICENSE.txt)
 */

function v(o) {
    console.log(o)
}

(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // Node/CommonJS style for Browserify
        module.exports = factory;
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($) {

    "use strict";

    const DEFAULT_TIMEOUT = 3;
    const STATUS_TIMEOUT = 3;

    const STATUS_CODE_SUCCESS = 0;
    const STATUS_CODE_WARNING = 100;
    const STATUS_CODE_SKIP = 200;
    const STATUS_CODE_UNKNOWN = 300;
    const STATUS_CODE_FAILURE = 1000;
    const STATUS_UNKNOWN = 'UNKNOWN';

    var tviMonitor = {
        iconMap: {
            [STATUS_CODE_SUCCESS]: 'fas fa-xs fa-check-circle',
            [STATUS_CODE_WARNING]: 'fas fa-xs fa-exclamation-circle',
            [STATUS_CODE_SKIP]: 'fas fa-xs fa-ban',
            [STATUS_CODE_UNKNOWN]: 'far fa-xs fa-question-circle',
            [STATUS_CODE_FAILURE]: 'fas fa-xs fa-exclamation-triangle',
            [STATUS_UNKNOWN]: 'far fa-xs fa-question-circlee'
        },
        classMap: {
            [STATUS_CODE_SUCCESS]: 'check-status-success',
            [STATUS_CODE_WARNING]: 'check-status-warning',
            [STATUS_CODE_SKIP]: 'check-status-skip',
            [STATUS_CODE_UNKNOWN]: 'check-status-unknown',
            [STATUS_CODE_FAILURE]: 'check-status-failure',
            [STATUS_UNKNOWN]: 'check-status-unknown'
        },
        icon: function (statusCode) {
            return tviMonitor.iconMap[statusCode] || tviMonitor.iconMap[STATUS_UNKNOWN]
        },
        class: function (statusCode) {
            return tviMonitor.classMap[statusCode] || tviMonitor.classMap[STATUS_UNKNOWN]
        },
        start: function ($checkResult, inIconMap) {

            this.iconMap = $.extend({}, this.iconMap, inIconMap);
            var $checks = $('.check', $checkResult);

            $checks.each(function () {

                var $check = $(this);

                var url = $check.data('url');
                var heardBeat = $check.data('heard-beat');

                var $status = $('.check-field-status', $check);
                var $statusName = $('.check-status-name', $status);
                var $statusCode = $('.check-status-code i', $status);

                var $message = $('.check-field-message', $check);
                var $data = $('.check-field-data', $check);

                var $refresh = $('.check-controll .check-refresh', $check);
                var $refreshStatus = $('.check-controll .check-refresh i', $check);

                var $refreshLock = $('.check-controll .check-refresh .check-lock', $check);
                var $refreshTime = $('.check-controll .check-refresh .check-time', $check);

                $refresh.attr('disabled', false);
                $refreshLock.attr('disabled', false);

                function setStatus(statusCode) {

                    $refreshStatus.removeClass('fa-spin');
                    $statusCode.removeAttr('style').hide().fadeIn();

                    var statusIcon = tviMonitor.icon(statusCode);
                    $statusCode.removeAttr('class').addClass(statusIcon);

                    var statusClass = tviMonitor.class(statusCode);
                    $check.removeAttr('class').addClass('check ' + statusClass)
                }

                function setData(data) {

                    $message.text(data.message || '');
                    $statusName.text(data.statusName || '');

                    if (data.data == undefined) {
                        $data.text('');
                    } else {
                        var data = data.data;

                        if (typeof data == 'object') {
                            data = JSON.stringify(data)
                        }
                        $data.text(data);
                    }
                }

                $refreshTime.on('blur', function () {
                    $refresh.trigger('click');

                    if (!$refreshTime.val()) {
                        $refreshTime.val(DEFAULT_TIMEOUT)
                    }
                });

                $refreshLock.on('change', function () {
                    var isChecked = $refreshLock.prop('checked');

                    if (isChecked) {
                        $refresh.addClass('check-refresh-active');
                        $refreshTime.attr('disabled', false);
                        $refreshTime.attr('hidden', false);

                        if (!$refreshTime.val()) {
                            $refreshTime.val(DEFAULT_TIMEOUT)
                        }

                    } else {
                        $refresh.removeClass('check-refresh-active');
                        $refreshTime.attr('hidden', true);
                        $refreshTime.attr('disabled', true);
                    }
                });

                var timer = null;

                function refreshByTimer() {
                    if (timer) {
                        clearTimeout(timer);
                    }

                    if (heardBeat > 0) {
                        timer = setTimeout(function () {
                            $refresh.trigger('click');
                        }, 1000 * heardBeat);
                    } else if ($refreshLock.prop('checked') > 0) {
                        timer = setTimeout(function () {
                            $refresh.trigger('click');
                        }, 1000 * $refreshTime.val());
                    }
                }

                $refresh.on('click', function (e) {
                    var $target = $(e.target);

                    if ($target.is(':checkbox') || $target.is('.check-time')) {
                        if ($target.is(':checkbox')) {
                            var isChecked = $target.prop('checked');
                            if (!isChecked) {
                                return;
                            }
                        } else {
                            return;
                        }
                    }

                    $refreshStatus.addClass('fa-spin');

                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            setData(data);
                            setStatus(data.statusCode);
                            refreshByTimer();
                        },
                        error: function () {
                            setData({});
                            setStatus(STATUS_UNKNOW);
                            refreshByTimer();

                            console.log("error while loading UI checks: " + url);
                        }
                    });
                });

                $refresh.trigger('click')
            });

            var $allRefresh = $('.head .controll .refresh');

            $allRefresh.on('click', function (e) {

                var isLock = $(e.target).is(':checkbox');

                if (isLock) {
                    var isChecked = $(e.target).prop('checked');

                    $checks.each(function (e) {

                        var $check = $(this);
                        var $lock = $('.check-refresh :checkbox:not(:disabled)', $check);

                        if ($lock.is(':checkbox')) {
                            $lock.prop('checked', isChecked).trigger('change');

                            if (isChecked) {
                                var $refresh = $('.check-refresh', $check);
                                $refresh.click();
                            }
                        }
                    });

                } else {

                    $checks.each(function (e) {

                        var $check = $(this);
                        var $lock = $('.check-refresh :checkbox:not(:disabled)', $check);

                        if ($lock.is(':checkbox') && !$lock.prop('checked')) {
                            var $refresh = $('.check-refresh', $check);
                            $refresh.click();
                        }
                    })
                }
            });

            return this
        }
    };

    $.fn.tviMonitor = function (inIconMap) {

        var $checkResult = $(this);
        tviMonitor.start($checkResult, inIconMap);

        return this
    }
}));
