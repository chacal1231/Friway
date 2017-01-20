<?php

    if(!empty($friends)){
        $response = array();
        foreach ($friends as $friend){
            $response[]= array(
                'id'=>$friend['User']['id'],
                'name'=>$friend['User']['name'],
                'avatar'=>$this->Moo->getItemPhoto(array('User' => $friend['User']),array('class' => 'user_avatar_small tip', 'prefix' => '50_square'),array(),true),
            );
        }
        echo  json_encode(array('data'=>$response));
    }
?>