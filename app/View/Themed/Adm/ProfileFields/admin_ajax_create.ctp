<script>
    $(document).ready(function(){
        $('#createButton').click(function(){
            disableButton('createButton');
            $.post("<?php echo $this->request->base?>/admin/profile_fields/ajax_save", $("#createFieldForm").serialize(), function(data){
                enableButton('createButton');
                var json = $.parseJSON(data);

                if ( json.result == 1 )
                    location.reload();
                else
                {
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
            return false;
        });

        jQuery('#type').change(function(){
            if ( jQuery(this).val() == 'list' || jQuery(this).val() == 'multilist' )
                jQuery('#field_values').show();
            else
                jQuery('#field_values').hide();
        });
    });
</script>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo __('Add New');?></h4>
</div>
<div class="modal-body">
<form id="createFieldForm" class="form-horizontal system-setting" role="form">
    <?php echo $this->Form->hidden('id', array('value' => $field['ProfileField']['id'])); ?>
    <div class="form-body">
        <div class="form-group">
            <label class="col-md-3 control-label"><?php echo __('Field Name'); ?></label>

            <div class="col-md-9">
                <?php echo $this->Form->text('name', array('placeholder' => __('Enter text'),'class' => 'form-control','value' => $field['ProfileField']['name'])); ?>

            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php echo __('Field Type');?></label>

            <div class="col-md-9">

                <?php echo $this->Form->select('type', array( 'heading'   => __('Heading'),
                        'textfield' => __('Text Field'),
                        'list' 	  => __('List'),
                        'multilist' => __('Multi Select List'),
                        'textarea'  => __('Text Area')
                    ),
                    array('class' => 'form-control','value' => $field['ProfileField']['type'])
                );
                ?>
            </div>
        </div>
        <div class="form-group" <?php if ( !in_array( $field['ProfileField']['type'], array( 'list', 'multilist' ) ) ) echo 'style="display:none"'; ?> id="field_values">
            <label class="col-md-3 control-label"><?php echo __('Field Values');?> (<a data-placement="top" data-original-title="<?php echo __('One value per line');?>" class="tooltips" href="javascript:void(0);">?</a>)</label>

            <div class="col-md-9">

                <?php echo $this->Form->textarea('values', array('class' => 'form-control','value' => $field['ProfileField']['values'])); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php echo __('Description');?></label>

            <div class="col-md-9">
                <?php echo $this->Form->textarea('description', array('class' => 'form-control','value' => $field['ProfileField']['description'])); ?>

            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php echo __('Required');?></label>

            <div class="col-md-9">
                <?php echo $this->Form->checkbox( 'required', array( 'checked' => $field['ProfileField']['required'] ) ); ?>

            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php echo __('Registration');?></label>

            <div class="col-md-9">
                <?php echo $this->Form->checkbox( 'registration', array( 'checked' => $field['ProfileField']['registration'] ) ); ?>

            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php echo __('Searchable');?> (<a data-placement="top" data-original-title="<?php echo __('Only values that have 4 characters or more can be searched. If you want to make it less than 4, contact your server admin to adjust ft_min_word_len parameter');?>" class="tooltips" href="javascript:void(0);">?</a>)</label>

            <div class="col-md-9">
                <?php echo $this->Form->checkbox( 'searchable', array( 'checked' => $field['ProfileField']['searchable'] ) ); ?>
               
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php echo __('Profile');?> (<a data-placement="top" data-original-title="<?php echo __('Check this if you want to display this field on the main user profile tab');?>" class="tooltips" href="javascript:void(0);">?</a>)</label>

            <div class="col-md-9">
                <?php echo $this->Form->checkbox( 'profile', array( 'checked' => $field['ProfileField']['profile'] ) ); ?>
                
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php echo __('Active');?></label>

            <div class="col-md-9">
                <?php echo $this->Form->checkbox( 'active', array( 'checked' => $field['ProfileField']['active'] ) ); ?>

            </div>
        </div>
    </div>

</form>
    <div class="alert alert-danger error-message" style="display:none;margin-top:10px;">

    </div>

</div>
<div class="modal-footer">

    <button type="button" class="btn default" data-dismiss="modal"><?php echo __('Close');?></button>
    <a href="#" id="createButton" class="btn btn-action"><?php echo __('Save Field');?></a>

</div>