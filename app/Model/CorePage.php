<?php
class CorePage extends AppModel{
    //public $tablePrefix = 'ms_';
    public $belongsTo = array(
        'MyCoreTheme'=>array(
            'className' => 'Theme',
            'foreignKey' => 'theme_id',
            'counterCache' => true
        )
    );
    public $hasMany = array(
        'CoreContent' => array(
            'className' => 'CoreContent',
            'foreignKey' => 'core_page_id',
            //'dependent' => true,
        )
    );
    public $findMethods = array('pages' => true);
    public $actsAs = array('Containable');
    /*public function setPageId($id = null){
        if(!$id){
            throw new NotFoundException(_('Id can\'t be null'));
        }
        $this->pageId = $id;
    }
    public function getPageId(){
        return $this->pageId;
    }*/
    public $validate = array(
        'name' => 	array(
            'rule' => 'notBlank',
            'message' => 'Name is required'
        ),
        'displayname' => 	array(
            'rule' => 'notBlank',
            'message' => 'Title is required'
        ),
        'theme_id' => 	array(
            'rule' => 'notBlank',
            'message' => 'Theme is required'
        ),
    );
    public function getCorePageByTheme($id = null){
            $site_core_pages = $this->find('list', array(
                    'conditions' => array('CorePage.theme_id'=>$id),
                )
            );
        return $site_core_pages;
    }
    public function getCorePageList(){//get a list of core page to use in select box
        $site_core_pages = Cache::read('site_core_pages_list');

        if ( empty($site_core_pages) )
        {
            $site_core_pages = $this->find('list', array( 'fields' => array( 'CorePage.id', 'CorePage.displayname')));
            Cache::write('site_core_pages_list', $site_core_pages);
        }

        return $site_core_pages;
    }
    public function getCorePageAll(){
        $site_core_pages = Cache::read('site_core_pages_all');

        if ( empty($site_core_pages) )
        {
            $site_core_pages = $this->find('list', array( 'fields' => array( 'CorePage.id', 'CorePage.displayname')));
            Cache::write('site_core_pages_al', $site_core_pages);
        }

        return $site_core_pages;
    }

    public function getCorePage($name = null, $pageId = null){
        return $this->find('first',array(
                    'conditions' => array(
                    'OR' => array(
                      'name' => $name,
                      'core_page_id' => $pageId,
                    )
                ),
            ));
    }

    /*public function getSinglePages($id = null){
        if(!$id){
            throw new NotFoundException(_('Invalid page'));
        }
        $aPage = $this->find('pages',array(
            'conditions' => array('CorePage.id'=>$id)
        ));
        if(!$aPage){
            throw new NotFoundException(_('Invalid page'));
        }
        return $aPage;
    }*/

    protected  function _findPages($state, $query, $results = array()){
        if($state === 'before'){
            /*if(!$query['conditions']['CorePage.id']){
                throw new NotFoundException(_('Invalid page'));
            }*/
            $this->contain();//this or method unbindModel below
            /*$this->unbindModel(array(
                'belongsTo' => array('MyCoreTheme'),
                'hasMany' => array('CoreContent')
            ));*/
            //$query['limit'] = 1;
            return $query;
        }
        return $results;
    }
    /*public function getSinglePages(){
        if(!$this->pageId){
            throw new NotFoundException(_('Invalid page'));
        }
        $aPage = $this->findById($this->pageId);
        if(!$aPage){
            throw new NotFoundException(_('Invalid page'));
        }
        return $aPage;
    }*/

    public function getContent($id=null){//to get only contents of a pages, not thing else
        //if(!$id){
        //    throw new NotFoundException(_('Invalid Id'));
        //}
        $contents =  $this->CoreContent->getCoreContentByPage($id);
		//$contents['Children']
        return $contents;
    }

    public function savePage($data,$id = null){
        Cache::delete('site_core_pages_list');
        Cache::delete('site_core_pages_all');
        if($id !== null){//it's an update
            $this->id = $id;
        }
        else{
            $this->create();
        }
        $this->save($data);
    }

    /*public function delete($id = null){
        $this->delete($id);
    }*/
    public function beforeDelete($cascade = true){
        if(!$this->id){
            throw new NotFoundException(_('Invalid page'));
            return false;
        }
        Cache::delete('site_core_pages_list');
        Cache::delete('site_core_pages_all');
        return true;
    }

    public function afterSave($created, $options = array()){
        Cache::delete('site_core_pages_list');
        Cache::delete('site_core_pages_all');
        if($created === true){//new record was added, otherwise it's an update

        }
    }

    public function saveContent($data, $id = null){
        if($id !== null){
            $this->CoreContent->id = $id;
            $this->CoreContent->save($data);
        }
        else{
            $this->CoreContent->savePageContent($data);
        }
        /*$this->CoreContent->save($data, array(
            'conditions' => array('CoreContent.core_page_id'=>$pageId)
        ));*/
    }

    public function clearContent($id = null){
        Cache::delete('site_core_contents_list');
        Cache::delete('site_core_contents_all');
        $this->CoreContent->deleteAll(array('CoreContent.core_page_id'=>$id), false, false);
        $this->CoreContent->updateCounterCache(array('core_page_id'=>$id));
    }
    public function updatePageColumn($data = null, $pageId = null, $region = null){
        $this->unbindModel(array('belongsTo' => array('MyCoreTheme')));
        $this->updateAll(array('column_style' => $data),array('CorePage.id' => $pageId));
    }
    /*public function addCorePage($data){
        Cache::delete('site_core_pages_list');
        Cache::delete('site_core_pages_all');
        $this->save($data);
    }
    public function editCorePage($id, $data){
        Cache::delete('site_core_pages_list');
        Cache::delete('site_core_pages_all');
        $this->id = $id;
        //$this->save
    }*/
}