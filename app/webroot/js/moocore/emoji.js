(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooEmoji = factory(root.jQuery);
    }
}(this, function ($) {
    var textAreaId;
    var actionType;
    var termLength;

    function init(id,type){
        textAreaId = id;
        actionType = type;
        var emojies = {};
        emojies = {'a48':':evil:' , 'a20':':sad:', 'a1':':laugh:', 'a41':':cool:', 'a50':':surprised:', 'a24':':crying:', 'a29':':sweating:', 'a52':':speechless:' , 'a8':':kiss:', 'a39':':cheeky:',
            'a6':':wink:', 'a4':':blushing:', 'a47':':wondering:', 'a42':':sleepy:', 'a7':':inlove:', 'a3':':smile:', 'a43':':yawn:', 'a37':':puke:', 'a35':':angry:', 'a15':':wasntme:',
            'a33':':worry:', 'a8':':love:', 'a74':':devil:', 'a72':':angel:', 'a19':':envy:', 'a53':':meh:', 'a23':':rofl:', 'a18':':happy:', 'a57':':smirk:', 'a77':':beer:',
            'a76':':clap:', 'a2':':sun:', 'a5':':flex:', 'a9':':no:', 'a10':':yes:', 'a11':':ok:', 'a12':':punch:', 'a13':':star:', 'a14':':car:', 'a16':':poop:', 'a17':':umbrella', 'a21':':cake:',
            'a22':':drink:', 'a25':':football:', 'a26':':mad:', 'a27':':silly:', 'a28':':flu:', 'a30':':excited:', 'a31':':pained:', 'a32':':cup:', 'a34':':music:', 'a36':':candy:', 'a38':':chicken:',
            'a40':':cow:', 'a44':':dog:', 'a45':':hih:', 'a46':':email:', 'a49':':bike:', 'a50':':time:', 'a51':':brokenheart:', 'a54':':slow:', 'a55':':eat:', 'a56':':corn:'};

        $('#'+textAreaId).textcomplete([
            { // emoji strategy
                match: /\B:([\w:]*)$/,///\B:([\-+\w:\(\)\|\^;\$\]\*]*)$/,
                search: function (term, callback) {
                    termLength = term.length;
                    callback($.map(emojies, function (emoji, key) {
                        return emoji.indexOf(':' + term) === 0 ? key : null;
                    }));
                },
                template: function (value) {
                    var emojies_name = emojies[value].replace(/:/g, '');
                    return '<span id="' + value + '" class="iconos"></span> ' + emojies_name;
                },
                replace: function (value) {
                    return emojies[value];
                },
                index: 1
            }
        ], { appendTo: 'body',zIndex: 1099 }
        /*{
            onKeydown: function (e, commands) {
                if (e.ctrlKey && e.keyCode === 74) { // CTRL-J
                    return commands.KEY_ENTER;
                }
            }
        }*/)
            .on({
                'textComplete:select': function (e, valueId, strategy) {
                    var value = emojies[valueId];
                    for(var key in emojies) {
                        if(emojies.hasOwnProperty(key)){
                            if(emojies[key] == value){
                                var replacePosition = $(this).prop('selectionStart') - (value.length) +1;
                                var positionObj = updateReplacePosition(replacePosition);
                                triggerReplaceMention(key,value,$(this), positionObj['start'], termLength);
                                var startPosition = replacePosition - 1;
                                //keyEffect[key] = {start:startPosition, end: $(this).prop('selectionStart'), length: ($(this).prop('selectionStart') - startPosition)};
                                if(typeof mooMention !== 'undefined')
                                    mooMention.reConfigOverlay($(this),true);
                            }
                        }
                    }
                }
            });

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

    function triggerReplaceMention(key, value, obj, replacePosition, termLength){
        var messageHidden = obj.siblings('.messageHidden');
        var originalObjVal = (messageHidden.length > 0) ? messageHidden.val() : obj.val();
        var strReplace = value;
        var frontValue = originalObjVal.substring(0,replacePosition - 1);
        var backValue = originalObjVal.substring(replacePosition);
        backValue = backValue.replace(originalObjVal.substring(replacePosition,(replacePosition+termLength)),strReplace);
        messageHidden.val(frontValue + backValue);
        //replaceLength[key] = {start: replacePosition -1, end: replacePosition + strReplace.length, length: strReplace.length + 1};
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
        if(typeof mooMention !== 'undefined')
            mooMention.reConfigOverlay(obj);

    }

    function getMentionPosition() {
        return {keyEffect: keyEffect, replaceLength: replaceLength};
    }

    function reConfigOverlay(obj,reRender){
        //reRender overlay
        var textAreaObj = obj.getInstanceOverlay(obj);
        obj.revokeOverlay([{match: keyEffect}],textAreaObj);
        if(typeof reRender !== undefined){
            obj.reRenderTextOnOverlay(textAreaObj);
        }
    }
    //    exposed public methods
    return {
        init:init
        //getMentionPosition: getMentionPosition()
    }
}));