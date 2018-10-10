/*! Copyright (c) 2018 Vlaimir Turnaev turnaev@gmail.com
 * Licensed under the MIT License (LICENSE.txt)
 */

(function (factory) {
    if ( typeof define === 'function' && define.amd ) {
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

    var DEFAULT_TIMEOUT = 3;

    var icons = {
        0: 'fa-check-circle-o',
        100: 'fa-exclamation-triangle',
        200: 'fa-ban',
        300: 'fa-question-circle',
        1000: 'fa-exclamation-circle',
        'def': 'fa-question-circle'
    };

    var classes = {
        0: 'status-success',
        100: 'status-warning',
        200: 'status-skip',
        300: 'status-unknown',
        1000: 'status-failure',
        'def': 'status-unknown'
    };
    var methods  = {

        icon: function(statusCode) {
            return icons[statusCode] || icons['def']
        },

        class: function(statusCode) {
            return classes[statusCode] || classes['def']
        }
    };

    $.fn.app = function () {

        var $checkResult = $(this);
        var $checks = $('.check', $checkResult);

        $checks.each(function() {

            var $check = $(this);

            var url = $check.data('url');
            var heardBeat = $check.data('heard-beat');

            var $status = $('.status', $check);
            var $statusName = $('.status-name', $status);
            var $statusCode = $('.status-code i', $status);

            var $message = $('.message', $check);
            var $data = $('.data', $check);

            var $refresh = $('.controll .refresh', $check);
            var $refreshStatus = $('.controll .refresh i', $check);

            var $refreshLock = $('.controll .refresh .lock', $check);
            var $refreshTime = $('.controll .refresh .time', $check);

            $refresh.removeClass('disabled');

            function setStatus(statusCode) {

                $refreshStatus.removeClass('fa-spin');

                $status.removeAttr( "style" ).hide().fadeIn();
                $message.removeAttr( "style" ).hide().fadeIn();
                $data.removeAttr( "style" ).hide().fadeIn();

                var statusIcon = methods.icon(statusCode);
                $statusCode.removeAttr('class').addClass('fa ' + statusIcon);

                var statusClass = methods.class(statusCode);
                $check.removeAttr('class').addClass('check ' + statusClass)
            }

            function setData(data) {

                $message.text(data.message || '');
                $statusName.text(data.statusName || '');

                if(data.data == undefined) {
                    $data.text('');
                } else {
                    var data = data.data;

                    if (typeof data == 'object') {
                        data = JSON.stringify(data)
                    }
                    $data.text(data);
                }
            }

            $refreshTime.on('blur', function() {
                $refresh.trigger('click');

                if(!$refreshTime.val()) {
                    $refreshTime.val(DEFAULT_TIMEOUT)
                }
            });

            $refreshLock.on('change', function() {
                var isChecked = $refreshLock.prop('checked');

                if(isChecked) {
                    $refreshTime.removeClass('hidden');
                    if(!$refreshTime.val()) {
                        $refreshTime.val(DEFAULT_TIMEOUT)
                    }
                } else {
                    $refreshTime.addClass('hidden');
                }
            });

            var timer = null;

            function refreshByTimer() {
                if(timer) {clearTimeout(timer);}

                if(heardBeat > 0) {
                    timer = setTimeout(function() {$refresh.trigger('click');}, 1000 * heardBeat);
                } else if($refreshLock.prop('checked') > 0) {
                    timer = setTimeout(function() {$refresh.trigger('click');}, 1000 * $refreshTime.val());
                }
            }

            $refresh.on('click', function(e) {

                var $target = $(e.target);

                if($target.is(':checkbox')) {

                    var isChecked = $target.prop('checked');
                    if(!isChecked) {
                        return;
                    }
                } else if($target.is('.time')) {
                    return;
                }

                $refreshStatus.addClass('fa-spin');

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        setData(data);
                        setStatus(data.statusCode);
                        refreshByTimer();
                    },
                    error: function() {
                        setData({});
                        setStatus('def');
                        refreshByTimer();

                        console.log("error while loading ui checks: " +  url);
                    }
                });

            });

            $refresh.trigger('click')
        });

        var $allControll = $('.check-head .controll', $checkResult);
        var $allRefresh = $('.refresh', $allControll);

        $allRefresh.on('click', function(e) {

            var isLock = $(e.target).is(':checkbox');

            if(isLock) {

                var isChecked = $(e.target).prop('checked');

                $checks.each(function(e) {

                    var $check = $(this);
                    var $lock = $('.refresh :checkbox:not(:disabled)', $check);

                    if($lock.size()) {

                        $lock.prop('checked', isChecked).trigger('change');

                        if(isChecked) {
                            var $refresh = $('.refresh', $check);
                            $refresh.click();
                        }
                    }
                });

            } else {

                $checks.each(function(e) {
                    var $check = $(this);
                    var $lock = $('.refresh :checkbox:not(:disabled)', $check);
                    if($lock.size()) {
                        var $refresh = $('.refresh', $check);
                        $refresh.click();
                    }
                })
            }
        });

        return this
    };
}));
