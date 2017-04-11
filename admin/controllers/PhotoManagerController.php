<?php

namespace Admin\Controller;

use App\Lib\Paginator,
	App\Model\Admin\Member;
use TinyFw\Support\Input;
use TinyFw\Support\Response;
use TinyFw\Support\View;

class PhotoManagerController extends AdminBaseController
{
    var $galleryPath;
    var $galleryThumbPath;
    var $galleryUrl;
    var $galleryThumbUrl;


	public function __construct()
	{
		parent::__construct();
        $this->galleryPath = __DATA_PATH.'/files_upload/images';
        $this->galleryUrl = __DATA_URL.'/files_upload/images';
        $this->galleryThumbPath = __DATA_PATH.'/files_upload/.thumbs/images';
        $this->galleryThumbUrl = __DATA_URL.'/files_upload/.thumbs/images';
        $arr = array(
            'galleryPath' => $this->galleryPath,
            'galleryUrl' => $this->galleryUrl,
            'galleryThumbPath' => $this->galleryThumbPath,
            'galleryThumbUrl' => $this->galleryThumbUrl,
        );
        View::setVars($arr);
	}

	public function indexAction() 
	{
		$this->listAction();
	}	
	
	public function listAction($subPath = null)
	{
//        $albums = array_diff(scandir($this->galleryPath), array('..', '.', '.DS_Store', '.htaccess'));
//        $this->sort_array($albums, $this->galleryPath, TRUE);
//
//        echo "<pre>";
//        print_r($albums);
//        echo "</pre>";
//        exit();

//		$this->oView->title = 'Member';
//		$this->oView->box_title = 'List Member';
//
//		$items_per_page = 10;
//
//		$objMember = new Member();
//
//		$rsMembers = $objMember->getRowset(NULL, NULL, NULL, $offset, $items_per_page);
//		$this->oView->rsMembers = $rsMembers;
//
//		$pages = new Paginator();
//		$pages->current_url = site_url("member/list/%d");
//		$pages->offset = $offset;
//		$pages->items_per_page = $items_per_page;
//		$pages->items_total = $objMember->getTotalRow();
//		$pages->mid_range = 7;
//		$pages->paginate();
//
//		$this->oView->pages = $pages;
		
		$this->renderView('photo-manager/list');
	}

    public function getFolderAction()
    {
        $currentDir = Input::post('dir', '.');

        $currentPath = $this->galleryPath.'/'.$currentDir;
        $currentUrl = $this->galleryUrl.'/'.$currentDir;
        $currentThumbUrl = $this->galleryThumbUrl.'/'.$currentDir;

        $arrVars = array(
            'currentDir' => $currentDir,
            'currentUrl' => $currentUrl,
            'currentThumbUrl' => $currentThumbUrl,
        );

        $foldersArr = array_diff(scandir($currentPath), array('..', '.', '.DS_Store', '.htaccess'));
        $this->sort_array($foldersArr, $currentPath, TRUE);

        $html = '';
        if ($currentDir != '.') {
            $arrVars['getParent'] = TRUE;
            $html .= View::fetch('photo-manager/_template_folder',$arrVars);
        }
        $arrVars['getParent'] = FALSE;
        foreach ($foldersArr as $item) {
            $arrVars['itemName'] = $item;
            if (is_dir($currentPath.'/'.$item)) { // Folder
                $html .= View::fetch('photo-manager/_template_folder',$arrVars);
            } else { // File
                $html .= View::fetch('photo-manager/_template_file',$arrVars);
            }
        }

        $returnVal = array(
            'currentPath' => $currentDir,
            'currentUrl' => $currentUrl,
            'html' => $html
        );

        return Response::setOutputJson($returnVal);
    }



    // return array sorted by date or name
    private function sort_array(&$array ,$dir ,$sort_by_date)
    { // array argument must be passed as reference
        if($sort_by_date)
        {
            foreach ($array as $key=>$item)
            {
                $stat_items = stat($dir .'/'. $item);
                $item_time[$key] = $stat_items['ctime'];
            }
            return array_multisort($item_time, SORT_DESC, $array);
        }
        else
        {
            return usort($array, 'strnatcasecmp');
        }

    }
	

}
