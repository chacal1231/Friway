<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */

class ToolsController extends AppController {	

	public function beforeFilter()
	{
		parent::beforeFilter();
        $this->_checkPermission(array('super_admin' => 1));
	}

	public function admin_bulkmail() 
	{
		$this->set('title_for_layout', __('Bulk Mail'));	
	}
	
	public function admin_ajax_bulkmail_start() 
	{
		$this->autoRender = false;
		
		if ( !empty( $this->request->data['subject'] ) && !empty( $this->request->data['body'] ) && !empty( $this->request->data['cycle'] ) )
		{
			$this->Session->write('bulkmail_subject', $this->request->data['subject']);
			$this->Session->write('bulkmail_body', $this->request->data['body']);
			$this->Session->write('bulkmail_cycle', $this->request->data['cycle']);
		}
		else
			echo __('All fields are required');
	}
	
	public function admin_ajax_bulkmail_test()
	{
		$this->autoRender = false;
		
		if ( !empty( $this->request->data['subject'] ) && !empty( $this->request->data['body'] ) )
		{			
			$this->_sendEmail( Configure::read('core.site_email'), $this->request->data['subject'], null, null, null, null, $this->request->data['body'] );
		}
		else
			echo __('All fields are required');
	}
	
	public function admin_ajax_bulkmail_send( $page = 1 )
	{
		$subject = $this->Session->read('bulkmail_subject');
		$body	 = $this->Session->read('bulkmail_body');
		$cycle	 = $this->Session->read('bulkmail_cycle');
		$cuser = $this->_getUser();
				
		if ( !empty( $subject ) && !empty( $body ) && !empty( $cycle ) )
		{
			$this->layout = '';
			$this->loadModel('User');
			
			$users = $this->User->find('all', array( 'conditions' => array( 'User.active' => 1, 
																			'User.notification_email' => 1 
																		  ), 
											   		 'limit' 	  => $cycle,
											   		 'page'  	  => $page										   
										)	);
										
			foreach ( $users as $user )
			{
				//$this->_sendEmail( $user['User']['email'], $subject, null, null, null, null, $body );
				$ssl_mode = Configure::read('core.ssl_mode');
        		$http = (!empty($ssl_mode)) ? 'https' :  'http';
        		$this->MooMail->send($user,'bulkmail',
    				array(
    					'recipient_title' => $user['User']['moo_title'],
    					'recipient_link' => $http.'://'.$_SERVER['SERVER_NAME'].$user['User']['moo_href'],
    					'sender_name' => $cuser['name'],
    					'sender_link' => $http.'://'.$_SERVER['SERVER_NAME'].$cuser['moo_href'],
    					'subject' => $subject,
    					'body' => $body
    				)
    			);
			}
			
			$this->set('users', $users);
			$this->set('page', $page + 1);
		}
	}

	public function admin_clean_tmp()
	{

		$path = WWW_ROOT . 'uploads' . DS . 'tmp';
		
		$files  = scandir( $path );
		$oneday = time() - 60 * 60 * 24; 
        $msg = "";
		foreach ( $files as $file )
		{
			if ( !is_dir( $file ) && $file != 'index.html' )
			{
				$created = filemtime( $path . DS . $file );
				if ( $oneday > $created )
				{
					$msg = __('Removing'). ' ' . $file . '...<br />';
					unlink( $path . DS . $file );
				}
			}
		}

		$msg .= __('Done!');
        $this->set('clean_tmp_msg', $msg);
	}
    
    public function admin_clear_cache()
    {
        Cache::clear(false,'_cake_core_');
        Cache::clear(false,'_cake_model_');
        Cache::clear(false,'_cache_group_');
        
        $this->Session->setFlash( __('All caches have been cleared'),'default',
            array('class' => 'Metronic-alerts alert alert-success fade in' ));
        $this->redirect( '/admin' );
    }
    
    public function admin_remove_notifications()
    {
        $this->loadModel('Notification');
        
        $this->Notification->deleteAll( array( 'Notification.read' => 1, 'DATE_SUB(CURDATE(),INTERVAL 30 DAY) >= Notification.created' ) );
        
        $this->Session->setFlash( __('Read notifications older than 30 days have been deleted') ,'default',
            array('class' => 'Metronic-alerts alert alert-success fade in' ));
        $this->redirect( '/admin' );
    }
}