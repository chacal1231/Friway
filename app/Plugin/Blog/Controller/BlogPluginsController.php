<?php 
class BlogPluginsController extends BlogAppController{
    public function admin_index()
    {
        $this->loadModel('Blog.Blog');

        $cond = array();
        if ( !empty( $this->request->data['keyword'] ) )
            $cond['MATCH(Blog.title) AGAINST(? IN BOOLEAN MODE)'] = $this->request->data['keyword'];

        $blogs = $this->paginate( 'Blog', $cond );

        $this->set('blogs', $blogs);
        $this->set('title_for_layout', __('Blogs Manager'));

    }
    public function admin_delete()
    {
        $this->loadModel('Blog.Blog');
        $this->_checkPermission(array('super_admin' => 1));

        if ( !empty( $_POST['blogs'] ) )
        {
            $blogs = $this->Blog->findAllById($_POST['blogs']);
            
            foreach ( $blogs as $blog ){
                
                $this->Blog->deleteBlog( $blog );
                
                $cakeEvent = new CakeEvent('Plugin.Controller.Blog.afterDeleteBlog', $this, array('item' => $blog));
                $this->getEventManager()->dispatch($cakeEvent);
            }

            $this->Session->setFlash( __('Blogs have been deleted') , 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));
        }

        $this->redirect( array(
            'plugin' => 'blog',
            'controller' => 'blog_plugins',
            'action' => 'admin_index'
        ) );
    }
}