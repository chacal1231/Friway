<?php 
class PagePluginsController extends PageAppController{
    public function beforeFilter(){
        parent::beforeFilter();
        $this->loadModel('Page.Page');
    }
    public function admin_index()
    {
        $pages = $this->paginate( 'Page',array('OR'=>array('Page.type !='=>'core','Page.type'=>'')) );

        $this->set('pages', $pages );
        $this->set('title_for_layout', __('Pages Manager'));
    }
    public function admin_create($id = null)
    {
        $this->_checkPermission( array( 'super_admin' => true ) );
        $conditions = '';
        if ( !empty( $id ) )
        {
            $page = $this->Page->findById($id);
            $this->_checkExistence($page);
            $conditions = array('conditions'=>array('Page.id !=' => $id));
            $params = unserialize($page['Page']['params']);

            $this->set('title_for_layout', $page['Page']['title']);
        }
        else {
            $page = $this->Page->initFields();
            $params = array('comments' => 1);

            $this->set('title_for_layout', __('Create New Page' ));
        }
        $all_pages = $this->Page->find('all', $conditions);
        foreach($all_pages as &$single_page){
            $tmp[$single_page['Page']['id']] = $single_page['Page']['title'] ;
        }
        $all_pages = $tmp;
        // get all roles
        $this->loadModel('Role');
        $roles = $this->Role->find('all');

        $this->set('page', $page);
        $this->set('params', $params);
        $this->set('roles', $roles);
        $this->set('all_pages', $all_pages);
    }
    public function admin_save( )
    {
        $this->loadModel('CoreContent');
        $this->_checkPermission( array( 'super_admin' => true ) );
        $this->autoRender = false;
        $old = 0;
        if ( !empty( $this->request->data['id'] ) ){
            $this->Page->id = $this->request->data['id'];
            $old = 1;//to check if page existed or not
        }
        $this->request->data['params'] = serialize( array('comments' => $this->request->data['comments']) );

        if ( empty( $this->request->data['alias'] ) )
            $this->request->data['alias'] = seoUrl( strtolower($this->request->data['title']) );

        $this->request->data['permission'] = (empty( $this->request->data['everyone'] )) ? implode(',', $_POST['permissions']) : '';
        $this->request->data['uri'] = 'pages.'.$this->request->data['alias'];
        $this->request->data['url'] = '/pages/'.$this->request->data['alias'];
        $this->request->data['type'] = 'page';
        $this->Page->set( $this->request->data );
        $this->_validateData( $this->Page );
        //if($this->Page->id !== null || isset($this->Page->id))
        //    $old = 1;//to check if page existed or not
        $this->Page->save();
        $newPageId = $this->Page->id;
        $containerId = null;
        $parentId = null;
        // To do
        if($old != 1){

            $this->CoreContent->create();
            $this->CoreContent->save(array ('page_id'=>$this->Page->id,'type'=>'container','name'=>'center','parent_id'=>null));
            $parentId = $this->CoreContent->id;
            $this->CoreContent->create();
            $this->CoreContent->save(array ('page_id'=>$this->Page->id,'type'=>'widget','order'=>1,'name'=>'invisiblecontent','params'=>'{"title":"Page Content","maincontent":"1"}','core_block_id'=>0,'parent_id'=>$parentId));
            $childId = $this->CoreContent->id;

            $langs = $this->Language->getLanguages();
            foreach($langs as $key => $lang)
            {
                $this->CoreContent->id = $parentId;
                $this->CoreContent->locale = $key;
                $this->CoreContent->saveField('core_block_title', '');

                $this->CoreContent->id = $childId;
                $this->CoreContent->locale = $key;
                $this->CoreContent->saveField('core_block_title', '');
            }

        }
        //inherit blocks
        if ( empty( $this->data['id'] ) ){
            if(!empty($this->request->data['inherit']))
            {
                $contents = $this->CoreContent->findAllByPageId($this->request->data['inherit']);
                if(!empty($contents)){
                    foreach($contents as $content)
                    {
                        $oldId = $content['CoreContent']['id'];
                        $this->loadModel('CoreBlock');

                        //check if this block has been restricted
                        $core_block = $this->CoreBlock->findById($content['CoreContent']['core_block_id']);
                        $is_restricted = false;
                        if(!empty($core_block) && !empty($core_block['CoreBlock']['restricted']))
                            $is_restricted = true;
                        if($content['CoreContent']['name'] != 'invisiblecontent' && $content['CoreContent']['name'] != 'center' && !$is_restricted)
                        {
                            $this->CoreContent->create();
                            unset($content['CoreContent']['id']);
                            unset($content['CoreContent']['lft']);
                            unset($content['CoreContent']['rght']);
                            $content['CoreContent']['parent_id'] = ($content['CoreContent']['type']== 'container') ? null :$containerId[$content['CoreContent']['parent_id']];
                            $content['CoreContent']['page_id'] = $newPageId;
                            $this->CoreContent->save($content['CoreContent']);
                            $lastInsertId = $this->CoreContent->id;
                            $langs = $this->Language->getLanguages();
                            foreach($langs as $key => $lang)
                            {
                                $this->CoreContent->locale = $key;
                                $this->CoreContent->saveField('core_block_title', '');
                            }
                        }
                        if($content['CoreContent']['type'] == 'container' && !empty($lastInsertId))
                        {
                            $containerId[$oldId] = $lastInsertId;
                        }
                        if($content['CoreContent']['name'] == 'center')
                        {
                            $containerId[$oldId] = $parentId;
                        }
                    }
                }
            }
        }

        $this->Session->setFlash(__('Page has been successfully saved'),'default',
            array('class' => 'Metronic-alerts alert alert-success fade in' ));

        Cache::clearGroup('cache_group', '_cache_group_');
        Cache::clearGroup('menu', 'menu');

        $response['result'] = 1;
        $response['page_id'] = $newPageId;

        echo json_encode($response);
    }
    public function admin_reorder()
    {
        $this->autoRender = false;

        $i = 1;
        foreach ($this->request->data['pages'] as $page_id)
	{

            $this->Page->id = $page_id;
            $this->Page->save( array( 'weight' => $i ));
            $i++;
        }

        Cache::clearGroup('cache_group', '_cache_group_');
    }

    public function admin_delete( $id )
    {
        $this->autoRender = false;

        $page = $this->Page->findById( $id );
        if($page['Page']['type']!='core'){
            $this->Page->delete( $id );

            $this->Session->setFlash(__('Page has been deleted'),'default',
                array('class' => 'Metronic-alerts alert alert-success fade in' ));

            Cache::clearGroup('cache_group', '_cache_group_');
        }else{
            $this->Session->setFlash(__('Can\'t delete Core page'),'default',
                array('class' => 'Metronic-alerts alert alert-danger fade in' ));
            
        }
        
        $this->redirect( array(
            'plugin' => 'page',
            'controller' => 'page_plugins',
            'action' => 'admin_index'
        ) );

    }
}