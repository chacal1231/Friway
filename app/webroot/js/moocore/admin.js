// global.js - doModeration
define(['jquery','alert'],function($,alert){
    return {
        doModeration:function(action, type){
            switch ( action )
            {
                case 'delete':
                    $('#deleteForm').attr('action', baseUrl + '/admin/' + type + '/delete');
                    alert.confirmSubmitForm('Are you sure you want to delete these ' + type + '?', 'deleteForm');
                    break;

                case 'move':
                    $('#deleteForm').attr('action', baseUrl + '/admin/' + type + '/move');
                    $('#category_id').show();
                    break;

                default:
                    $('#category_id').hide();
            }
        }
    }
});