<?php

namespace App\Controller\SiteIndex;

use TinyFw\Core\BaseController;
use TinyFw\Core\Config;
use TinyFw\Core\Request;
use TinyFw\Logger;

class HomeController extends BaseController
{
	var $_cfg_upload_file;
	var $_cfg_thumb_image;

	public function __construct()
	{
		parent::__construct();

        $this->oView->menuGroup = 'content';
		
		$this->_cfg_upload_file = array();
		$this->_cfg_upload_file['upload_path'] = __UPLOAD_DATA_PATH;
		$this->_cfg_upload_file['allowed_types'] = 'gif|jpg|png';
		$this->_cfg_upload_file['max_size']	= '500';
		$this->_cfg_upload_file['max_width']  = '2048';
		$this->_cfg_upload_file['max_height']  = '1536';

		$this->_cfg_thumb_image['create_thumb'] = TRUE;
		$this->_cfg_thumb_image['maintain_ratio'] = TRUE;
		$this->_cfg_thumb_image['width'] = 175;
		$this->_cfg_thumb_image['height'] = 0;
	}

	public function indexAction() 
	{
		$this->oSession->set('test_1', 'Thong tin duoc luu vao test');
		$this->oSession->set('test', 'Thong tin duoc luu vao test');
	    $this->oView->title = 'Welcome to Bui Van Tien Duc MVC RENDER';
	    $this->renderView('site-index/home/index');
	}
	
	public function tinymceAction()
	{
		$this->oView->title = 'Welcome to Bui Van Tien Duc MVC RENDER';
		$this->renderView('site-index/home/tinymce');
	}

	public function ckeditorAction()
	{
		// Cho phep truy cap KCFINDER
		// Tranh truong hop truy cap thong wa duong link cua iframe
		$_SESSION['KCFINDER'] = array();
		$_SESSION['KCFINDER']['disabled'] = false; // Activate the uploader,
		
		if ($this->oInput->isPost()) 
		{
			$this->_cfg_upload_file['file_name']  = 'img_'.time();
			
			$uploadLib = new UploadLib($this->_cfg_upload_file);
			
			$returnValue = $uploadLib->do_multi_upload("content_file");
				
			if (empty($returnValue))
			{
				echo $uploadLib->display_errors();
				exit();
			}
			else
			{
				foreach ($returnValue as $fileData)
				{
					$this->_cfg_thumb_image['source_image']	= $fileData['full_path'];
					
					$imageLib = new ImageLib($this->_cfg_thumb_image);
					if ( ! $imageLib->resize())
					{
						echo $imageLib->display_errors();
						exit();
					}
				}
// 				echo "Upload thanh cong";
			}

// 			echo "<pre>";
// 			print_r($returnValue);
// 			echo "</pre>";
// 			exit();
		}
		
// 		$this->oView->title = 'Welcome to Bui Van Tien Duc MVC RENDER';
// 		$this->renderView('site/home/ckeditor');

		$this->_children[] = new Request('site-index/home/child-first');
		$this->_children[] = new Request('site-index/home/child-second',array('Title duoc truyen vao site/home/child-second'));		
		
		$this->oView->title = 'Welcome to Bui Van Tien Duc MVC RENDER';
		$this->renderView('site-index/home/ckeditor');		
	}
	
	public function testResizeImageAction()
	{
		$this->_cfg_thumb_image['source_image']	= __UPLOAD_DATA_PATH."img_1387339644.jpg";//$fileData['full_path'];
		
		
		$imageLib = new ImageLib($this->_cfg_thumb_image);
		if ( ! $imageLib->resize())
		{
			echo $imageLib->display_errors();
			exit();
		}
		
		echo "<pre>";
		print_r($this->_cfg_thumb_image);
		echo "</pre>";
		exit();
		
		
		$this->renderView('site/home/test_resize_image');
	}
	
