<?php
echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));

$this->Html->addCrumb('Site Manager');
$this->Html->addCrumb('Hook', array('controller' => 'hooks', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "hooks"));
$this->end();
?>

<?php  $this->Html->scriptStart(array('inline' => false));   ?>
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
<?php $this->Html->scriptEnd();  ?>
<style type="text/css">
    .tabbable-custom > .nav-tabs {
        border-bottom: 1px solid #DDDDDD;
    }
</style>
<div id="hook_view">
<div class="row">
    <div class="col-md-12">
        <div class="tabbable tabbable-custom boxless tabbable-reversed">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#hook_info_content" data-toggle="tab">
                        Hook Info </a>
                </li>
                <li class="">
                    <a href="#hook_settings_content" data-toggle="tab">
                        Settings </a>
                </li>
                <li class="">
                    <a href="#hook_permissions_content" data-toggle="tab">
                        Permissions </a>
                </li>
            </ul>
            <div class="row" style="padding-top: 10px;">
                <div class="col-md-12">
                    <div class="portlet-body form">
                        <form id="createForm" class="form-horizontal">
                        <div class="tab-content">
                            <div id="hook_info_content" class="tab-pane active">	
                                <ul class="list6 info">
                                        <li><label>Name</label><?php echo $hook['Hook']['name']?></li>
                                        <li><label>Key</label><?php echo $hook['Hook']['key']?></li>
                                        <li><label>Version</label><?php echo $hook['Hook']['version']?> <?php if ( $info->version > $hook['Hook']['version'] ):?>(<a href="<?php echo $this->request->base?>/admin/hooks/do_upgrade/<?php echo $hook['Hook']['id']?>">Upgrade</a>)<?php endif; ?></li>
                                        <li><label>Author</label><?php echo $info->author?></li>
                                        <li><label>Website</label><?php echo $info->website?></li>
                                        <li><label>Description</label><?php echo $info->description?></li>
                                </ul>
                            </div>
                            
                            <div id="hook_settings_content" class="tab-pane">        
                                <?php echo $this->Form->hidden('id', array('value' => $hook['Hook']['id'])); ?>
                                <div class="list6">
                                    <?php foreach ( get_object_vars($info->settings) as $key => $data ): ?>
                                    <div class="form-group"><label class="col-md-3 control-label"><?php echo $data->label?> (<a href="javascript:void(0)" class="tip" title="<?php echo $data->description?>">?</a>)</label>
                                    <div class="col-md-4">
                                    <?php 
                                    switch ( $data->type )
                                    {
                                        case 'checkbox':
                                            echo $this->Form->checkbox($key, array( 'class' => 'form-control','checked' => $settings[$key] ));
                                        break;

                                        case 'select':
                                            $tmp = explode(',', $data->options);

                                            foreach ( $tmp as $o )
                                                $options[$o] = $o;

                                            echo $this->Form->select($key, $options, array( 'class' => 'form-control','value' => $settings[$key] ));
                                        break;

                                        default:
                                            echo $this->Form->text($key, array( 'class' => 'form-control','value' => $settings[$key] ));
                                    }

                                    if ( !empty( $data->description ) ):
                                    ?>
                                    
                                    <?php 
                                    endif;
                                    ?></div></div>
                                    <?php endforeach; ?>
                                    <div class="form-group"> 
                                        <label class="col-md-3 control-label">Controller (<a href="javascript:void(0)" class="tip" title="Leave empty if you want to run this hook globally">?</a>)</label>
                                        <div class="col-md-4"><?php echo $this->Form->text('controller', array( 'class' => 'form-control','value' => $hook['Hook']['controller'] ))?> </div></div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Action</label>
                                        <div class="col-md-4"><?php echo $this->Form->text('action', array( 'class' => 'form-control','value' => $hook['Hook']['action'] ))?></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Position</label>
                                        <div class="col-md-4"><?php echo $this->Form->text('position', array( 'class' => 'form-control','value' => $hook['Hook']['position'] ))?></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Enabled</label>
                                        <div class="col-md-4"><?php echo $this->Form->checkbox('enabled', array( 'class' => 'form-control','checked' => $hook['Hook']['enabled'] ))?></div>
                                    </div>
                                </div>
                            </div>
                            <div id="hook_permissions_content" class="tab-pane">
                                <?php echo $this->element('admin/permissions', array('permission' => $hook['Hook']['permission'])); ?>        
                            </div>
                            
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="form-actions">
    <div class="row">
        <div class="col-md-offset-3 col-md-9">
            <div class="regSubmit">
                <a href="#" id="createButton" class="btn btn-action">Save Changes</a>
            </div>

        </div>
    </div>
</div>
<div class="error-message" style="display:none;margin-top:10px"></div>
