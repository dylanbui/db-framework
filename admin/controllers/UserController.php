<?php

namespace Admin\Controller;

use Admin\Lib\Support\UserAuth;
use App\Model\Admin\Group;
use App\Model\Admin\User;
use TinyFw\Helper\File;
use TinyFw\Image;
use TinyFw\Support\Input;
use TinyFw\Support\Session;
use TinyFw\Upload;


class UserController extends AdminBaseController
{
    var $_cfg_upload_file;
    var $_cfg_thumb_image;

    public function __construct()
    {
        parent::__construct();
        $this->detectModifyPermission('home/user');

        $this->_cfg_upload_file = array();
        $this->_cfg_upload_file['upload_path'] = __UPLOAD_DATA_PATH.'/user/';
        $this->_cfg_upload_file['allowed_types'] = 'gif|jpg|png';
        $this->_cfg_upload_file['max_size']	= 2000;
        $this->_cfg_upload_file['max_width']  = 2048;
        $this->_cfg_upload_file['max_height']  = 1536;
    }

    public function indexAction()
    {
        return $this->forward('user/list');
    }

    public function listAction()
    {
        $objUser = new User();
        $rsUsers = $objUser->getRowset();
        $this->renderView('user/list', array(
            'rsUsers' => $rsUsers,
            'objGroup' => new Group()
        ));
    }

    public function addAction()
    {
        if (!$this->_isModify)
            return $this->forward('error/error-deny');

        $viewData = array(
            'box_title' => "Add New User",
            'link_url' => site_url('user/add'),
            'cancel_url' => site_url('user/list')
        );

        $objGroup = new Group();
        $rsGroups = $objGroup->getRowset();
        $viewData['rsGroups'] = $rsGroups;

        if (Input::isPost())
        {
            // TODO : Check validate
            $group_id = Input::post("group_id",array());
            $group_id = implode(",", $group_id);

            $data = array(
                "username" => Input::post("username",""),
                "display_name" => Input::post("display_name",""),
                "email" => Input::post("email",""),
                "password" => encryption(Input::post("pw","")),
                "group_id" => $group_id,
                "active" => Input::post("active",0)
            );

            // -- Upload icon file --
            if (!empty(Input::file('iconUser')))
            {
                $this->_cfg_upload_file['file_name']  = 'img_'.strtolower(create_uniqid(5)).'_'.time();
                $uploadObj = new Upload($this->_cfg_upload_file);
                if (!$uploadObj->do_upload_and_resize('iconUser', 200, 200))
                {
                    echo $uploadObj->display_errors();
                    exit();
                }
                $data['icon'] = $uploadObj->data('file_name');
            }

            $oUser = new User();
            $oUser->insert($data);

            // Notify insert successfully !
            Session::setFlashData('notify_msg',array(
                'msg_title' => "Notify",
                'msg_code' => "success",
                'msg_content' => "Insert successfully !"));

            redirect('user/list');
        }

        $this->renderView('user/add', $viewData);
    }

    public function editAction($user_id)
    {
        if (!$this->_isModify)
            return $this->forward('common/error/error-deny');

        $viewData = array(
            'box_title' => "Update User",
            'link_url' => site_url('user/edit/'.$user_id),
            'cancel_url' => site_url('user/list')
        );

        $objGroup = new Group();
        $rsGroups = $objGroup->getRowset();
        $viewData['rsGroups'] = $rsGroups;

        $oUser = new User();

        $rowUser = $oUser->get($user_id);
        if (empty($rowUser)) {
            // TODO : User validate
        }

        if (Input::isPost())
        {
            // TODO : Check validate
            $group_id = Input::post("group_id",array());
            $group_id = implode(",", $group_id);

            $data = array(
                "username" => Input::post("username",""),
                "display_name" => Input::post("display_name",""),
                "email" => Input::post("email",""),
                "group_id" => $group_id,
                "active" => Input::post("active",0),
                "icon" => Input::post('iconUserOld')
            );

            $reset_password = Input::post('reset_password',NULL);
            if ($reset_password != NULL)
            {
                // TODO : Check validate password
                $data['password'] = encryption(Input::post("pw",""));
            }

            // -- Upload icon file --
            if (!empty(Input::file('iconUser')))
            {
                $this->_cfg_upload_file['file_name']  = 'img_'.strtolower(create_uniqid(5)).'_'.time();
                $uploadObj = new Upload($this->_cfg_upload_file);
                if (!$uploadObj->do_upload_and_resize('iconUser', 200, 200))
                {
                    echo $uploadObj->display_errors();
                    exit();
                }
                $data['icon'] = $uploadObj->data('file_name');
            }

            $oUser->update($user_id,$data);

            $currentUser = UserAuth::currentUser();
            if ($user_id == $currentUser['id'])
            {
                if ($reset_password != NULL || $data['username'] != $currentUser['name'])
                {
                    // Reseted pw or change username => logout
                    redirect("home/logout");
                }
            }

            // Notify update successfully !
            Session::setFlashData('notify_msg',array(
                'msg_title' => "Notify",
                'msg_code' => "success",
                'msg_content' => "Update successfully !"));

            redirect('user/list');
        }

        $viewData['rowUser'] = $rowUser;
        $viewData['arrGroupIds'] = explode(",",$rowUser['group_id']);

        $this->renderView('user/edit', $viewData);
    }

    public function deleteAction($user_id)
    {
//        if (!$this->_isModify)
//            return $this->forward('error/error-deny');

        // -- Cannot delete current user --
        $currentUser = UserAuth::currentUser();
        if($user_id == $currentUser['id'])
            redirect("user/list");

        $oUser = new User();
        $rowAffected = $oUser->delete($user_id);
        $msg_code = 'error';
        $msg_content = 'Error delete this row !';
        if(!empty($rowAffected))
        {
            $msg_code = 'success';
            $msg_content = 'Delete successfully !';
        }

        // Notify update successfully !
        Session::setFlashData('notify_msg',array(
            'msg_title' => "Notify",
            'msg_code' => $msg_code,
            'msg_content' => $msg_content));

        redirect("user/list");
    }

    public function activeAction($user_id)
    {
//        if (!$this->_isModify)
//            return $this->forward('error/error-deny');

        $oUser = new User();
        $oUser->setActiveField($user_id);
        redirect("user/list");
    }
}