	public function partRenderAction($title)
	{
	    $this->oView->title = 'Day la phan noi dung duoc render vao';
	    $this->oView->render_title = $title;
	    return $this->oView->fetch('site-index/home/part_render');
	}
	
	public function renderAction()
	{
		$this->oSession->userdata['c'] = 2000;
		$this->oView->title_aaa = 'Day la trang dung chuc nang renderAction --- '.$this->oSession->userdata['test'];

        $this->oView->part_render = Request::staticRun(new Request('site-index/home/part-render',array('Title duoc truyen vao '.$this->oSession->userdata['c'])));

        $this->_children[] = new Request('site-index/home/child-first');
        $this->_children[] = new Request('site-index/home/child-second',array('Title duoc truyen vao site/home/child-second'));

        $this->renderView('site-index/home/render');
	}

	public function firstAction()
	{
		$this->oView->param_first = "Thong tin load tu firstAction";
	}
	
	public function secondAction()
	{
		$this->oView->param_second = "Thong tin load tu secondAction";	
	}
	
	public function denyAction($title_deny)
	{
		$this->oView->title_deny = $title_deny;
		$this->display('site/home/deny');
	}	
	
	public function childFirstAction()
	{
		return "Thong tin load tu childFirstAction";
	}
	
	public function childSecondAction($title)
	{
		return "Thong tin load tu childSecondAction - title : " . $title;
	}

    public function siteRenderAction()
    {
        $this->oView->file_title = 'siteRenderAction';
        $this->oView->title = 'Title truyen vao cac trang con';
        $this->renderView('site/home/site_render');
    }

    public function forwardDemoAction()
    {
        $this->oView->forward_title = "Duoc forward tu thang 'site-index/home/forward-demo'";
        return $this->forward('site-index/index/captcha');
    }

    public function syntaxErrorAction()
    {
        $this->oView->forward_title = "Demo loi xay ra";
        $this->renderView('site-index/home/syntax-error');
    }

    public function dynamicUrlAction()
    {
        $this->oView->title = 'Dynamically change URL using Push and Popstate';
        $this->renderView('site-index/home/dynamic_url');
    }

    public function saveCacheAction()
    {
        $this->oView->menuGroup = 'cache';

        $cache_name = "my_cache_data";
        $data = array(
            'cache_1' => "Thong tin cache tai day",
            'cache_2' => array('mang cahe 1', 'mang cahe 2')
        );

        $this->oCache->set($cache_name, $data, array('main'));
        $this->oCache->set('ten_gi_ke_no', $data, array('second'));

        $this->oView->dataCache = $data;
        $this->renderView('site-index/home/save-cache');
    }

    public function loadCacheAction()
    {
        $this->oView->menuGroup = 'cache';

        $cache_name = "my_cache_data";

//        $this->oCache->delete($cache_name);
//        $this->oCache->delete_by_tag(array('main', 'second'));

        $data = $this->oCache->get($cache_name);

        if(empty($data))
            $data = "Cache nay khong ton tai";

        $this->oView->dataCache = $data;
        $this->oView->cacheInfo = $this->oCache->cache_info();

        $this->renderView('site-index/home/load-cache');
    }

    public function loadLoggerAction()
    {
        $this->oView->menuGroup = 'cache';

        Logger::debugLog( "This is a DEBUG log message", 210, __FILE__, __LINE__ );
        Logger::auditLog( "This is an AUDIT log message", 220, __FILE__, __LINE__ );
        Logger::errorLog( "This is an ERROR log message", 220, __FILE__, __LINE__ );
        Logger::warningLog( "This is an WARNING log message", 220, __FILE__, __LINE__ );

        $config = Config::getInstance();
        $log_file = __SITE_PATH.rtrim($config->config_values['logging']['log_dir'], '/');
        $log_file .= '/log-'.date('Y-m-d').'.log';
        $content_log = file_get_contents($log_file);

        $this->oView->log_file = $log_file;
        $this->oView->content_log = $content_log;

        $this->renderView('site-index/home/load-logger');
    }



}
