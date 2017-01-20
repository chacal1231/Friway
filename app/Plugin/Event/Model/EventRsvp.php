<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */

App::uses('EventAppModel','Event.Model');
class EventRsvp extends EventAppModel {
	
	public $belongsTo = array( 'User', 
							   'Event'  => array( 'className' => 'Event.Event',
                                                  'counterCache' => true,
											 	  'counterScope' => array('rsvp' => RSVP_ATTENDING)
							)	);
							
	public $order = 'EventRsvp.id desc';
	
	public $validate = array( 'event_id' => array( 'rule' => 'notBlank'),
							  'user_id' => array( 'rule' => 'notBlank')
	);
	
							
	/*
	 * Get events based on type
	 * @param string $type - possible value: home, my, friends
	 * @param int $uid - user id
	 * @param int $page - page number
	 * @return array $events
	 */
	public function getEvents( $type = null, $uid = null, $page = 1, $role_id = null )
	{
        $pp = Configure::read('Event.event_item_per_pages');
        $limit = (!empty($pp)) ? $pp : RESULTS_LIMIT;
		$cond = array();
		
		switch ( $type )
		{			
			// Get my future events (attending and waiting response)	
			case 'home':
			case 'my':				
				if ( $uid )
					$cond = array( 'EventRsvp.user_id' => $uid, 
								   '(EventRsvp.rsvp = ' . RSVP_ATTENDING . ' OR EventRsvp.rsvp = ' . RSVP_AWAITING . ')', 
								   'Event.to >= CURDATE()' 
								);					
				break;

            // Get my past events (attending and waiting response)
            case 'mypast':
                if ( $uid )
                    $cond = array( 'EventRsvp.user_id' => $uid,
                        '(EventRsvp.rsvp = ' . RSVP_ATTENDING . ' OR EventRsvp.rsvp = ' . RSVP_AWAITING . ')',
                        'Event.to < CURDATE()'
                    );
                break;

			// Get my events that friends are attending	excluding private events (type < 3)
			case 'friends':				
				if ( $uid )
				{
					App::import('Model', 'Friend');	
					$friend = new Friend();
					$friends = $friend->getFriends( $uid );	

                    if($role_id == ROLE_ADMIN)
                        $cond = array( 'EventRsvp.user_id' => $friends,
                            'EventRsvp.rsvp' => RSVP_ATTENDING,
                            'Event.to >= CURDATE()'
                        );
                    else
                        $cond = array( 'EventRsvp.user_id' => $friends,
                            'EventRsvp.rsvp' => RSVP_ATTENDING,
                            'Event.type' => PRIVACY_PUBLIC,
                            'Event.to >= CURDATE()'
                        );
				}					
				break;
		}
        if($type === null || !in_array($type,array('my','mypast','friends')))
            $events = Cache::read('eventrsvp.'.($type === null ? 'all' : $type).'.page.'.$page,'event');
        else
            $events = Cache::read('eventrsvp.'.$type.'.'.$uid.'.page.'.$page,'event');
        if(empty($events)){
            $events = $this->find( 'all', array( 'conditions' => $cond,
                //'fields' => array( 'DISTINCT Event.id', 'Event.*'),
                'limit' => $limit,
                'page' => $page
            ) );            if($type === null || !in_array($type,array('my','mypast','friends')))
                Cache::write('eventrsvp.'.($type === null ? 'all' : $type).'.page.'.$page,$events,'event');
            else
                Cache::write('eventrsvp.'.$type.'.'.$uid.'.page.'.$page,$events,'event');
        }

		
		return $events;
	}
	
	/*
	 * Get rsvps of an event based on $type	 
	 * @param int $event_id
	 * @param mixed $type from 0 to 3
	 * @param int $page - page number
	 * @return array $rsvps
	 */
	public function getRsvp( $event_id, $type = null, $page = null, $limit = RESULTS_LIMIT )
	{
		$cond = array( 'event_id' => $event_id );
		
		if ( $type !== null )
			$cond['rsvp'] = $type;
		$rsvps = Cache::read('eventrsvp.getrsvp.'.$event_id.'_'.$type.'.page.'.$page, 'event');
        if(!is_array($rsvps))
        {
		    $rsvps = $this->find( 'all', array( 'conditions' => $cond, 'limit' => $limit, 'page' => $page ) );
            Cache::write('eventrsvp.getrsvp.'.$event_id.'_'.$type.'.page.'.$page, $rsvps, 'event');
        }
		return $rsvps;
	}
	
	/*
	 * Get rsvps count of an event based on $type	 
	 * @param int $event_id
	 * @param mixed $type from 0 to 3
	 * @return int $count
	 */
	public function getRsvpCount( $event_id, $type )
	{
        //$count = Cache::read('eventrsvp.rsvpcount.'.$event_id.'.'.$type, 'event');
        //if(empty($count))
        //{
		    $count = $this->find( 'count', array( 'conditions' => array( 'EventRsvp.event_id' => $event_id,
																	 'EventRsvp.rsvp' => $type
							 ) ) );
            //Cache::write('eventrsvp.rsvpcount.'.$event_id.'.'.$type, $count, 'event');
        //}
		return $count;
	}
	
	/*
	 * Get a list of user id that rsvp	 
	 * @param int $event_id
	 * @return array $users
	 */
	public function getRsvpList( $event_id )
	{
		$users = $this->find( 'list', array( 'conditions' => array( 'EventRsvp.event_id' => $event_id ),
											 'fields' => array( 'EventRsvp.user_id' )
							) );
							
		return $users;
	}
	
	/*
	 * Get user's rsvp of an event
	 * @param int $uid
	 * @param int $event_id
	 * @return array $rsvp
	 */
	public function getMyRsvp( $uid, $event_id )
	{

            $rsvp = $this->find( 'first', array( 'conditions' => array( 'EventRsvp.event_id' => $event_id,
																  	'EventRsvp.user_id' => $uid
							) ) );

		return $rsvp;
	}
	
	public function getMyEventsList( $uid )
	{
		$events = $this->find('list', array( 'conditions' => array( 'EventRsvp.user_id' => $uid,
													    'EventRsvp.rsvp' => RSVP_ATTENDING ),
								     'fields' => array('EventRsvp.event_id')
		));
		return $events;
	}
	
	public function getMyEventsCount( $uid )
	{
		$events = $this->find( 'count', array( 'conditions' => array( 'EventRsvp.user_id' => $uid, 
																 	   '(rsvp = ' . RSVP_ATTENDING . ' OR rsvp = ' . RSVP_AWAITING . ')', 
																 	   'Event.to >= CURDATE()' 
						) )	);
										
		return $events;
	}
    public function afterSave($creates,$options = array()){
        Cache::clearGroup('event');
    }
    public function afterDelete(){
        Cache::clearGroup('event');
    }
}
