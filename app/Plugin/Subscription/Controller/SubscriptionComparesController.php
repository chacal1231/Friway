<?php
/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
class SubscriptionComparesController extends SubscriptionAppController
{
    public function __construct($request = null, $response = null) 
    {
        parent::__construct($request, $response);
        $this->url = '/admin/subscription/subscription_compares/';
        $this->set('url', $this->url);
        $this->set('url_create', $this->url_create);
        $this->set('url_delete', $this->url_delete);
    }
    
    public function beforeFilter()
	{
		parent::beforeFilter();
		$this->_checkPermission(array('super_admin' => 1));        
        $this->loadModel('SubscriptionCompare');
	}
    
    public function admin_index()
    {
        $this->loadModel('SubscriptionPackage');
        $columns = $this->SubscriptionPackage->find('all',array(
        	'conditions' => array(
        		'SubscriptionPackage.deleted <> ' => 1	
        	)
        ));
        $compares = $this->SubscriptionCompare->find('all');
        if($compares != null)
        {
            foreach($compares as $k => $v)
            {
                $compares[$k]['SubscriptionCompare']['compare_value'] = json_decode($v['SubscriptionCompare']['compare_value'], true);
            }
        }
        $this->set('columns', $columns);
        $this->set('compares', $compares);
    }
    
    public function admin_save()
    {
        if ($this->request->is('post')) 
        {
            //debug($this->request->data);die;
            $count = -1;
            $data = array();
            $this->SubscriptionCompare->deleteAll(array('1 = 1'));
            foreach($this->request->data['SubscriptionCompare']['name'] as $compare)
            {
                $item = array();
                $count++;
                if($count > 0)
                {
                    if($compare != '')
                    {
                        $item['compare_name'] = $compare;
                        $value = array();
                        foreach($this->request->data['compare_type'] as $k => $v)
                        {
                            $value[$k]['type'] = $v[$count];
                        }
                        foreach($this->request->data['yesno_value'] as $k => $v)
                        {
                            if($value[$k]['type'] == 'yesno')
                            {
                                $value[$k]['value'] = $v[$count];
                            }
                        }
                        foreach($this->request->data['text_value'] as $k => $v)
                        {
                            if($value[$k]['type'] == 'text')
                            {
                                $value[$k]['value'] = $v[$count];
                            }
                        }
                        $item['compare_value'] = json_encode($value);
                        $this->SubscriptionCompare->create();
                        $this->SubscriptionCompare->save($item);
                    }
                }
            }
            
            $this->Session->setFlash(__( 'Changes saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));
            $this->redirect($this->url);
        }
        else 
        {
            $this->redirect($this->url);
        }
    }
}
