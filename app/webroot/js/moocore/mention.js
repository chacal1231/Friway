(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooMention = factory(root.jQuery);
    }
}(this, function ($) {
    var textAreaId;
    var textAreaObj;
    var originalTextAreaValue;
    var userObj = {};
    var userSelected = [];
    var mirror;
    var termLength;
    var actionType;
    var mentions = {},avatar = {};
    $('body').on('focus','textarea',function(){
        var hiddenInput = $(this).siblings('input.messageHidden');
        if(hiddenInput.length >0 /*&& hiddenInput.val() != ''*/){
            resetMentionObject($(this));
            editTextarea(hiddenInput.val(),$(this));
        }
    });

    $('body').on('keydown',function(e){
        e.stopPropagation();
        if(e.which == 116){
            var textarea = jQuery('textarea');
            $.each(textarea,function(key,obj){
                if($(obj).val() != ''){
                    e.preventDefault();
                    var confirm = window.confirm('This page is asking you to confirm that you want to leave - data you have entered may not be saved.')
                    //mooConfirm('This page is asking you to confirm that you want to leave - data you have entered may not be saved.',baseUrl+'')
                    if(confirm){
                        $('textarea').val('');
                        $('.messageHidden').val('');
                        window.location.reload();
                    }
                    return false;
                }
            });
        }
    });

    function init(id,type){
        textAreaId = id;
        actionType = type;
        switch(type){
            case 'edit_activity':
                originalTextAreaValue = $('#'+textAreaId).val();
                editTextarea(originalTextAreaValue,$('#'+textAreaId));
                break;
        }
        if(Object.keys(mentions).length == 0){
            var friends = new Bloodhound({
                datumTokenizer:function(d){
                    return Bloodhound.tokenizers.whitespace(d.name);
                },
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                prefetch: {
                    url: baseUrl + '/users/friends.json',
                    cache: false,
                    filter: function(list) {
                        $.each(list.data, function(key, value){
                            mentions[value.id] = value.name;
                            userObj[value.id] = value.name;
                            avatar[value.id] = value.avatar;
                        });
                        textCompleteInit(mentions);
                        return $.map(list.data, function(obj) {
                            return obj;
                        });
                    }
                },

                identify: function(obj) { return obj.id; }
            });
            friends.initialize();
        }
        else{
            textCompleteInit(mentions);
            //refocus textarea
            setTimeout(function() {
                $('#'+textAreaId).focus();
            }, 0);

        }

    }
    var textCompleteInit = function(mentions){
        $('#'+textAreaId).destroyOverlayInstance($('#'+textAreaId));
        $('#'+textAreaId).textcomplete([
            { // html
                mentions:  mentions,//['yuku_t'],now {1:'yuku_t'}
                match: /\B@(.+)$/,
                search: function (term, callback) {
                    termLength = term.length;

                    callback($.map(this.mentions, function (mention, key) {
                        //remove selected user out of mention list
                        if(userSelected.indexOf(key) != -1){
                            return null;
                        }
                        if(inSpecialCharacter(term.toLowerCase()))
                            return null;
                        var re = new RegExp('\\b'+term.toLowerCase(), 'g');
                        return (mention.toLowerCase().match(re))? key : null;
                    }));
//                    var existed;
//                    $.each(this.mentions, function (key, mention) {
//                        if(mention.indexOf(term) === 0){
//                            existed = true;
//                            return false;
//                        }else{
//                            existed = false;
//                        }
//                    });
                    // selected all users in mentions list;
//                    var arrMention = Object.keys(mentions);
//                    if( !existed || ($(arrMention).not(userSelected).length === 0 && $(userSelected).not(arrMention).length === 0) ){
//                        //get already fetching user
//                        var ids = Object.keys(userObj);
//                        ids = ids.join();
//                        var url = baseUrl + '/users/get_user_mention.json';
//
//                        $.post(url,{ids: ids, q: term},function(list){
//                            if(Object.keys(list).length > 0){
//                                $.each(list.friends, function(key, value){
//                                    mentions[value.id] = value.name;
//                                    userObj[value.id] = value.name;
//                                });
//                            }
//                            callback($.map(mentions, function (mention, key) {
//                                //remove selected user out of mention list
//                                if(userSelected.indexOf(key) != -1){
//                                    return null;
//                                }
//                                return mention.indexOf(term) === 0 ? mention : null;
//                            }));
//                            /*
//                             $('#textarea_id').overlay([
//                             {
//                             match: $.map(mentions,function(val, key){return val;}),
//                             css: {
//                             'background-color': '#CAD8F4'
//                             }
//                             }
//                             ]);
//                             */
//                        })
//                    }else{
//                        callback($.map(this.mentions, function (mention, key) {
//                            //remove selected user out of mention list
//                            if(userSelected.indexOf(key) != -1){
//                                return null;
//                            }
//                            return mention.indexOf(term) === 0 ? mention : null;
//                        }));
//                    }
                },
                template: function (value) {
                    return '<img src="'+avatar[value]+'" class="user_avatar_small" /> ' + userObj[value];
                },
                index: 1,
                replace: function (mention) {
                    return userObj[mention];
                }
            }
        ], { appendTo: 'body',zIndex: 1055 }).overlay([
                {
                    match: {},//$.map(mentions,function(val, key){return val;}),
                    css: {
                        'background-color': '#CAD8F4'//'#d8dfea'
                    }
                }
            ])
            .on({
                'textComplete:select': function (e, valueId, strategy) {
                    var value = userObj[valueId];
                    for(var key in userObj) {
                        if(userObj.hasOwnProperty(key)){
                            if(userObj[key] == value){
                                var replacePosition = $(this).prop('selectionStart') - (value.length) +1;
                                var positionObj = updateReplacePosition(replacePosition);
                                triggerReplaceMention(key,value,$(this), positionObj['start'], termLength);
                                userSelected.push(key);
                                var startPosition = replacePosition - 1;
                                keyEffect[key] = {start:startPosition, end: $(this).prop('selectionStart'), length: ($(this).prop('selectionStart') - startPosition)};

                                reConfigOverlay($(this),true);
                            }
                        }
                    }
                }
            });

        if(jQuery('#'+textAreaId).siblings('input.messageHidden').length == 0){
            if(actionType == 'edit_activity'){
                jQuery('#'+textAreaId).after('<input type="hidden" id="'+textAreaId+'_hidden" name="data[message]" class="messageHidden" />');
            }else{
                jQuery('#'+textAreaId).after('<input type="hidden" name="data[message]" class="messageHidden" />');
            }
        }
        mirror = jQuery('#'+textAreaId).siblings('.messageHidden')[0];

        if(actionType == 'edit_activity'){
            mirror.value = originalTextAreaValue;
        }

        // Bind the textarea's event
        $('#'+textAreaId).on('keypress keydown',{obj:jQuery('#'+textAreaId)[0],mirror:mirror} ,growMentionTextarea );
        //$('#'+textAreaId).on('keydown',{obj:jQuery('#'+textAreaId)[0],mirror:mirror} ,growMentionTextarea );

        //bind parse,cut event
        $('#'+textAreaId).on('paste',{obj:jQuery('#'+textAreaId)[0],mirror:mirror} ,parseTextArea);
        $('#'+textAreaId).on('cut',{obj:jQuery('#'+textAreaId)[0],mirror:mirror} ,cutTextArea);

    };


    function editTextarea(originalTextAreaValue,obj){
        var mentionLength = 0;
        var nameLength = 0;
        resetMentionObject(obj);
        var newValue = originalTextAreaValue.replace(/@\[(\d+):([^\]]+)\]/g,function(match,id,name){
            userSelected.push(id);
            replaceLength[id] = {start: originalTextAreaValue.indexOf(match),end: originalTextAreaValue.indexOf(match) + match.length, length: match.length};
            var keyEffectStart = originalTextAreaValue.indexOf(match) - mentionLength + nameLength;
            keyEffect[id] = {start:keyEffectStart, end: keyEffectStart + name.length, length: name.length};
            mentionLength += match.length;
            nameLength += name.length;
            return name;
        });
        obj.val(newValue);
        reConfigOverlay(obj);

    }

    function growMentionTextarea(event, isMouseEvent) {

        //re update mentionPostion

        if(isInputKey(event) || typeof isMouseEvent != 'undefined'){
            var selectionStart = jQuery(event.data.obj).prop('selectionStart');
            var originalSelectionStart = selectionStart;
            var selectionEnd = jQuery(event.data.obj).prop('selectionEnd');
            var originalSelectionEnd = selectionEnd;
            var mirrorValue = String(event.data.mirror.value);
            var textAreaValue = String(event.data.obj.value);
            var updated = false;
            var letter, letterRange, alternateSelectionStart,alternateSelectionEnd;
            var positionObj = {}, range = 0;

            if(Object.keys(keyEffect).length > 0){
                var flag = false;
                for(var key in keyEffect) {
                    if(flag && !updated){
                        break;
                    }
                    if (keyEffect.hasOwnProperty(key)) {
                        if(originalSelectionStart >= keyEffect[key]['end']){ //pointer after mention
                            selectionStart = adjustPositionBeforeMention(originalSelectionStart);
                            selectionEnd = adjustPositionBeforeMention(originalSelectionStart, originalSelectionEnd);
                            if( (originalSelectionStart == keyEffect[key]['end']) && event.type == 'keydown' && event.which == 8 && (originalSelectionStart == originalSelectionEnd) ){//delete at last character of mention user
                                event.preventDefault();
                                originalSelectionStart = findWhiteSpaceAtTheBeginning(originalSelectionStart - 1,textAreaValue,key);
                                range = originalSelectionEnd - originalSelectionStart;

                                updateTextAreaValue(event,textAreaValue,originalSelectionStart,originalSelectionEnd);

                                positionObj = deleteInKeyAreaEffect(key,selectionStart,selectionEnd,originalSelectionStart,originalSelectionEnd);
                                selectionStart = positionObj.selectionStart;
                                selectionEnd = positionObj.selectionEnd;
                                flag = true;

                            }
                        }
                        else if((originalSelectionEnd > keyEffect[key]['start'] && originalSelectionStart < keyEffect[key]['start']) ||
                            (originalSelectionEnd > keyEffect[key]['end'] && originalSelectionStart < keyEffect[key]['end'])
                            ){// return all character at the end to normal
                            var textareaValue = String(event.data.obj.value);
                            var selectionStart = adjustPositionBeforeMention(originalSelectionStart);

                            var before = adjustBeforeText(selectionStart, textareaValue, mirrorValue, originalSelectionStart)

                            var after = textareaValue.substring(originalSelectionEnd);
                            if(event.which == 13){//enter key
                                letter = "\\n";
                            }else if(event.which == 32){//space key
                                letter = ' ';
                            }else if( event.type == 'keydown' && (event.which == 46  || event.which == 8) ){//delete or backspace key
                                letter = '';
                            }else if(typeof isMouseEvent != 'undefined' && (event.type == 'cut' || event.type == 'delete') ){ //ctrl+x and mouse command cut
                                letter = '';
                            }else{
                                letter = String.fromCharCode(event.which);
                            }
                            event.data.mirror.value = before + letter + after;
                            deleteAfterMention(key, originalSelectionStart);

                            reConfigOverlay(jQuery(event.data.obj));

                            return;

                        }
                        else if( (originalSelectionStart == keyEffect[key]['start']) || ( (originalSelectionStart == keyEffect[key]['start'])  &&  (originalSelectionEnd == keyEffect[key]['end']) ) ){ //select all character in mention or pointer at first character
                            if((originalSelectionStart != originalSelectionEnd) || ((originalSelectionStart == originalSelectionEnd) && (event.type == 'keydown' && (event.which == 46) ) )){
                                event.preventDefault();
                                var a = 0;

                                // 'a' is to get white space after selected word;
                                if(originalSelectionStart != originalSelectionEnd && event.which == 8)
                                {
                                    a = 1;
                                }else if(event.which == 46){
                                    a = 1;
                                }

                                selectionStart = adjustPositionBeforeMention(originalSelectionStart);
                                selectionEnd = adjustPositionBeforeMention(originalSelectionStart, originalSelectionEnd);

                                originalSelectionEnd = findWhiteSpaceAtTheEnd(originalSelectionStart,textAreaValue,key);
                                var b = (originalSelectionEnd < keyEffect[key]['end']) ? 1 : 0; //1 is white space
                                if(originalSelectionEnd == keyEffect[key]['end']) event.preventDefault();

                                //if selected word is the last, we don't need to get white space after it;
                                a = (originalSelectionEnd < keyEffect[key]['end'])? a: 0;

                                range = originalSelectionEnd - originalSelectionStart;
                                selectionStart = (replaceLength[key]['end'] - keyEffect[key]['length'] - 1 ); // 1 is "]" character
                                selectionEnd = selectionStart + range + b;

                                originalSelectionEnd += a;

                                updateTextAreaValue(event,textAreaValue,originalSelectionStart,originalSelectionEnd);
                                //update length and end position of replaceLength and keyEffect
                                updateSelfMentionPosition(selectionStart, selectionEnd, key);
                                flag = true;
                            }
                        }
                        else if( (keyEffect[key]['start'] < originalSelectionStart) && (keyEffect[key]['end'] > originalSelectionStart) ){ //pointer in key effect area
                            if( (event.type == 'keydown' && event.which == 46 ) || event.which == 8 ){

                                event.preventDefault();
                                selectionStart = adjustPositionBeforeMention(originalSelectionStart);
                                selectionEnd = adjustPositionBeforeMention(originalSelectionStart, originalSelectionEnd);

                                if(originalSelectionStart == originalSelectionEnd){
                                    if(event.which == 8 && originalSelectionStart > keyEffect[key]['start'])//backspace
                                    {
                                        originalSelectionEnd--;
                                        originalSelectionStart--;
                                    }
                                    else if(event.which == 46 && originalSelectionEnd < keyEffect[key]['end'])//deletekey
                                    {
                                        originalSelectionEnd++;
                                        originalSelectionStart++;
                                    }
                                    if(originalSelectionStart > keyEffect[key]['start']) originalSelectionStart--;
                                }

                                originalSelectionStart = findWhiteSpaceAtTheBeginning(originalSelectionStart, textAreaValue, key);
                                originalSelectionEnd = findWhiteSpaceAtTheEnd(originalSelectionEnd, textAreaValue, key);
                                originalSelectionEnd += (originalSelectionEnd < keyEffect[key]['end'] && originalSelectionStart == keyEffect[key]['start']) ?  1 : 0; //1 is white space
                                range = originalSelectionEnd - originalSelectionStart;

                                updateTextAreaValue(event,textAreaValue,originalSelectionStart,originalSelectionEnd);

                                positionObj = deleteInKeyAreaEffect(key, selectionStart , selectionEnd , originalSelectionStart, originalSelectionEnd);
                                selectionStart = positionObj.selectionStart;
                                selectionEnd = positionObj.selectionEnd;
                            }else{
                                var textareaValue = String(event.data.obj.value);

                                var before = mirrorValue.substring(0,replaceLength[key]['start']);
                                before += textareaValue.substring(keyEffect[key]['start'], originalSelectionStart);
                                var after = textareaValue.substring(originalSelectionEnd);
                                if(event.which == 13){//enter key
                                    letter = "\\n";
                                }else if(event.which == 32){//space key
                                    letter = ' ';
                                }else if((event.type == 'keydown' && event.which == 46 ) || event.which == 8){//delete or backspace key
                                    letter = '';
                                }else if(typeof isMouseEvent != 'undefined' && (event.type == 'cut' || event.type == 'delete') ){ //ctrl+x and mouse command cut
                                    letter = '';
                                }else{
                                    letter = String.fromCharCode(event.which);
                                }
                                event.data.mirror.value = before + letter + after;
                                deleteAfterMention(key, originalSelectionStart);

                                reConfigOverlay(jQuery(event.data.obj));

                                return;
                            }
                            flag = true;
                        }

                        //delete all character in mention
                        if(keyEffect[key]['length'] == 0){
                            selectionStart = replaceLength[key]['start'];
                            selectionEnd = replaceLength[key]['end'] + range;
                            for (var arrKey in userSelected) {
                                if (userSelected[arrKey] == key) {
                                    userSelected.splice(arrKey, 1);
                                }
                            }
                            delete replaceLength[key];
                            delete keyEffect[key];

                            updateTextAreaValue(event,textAreaValue,originalSelectionStart,originalSelectionEnd);
                            reConfigOverlay(jQuery(event.data.obj));

                            alternateSelectionStart = selectionStart;
                            alternateSelectionEnd = selectionEnd;
                            letterRange = range;
                            updated = true;
                            break;
                        }
                    }
                }
            }
            if((typeof alternateSelectionStart != 'undefined') && (typeof letterRange != 'undefined')){
                updateOtherMentionPosition(alternateSelectionStart, alternateSelectionEnd, event, letterRange);
            }else{
                updateOtherMentionPosition(originalSelectionStart, originalSelectionEnd, event);
            }

            if(event.which == 13){//enter key
                letter = "\n";
            }else if(event.which == 32){//space key
                letter = ' ';
            }else if(event.type == 'keydown' && (event.which == 46  || event.which == 8) ){//delete or backspace key
                letter = '';
                if(selectionStart == selectionEnd){
                    if(event.which == 8){//backspace
                        selectionStart -= 1;
                    }else if(event.which == 46 ){//delete
                        selectionEnd += 1;
                    }
                }
            }else if(typeof isMouseEvent != 'undefined' && (event.type == 'cut' || event.type == 'delete')){ //ctrl+x and mouse command cut
                letter = '';
            }else{
                letter = String.fromCharCode(event.which);
            }


            //alert(letter);

            var front = mirrorValue.substring(0,selectionStart);
            var back = mirrorValue.substring(selectionEnd);
            mirrorValue = front + letter + back;
            event.data.mirror.value = mirrorValue;

            reConfigOverlay(jQuery(event.data.obj));
        }
    }

    function parseTextArea(event){
        //alert('parse');
        var selectionStart = jQuery(event.data.obj).prop('selectionStart');
        var originalSelectionStart = selectionStart;
        var selectionEnd = jQuery(event.data.obj).prop('selectionEnd');
        var originalSelectionEnd = selectionEnd;
        var letter = '', letterRange,inputLength;
        var mirrorValue = String(event.data.mirror.value);
        setTimeout(function(){
            var textareaValue = String(event.data.obj.value);
            var afterParseSelectionStart =  jQuery(event.data.obj).prop('selectionStart');
            var afterParseSelectionEnd =  jQuery(event.data.obj).prop('selectionEnd');
            letterRange = (afterParseSelectionStart - originalSelectionStart) - (originalSelectionEnd - originalSelectionStart);
            letter = textareaValue.substring(originalSelectionStart,afterParseSelectionStart);

            if(Object.keys(keyEffect).length > 0){
                var flag = false;
                for(var key in keyEffect) {
                    if(flag && !updated){
                        break;
                    }
                    if (keyEffect.hasOwnProperty(key)) {
                        if(!( (originalSelectionStart == originalSelectionEnd) && (originalSelectionStart <= keyEffect[key]['start'] || originalSelectionEnd >= keyEffect[key]['end']) ) ){// return all character at the end to normal
                            selectionStart = adjustPositionBeforeMention(originalSelectionStart);

                            var before = adjustBeforeText(selectionStart, textareaValue, mirrorValue, originalSelectionStart)

                            var after = textareaValue.substring(afterParseSelectionStart);

                            event.data.mirror.value = before + letter + after;
                            deleteAfterMention(key, originalSelectionStart);
                            return;

                        }else{
                            selectionStart = adjustPositionBeforeMention(originalSelectionStart);
                            selectionEnd = adjustPositionBeforeMention(originalSelectionStart,originalSelectionEnd);
                        }
                    }
                }
            }
            updateOtherMentionPositionParse(originalSelectionStart, letterRange);
            inputLength = mirrorValue.length

            var front = mirrorValue.substring(0,selectionStart);
            var back = mirrorValue.substring(selectionEnd,mirrorValue.length);
            mirrorValue = front + letter + back;
            event.data.mirror.value = mirrorValue;
        },10); //just break the callstack to let the event finish
    }

    function cutTextArea(event){
        growMentionTextarea(event,true);
    }

    function changeTextArea(event){
        var obj = event.data.obj;
        var mirror = event.data.mirror;
        setTimeout(function(){
            mirror.value = String(obj.value);
            resetMentionObject($(obj));
        },10);
    }

    function triggerReplaceMention(key, value, obj, replacePosition, termLength){
        var messageHidden = obj.siblings('.messageHidden');
        var originalObjVal = messageHidden.val();
        var strReplace = '[' + key + ':' + value + ']';
        var frontValue = originalObjVal.substring(0,replacePosition);
        var backValue = originalObjVal.substring(replacePosition);
        backValue = backValue.replace(originalObjVal.substring(replacePosition,(replacePosition+termLength)),strReplace);
        messageHidden.val(frontValue + backValue);

        replaceLength[key] = {start: replacePosition -1, end: replacePosition + strReplace.length, length: strReplace.length + 1};
        var length = strReplace.length + 1;
        //update all mention before it
        if(Object.keys(keyEffect).length > 0){
            for(var objKey in keyEffect) {
                if (keyEffect.hasOwnProperty(objKey)) {
                    if((replacePosition -1) <= keyEffect[objKey]['start']){ //pointer before mention
                        replaceLength[objKey]['start'] = replaceLength[objKey]['start'] - (termLength + 1) + length;
                        replaceLength[objKey]['end'] = replaceLength[objKey]['end'] - (termLength + 1) + length;
                        keyEffect[objKey]['start'] = keyEffect[objKey]['start'] - (termLength + 1) + value.length;
                        keyEffect[objKey]['end'] = keyEffect[objKey]['end'] - (termLength + 1) + value.length;
                    }
                }
            }
        }
        reConfigOverlay(obj);
    }

    function updateSelfMentionPosition(selectionStart, selectionEnd, key){
        if(Object.keys(keyEffect).length > 0){
            var a = ( (selectionEnd - selectionStart) == 0 ) ? 1 : selectionEnd - selectionStart;
            replaceLength[key]['end'] -= a;
            replaceLength[key]['length'] -= a;
            keyEffect[key]['end'] -= a;
            keyEffect[key]['length'] -= a;
        }
    }
    function isInputKey(event){
        var keyWhich = event.which;
        if(event.type == 'keypress' && keyWhich!= 8){
            if(keyWhich != 0/*(48 <= keyWhich && keyWhich <= 90) || (96 <= keyWhich && keyWhich <= 111) || (186 <= keyWhich && keyWhich <= 222) || (keyWhich == 13) || (keyWhich == 32) || (keyWhich == 8) || keyWhich == 46 */){
                if(!event.ctrlKey){
                    if($('ul[id^="textcomplete-dropdown"]').css('display') != 'none'  && keyWhich == 13){
                        return false;
                    }else{
                        return true;
                    }
                }else if(event.ctrlKey && (keyWhich == 90 || keyWhich == 89)){//ctrl+z, ctrl+y
                    changeTextArea(event);
                    return false;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else if(event.type == 'keydown'){
            if(keyWhich == 46 || keyWhich == 8){ //delete key was press
                return true;
            }else if(keyWhich == 116){
                if(Object.keys(keyEffect).length > 0){
                    event.preventDefault();
                    event.stopPropagation();
                    var confirm = window.confirm('This page is asking you to confirm that you want to leave - data you have entered may not be saved.')
                    //mooConfirm('This page is asking you to confirm that you want to leave - data you have entered may not be saved.',baseUrl+'')
                    if(confirm){
                        $('textarea').val('');
                        $('.messageHidden').val('');
                        window.location.reload();
                    }
                }
            }else{
                return false;
            }
        }
    }

    function updateReplacePosition(selectionStart, selectionEnd){
        var positionObj = {};
        if(Object.keys(keyEffect).length > 0){
            for(var key in keyEffect) {
                if (keyEffect.hasOwnProperty(key)) {
                    if(selectionStart >= keyEffect[key]['end']){ //pointer after mention
                        selectionStart += (replaceLength[key]['length'] - keyEffect[key]['length']);
                        selectionEnd += (replaceLength[key]['length'] - keyEffect[key]['length']);
                    }
                }
            }
        }
        positionObj['start'] = selectionStart;
        positionObj['end'] = selectionEnd;
        return positionObj;
    }
    function updateOtherMentionPositionParse(selectionStart,letterRange){
        if(Object.keys(keyEffect).length > 0){
            for(var key in keyEffect) {
                if (keyEffect.hasOwnProperty(key)) {
                    if(selectionStart <= keyEffect[key]['start'] ){
                        replaceLength[key]['start'] = replaceLength[key]['start'] + letterRange;
                        replaceLength[key]['end'] = replaceLength[key]['end'] + letterRange;
                        keyEffect[key]['start'] = keyEffect[key]['start'] + letterRange;
                        keyEffect[key]['end'] = keyEffect[key]['end'] + letterRange;
                    }
                }
            }
        }
    }
    function updateOtherMentionPosition(selectionStart, selectionEnd ,event, letterRange){
        if(Object.keys(keyEffect).length > 0){
            for(var key in keyEffect) {
                if (keyEffect.hasOwnProperty(key)) {
                    if(selectionStart < keyEffect[key]['start'] && !(event.which == 8 && (selectionStart == 0 && selectionEnd == 0)) ){
                        if( (event.type == 'keydown' && (event.which == 46  || event.which == 8))  || event.type == 'cut'){ //delete, backspace, ctrl+x
                            replaceLength[key]['start'] = replaceLength[key]['start'] - ((selectionEnd == selectionStart) ? 1 : (selectionEnd - selectionStart));
                            replaceLength[key]['end'] = replaceLength[key]['end'] - ((selectionEnd == selectionStart) ? 1 : (selectionEnd - selectionStart));
                            if(typeof letterRange == 'undefined' || letterRange == 0){
                                keyEffect[key]['start'] = keyEffect[key]['start'] - ((selectionEnd == selectionStart) ? 1 : (selectionEnd - selectionStart));
                                keyEffect[key]['end'] = keyEffect[key]['end'] - ((selectionEnd == selectionStart) ? 1 : (selectionEnd - selectionStart));
                            }else{
                                keyEffect[key]['start'] -= letterRange;
                                keyEffect[key]['end'] -= letterRange;
                            }
                        }else{
                            replaceLength[key]['start'] = replaceLength[key]['start'] - (selectionEnd - selectionStart) + 1;
                            replaceLength[key]['end'] = replaceLength[key]['end'] - (selectionEnd - selectionStart) + 1;
                            if(typeof letterRange == 'undefined' || letterRange == 0){
                                keyEffect[key]['start'] = keyEffect[key]['start'] - (selectionEnd - selectionStart) + 1;
                                keyEffect[key]['end'] = keyEffect[key]['end'] - (selectionEnd - selectionStart) + 1;
                            }else{
                                keyEffect[key]['start'] = keyEffect[key]['start'] - letterRange + 1;
                                keyEffect[key]['end'] = keyEffect[key]['end'] - letterRange + 1;
                            }
                        }
                    }else if(selectionStart == keyEffect[key]['start'] && !( event.type == 'keydown' && (event.which == 46  || event.which == 8)  )){
                        replaceLength[key]['start'] = replaceLength[key]['start'] - (selectionEnd - selectionStart) + 1;
                        replaceLength[key]['end'] = replaceLength[key]['end'] - (selectionEnd - selectionStart) + 1;
                        if(typeof letterRange == 'undefined' || letterRange == 0){
                            keyEffect[key]['start'] = keyEffect[key]['start'] - (selectionEnd - selectionStart) + 1;
                            keyEffect[key]['end'] = keyEffect[key]['end'] - (selectionEnd - selectionStart) + 1;
                        }else{
                            keyEffect[key]['start'] = keyEffect[key]['start'] - letterRange + 1;
                            keyEffect[key]['end'] = keyEffect[key]['end'] - letterRange + 1;
                        }
                    }else if(selectionStart == keyEffect[key]['start'] && (event.which == 8)){
                        if(replaceLength[key]['start'] >0 && keyEffect[key]['start'] >0 && !(selectionStart == selectionEnd == 0)){
                            replaceLength[key]['start']--;
                            replaceLength[key]['end']--;
                            keyEffect[key]['start']--;
                            keyEffect[key]['end']--;
                        }
                    }
                }
            }
        }
    }
    function deleteAfterMention(key, originalSelectionStart){
        for(var userId in keyEffect){
            if (keyEffect.hasOwnProperty(userId)) {
                if(typeof originalSelectionStart != 'undefined'){
                    if( (keyEffect[userId]['start'] <= originalSelectionStart) && (keyEffect[userId]['end'] > originalSelectionStart) ){
                        key = userId;
                    }
                }
            }
        }
        var startPosition = keyEffect[key]['start'];
        var tempKeyEffectObj = {}, tempReplaceLengthObj = {};
        var tempUserSelected = [];

        for(var userId in keyEffect){
            if (keyEffect.hasOwnProperty(userId)) {
                if(keyEffect[userId]['start'] < startPosition){
                    tempKeyEffectObj[userId] = keyEffect[userId];
                    tempReplaceLengthObj[userId] = replaceLength[userId];
                    tempUserSelected.push(userId);
                }
            }
        }
        keyEffect = tempKeyEffectObj;
        replaceLength = tempReplaceLengthObj;
        userSelected = tempUserSelected;
    }

    function adjustPositionBeforeMention(originalStartPosition,originalEndPosition){
        var startPosition = originalStartPosition,endPosition = originalEndPosition;
        for(var userId in keyEffect){
            if (keyEffect.hasOwnProperty(userId)) {
                if(keyEffect[userId]['end'] <= originalStartPosition){
                    if(typeof originalEndPosition == 'undefined'){
                        startPosition += (replaceLength[userId]['length'] - keyEffect[userId]['length']);
                    }else{
                        endPosition += (replaceLength[userId]['length'] - keyEffect[userId]['length']);
                    }
                }
            }
        }
        return (typeof originalEndPosition == 'undefined')? startPosition : endPosition;
    }

    function adjustBeforeText(selectionStart, textareaValue, mirrorValue, originalSelectionStart){
        var a = '';
        for(var userId in keyEffect){
            if (keyEffect.hasOwnProperty(userId)) {
                if(selectionStart > replaceLength[userId]['start'] && selectionStart < replaceLength[userId]['end']){
                    selectionStart = replaceLength[userId]['start'];
                    a =  textareaValue.substring(keyEffect[userId]['start'], originalSelectionStart);
                }
            }
        }
        var before = mirrorValue.substring(0,selectionStart) + a;
        return before;
    }
    function deleteMention(range,event){
        var selectionStart, selectionEnd;
        var letter, letterRange, alternateSelectionStart,alternateSelectionEnd;
        var updated = false;
        for(var key in keyEffect){
            if (keyEffect.hasOwnProperty(key)) {
                if(keyEffect[key]['length'] == 0){
                    selectionStart = replaceLength[key]['start'];
                    selectionEnd = replaceLength[key]['end'] + range;

                    for (var arrKey in userSelected) {
                        if (userSelected[arrKey] == key) {
                            userSelected.splice(arrKey, 1);
                        }
                    }
                    delete replaceLength[key];
                    delete keyEffect[key];

                    alternateSelectionStart = selectionStart;
                    alternateSelectionEnd = selectionEnd;
                    letterRange = range;

                    updateOtherMentionPosition(alternateSelectionStart, alternateSelectionEnd, event, letterRange);
                    updated = true;
                }
            }
        }
        return updated;
    }
    function resetMention(obj){
        obj.siblings('input.messageHidden').val('');
        obj.siblings('div.textoverlay').html('');
        resetMentionObject(obj);
    }
    function resetMentionObject(obj){
        keyEffect = {};
        replaceLength = {};
        userSelected = [];
        reConfigOverlay(obj);

    }

    function reConfigOverlay(obj,reRender){
        //reRender overlay
        textAreaObj = obj.getInstanceOverlay(obj);
        obj.revokeOverlay([{match: keyEffect}],textAreaObj);
        if(typeof reRender !== undefined){
            obj.reRenderTextOnOverlay(textAreaObj);
        }
    }

    function findWhiteSpaceAtTheBeginning(startPosition, textAreaValue ,key){
        for(var char = textAreaValue.substr(startPosition,1); char != ' ' && startPosition > keyEffect[key]['start']; char = textAreaValue.substr(startPosition,1)){
            startPosition--;
        }
        return startPosition;
    }
    function findWhiteSpaceAtTheEnd(endPosition, textAreaValue, key){
        for(var char = textAreaValue.substr(endPosition,1); char != ' ' && endPosition < keyEffect[key]['end']; char = textAreaValue.substr(endPosition,1)){
            endPosition++;
        }
        return endPosition;
    }

    function deleteInKeyAreaEffect(key,selectionStart, selectionEnd, originalSelectionStart, originalSelectionEnd){
        selectionStart = adjustPositionBeforeMention(originalSelectionStart);
        selectionEnd = adjustPositionBeforeMention(originalSelectionStart, originalSelectionEnd);

        var keyEffectPositionEnd = selectionEnd - replaceLength[key]['start'];
        var keyEffectPositionStart = selectionStart - replaceLength[key]['start'];
        keyEffectPositionEnd = keyEffect[key]['length'] - keyEffectPositionEnd;
        keyEffectPositionStart = keyEffect[key]['length'] - keyEffectPositionStart;

        selectionStart = replaceLength[key]['end'] - keyEffectPositionStart - 1; // 1 is the ']' character
        selectionEnd = replaceLength[key]['end'] - keyEffectPositionEnd - 1; // 1 is the ']' character

        //update length and end position of replaceLength and keyEffect
        updateSelfMentionPosition(selectionStart, selectionEnd, key);

        return {selectionStart: selectionStart,selectionEnd:selectionEnd};
    }

    function updateTextAreaValue(event, textAreaValue,originalSelectionStart, originalSelectionEnd){
        //update textarea value
        var textareaFront = textAreaValue.substring(0,originalSelectionStart);
        var textareaEnd = textAreaValue.substring(originalSelectionEnd);

        event.data.obj.value = textareaFront+textareaEnd;
        setSelectionRange(event.data.obj,originalSelectionStart,originalSelectionStart)
    }

    function setSelectionRange(input, selectionStart, selectionEnd) {
        if (input.setSelectionRange) {
            input.focus();
            input.setSelectionRange(selectionStart, selectionEnd);
        }
        else if (input.createTextRange) {
            var range = input.createTextRange();
            range.collapse(true);
            range.moveEnd('character', selectionEnd);
            range.moveStart('character', selectionStart);
            range.select();
        }
    }
    function getMentionPosition(){
        return {keyEffect: keyEffect,replaceLength: replaceLength};
    }
    function inSpecialCharacter(term){
        var arrSpecialChar = ['\\','(',')','[',']','^','{','}','*','+','?','$','.'];
        for(var arrKey in arrSpecialChar) {
            if (arrSpecialChar.hasOwnProperty(arrKey)) {
                if(term.indexOf(arrSpecialChar[arrKey]) != -1)
                    return true;
            }
        }
        return false;
    }
    //    exposed public methods
    return {
        init:init,
        resetMention: resetMention,
        reConfigOverlay: reConfigOverlay
    //getMentionPosition: getMentionPosition,
    }
}));