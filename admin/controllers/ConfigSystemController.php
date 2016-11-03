<?php

namespace Admin\Controller;

use Admin\Lib\MysqlDump,
	App\Model\Admin\ConfigureSystem;
use TinyFw\Support\Config;
use TinyFw\Support\Input;
use TinyFw\Support\Session;

class ConfigSystemController extends \Admin\Controller\AdminController
{

	public function __construct()
	{
		parent::__construct();
//		$this->detectModifyPermission('home/config-system');
//		$this->oView->_isModify = $this->_isModify;
	}

	public function indexAction() 
	{
		return $this->forward('config-system/list');
	}	
	
	public function listAction($tab_pos = 0)
	{
		$oConfigSys = new ConfigureSystem();
		$data = $oConfigSys->getAllGroups();

        $viewData = array(
            'tab_pos' => $tab_pos,
            'arrConfigData' => $data,
            'save_link' => site_url('home/config-system/save')
        );
		
		$this->renderView('config-system/list' ,$viewData);
	}
	
	public function saveAction($group_id)
    {
//        if (!$this->_isModify)
//            return $this->forward('common/error/error-deny');

        $tab_pos = 0;
        if ($this->oInput->isPost())
        {
            $tab_pos = $this->oInput->post('tab_pos', 0);
            $post = $this->oInput->_post;
            $oConfigSys = new ConfigureSystem();
            foreach ($post as $key => $value) {
                $num = $oConfigSys->updateConfigSystem($group_id, $key, array("value" => $value));
            }
            // Notify update successfully !
            Session::setFlashData('notify_msg',array(
                'msg_title' => "Notify",
                'msg_code' => "success",
                'msg_content' => "Update successfully !"));
        }

		redirect('config-system/list/'.$group_id);
	}
	
	public function backupDbAction()
	{
//		if (!$this->_isModify)
//			return $this->forward('error/error-deny');

		if (Input::isPost())
		{
		    $dbInfor = Config::get('database_master');
			$hostname = $dbInfor['db_hostname'];
			$database = $dbInfor['db_name'];
			$username = $dbInfor['db_username'];
			$password = $dbInfor['db_password'];
			
			$time = str_replace(array('-',':',' '), "_", now_to_mysql());
			
			$dumpSettings['compress'] = Input::post('backup_type');
			
			try {
				$dump = new MysqlDump($database, $username, $password, $hostname, "mysql", $dumpSettings, array());
				$dump->start(__SITE_PATH.'/sql/backup_'.$time.'_mysql.sql');
			} catch (Exception $e) {
				echo 'mysqldump-php error: ' . $e->getMessage();
				exit();
			}
			
			redirect('config-system/backup-db');
		}

		$viewData['box_title'] = "Backup Database";
        $viewData['box_action'] = "List Backup Files";
        $viewData['upload_dir'] = __SITE_PATH.'/sql';

		$this->renderView('config-system/backup-db' ,$viewData);
	}

	public function importDbAction($filename)
	{
        $dbInfor = Config::get('database_master');
		$database = $dbInfor['db_name'];
		$username = $dbInfor['db_username'];
		$password = $dbInfor['db_password'];
		
		$restore_file = __SITE_PATH.'/sql/'.$filename;
		
		if (preg_match("/\.gz$/i",$filename))
		{
			$source_file = __SITE_PATH.'/sql/'.$filename;
			$restore_file = str_replace('.gz', '', $source_file);;
			
			$fp = fopen($restore_file, "w");
			fwrite($fp, implode("", gzfile($source_file)));
			fclose($fp);
		}
		
		#Now restore from the .sql file
		$command = "mysql --user={$username} --password={$password} --database={$database} < ".$restore_file;
		exec($command);

		redirect('config-system/backup-db');
	}

	public function deleteDbAction($filename)
	{
		unlink(__SITE_PATH.'/sql/'.$filename);
		redirect('config-system/backup-db');
	}

	public function dumpDbAction()
	{
        $dbInfor = Config::get('database_master');
		$database = $dbInfor['db_name'];
		$username = $dbInfor['db_username'];
		$password = $dbInfor['db_password'];
		//Gzip
		$time = str_replace(array('-',':',' '), "_", now_to_mysql());
	
		try {
			$dump = new MysqlDump($database, $username, $password);
			$dump->start(__SITE_PATH.'/sql/backup_'.$time.'_mysql.sql');
		} catch (Exception $e) {
			echo 'mysqldump-php error: ' . $e->getMessage();
		}
	
		echo "<pre>";
		print_r("Xonggggg");
		echo "</pre>";
		exit();
	}


}
