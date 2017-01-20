// global.js - submitComment(activity_id)
// global.js - submitItemComment(item_type, item_id, activity_id)
// global.js - showPostedComment(activity_id, data)
// global.js - showCommentButton(activity_id)
// global.js - showCommentForm(activity_id)
// global.js - postWall
// global.js - editActivity
// global.js - cancelEditActivity
// global.js - confirmEditActivity
// global.js - showFeedVideo
// global.js - showAllComments
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery','moophrase'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooActivities = factory(root.jQuery,root.MooPhrase);
    }
}(this, function ($,MooPhrase) {
    var config = {};
    function removeTags(item_id, item_type){
        $.fn.SimpleModal({
            btn_ok: MooPhrase.__('confirm'),
            callback: function(){
                $.post(mooConfig.url.base+'/activities/ajax_remove_tags', {item_id: item_id, item_type : item_type}, function() {
                    window.location.reload();
                });
            },
            title: MooPhrase.__('remove_tags'),
            contents: MooPhrase.__('remove_tags_contents'),
            model: 'confirm', hideFooter: false, closeButton: false
        }).showModal();
    }
    function removeActivity(id)
    {
        $.fn.SimpleModal({
            btn_ok: MooPhrase.__('ok'),
            callback: function(){
                $.post(mooConfig.url.base+'/activities/ajax_remove', {id: id}, function() {
                    $('#activity_'+id).fadeOut('normal', function() {
                        $('#activity_'+id).remove();
                    });
                });
            },
            title: MooPhrase.__('please_confirm_remove_this_activity'),
            contents: MooPhrase.__(''),
            model: 'confirm', hideFooter: false, closeButton: false
        }).showModal();
    }

    var activity_edit_array = [];
    function editActivity(activity_id)
    {
        if (activity_edit_array.indexOf(activity_id) === -1)
        {
            $.post(baseUrl + '/activities/ajax_loadActivityEdit/'+ activity_id, function(data){
                $('#activity_feed_content_text_'+activity_id + ' .comment_message').hide();
                $(data).insertAfter($('#activity_feed_content_text_'+activity_id + ' .comment_message'));
                activity_edit_array.push(activity_id);
                init();

                if(typeof mooMention !== 'undefined'){
                    //user mention
                    mooMention.init($(data).find('textarea').attr('id'),'edit_activity');
                }
                //user emoji
                mooEmoji.init($(data).find('textarea').attr('id'));
            });
        }
    }

    function cancelEditActivity(activity_id)
    {
        //destroy overlay instance;
        if($('#message_edit_'+activity_id).siblings('.textoverlay')){
            $('#message_edit_'+activity_id).destroyOverlayInstance($('#message_edit_'+activity_id));
        }

        $('#activity_feed_content_text_'+activity_id + ' .comment_message').show();
        $('#activity_edit_'+activity_id).remove();

        var index = $.inArray(activity_id, activity_edit_array);
        activity_edit_array.splice(index, 1);
    }

    function confirmEditActivity(activity_id)
    {
        if ($.trim($('#message_edit_'+activity_id).val()) != '')
        {
            var messageVal;
            if(jQuery("#message_edit_"+activity_id+"_hidden").length != 0){
                messageVal = jQuery("#message_edit_"+activity_id+'_hidden').val();
            }else{
                messageVal = jQuery("#message_edit_"+activity_id).val()
            }
            $.post(baseUrl + '/activities/ajax_editActivity/'+ activity_id,{message: messageVal}, function(data){
                //destroy overlay instance;
                if($('#message_edit_'+activity_id).siblings('.textoverlay')){
                    $('#message_edit_'+activity_id).destroyOverlayInstance($('#message_edit_'+activity_id));
                }

                $('#activity_feed_content_text_'+activity_id + ' .comment_message').html($(data).html());
                $('#history_activity_'+activity_id).show();
                cancelEditActivity(activity_id);
            });
        }
    }
    function removeActivityComment(id)
    {
        $.fn.SimpleModal({
            btn_ok: MooPhrase.__('ok'),
            callback: function(){
                $.post(mooConfig.url.base+'/activities/ajax_removeComment', {id: id}, function() {
                    $('#comment_'+id).fadeOut('normal', function() {
                        $('#comment_'+id).remove();
                    });
                });
            },
            title: MooPhrase.__('please_confirm'),
            contents: MooPhrase.__('please_confirm_remove_this_activity'),
            model: 'confirm', hideFooter: false, closeButton: false
        }).showModal();
    }
    function removeItemComment(id)
    {
        $.fn.SimpleModal({
            btn_ok: MooPhrase.__('ok'),
            btn_cancel: MooPhrase.__('cancel'),
            callback: function(){
                $.post(mooConfig.url.base+'/comments/ajax_remove', {id: id}, function() {
                    $('#itemcomment_'+id).fadeOut('normal', function() {
                        $('#itemcomment_'+id).remove();
                    });
                });
            },
            title:  MooPhrase.__('please_confirm'),
            contents: MooPhrase.__('please_confirm_remove_this_activity'),
            model: 'confirm', hideFooter: false, closeButton: false
        }).showModal();
    }
    function removeActivityPhotoComment(id)
    {
        $.fn.SimpleModal({
            btn_ok: MooPhrase.__('ok'),
            btn_cancel: MooPhrase.__('cancel'),
            callback: function(){
                $.post(mooConfig.url.base+'/comments/ajax_remove', {id: id}, function() {
                    $('#photo_comment_'+id).fadeOut('normal', function() {
                        $('#photo_comment_'+id).remove();
                    });
                });
            },
            title:  MooPhrase.__('please_confirm'),
            contents: MooPhrase.__('please_confirm_remove_this_activity'),
            model: 'confirm', hideFooter: false, closeButton: false
        }).showModal();
    }
    function submitComment(activity_id)
    {
        if (jQuery.trim(jQuery("#commentForm_"+activity_id).val()) != '' || $('#comment_image_'+activity_id).val() != '')
        {
            $('#commentButton_' + activity_id + ' a').addClass('disabled');
            $('#commentButton_' + activity_id + ' a').prepend('<i class="icon-refresh icon-spin"></i>');
            var comment = (jQuery("#commentForm_"+activity_id).siblings('input.messageHidden').length > 0) ? jQuery("#commentForm_"+activity_id).siblings('input.messageHidden').val() : jQuery("#commentForm_"+activity_id).val();
            $.post(baseUrl + "/activities/ajax_comment", {activity_id: activity_id,thumbnail:$('#comment_image_'+activity_id).val(), comment: comment}, function(data){
                if (data != ''){
                    showPostedComment(activity_id, data);

                    if(typeof mooMention !== 'undefined'){
                        //reset mention
                        var textArea = jQuery("#commentForm_"+activity_id);
                        mooMention.resetMention(textArea);
                    }
                }
            });
        }
    }
    function registerImageComment()
    {
        if ($('.comment_thumb a').length)
        {
            $('.comment_thumb a').magnificPopup({
                type:'image',
                gallery: { enabled: false },
                zoom: {
                    enabled: true,
                    opener: function(openerElement) {
                        return openerElement;
                    }
                }
            });
        }
    }
    function submitItemComment(item_type, item_id, activity_id)
    {
        if ($.trim(jQuery("#commentForm_"+activity_id).val()) != '' || $('#comment_image_'+activity_id).val() != '')
        {
            $('#commentButton_' + activity_id + ' a').prepend('<i class="icon-refresh icon-spin"></i>');
            $('#commentButton_' + activity_id + ' a').addClass('disabled');
            var message = '';
            if(jQuery("#commentForm_"+activity_id).siblings('.messageHidden').length > 0){
                message = jQuery("#commentForm_"+activity_id).siblings('.messageHidden').val();
            }else{
                message = jQuery("#commentForm_"+activity_id).val();
            }
            $.post(baseUrl + "/comments/ajax_share", {type: item_type, target_id: item_id, thumbnail:$('#comment_image_'+activity_id).val() ,message: message, activity: 1}, function(data){
                if (data != ''){
                    showPostedComment(activity_id, data);

                    if(typeof mooMention !== 'undefined'){
                        //reset mention
                        var textArea = jQuery("#commentForm_"+activity_id);
                        mooMention.resetMention(textArea);
                    }
                }
            });
        }
    }

    function showPostedComment(activity_id, data)
    {
        $('#newComment_'+activity_id).after(data);
        $('.slide').slideDown();
        $('#commentButton_' + activity_id + ' a').removeClass('disabled');
        $('#commentButton_' + activity_id + ' a i').remove();
        $("#commentForm_"+activity_id).val('');
        $("#commentButton_"+activity_id).hide();
        registerCrossIcons();
        $('.commentBox').css('height', '27px');
        $('#comment_preview_image_' + activity_id).html('');
        $('#comment_image_' + activity_id).val('');
        $('#comment_button_attach_'+activity_id).show();
        registerImageComment();
        init();
    }

    function showCommentButton(activity_id)
    {
        $("#commentButton_"+activity_id).show();
    }

    function showCommentForm(activity_id)
    {
        jQuery("#comments_"+activity_id).show();
        jQuery("#newComment_"+activity_id).show();
    }

    function changeActivityPrivacy(obj, activity_id, privacy)
    {
        $.post(baseUrl + '/activities/ajax_changeActivityPrivacy/',{activityId: activity_id, privacy: privacy}, function(data){
            if(data != ''){
                data = JSON.parse(data);
                var parent = obj.parents('.dropdown');
                parent.find('a#permission_'+activity_id).attr('original-title',data.text);
                parent.find('a#permission_'+activity_id+' i').attr('class',data.icon);
                parent.find('.dropdown-menu li a').removeClass('n52');
                obj.addClass('n52');
            }
        });
    }

    function init(configParam){
        $('textarea:not(.no-grow)').autogrow();
        if( typeof config !== undefined) config = configParam;

        // remove event
		$('body').off('click.activity','a.owner-remove-tags').on('click.activity','a.owner-remove-tags',function(){        
            var data = $(this).data();
            if( typeof data.activityItemType !== undefined  && typeof data.activityId !== undefined)
                removeTags(data.activityId,data.activityItemType);
        });
        // edit event		
		
        $('body').off('click.activity','a.admin-edit-activity').on('click.activity','a.admin-edit-activity',function(){
            var data = $(this).data();

            if( typeof data.activityId !== undefined)
                editActivity(data.activityId);
        });
        // remove activity event
        $('body').off('click.activity','a.admin-or-owner-remove-activity').on('click.activity','a.admin-or-owner-remove-activity',function(){
            var data = $(this).data();

            if( typeof data.activityId !== undefined)
                removeActivity(data.activityId);
        });
        // cancel edit activity event
		$('body').off('click.activity','a.admin-or-owner-cancel-edit-activity').on('click.activity','a.admin-or-owner-cancel-edit-activity',function(){
            var data = $(this).data();

            if( typeof data.activityId !== undefined)
                cancelEditActivity(data.activityId);
        });
        // comfirm edit activity event
		$('body').off('click.activity','a.admin-or-owner-confirm-edit-activity').on('click.activity','a.admin-or-owner-confirm-edit-activity',function(){
            var data = $(this).data();

            if( typeof data.activityId !== undefined)
                confirmEditActivity(data.activityId);
        });
        // remove  activity comment event		
		$('body').off('click.activity','a.admin-or-owner-confirm-delete-activity-comment').on('click.activity','a.admin-or-owner-confirm-delete-activity-comment',function(){
            var data = $(this).data();

            if( typeof data.activityCommentId !== undefined)
                removeActivityComment(data.activityCommentId);
        });
        // remove  activity comment event
		$('body').off('click.activity','a.admin-or-owner-confirm-delete-activity-comment').on('click.activity','a.admin-or-owner-confirm-delete-activity-comment',function(){
            var data = $(this).data();

            if( typeof data.activityCommentId !== undefined)
                removeActivityComment(data.activityCommentId);
        });
        // remove  activity comment event
		$('body').off('click.activity','a.admin-or-owner-confirm-delete-item-comment').on('click.activity','a.admin-or-owner-confirm-delete-item-comment',function(){
            var data = $(this).data();

            if( typeof data.commentId !== undefined)
                removeItemComment(data.commentId);
        });
        // remove  activity photo comment event
        $('body').off('click.activity','a.admin-or-owner-confirm-delete-photo-comment').on('click.activity','a.admin-or-owner-confirm-delete-photo-comment',function(){
            var data = $(this).data();

            if( typeof data.commentId !== undefined)
                removeActivityPhotoComment(data.commentId);
        });
        // submitComment event
        $('body').off('click.activity','a.viewer-submit-comment').on('click.activity','a.viewer-submit-comment',function(){
            var data = $(this).data();

            if( typeof data.activityId !== undefined)
                submitComment(data.activityId);
        });
        // submitComment event
		$('body').off('click.activity','a.viewer-submit-item-comment').on('click.activity','a.viewer-submit-item-comment',function(){
            var data = $(this).data();

            if( typeof data.itemType !== undefined && typeof data.activityItemId !== undefined && typeof data.activityId !== undefined)
                submitItemComment(data.itemType,data.activityItemId,data.activityId);
        });
        //change activity's privacy
		$('body').off('click.activity','a.change-activity-privacy').on('click.activity','a.change-activity-privacy',function(){
            var data = $(this).data();
            if(typeof data.activityId !== undefined && typeof data.privacy !== undefined)
                changeActivityPrivacy($(this),data.activityId, data.privacy);
        });
    }
    //    exposed public method
    return {
        init:init
    };
}));