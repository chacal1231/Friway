
<?php
$this->Html->addCrumb(__('Site Manager'));
$this->Html->addCrumb(__('Themes Manager'), array('controller' => 'themes', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "themes"));
$this->end();
?>
<?php $this->start('page-toolbar'); ?>

<?php $this->end('page-toolbar'); ?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
$(document).on('loaded.bs.modal', function (e) {
Metronic.init();
});
$(document).on('hidden.bs.modal', function (e) {
$(e.target).removeData('bs.modal');
});

<?php $this->Html->scriptEnd(); ?>
<style type="text/css">
    .tabbable-custom > .nav-tabs {
        border-bottom: 1px solid #DDDDDD;
    }
</style>
<div class="note note-warning">
    <h4 class="block"><?php echo __('mooSocial Base Theme');?></h4>
    <p>
        <?php echo __("To customize the template files for your custom theme, click mooSocial Base Theme -> navigate to the template file you want to modify -> click Copy and select your theme. Click Go to make a copy of the template file in your theme folder. Now you can make changes to the file without affecting the base theme. Any changes made to the base theme will be overwritten when you upgrade the software. Changes in your custom theme will not be affected after upgrading. This means you don't have to redo your changes but if the base theme was changed in the new version, your custom theme will need to be updated (manually) as well.");?>
    </p>
</div>

        <div class="table-toolbar">
            <div class="row">
                <div class="col-md-12">
                    <div class="btn-group">
                        <button href="<?php echo $this->request->base?>/admin/themes/ajax_create" class="btn btn-gray" data-target="#ajax" data-toggle="modal">
                            <?php echo __('Create New Theme');?>
                        </button>
                    </div>

                </div>

            </div>

        </div>
        <div class=" portlet-tabs">
            <div class="tabbable tabbable-custom boxless tabbable-reversed">
                    <ul class="nav nav-tabs list7 chart-tabs">
                        <li class="active">
                            <a href="#portlet_tab1" data-toggle="tab">
                                <?php echo __('Installed Themes');?> </a>
                        </li>
                        <li>
                            <a href="#portlet_tab2" data-toggle="tab">
                                <?php echo __('Not Installed Themes');?> </a>
                        </li>
                        
                    </ul>
            <div class="row" style="padding-top: 10px;">
                <div class="col-md-12">
                    <div class="tab-content">
                        <div class="tab-pane active" id="portlet_tab1">
                            <table class="table table-striped table-bordered table-hover" id="sample_1">
                                <thead>
                                <tr>
                                    <th><?php echo __('ID');?></th>
                                    <th><?php echo __('Name');?></th>
                                    <th><?php echo __('Key');?></th>
                                    <th width="70"><?php echo __('Actions');?></th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php $count = 0;
                                foreach ($themes as $theme): ?>
                                    <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                                        <td><?php echo $theme['Theme']['id']?></td>
                                        <td><a href="<?php echo $this->request->base?>/admin/themes/editor/<?php echo $theme['Theme']['id']?>"><?php echo h($theme['Theme']['name'])?></a></td>
                                        <td><?php echo $theme['Theme']['key']?></td>
                                        <td><a href="<?php echo $this->request->base?>/admin/themes/do_uninstall/<?php echo $theme['Theme']['id']?>"><i class="icon-trash " title="<?php __('Uninstall');?>"></i></a>&nbsp;
                                            <a href="<?php echo $this->request->base?>/admin/themes/do_download/<?php echo $theme['Theme']['key']?>" target="_blank"><i class="fa  fa-download  " title="<?php echo __('Download')?>"></i></a>
                                        </td></tr>
                                <?php endforeach ?>

                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="portlet_tab2">
                            <ul class="list-group">
                                <?php foreach ($not_installed_themes as $theme): ?>
                                    <li class="list-group-item"><?php echo $theme?>
                                        <span class="badge badge-success">
                                            <a href="<?php echo $this->request->base?>/admin/themes/do_install/<?php echo $theme?>" style="color:#fff;"><?php echo __('Install');?></a>
                                        </span>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
