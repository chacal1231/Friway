<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */

class HooksController extends AppController 
{
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->_checkPermission( array('super_admin' => true) ); 
    } 
        
    public function admin_index()
    {
        // get all installed hooks
        $hooks = $this->Hook->find( 'all' );
        $installed_hooks = array();
        
        foreach ( $hooks as $hook )
            $installed_hooks[] = $hook['Hook']['key'];
        
        // get all hooks in folder
        $all_hooks  = scandir( APP . 'Config' . DS . 'hooks' );      
        $not_installed_hooks = array();        
            
        foreach ( $all_hooks as &$hook )
        {
            $hook = substr($hook, 0, -4);
            if ( !in_array( $hook, $installed_hooks ) && !empty( $hook ) )
                $not_installed_hooks[] = $hook;
        }
        
        $this->set('hooks', $hooks);
        $this->set('not_installed_hooks', $not_installed_hooks);
    } 
    
    public function admin_ajax_view( $id = null )
    {
        $hook = $this->Hook->findById( $id );
        $this->_checkExistence($hook);        

        // get hook info
        $content = file_get_contents( APP . 'Config' . DS . 'hooks' . DS . $hook['Hook']['key'] . '.xml' );
        $info = new SimpleXMLElement($content);
                
        $settings = unserialize($hook['Hook']['settings']);
        
        // get all roles
        $this->loadModel('Role');
        $roles = $this->Role->find('all');
        
        $this->set('roles', $roles);        
        $this->set('info', $info);      
        $this->set('hook', $hook);     
        $this->set('settings', $settings);      
    }
    
    public function admin_do_download( $key )
    {
        $zip = new ZipArchive;
        
        if ( $zip->open( WWW_ROOT . 'uploads' . DS . 'tmp' . DS . $key . '.zip', ZipArchive::CREATE ) === TRUE ) 
        {
            // add xml file
            $hook_dir = 'Config' . DS . 'hooks';
            $zip->addEmptyDir( $hook_dir );
            $zip->addFile( APP . $hook_dir . DS . $key . '.xml', $hook_dir . DS . $key . '.xml' );
            
            // add component file if exists
            $component = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            $component_file = APP . 'Controller' . DS . 'Component' . DS . $component . 'Component.php';
            
            if ( file_exists( $component_file ) )
            {
                $zip->addEmptyDir( 'Controller' . DS . 'Component' );
                $zip->addFile( $component_file, 'Controller' . DS . 'Component' . DS . $component . 'Component.php' );
            }
            
            // add view file if exists
            $view_file = 'View' . DS . 'Elements' . DS . 'hooks' . DS . $key . '.ctp';
            if ( file_exists( APP . $view_file ) )
            {
                $zip->addEmptyDir( 'View' . DS . 'Elements' . DS . 'hooks' );
                $zip->addFile( APP . $view_file, $view_file );
            }
            
            if ( !$zip->close() )
                $this->_showError('Cannot create zip file');
            
            $this->redirect( '/uploads/tmp/' . $key . '.zip' );
        }
        else
            $this->_showError('Cannot create zip file');
    }
    
    public function admin_do_install( $key )
    {
        if ( file_exists( APP . 'Config' . DS . 'hooks' . DS . $key . '.xml' ) )
        {
            $content = file_get_contents( APP . 'Config' . DS . 'hooks' . DS . $key . '.xml' );
            $info = new SimpleXMLElement($content);            
            $settings = array();
            
            if ( !empty( $info->settings ) )
                foreach ( get_object_vars($info->settings) as $key => $data )
                {
                    $data = get_object_vars($data);       
                    $settings[$key] = $data['value'];
                }                

            $this->Hook->set( array( 'name' => $info->name, 
                                     'key' => $info->key, 
                                     'controller' => $info->controller, 
                                     'action' => $info->action, 
                                     'position' => $info->position, 
                                     'version' => $info->version, 
                                     'settings' => serialize($settings)
                            ) );
            
            if ( $this->Hook->save() )
            {
                $component = Inflector::camelize($info->key);
                $component_file = APP . 'Controller' . DS . 'Component' . DS . $component . 'Component.php';
                
                if ( file_exists( $component_file ) )
                {
                    $this->$component = $this->Components->load($component);
                    
                    if ( method_exists($this->$component, 'install') )
                        $this->$component->install($this);                    
                }
                    
                $this->Session->setFlash('Hook has been successfully installed');
            }
            else
                $this->Session->setFlash( 'An error has occured', 'default', array( 'class' => 'error-message') );
        }
        else
            $this->Session->setFlash( 'Cannot read hook info file', 'default', array( 'class' => 'error-message') );
        
        $this->redirect( $this->referer() );
    }
    
    public function admin_do_uninstall( $id )
    {
        $hook = $this->Hook->findById( $id );
        $this->_checkExistence( $hook );

        $component = str_replace(' ', '', ucwords(str_replace('_', ' ', $hook['Hook']['key'])));
        $component_file = APP . 'Controller' . DS . 'Component' . DS . $component . 'Component.php';
        
        if ( file_exists( $component_file ) )
        {
            $this->$component = $this->Components->load($component);

            if ( method_exists($this->$component, 'uninstall') )
                $this->$component->uninstall($this);                      
        }
        
        $this->Hook->delete( $id );   
        
        $this->Session->setFlash('Hook has been successfully uninstalled');
        $this->redirect( $this->referer() );
    }
    
    public function admin_do_upgrade( $id )
    {
        $hook = $this->Hook->findById( $id );
        $this->_checkExistence( $hook );
            
        if ( file_exists( APP . 'Config' . DS . 'hooks' . DS . $hook['Hook']['key'] . '.xml' ) )
        {
            $content = file_get_contents( APP . 'Config' . DS . 'hooks' . DS . $hook['Hook']['key'] . '.xml' );
            $info = new SimpleXMLElement($content);
            
            // upgrade if there's a new version
            if ( $info->version > $hook['Hook']['version'] )
            {
                $component = Inflector::camelize($info->key);
                $component_file = APP . 'Controller' . DS . 'Component' . DS . $component . 'Component.php';
                
                if ( file_exists( $component_file ) )
                {
                    $this->$component = $this->Components->load($component);
                    
                    if ( method_exists($this->$component, 'upgrade') )
                        $this->$component->upgrade($this);                
                }
                
                $this->Hook->id = $id;
                $this->Hook->save( array( 'version' => $info->version ) );
                
                $this->Session->setFlash('Hook has been successfully installed');            
            }
            else
                $this->Session->setFlash('Hook is already up to date');    
        }
        else
            $this->Session->setFlash( 'Cannot read hook info file', 'default', array( 'class' => 'error-message') );
        
        $this->redirect( $this->referer() );
    }

	public function admin_do_enable( $id )
    {
        $hook = $this->Hook->findById( $id );
        $this->_checkExistence( $hook );
		
		$this->Hook->id = $id;
        $this->Hook->save( array( 'enabled' => 1 ) );
        
        $this->Session->setFlash('Hook has been successfully enabled');
        $this->redirect( $this->referer() );
	}
    
    public function admin_do_disable( $id )
    {
        $hook = $this->Hook->findById( $id );
        $this->_checkExistence( $hook );
        
        $this->Hook->id = $id;
        $this->Hook->save( array( 'enabled' => 0 ) );
        
        $this->Session->setFlash('Hook has been successfully disabled');
        $this->redirect( $this->referer() );
    }
    
    public function admin_ajax_reorder()
    {
        $this->autoRender = false;
        
        $i = 1;
        foreach ($this->request->data['hooks'] as $hook_id)
        {
            $this->Hook->updateAll( array( 'weight' => $i ), array( 'id' => $hook_id ) );
            $i++;
        }
    }
    
    public function admin_ajax_save()
    {
        $this->autoRender = false;
        
        $hook = $this->Hook->findById( $this->request->data['id'] );
        $this->_checkExistence( $hook );
        
        $settings = array();
        
        if ( file_exists( APP . 'Config' . DS . 'hooks' . DS . $hook['Hook']['key'] . '.xml' ) )
        {
            $content = file_get_contents( APP . 'Config' . DS . 'hooks' . DS . $hook['Hook']['key'] . '.xml' );
            $info = new SimpleXMLElement($content);            
            
            if ( !empty( $info->settings ) )
                foreach ( get_object_vars($info->settings) as $key => $data )
                    $settings[$key] = $this->request->data[$key];    
        }
        
        $this->request->data['permission'] = (empty( $this->request->data['everyone'] )) ? implode(',', $_POST['permissions']) : '';
		$this->request->data['settings'] = serialize($settings);
        
        $this->Hook->id = $this->request->data['id'];
        $this->Hook->set( $this->request->data );
        
        $this->_validateData( $this->Hook );
        $this->Hook->save();
        
        $this->Session->setFlash('Hook has been successfully updated');
        
        $response['result'] = 1;
        echo json_encode($response);
    }
}
    