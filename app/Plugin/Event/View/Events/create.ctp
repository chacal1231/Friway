<?php $this->setCurrentStyle(4);?>
<?php
$eventHelper = MooCore::getInstance()->getHelper('Event_Event');
echo $this->Html->css(array('pickadate', 'fineuploader'), null, array('inline' => false));
echo $this->Html->script(array('pickadate/picker', 'pickadate/picker.date', 'pickadate/picker.time', 'pickadate/legacy', 'jquery.fileuploader'), array('inline' => false));
?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
$(document).ready(function(){
    var errorHandler = function(event, id, fileName, reason) {
        if ($('.qq-upload-list .errorUploadMsg').length > 0){
            $('.qq-upload-list .errorUploadMsg').html('<?php echo addslashes(__('Can not upload file more than ')) . $file_max_upload?>');
        }else {
            $('.qq-upload-list').prepend('<div class="errorUploadMsg"><?php echo addslashes(__('Can not upload file more than ')) . $file_max_upload?></div>');
        }
        $('.qq-upload-fail').remove();
    };
    var uploader = new qq.FineUploader({
        element: $('#select-0')[0],
        multiple: false,
        text: {
            uploadButton: '<div class="upload-section"><i class="icon-camera"></i><?php echo addslashes(__( 'Drag or click here to upload photo'))?></div>'
        },
        validation: {
            allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
            <?php if(!empty($sizeLimit)): ?>
                sizeLimit: <?php echo $sizeLimit ?>
            <?php endif; ?>
        },
        request: {
            endpoint: "<?php echo $this->request->base?>/event/event_upload/avatar"
        },
        callbacks: {
            onError: errorHandler,
            onComplete: function(id, fileName, response) {
                $('#photo').val(response.filename);
                $('#item-avatar').attr('src', response.thumb);
                $('#item-avatar').show();
                $('#photo').val(response.file_path);
            }
        }
    });
    
    $(".datepicker").pickadate({
        monthsFull: ['<?php echo addslashes(__( 'January'))?>', '<?php echo addslashes(__( 'February'))?>', '<?php echo addslashes(__( 'March'))?>', '<?php echo addslashes(__( 'April'))?>', '<?php echo addslashes(__( 'May'))?>', '<?php echo addslashes(__( 'June'))?>', '<?php echo addslashes(__( 'July'))?>', '<?php echo addslashes(__( 'August'))?>', '<?php echo addslashes(__( 'September'))?>', '<?php echo addslashes(__( 'October'))?>', '<?php echo addslashes(__( 'November'))?>', '<?php echo addslashes(__( 'December'))?>'],
        monthsShort: ['<?php echo addslashes(__( 'Jan'))?>', '<?php echo addslashes(__( 'Feb'))?>', '<?php echo addslashes(__( 'Mar'))?>', '<?php echo addslashes(__( 'Apr'))?>', '<?php echo addslashes(__( 'May'))?>', '<?php echo addslashes(__( 'Jun'))?>', '<?php echo addslashes(__( 'Jul'))?>', '<?php echo addslashes(__( 'Aug'))?>', '<?php echo addslashes(__( 'Sep'))?>', '<?php echo addslashes(__( 'Oct'))?>', '<?php echo addslashes(__( 'Nov'))?>', '<?php echo addslashes(__( 'Dec'))?>'],
        weekdaysFull: ['<?php echo addslashes(__( 'Sunday'))?>', '<?php echo addslashes(__( 'Monday'))?>', '<?php echo addslashes(__( 'Tuesday'))?>', '<?php echo addslashes(__( 'Wednesday'))?>', '<?php echo addslashes(__( 'Thursday'))?>', '<?php echo addslashes(__( 'Friday'))?>', '<?php echo addslashes(__( 'Saturday'))?>'],
        weekdaysShort: ['<?php echo addslashes(__( 'Sun'))?>', '<?php echo addslashes(__( 'Mon'))?>', '<?php echo addslashes(__( 'Tue'))?>', '<?php echo addslashes(__( 'Wed'))?>', '<?php echo addslashes(__( 'Thu'))?>', '<?php echo addslashes(__( 'Fri'))?>', '<?php echo addslashes(__( 'Sat'))?>'],
        today:"<?php echo addslashes(__( 'Today'))?>",
        clear:"<?php echo addslashes(__( 'Clear'))?>",
        close: "<?php echo addslashes(__( 'Close'))?>",
        format: 'yyyy-mm-dd',
        close: false,
        onClose: function() {
            if ( $('#to').val() != '' && ($('#from').val() > $('#to').val()) )
            {
                mooAlert('<?php echo addslashes(__( 'To date must be greater than From date'))?>');
                $('#to').val('');
            }
            if ( $('#to').val() != '' && ($('#from').val() >= $('#to').val()) )
            {
                var fromTime = parseInt($('#from_time_root .picker__list-item--selected').attr('data-pick'));
                var toTime = parseInt($('#to_time_root .picker__list-item--selected').attr('data-pick'));
                if ($('#to_time').val() != '' && toTime <= fromTime){
                    mooAlert('<?php echo addslashes(__( 'To time must be greater than From time'))?>');
                    $('#to').val('');
                    $('#to_time').val('');
                }
            }
        }
    });
    
    $(".timepicker").pickatime({
        clear: '<?php echo addslashes(__( 'Clear'))?>',
        format: '<?php echo (Configure::read('core.time_format') == '24') ? 'H:i' : 'h:i A'?>',
        onClose : function(time){
            if ( $('#to').val() != '' && ($('#from').val() >= $('#to').val()) )
            {
                var fromTime = parseInt($('#from_time_root .picker__list-item--selected').attr('data-pick'));
                var toTime = parseInt($('#to_time_root .picker__list-item--selected').attr('data-pick'));
                if ($('#to_time').val() != '' && toTime <= fromTime){
                    mooAlert('<?php echo addslashes(__( 'To time must be greater than From time'))?>');
                    $('#to').val('');
                    $('#to_time').val('');
                }
            }
        }
    });
    
    
    $('#saveBtn').click(function(){
        $(this).addClass('disabled');
        createItem('events', true);
    });
    
});

