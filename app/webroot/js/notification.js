(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.MooNotification = factory(root.jQuery);
    }
}(this, function ($) {
    var url = {};
    var active = true;
    var interval = 10000; // seconds

    var initLoadNotification = function() {
        if ($('#notificationDropdown')) {
            $('#notificationDropdown').click(function() {
                var show_notification_url = url.show_notification;
                if (typeof(show_notification_url) != 'undefined'){
                    $(this).next('ul:first').spin('tiny');
                    mooAjax(show_notification_url, 'get', '', function(data) {
                        $('#notifications_list').html(data);
                        $('#notificationDropdown').next('ul:first').spin(false);
                        $('.initSlimScroll').slimScroll({ height: '500px' });
                        //binding hover delete icon
                        $("#notifications_list li").hover(
                            function () {
                                $(this).contents().find('.delete-icon').show();
                            },
                            function () {
                                $(this).contents().find('.delete-icon').hide();
                            }
                        );
                    });
                }
            });
        }

        if ($('#conversationDropdown')) {
            $('#conversationDropdown').click(function() {
                var show_conversation_url = url.show_conversation;
                $(this).next('ul:first').spin('tiny');
                mooAjax(show_conversation_url, 'get', '', function(data) {
                    $('#conversation_list').html(data);
                    $('#conversationDropdown').next('ul:first').spin(false);
                    $('.initSlimScroll').slimScroll({ height: '500px' });
                });
            });
        }
    }

    var initRefreshNotification = function(){
        var refresh_notification_url = url.refresh_notification_url;
        if (typeof(refresh_notification_url) != 'undefined'){
            setInterval(function(){
                $.getJSON(refresh_notification_url, function(data) {
                    // update notification count for sidebar menu
                    if ($('#notification_count')){
                        $('#notification_count').html(data.notification_count);
                    }

                    // update notification count for topbar menu
                    if (parseInt(data.notification_count) > 0){
                        if($('.notification_count').length > 0)
                        {
                            $('.notification_count').html(data.notification_count);

                        }else{
                            $('#notificationDropdown').append('<span class="notification_count">1</span>');
                        }
                    }else{
                        if($('.notification_count')){
                            $('.notification_count').remove();
                        }
                    }

                    // update conversation count
                    if (parseInt(data.conversation_count) > 0){
                        if($('.conversation_count').length > 0)
                        {
                            $('.conversation_count').html(data.conversation_count);

                        }else{
                            $('#conversationDropdown').append('<span class="conversation_count">1</span>');
                        }
                    }else{
                        if($('.conversation_count')){
                            $('.conversation_count').remove();
                        }
                    }

                }).fail(function() {
                    console.log("Error when calling " + refresh_notification_url)
                });
            }, interval);
        }
    }

    return{
        init: function() {
    		if (active)
    		{
	            initLoadNotification();
	            initRefreshNotification();
    		}
        },
        setUrl: function(a) {
            url = a;
        },
        setActive: function (a)
        {
        	active = a;
        },
        setInterval: function(a){
            // only set new interval when it greater than 0, by default it's 30 seconds
            if (a > 0){
                interval = a * 1000;
            }
        }
    }
}));