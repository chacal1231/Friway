// global.js - likeIt
// global.js - likePhoto
// global.js - likeActivity
define(['jquery','user'],function($,user){
    return {
        likeIt:function(type, item_id, thumb_up){
            jQuery.post(baseUrl + '/likes/ajax_add/' + type + '/' + item_id + '/' + thumb_up, { noCache: 1 }, function(data){
                try
                {
                    var res = jQuery.parseJSON(data);

                    jQuery('#like_count').html( parseInt(res.like_count) );
                    jQuery('#dislike_count').html( parseInt(res.dislike_count) );
                    jQuery('#like_count2').html( parseInt(res.like_count) );
                    jQuery('#dislike_count2').html( parseInt(res.dislike_count) );

                    if ( thumb_up )
                    {
                        jQuery('#like_count').parent().prev().toggleClass('active');
                        jQuery('#dislike_count').parent().prev().removeClass('active');
                    }
                    else
                    {
                        jQuery('#dislike_count').parent().prev().toggleClass('active');
                        jQuery('#like_count').parent().prev().removeClass('active');
                    }
                }
                catch (err)
                {
                    user.validateUser();
                }
            });
        },
        likePhoto:function(item_id, thumb_up){
            jQuery.post(baseUrl + '/likes/ajax_add/Photo_Photo/' + item_id + '/' + thumb_up, { noCache: 1 }, function(data){
                try
                {
                    var res = jQuery.parseJSON(data);

                    jQuery('#photo_like_count2').html( parseInt(res.like_count) );
                    jQuery('#photo_dislike_count2').html( parseInt(res.dislike_count) );

                    if ( thumb_up )
                    {
                        jQuery('#photo_like_count').toggleClass('active');
                        jQuery('#photo_dislike_count').removeClass('active');
                    }
                    else
                    {
                        jQuery('#photo_dislike_count').toggleClass('active');
                        jQuery('#photo_like_count').removeClass('active');
                    }
                }
                catch (err)
                {
                    user.validateUser();
                }
            });
        },
        likeActivity:function(type, id, thumb_up){
            $.post(baseUrl + '/likes/ajax_add/' + type + '/' + id + '/' + thumb_up, { noCache: 1 }, function(data){
                try
                {
                    var res = $.parseJSON(data);
                    $('#' + type + '_like_' + id).html( parseInt(res.like_count) );
                    $('#' + type + '_dislike_' + id).html( parseInt(res.dislike_count) );

                    if ( thumb_up )
                    {
                        $('#' + type + '_l_' + id).toggleClass('active');
                        $('#' + type + '_d_' + id).removeClass('active');
                    }
                    else
                    {
                        $('#' + type + '_d_' + id).toggleClass('active');
                        $('#' + type + '_l_' + id).removeClass('active');
                    }
                }
                catch (err)
                {
                    user.validateUser();
                }
            });
        }

    }
});