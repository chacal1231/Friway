<?php $this->setCurrentStyle(4);?>
<script>
function respondRequest(id, status)
{
    if ($('#friend_request_count').length > 0){
        var current_request = $('#friend_request_count').html();
        var new_request = parseInt(current_request - 1);
        if (new_request <= 0){
            $('#friend_request_count').parents('li:first').remove();
        }else {
            $('#friend_request_count').html(new_request);
        }
    }
    
    jQuery.post('<?php echo $this->request->base?>/friends/ajax_respond', {id: id, status: status}, function(data){
        jQuery('#request_'+id).html(data);
    });
}
</script>
<div class="bar-content m_d_7">
    <div class="content_center">
        <div class="mo_breadcrumb">
            <h1><?php echo __('Friend Requests')?></h1>
        </div>
	<div class="full_content p_m_10">

        <?php if (empty($requests)): echo '<div align="center">' . __('You have no friend requests') . '</div>';
        else: ?>
        <ul class="list6 comment_wrapper" style="margin-top:0">
        <?php foreach ($requests as $request): ?>
                <li id="request_<?php echo $request['FriendRequest']['id']?>">
                        <div style="float:right">
                            <a href="javascript:void(0)" onclick="respondRequest(<?php echo $request['FriendRequest']['id']?>, 1)" class="btn btn-action"><?php echo __('Accept')?></a>
                            <a href="javascript:void(0)" onclick="respondRequest(<?php echo $request['FriendRequest']['id']?>, 0)" class="button "><?php echo __('Delete')?></a>
                        </div>
                        <?php echo $this->Moo->getItemPhoto(array('User' => $request['Sender']), array( 'prefix' => '100_square'), array('class' => 'img_wrapper2 user_avatar_large'))?>
                        <div class="friend-request-info">
                                <?php echo $this->Moo->getName($request['Sender'])?><br /><?php echo nl2br(h($request['FriendRequest']['message']))?><br />
                                <span class="date"><?php echo $this->Moo->getTime( $request['FriendRequest']['created'], Configure::read('core.date_format'), $utz )?></span>
                        </div>
                </li>
        <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        </div>
    </div>
</div>