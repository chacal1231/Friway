<?php if($this->request->is('ajax')) $this->setCurrentStyle(4) ?>

<?php
if(!isset($events)){
    if($uid !== null){
        $events = $this->requestAction("events/upcomming/uid:".$uid);
    }else{
        $events = array();
    }
}
    if (count($events) > 0):
        foreach ($events as $event):
    ?>
        <li class="full_content">
                <div class="event-date"><?php echo $this->Time->format('M d', $event['Event']['from'])?></div>
                <div class="comment">
                    <a href="<?php echo $this->request->base?>/events/view/<?php echo $event['Event']['id']?>/<?php echo seoUrl($event['Event']['title'])?>"><img src="<?php echo $this->Moo->getItemPicture($event['Event'], 'events')?>" class="img_wrapper2 thumb_100 img_list_mobile"></a>
                    <div class="list-item-info">
                        <a href="<?php echo $this->request->base?>/events/view/<?php echo $event['Event']['id']?>/<?php echo seoUrl($event['Event']['title'])?>">
                            <b><?php echo h($event['Event']['title'])?></b>
                        </a>
                        <div class="event-info">
                            <div style='margin-bottom: 5px;'>
                                <i class="icon-map-marker"></i> <?php echo h($event['Event']['location'])?>
                            </div>
                            <div>
                                <i class="icon-group"></i> <?php echo __( '%s attending', $event['Event']['event_rsvp_count'])?>
                            </div>
                        </div>

                    </div>
                </div>
        </li>
    <?php
        endforeach;
    else:
        echo '<div align="center">' . __( 'No more results found') . '</div>';
    endif;

?>

<?php
if (count($events) >= RESULTS_LIMIT):
?>

    <?php $this->Html->viewMore($more_url); ?>
<?php
endif;

?>
