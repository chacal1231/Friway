<?php
App::uses('Widget','Controller/Widgets');

class popularBlogWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$num_item_show = $this->params['num_item_show'];
    	$controller->loadModel('Blog.Blog');
    	$popular_blogs = Cache::read('blog.popular_blog.'.$num_item_show,'blog');
    	if (!$popular_blogs)
    	{
    		$popular_blogs = $controller->Blog->getPopularBlogs( $num_item_show, Configure::read('core.popular_interval') );
    		Cache::write('blog.popular_blog.'.$num_item_show,$popular_blogs,'blog');
    	}
    	
    	$this->setData('popular_blogs', $popular_blogs);
    }
}