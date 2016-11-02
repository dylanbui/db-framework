<?php

namespace Admin\Controller;

use Admin\Lib\Support\UserAuth;
use Admin\Model\Group;
use Admin\Model\User;
use TinyFw\Support\Input;
use TinyFw\Support\Session;


class UserController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
//        $this->detectModifyPermission('home/user');
//        $this->oView->_isModify = $this->_isModify;
    }

    public function indexAction()
    {
        return $this->forward('user/list');
    }

    public function listAction()
    {
        $objUser = new User();
        $rsUsers = $objUser->getRowset();
        $this->renderView('user/list', array('rsUsers' => $rsUsers));
    }

    public function addAction()
    {
//        if (!$this->_isModify)
//            return $this->forward('error/error-deny');

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
//        if (!$this->_isModify)
//            return $this->forward('common/error/error-deny');

        $viewData = array(
            'box_title' => "Update User",
            'link_url' => site_url('user/edit/'.$user_id),
            'cancel_url' => site_url('user/list')
        );

        $objGroup = new Group();
        $rsGroups = $objGroup->getRowset();
        $viewData['rsGroups'] = $rsGroups;

        $oUser = new User();

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
                "active" => Input::post("active",0)
            );

            $reset_password = Input::post('reset_password',NULL);
            if ($reset_password != NULL)
            {
                // TODO : Check validate password
                $data['password'] = encryption(Input::post("pw",""));
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

        $rowUser = $oUser->get($user_id);

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
