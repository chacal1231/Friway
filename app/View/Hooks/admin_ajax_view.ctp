<script>
$(document).ready(function(){
    $('#createButton').click(function(){
        var checked = false;
        $('#permission_list :checkbox').each(function(){
            if ($(this).is(':checked'))
               checked = true;
        })
        
        if (!checked)
        {
           $(".error-message").show();
           $('.error-message').html('Please check at least one user role in the Permissions tab');
           return;
        }
        
        disableButton('createButton');
        $.post("<?php echo $this->request->base?>/admin/hooks/ajax_save", $("#createForm").serialize(), function(data){
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
    
    initTabs('hook_view');
});
</script>

<div id="hook_view">
    <div class="tabs-wrapper">
        <ul class="tabs">
            <li id="hook_info" class="active">Hook Info</li>
            <li id="hook_settings">Settings</li>
            <li id="hook_permissions">Permissions</li>  
        </ul>
    </div>
    
    <div id="hook_info_content" class="tab" style="display:block">	
        <ul class="list6 info">
        	<li><label>Name</label><?php echo $hook['Hook']['name']?></li>
        	<li><label>Key</label><?php echo $hook['Hook']['key']?></li>
        	<li><label>Version</label><?php echo $hook['Hook']['version']?> <?php if ( $info->version > $hook['Hook']['version'] ):?>(<a href="<?php echo $this->request->base?>/admin/hooks/do_upgrade/<?php echo $hook['Hook']['id']?>">Upgrade</a>)<?php endif; ?></li>
        	<li><label>Author</label><?php echo $info->author?></li>
        	<li><label>Website</label><?php echo $info->website?></li>
        	<li><label>Description</label><?php echo $info->description?></li>
        </ul>
    </div>
    <form id="createForm">
    <div id="hook_settings_content" class="tab">        
        <?php echo $this->Form->hidden('id', array('value' => $hook['Hook']['id'])); ?>
        <ul class="list6">
            <?php foreach ( get_object_vars($info->settings) as $key => $data ): ?>
            <li><label><?php echo $data->label?></label>
            <?php 
            switch ( $data->type )
            {
                case 'checkbox':
                    echo $this->Form->checkbox($key, array( 'checked' => $settings[$key] ));
                break;
                
                case 'select':
                    $tmp = explode(',', $data->options);
                    
                    foreach ( $tmp as $o )
                        $options[$o] = $o;
                    
                    echo $this->Form->select($key, $options, array( 'value' => $settings[$key] ));
                break;
                    
                default:
                    echo $this->Form->text($key, array( 'value' => $settings[$key] ));
            }
            
            if ( !empty( $data->description ) ):
            ?>
            (<a href="javascript:void(0)" class="tip" title="<?php echo $data->description?>">?</a>)
            <?php 
            endif;
            ?></li>
            <?php endforeach; ?>
            <li><label>Controller</label><?php echo $this->Form->text('controller', array( 'value' => $hook['Hook']['controller'] ))?> (<a href="javascript:void(0)" class="tip" title="Leave empty if you want to run this hook globally">?</a>)</li>
            <li><label>Action</label><?php echo $this->Form->text('action', array( 'value' => $hook['Hook']['action'] ))?></li>
            <li><label>Position</label><?php echo $this->Form->text('position', array( 'value' => $hook['Hook']['position'] ))?></li>
            <li><label>Enabled</label><?php echo $this->Form->checkbox('enabled', array( 'checked' => $hook['Hook']['enabled'] ))?></li>
        </ul>
    </div>
    <div id="hook_permissions_content" class="tab">
        <?php echo $this->element('admin/permissions', array('permission' => $hook['Hook']['permission'])); ?>        
    </div>
    </form>
    
    <div class="regSubmit">
        <a href="#" id="createButton" class="button button-action"><i class="icon-save"></i> Save Changes</a>
    </div>
    
    <div class="error-message" style="display:none;margin-top:10px"></div>
</div>