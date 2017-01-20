<?php
class MooPeopleHelper extends AppHelper {
    public $helpers = array('Text');
    public $isLoadedTooltipJs = false;
    public function initTooltipJs(){
        if($this->isLoadedInitJs) return true;
        $this->isLoadedTooltipJs = true;
        $this->_View->addInitJs('$(function(){$(\'[data-toggle="tooltip"]\').tooltip()});');
    }
    public function with($tagging_id = null, $ids=array(),$autoRender=true){
        if(!($ids =$this->_convertToArray($ids))) return false;
        $count = count($ids);
        $with1 = __(' — with %s');
        $with2 = __(' — with %s and %s');
        $with3 = __(' — with %s and %d others.');
        $with = "";
        switch($count){
            case 1:
                $with= sprintf($with1,$this->getName($ids[0]));
                break;
            case 2:
                $with = sprintf($with2,$this->getName($ids[0]),$this->getName($ids[1]));
                break;
            case 3:
            default:
            $with3a = explode('%d',$with3);
            $tooltipText = sprintf('%d'.$with3a[1],$count-1);
            $with3 = str_replace('%d'.$with3a[1],$this->tooltip($tagging_id, $ids,$tooltipText),$with3);

            $with = sprintf($with3,$this->getName($ids[0]));
        }
        if($autoRender) {
            echo $with;
            return true;
        }
        return $with;
    }
    
    public function isTagged($uid, $item_id, $item_type){
        if($item_type == 'Photo_Album')
            $item_type = 'activity';
        $UserTagging = MooCore::getInstance()->getModel('UserTagging');
        return $UserTagging->isTagged($uid, $item_id, $item_type);
    }

    public function isMentioned($uid, $item_id){
        $activityModel = MooCore::getInstance()->getModel('Activity');
        return $activityModel->isMentioned($uid, $item_id);
    }

    public function getName($data, $bold = true ,$idOnly=true,$textOnly=false) {
        if($idOnly){
            $user = MooPeople::getInstance()->get($data);

        }else{
            $user = $data;
        }

        if (!empty($user)) {
            $name = h($this->Text->truncate($user['User']['name'], 30));
            if($textOnly)
                return $name;
            $url = $user['User']['moo_href'];

            if ($bold)
                return '<a href="' . $url . '"><b>' . $name . '</b></a>';
            else
                return '<a href="' . $url . '">' . $name . '</a>';
        }
    }
    
    public function get($uid = null){
        if (!empty($uid)){
            $user = MooPeople::getInstance()->get($uid);
            return $user;
        }
        
        return false;
    }


    public function tooltip($tagging_id, $ids=array(),$tooltipText){
        if(!($ids =$this->_convertToArray($ids))) return false;
        $this->initTooltipJs();
        $title = '';
        unset($ids[0]);
        foreach($ids as $id){
            $title .= $this->getName($id,true,true,true)."<br/>";
        }
        return '<a data-toggle="modal" data-target="#themeModal" href="' . Router::url(array('controller'=>'users', 'action'=>'tagging', 'tagging_id' => $tagging_id)) .'">' . '<span class="tip" original-title="'.$title.'"><b>' . $tooltipText . '</b></span>' . '</a>';
    }
    private function _convertToArray($data){
        if(empty($data)) return false;
        if(!is_array($data)){
            return explode(',',$data);
        }else{
            return $data;
        }
        return false;
    }
    
    
    // check current viewer is friend with user_id
    // return boolean
    public function isFriend($user_id){
        $viewer = MooCore::getInstance()->getViewer();
        $viewer_id = MooCore::getInstance()->getViewer(true);
        $friendModel = MooCore::getInstance()->getModel('Friend');
        
        if ($friendModel->areFriends($viewer_id, $user_id)){
            return true;
        }
        
        return false;
    }
    
    // check current viewer sent request to user_id
    // return boolean
    public function sentFriendRequest($user_id){
        $viewer = MooCore::getInstance()->getViewer();
        $viewer_id = MooCore::getInstance()->getViewer(true);
        $friendRequestModel = MooCore::getInstance()->getModel('FriendRequest');
        $requests = $friendRequestModel->getRequestsList( $viewer_id );
        
        if (in_array($viewer_id, $requests) && $viewer_id != $user_id){
            return true;
        }
        
        return false;
    }
    
    // check current viewer is sent request from user_id
    // return boolean
    public function respondFriendRequest($user_id){
        $viewer = MooCore::getInstance()->getViewer();
        $viewer_id = MooCore::getInstance()->getViewer(true);
        $friendRequestModel = MooCore::getInstance()->getModel('FriendRequest');
        $respond = $friendRequestModel->getRequests( $viewer_id );
        $respond = Hash::extract($respond,'{n}.FriendRequest.sender_id');
        
        if(in_array($viewer_id, $respond) && $viewer_id != $user_id){
            return true;
        }
        
        return false;
    }
    
    

}