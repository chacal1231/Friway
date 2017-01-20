<?php
App::uses('AppHelper', 'View/Helper');
class EventHelper extends AppHelper {
	
	public function isMember($event,$uid)
	{
		$model = $model = MooCore::getInstance()->getModel('Event_Event_Rsvp');	
		$my_rsvp = $model->getMyRsvp( $uid, $event['Event']['id'] );
		
		return $my_rsvp;
	}
	
	public function getEnable()
	{
		return Configure::read('Event.event_enabled');
	}
	
	public function checkPostStatus($event,$uid)
	{
		if (!$uid)
			return false;	
	    
		$my_rsvp = $this->isMember($event, $uid);
		
		if ($my_rsvp || $event['Event']['type'] != PRIVACY_PRIVATE )
			return true;
			
		return false;
	}
	
	public function checkSeeActivity($event,$uid)
	{
		$is_member = $this->isMember($event, $uid);
		if ($event['Event']['type'] == PRIVACY_PRIVATE) {
			$cuser = MooCore::getInstance()->getViewer();

       		if (!$cuser['Role']['is_admin'] && !$is_member)
				return false;
		}
		return true;
	}
	
	public function getAdminList($event)
	{
		return array($event['Event']['user_id']);
	}
        
        public function getImage($item, $options) {
            $request = Router::getRequest();
            $view = MooCore::getInstance()->getMooView();
            $prefix = '';
            if (isset($options['prefix'])) {
                $prefix = $options['prefix'] . '_';
            }

            if ($item['Event']['photo']) {
                $url = FULL_BASE_URL . $request->webroot . 'uploads/events/photo/' . $item['Event']['id'] . '/' . $prefix . $item['Event']['photo'];
            } else {
                $url = FULL_BASE_URL . $this->assetUrl('Event.noimage/event.png', $options + array('pathPrefix' => Configure::read('App.imageBaseUrl')));
            }

            return $url;
        }

}
