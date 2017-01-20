// global.js - registerAttachComment
// global.js - registerAttachCommentEdit
define(['jquery'],function($){
    return {
        registerAttachComment:function(id){
            var errorHandler = function(event, id, fileName, reason) {
                qq.log("id: " + id + ", fileName: " + fileName + ", reason: " + reason);
            };
            var uploader = new qq.FineUploader({
                element: $('#comment_button_attach_'+id)[0],
                text: {
                    uploadButton: '<div class="upload-section"><i class="icon-camera"></i></div>'
                },
                validation: {
                    allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
                    sizeLimit: 10 * 1024 * 1024
                },
                multiple: false,
                request: {
                    endpoint: baseUrl+"/upload/wall"
                },
                callbacks: {
                    onError: errorHandler,
                    onComplete: function(id_img, fileName, response, xhr) {
                        $(this.getItemByFileId(id_img)).remove();
                        //console.log(this._find('icon-camera'));
                        var element = $('<span style="background-image:url(' + baseUrl + '/' + response.photo + ')"><a href="javascript:void(0);"><i class="icon-delete"></i></span></a>');

                        $('#comment_preview_image_'+id).append(element);
                        element.find('.icon-delete').click(function(){
                            element.remove();
                            $('#comment_button_attach_'+id).show();
                            $('#comment_image_'+id).val('');
                        });

                        $('#comment_image_'+id).val(response.photo);

                        $('#comment_button_attach_'+id).hide();
                    }
                }
            });
        },
        registerAttachCommentEdit:function(type,id){
            var errorHandler = function(event, id, fileName, reason) {
                qq.log("id: " + id + ", fileName: " + fileName + ", reason: " + reason);
            };
            var uploader = new qq.FineUploader({
                element: $('#'+type+'_comment_attach_'+id)[0],
                text: {
                    uploadButton: '<div class="upload-section"><i class="icon-camera"></i></div>'
                },
                validation: {
                    allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
                    sizeLimit: 10 * 1024 * 1024
                },
                multiple: false,
                request: {
                    endpoint: baseUrl+"/upload/wall"
                },
                callbacks: {
                    onError: errorHandler,
                    onComplete: function(id_img, fileName, response, xhr) {
                        $(this.getItemByFileId(id_img)).remove();
                        var element = $('<span style="background-image:url(' + baseUrl + '/' + response.photo + ')"><a href="javascript:void(0);"><i class="icon-delete"></i></span></a>');

                        $('#'+type+'_comment_preview_attach_'+id).append(element);
                        element.find('.icon-delete').click(function(){
                            element.remove();
                            $('#'+type+'_comment_attach_'+id).show();
                            $('#'+type+'_comment_attach_id_'+id).val('');
                        });

                        $('#'+type+'_comment_attach_id_'+id).val(response.photo);

                        $('#'+type+'_comment_attach_'+id).hide();
                    }
                }
            });
        }
    }
});
