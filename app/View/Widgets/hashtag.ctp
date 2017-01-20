<?php
    $this->loadLibrary(array('tagCloud'));
?>
<?php if (!empty($formatted_hashtag)): ?>
    <?php $this->Html->scriptStart(array('inline' => false)); ?>
        var oWords = <?php echo json_encode($formatted_hashtag); ?>;

        var word_array = [];
        var link = baseUrl+'/search/hashtags/';
        $.each(oWords,function(key,val){
            word_array.push({text: key, weight: val, link: link+key+'/tabs:<?php echo $type; ?>'});
        });
        //console.log(word_array);
        /*var word_array = [
            {text: "Lorem", weight: 15},
            {text: "Ipsum", weight: 9, link: "http://jquery.com/"},
            {text: "Dolor", weight: 6, html: {title: "I can haz any html attribute"}},
            {text: "Sit", weight: 7},
            {text: "Amet", weight: 5}
            // ...as many words as you want
        ];*/

        $(function() {
            // When DOM is ready, select the container element and call the jQCloud method, passing the array of words as the first argument.
            $(".tag-cloud").jQCloud(word_array);
        });
    <?php $this->Html->scriptEnd(); ?>
    <div class="box2">
        <?php if($title_enable): ?>
            <h3><?php echo $title;?></h3>
        <?php endif; ?>
        <div class="box_content">
            <div class="tag-cloud" style="height:350px"></div>
        </div>
    </div>
<?php endif; ?>