<?php
Cache::config('page', array('engine' => Configure::read('App.mooCacheEngine'),'groups'=>array('page')));