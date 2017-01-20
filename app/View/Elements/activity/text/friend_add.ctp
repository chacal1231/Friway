<?php

$ids = explode(',', $activity['Activity']['items']);
$userModel = MooCore::getInstance()->getModel('User');
$userModel->cacheQueries = false;
$users = $userModel->find('all', array('conditions' => array('User.id' => $ids)));
echo __('is now friends with') . ' ';

$friend_add1 = '%s';
$friend_add2 = '%s and %s';
$friend_add3 = '%s and %s';
$friend_add = '';

switch (count($users)) {
    case 1:
        $friend_add = sprintf($friend_add1, '<a href="' . $users[0]['User']['moo_href'] . '">' . h($users[0]['User']['name']) . '</a>');
        break;
    case 2:
        $friend_add = sprintf($friend_add2, '<a href="' . $users[0]['User']['moo_href'] . '">' . h($users[0]['User']['name']) . '</a>', '<a href="' . $users[1]['User']['moo_href'] . '">' . h($users[1]['User']['name']) . '</a>');
        break;
    case 3:
    default :
        $friend_add = sprintf($friend_add3, '<a href="' . $users[0]['User']['moo_href'] . '">' . h($users[0]['User']['name']) . '</a>', '<a data-toggle="modal" data-target="#themeModal" href="' . $this->Html->url(array('controller' => 'users', 'action' => 'ajax_friend_added', 'activity_id' => $activity['Activity']['id'])) . '">' . abs(count($users) - 1) . ' ' . __('others') . '</a>');
        break;
}

echo $friend_add;

?>