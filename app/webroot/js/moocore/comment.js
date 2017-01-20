// global.js - ajax_postComment
// global.js - editActivityComment
// global.js - cancelEditActivityComment
// global.js - confirmEditActivityComment
// global.js - editItemComment
// global.js - cancelEditItemComment
// global.js - confirmEditItemComment
// global.js - removePhotoComment
define(['jquery','attach','behavior'],function($,attach,behavior){
    var activity_comment_edit_array = [];
    var item_comment_edit_array = [];
    return {
        ajax_postComment:function(id){
            if ($.trim($('#postComment').val()) != '' || $.trim($('#theaterPhotoComment').val()) != '' || $('#comment_image_' + id).val() != '')
            {
                $('#shareButton').addClass('disabled');
                $('#shareButton').prepend('<i class="icon-refresh icon-spin"></i>');
                var commentFormSerialize = '';
                if ($('#commentForm').length){
                    commentFormSerialize = $("#commentForm").serialize();
                }

                if ($('#theaterPhotoCommentForm').length){
                    commentFormSerialize = $("#theaterPhotoCommentForm").serialize();
                }
                $.post(baseUrl + "/comments/ajax_share", commentFormSerialize, function(data){

                    $('#shareButton').removeClass('disabled');
                    $('#shareButton i').remove();
                    $('.commentForm').css('height', '35px');

                    if ($('#postComment').length){
                        $('#postComment').val("");
                    }

                    if ($('#theaterPhotoComment').length){
                        $('#theaterPhotoComment').val("");
                    }

                    if (data != '')
                    {
                        if ($('#theaterComments').length){
                            $('#theaterComments').append(data);
                        }
                        else {
                            $('#comments').append(data);
                        }

                        $('.slide').slideDown();
                        $("#comment_count").html( parseInt($("#comment_count").html()) + 1 );


                        $('#comment_preview_image_' + id).html('');
                        $('#comment_image_' + id).val('');
                        $('#comment_button_attach_'+id).show();
                        behavior.registerImageComment();
                    }
                });
            }else{
                // mooAlert('Comment can not empty');
                $.fn.SimpleModal({
                    btn_ok : mooPhraseVars['btn_ok'],
                    model: 'modal',
                    title: mooPhraseVars['warning'],
                    contents: mooPhraseVars['comment_empty']
                }).showModal();
            }
        },
        editActivityComment:function(comment_id){
            if (activity_comment_edit_array.indexOf(comment_id) === -1)
            {
                $.post(baseUrl + '/activities/ajax_loadActivityCommentEdit/'+ comment_id, function(data){
                    $('#activity_feed_comment_text_'+comment_id).hide();
                    $(data).insertAfter($('#activity_feed_comment_text_'+comment_id));
                    attach.registerAttachCommentEdit('activity',comment_id);
                    activity_comment_edit_array.push(comment_id);
                    $('textarea:not(.no-grow)').autogrow();

                    //user mention
                    mooMention.init($(data).find('textarea').attr('id'),'edit_activity');
                    //user emoji
                    mooEmoji.init($(data).find('textarea').attr('id'));
                });
            }
        },
        cancelEditActivityComment:function(comment_id){
            $('#activity_feed_comment_text_'+comment_id).show();
            $('#activity_comment_edit_'+comment_id).remove();

            var index = $.inArray(comment_id, activity_comment_edit_array);
            this.activity_comment_edit_array.splice(index, 1);
        },
        confirmEditActivityComment:function(comment_id){
            if ($.trim($('#message_activity_comment_edit_'+comment_id).val()) != '' || $('#activity_comment_attach_id_'+comment_id).val() != '')
            {
                $.post(baseUrl + '/activities/ajax_editActivityComment/'+ comment_id,{'comment_attach': $('#activity_comment_attach_id_'+comment_id).val() ,message: jQuery("#message_activity_comment_edit_"+comment_id).val()}, function(data){
                    $('#activity_feed_comment_text_'+comment_id).html($(data).html());
                    $('#history_activity_comment_'+comment_id).show();
                    behavior.registerImageComment();
                    this.cancelEditActivityComment(comment_id);
                });
            }
        },
        editItemComment:function(comment_id){
            if (item_comment_edit_array.indexOf(comment_id) === -1)
            {
                $.post(baseUrl + '/comments/ajax_loadCommentEdit/'+ comment_id, function(data){
                    $('#item_feed_comment_text_'+comment_id).hide();
                    $(data).insertAfter($('#item_feed_comment_text_'+comment_id));
                    attach.registerAttachCommentEdit('item',comment_id);
                    this.item_comment_edit_array.push(comment_id);
                    $('textarea:not(.no-grow)').autogrow();
                });
            }
        },
        cancelEditItemComment:function(comment_id){
            $('#item_feed_comment_text_'+comment_id).show();
            $('#item_comment_edit_'+comment_id).remove();

            var index = $.inArray(comment_id, this.item_comment_edit_array);
            this.item_comment_edit_array.splice(index, 1);
        },
        confirmEditItemComment:function(comment_id){
            if ($.trim($('#message_item_comment_edit_'+comment_id).val()) != '' || $('#item_comment_attach_id_'+comment_id).val() != '')
            {
                $.post(baseUrl + '/comments/ajax_editComment/'+ comment_id,{'comment_attach': $('#item_comment_attach_id_'+comment_id).val() ,message: jQuery("#message_item_comment_edit_"+comment_id).val()}, function(data){
                    $('#item_feed_comment_text_'+comment_id).html($(data).html());
                    $('#history_item_comment_' + comment_id).show();
                    behavior.registerImageComment();
                    this.cancelEditItemComment(comment_id);
                });
            }
        },
        removePhotoComment:function(type,id){
            $('#'+type+'_comment_attach_id_'+id).val('');
            $('#'+type+'_comment_preview_attach_'+id).html('');
            $('#'+type+'_comment_attach_'+id).show();
        },
    }
});