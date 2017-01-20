<?php

$response = array();
if(!empty($users)){
    foreach ($users as $user){
        $response[]= array(
            'id'=>$user['User']['id'],
            'name'=>$user['User']['name'],
            'avatar'=>$this->Moo->getItemPhoto(array('User' => $user['User']),array('class' => 'user_avatar_small tip', 'prefix' => '50_square'),array(),true),
        );
    }
}
echo  json_encode(array('friends'=>$response));
