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
	$( ".mooTable" ).sortable( {
        items: "tr:not(.tbl_head)", 
        handle: ".reorder",
        update: function(event, ui) {
            var list = jQuery('.mooTable').sortable('toArray');
            $.post('<?php echo $this->request->base?>/admin/hooks/ajax_reorder', { hooks: list });
        }
    });
	
	initTabs('hook_index');
	$('.footable').footable();
});

function toggleTab(obj)
{
    $(obj).next().slideToggle();
}
<?php $this->Html->scriptEnd();  ?>

<style>
.position_tab:hover {
    text-decoration: none;
}
.tabbable-custom > .nav-tabs {
    border-bottom: 1px solid #DDDDDD;
}
</style>
<div class="row">
<div class="col-md-12">
    <div class="box1 p_b_10">
	<a href="#" class="overlay" title="Built-in Hook Positions" rel="hook_positions">Click here</a> to view a list of built-in hook positions.
    </div>
    <div class="tabbable tabbable-custom boxless tabbable-reversed">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#installed_content" data-toggle="tab">
                    Installed Hooks </a>
            </li>
            <li class="">
                <a href="#not_installed_content" data-toggle="tab">
                    Not Installed Hooks </a>
            </li>
        </ul>
        <div class="row" style="padding-top: 10px;">
            <div class="col-md-12">
                <div class="portlet-body form">
                    <div class="tab-content">
                        <div id="installed_content" class="tab-pane active">
                            <table class="table table-striped table-bordered table-hover" cellpadding="0" cellspacing="0">
                                <thead>
                                    <tr class="tbl_head">
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Key</th>
                                            <th data-hide="phone">Version</th>
                                            <th data-hide="phone">Controller</th>
                                            <th data-hide="phone">Action</th>
                                            <th data-hide="phone">Position</th>
                                            <th data-hide="phone">Enabled</th>
                                            <th data-hide="phone">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($hooks as $hook): ?>
                                    <tr id="<?php echo $hook['Hook']['id']?>">
                                            <td><?php echo $hook['Hook']['id']?></td>
                                            <td><a href="<?php echo $this->request->base?>/admin/hooks/ajax_view/<?php echo $hook['Hook']['id']?>" class="overlay" title="<?php echo h($hook['Hook']['name'])?> Hook"><?php echo h($hook['Hook']['name'])?></a></td>
                                            <td class="reorder"><?php echo $hook['Hook']['key']?></td>
                                            <td class="reorder"><?php echo $hook['Hook']['version']?></td>
                                            <td class="reorder"><?php echo (!empty($hook['Hook']['controller'])) ? $hook['Hook']['controller'] : 'GLOBAL'?></td>
                                            <td class="reorder"><?php echo $hook['Hook']['action']?></td>
                                            <td class="reorder"><?php echo $hook['Hook']['position']?></td>
                                            <td class="reorder"><?php echo ($hook['Hook']['enabled'])?'Yes':'No'?></td>
                                            <td><?php if ( $hook['Hook']['enabled'] ): ?>
                                                <a href="<?php echo $this->request->base?>/admin/hooks/do_disable/<?php echo $hook['Hook']['id']?>"><i class="tip icon-remove icon-small" title="Disable"></i></a>&nbsp;
                                                <?php else: ?>
                                                <a href="<?php echo $this->request->base?>/admin/hooks/do_enable/<?php echo $hook['Hook']['id']?>"><i class="tip icon-check icon-small" title="Enable"></i></a>&nbsp;
                                                <?php endif; ?>
                                                <a href="javascript:void(0)" onclick="mooConfirm('<?php echo addslashes(__('Are you sure you want to uninstall this hook?')) ?>', '<?php echo $this->request->base?>/admin/hooks/do_uninstall/<?php echo $hook['Hook']['id']?>')"><i class="tip icon-trash icon-small" title="Uninstall"></i></a>&nbsp;
                                    <a href="<?php echo $this->request->base?>/admin/hooks/do_download/<?php echo $hook['Hook']['key']?>" target="_blank"><i class="tip icon-download-alt icon-small" title="Download"></i></a>
                                </td>
                                    </tr>
                                    <?php endforeach ?>
                                    </tbody>
                            </table>
                        </div>
                        <div id="not_installed_content" class="tab-pane">
                            <ul class="list6">
                            <?php foreach ($not_installed_hooks as $hook): ?>
                                    <li><?php echo $hook?>
                               <div style="float:right">
                                   <a href="<?php echo $this->request->base?>/admin/hooks/do_install/<?php echo $hook?>">Install</a>
                               </div>    
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

</div>

<div id="hook_positions" style="display:none">
    <a href="javascript:void(0)" onclick="javascript:toggleTab(this)" class="position_tab"><h2 style="margin-top:0px;">All Pages</h2></a>
    <div>global_top, global_bottom, global_sidebar</div>
    <a href="javascript:void(0)" onclick="javascript:toggleTab(this)" class="position_tab"><h2>Home Page</h2></a>
    <div>home_top, home_sidebar</div>
    <a href="javascript:void(0)" onclick="javascript:toggleTab(this)" class="position_tab"><h2>User Pages</h2></a>
    <div>users_sidebar, users_top, profile_sidebar, profile_top</div>
    <a href="javascript:void(0)" onclick="javascript:toggleTab(this)" class="position_tab"><h2>Photo Pages</h2></a>
    <div>photos_sidebar, photos_top</div>
    <a href="javascript:void(0)" onclick="javascript:toggleTab(this)" class="position_tab"><h2>Event Pages</h2></a>
    <div>events_sidebar, events_top, event_detail_sidebar</div>
    <a href="javascript:void(0)" onclick="javascript:toggleTab(this)" class="position_tab"><h2>Blog Pages</h2></a>
    <div>blogs_sidebar, blogs_top, blog_detail_sidebar</div>
    <a href="javascript:void(0)" onclick="javascript:toggleTab(this)" class="position_tab"><h2>Group Pages</h2></a>
    <div>groups_sidebar, groups_top, group_detail_sidebar</div>
    <a href="javascript:void(0)" onclick="javascript:toggleTab(this)" class="position_tab"><h2>Topic Pages</h2></a>
    <div>topics_sidebar, topics_top, topic_detail_sidebar</div>
    <a href="javascript:void(0)" onclick="javascript:toggleTab(this)" class="position_tab"><h2>Video Pages</h2></a>
    <div>videos_sidebar, videos_top, video_detail_sidebar</div>
</div>
