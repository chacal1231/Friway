<ul class="list2 menu-list" id="browse">
    <li class="current"><?php echo $this->Html->link(__('Upcoming Events'), '/events/browse/upcoming', array('data-url' => $this->request->base . '/events/browse/upcoming', 'class' => 'json-view')); ?>
        <?php if (!empty($uid)): ?>
        <li><?php echo $this->Html->link(__('My Upcoming Events'), '/events/browse/my', array('data-url' => $this->request->base . '/events/browse/my', 'class' => 'json-view')); ?>
        <li><?php echo $this->Html->link(__('My Past Events'), '/events/browse/mypast', array('data-url' => $this->request->base . '/events/browse/mypast', 'class' => 'json-view')); ?>
        <li><?php echo $this->Html->link(__('Friends Attending'), '/events/browse/friends', array('data-url' => $this->request->base . '/events/browse/friends', 'class' => 'json-view')); ?>
        <?php endif; ?>
    <li><?php echo $this->Html->link(__('Past Events'), '/events/browse/past', array('data-url' => $this->request->base . '/events/browse/past', 'class' => 'json-view')); ?>
    <li class="separate"></li>     
</ul>