<?php $this->Html->scriptEnd(); ?>
<div class="create_form">
<div class="bar-content">
    <div class="content_center">
        <form id="createForm">
        <?php
        if (!empty($event['Event']['id'])){
            echo $this->Form->hidden('id', array('value' => $event['Event']['id']));
            echo $this->Form->hidden('photo', array('value' => $event['Event']['photo']));
        }else{
            echo $this->Form->hidden('photo', array('value' => ''));
        }
        ?>	

        <div class="box3">	
            <div class="mo_breadcrumb">
                <h1><?php if (empty($event['Event']['id'])) echo __( 'Add New Event'); else echo __( 'Edit Event');?></h1>
            </div>

            <div class="full_content p_m_10">
                <div class="form_content">
                <ul class="list6 list6sm2">
                        <li>
                            <div class="col-md-2">
                                <label><?php echo __( 'Event Title')?></label>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->text('title', array('value' => $event['Event']['title'])); ?>
                            </div>
                        </li>
                        <li>
                            <div class="col-md-2">
                                <label><?php echo __( 'Category')?></label>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->select( 'category_id', $categories, array( 'value' => $event['Event']['category_id'] ) ); ?>
                            </div>
                        </li>
                        <li>
                            <div class="col-md-2">
                            <label><?php echo __( 'Location')?></label>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->text('location', array('value' => $event['Event']['location'])); ?>
                                <a href="javascript:void(0)" class="tip profile-tip" title="<?php echo __( 'e.g. Aluminum Hall, Carleton University')?>">(?)</a>
                            </div>

                        </li>
                        <li>
                            <div class='col-md-2'>
                            <label><?php echo __( 'Address')?></label>
                            </div>
                            <div class='col-md-10'>
                                <?php echo $this->Form->text('address', array('value' => $event['Event']['address'])); ?>
                                <a href="javascript:void(0)" class="tip profile-tip" title="<?php echo __( 'Enter the full address (including city, state, country) of the location.<br />This will render a Google map on your event page (optional)')?>">(?)</a>
                            </div>

                        </li>
                        <li>
                            <div class='col-md-2'>
                            <label><?php echo __( 'From')?></label>
                            </div>
                            <div class="col-md-10">
                                <div class='col-xs-6'>
                                    <?php
                                    echo $this->Form->text('from', array('class' => 'datepicker', 'value' => $event['Event']['from'])); ?>
                                </div>
                                <div class='col-xs-6'>
                                    <div class="m_l_2">
                                        <?php

                                        echo $this->Form->text('from_time', array('value' => $event['Event']['from_time'], 'class' => 'timepicker'));
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class='col-md-2'>
                                <label><?php echo __( 'To')?></label>
                            </div>
                            <div class="col-md-10">
                                <div class='col-xs-6'>
                                    <?php
                                    echo $this->Form->text('to', array('class' => 'datepicker', 'value' => $event['Event']['to']));	 ?>
                                </div>
                                <div class='col-xs-6'>
                                    <div class="m_l_2">
                                    <?php

                                    echo $this->Form->text('to_time', array('value' => $event['Event']['to_time'], 'class' => 'timepicker'));

                                    ?>
                                        </div>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-2">
                                <label><?php echo __( 'Timezone')?></label>
                            </div>
                            <div class="col-md-10">
                                <?php $currentTimezone = !empty($event['Event']['timezone']) ? $event['Event']['timezone'] : $cuser['timezone']; ?>
                                <?php echo $this->Form->select('timezone', $this->Moo->getTimeZones(), array('empty' => false, 'value' => $currentTimezone)); ?>
                            </div>
                        </li>
                        <li>
                            <div class='col-md-2'>
                                <label><?php echo __( 'Information')?></label>
                            </div>
                            <div class='col-md-10'>
                                <?php echo $this->Form->textarea('description', array('style' => 'height:100px', 'value' => $event['Event']['description'])); ?>
                            </div>
                        </li>

                        <li>
                            <div class='col-md-2'>
                                <label><?php echo __( 'Event Type')?></label>
                            </div>
                            <div class='col-md-10'>
                                 <?php 
                                echo $this->Form->select('type', array( PRIVACY_PUBLIC  => __( 'Public'), 
                                                                                                                PRIVACY_PRIVATE => __( 'Private')
                                                                                                        ), 
                                                                                                 array( 'value' => $event['Event']['type'], 'empty' => false ) 
                                                                                ); 
                                ?>
                                <a href="javascript:void(0)" class="tip profile-tip" title="<?php echo __( 'Public: anyone can view and RSVP<br />Private: only invited guests can view and RSVP')?>">(?)</a>
                            </div>

                        </li>		
                        <li>
                            <div class="col-md-2">
                                <label><?php echo __( 'Photo')?></label>
                            </div>
                            <div class="col-md-10">
                                <div id="select-0" style="margin: 10px 0 0 0px;"></div>
                                <?php if (!empty($event['Event']['photo'])): ?>
                                <img width="150" id="item-avatar" class="img_wrapper" src="<?php echo  $eventHelper->getImage($event, array('prefix' => '150_square')) ?>" />
                                <?php else: ?>
                                    <img width="150" id="item-avatar" class="img_wrapper" style="display: none;" src="" />
                                <?php endif; ?>
                                
                            </div>
                            <div class="clear"></div>
                        </li>		
                        <li>
                            <div class="col-md-2">
                                <label>&nbsp;</label>
                            </div>
                            <div class="col-md-10">
                                <button type='button' class='btn btn-action' id="saveBtn"><?php echo __( 'Save')?></button>
                                
                                <?php if ( !empty( $event['Event']['id'] ) ): ?>
                                    <a href="<?php echo $this->request->base?>/events/view/<?php echo $event['Event']['id']?>" class="button"><?php echo __( 'Cancel')?></a>
                                <?php endif; ?>
                                <?php if ( ($event['Event']['user_id'] == $uid ) || ( !empty( $event['Event']['id'] ) && !empty($cuser['Role']['is_admin']) ) ): ?>
                                    <a href="javascript:void(0)" onclick="mooConfirm( '<?php echo addslashes(__( 'Are you sure you want to remove this event?'))?>', '<?php echo $this->request->base?>/events/do_delete/<?php echo $event['Event']['id']?>' )" class="button"><?php echo __( 'Delete')?></a>
                                <?php endif; ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                </ul>
           
                <div class="error-message" style="display:none;"></div>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>
</div>