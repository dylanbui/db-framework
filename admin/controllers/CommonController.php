<?php

namespace Admin\Controller;

use Admin\Lib\Support\UserAuth;
use App\Lib\Core\FrontController;
use TinyFw\Core\Controller;
use TinyFw\Support\Input;

class CommonController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function indexAction() 
	{
		// ket thuc chuong trinh
		// thong bao link nay khong ton tai
		// return $this->forward("common/error/error-404");
		// exit();
		throw new \Exception("Page Not Found", 404);
	}	
	
	public function checkLoginAction() 
	{
		if (UserAuth::isLoggedIn() != true)
		{
			// ket thuc chuong trinh
			// thong bao link nay khong ton tai
			// return $this->forward("common/error/error-404");
//			throw new \Exception("Page Not Found", 404);
            return $this->forward('common/login');
		}

	}
	
	public function checkPermissionAction()
	{
		$ignore = array(
				'common/home',
				'common/error',
				'home/dashboard',
				'page/gallery'
		);

		$curr_request = FrontController::getInstance()->getCurrentRequest();
		$route = $curr_request->getModule().'/'.$curr_request->getController();

		// Dont need to log in, this is an open page
		if(in_array($route, $ignore))
		{
			return null;
		}

		// Den dong nay thi user da login roi, ton tai session
		$current_user = UserAuth::currentUser();
		
		// Neu la super admin => full access
		if ($current_user['is_admin'] == 1)
			return;
		
		// Special Module 
		if ($route == 'page/content' || $route == 'page/category') 
		{
			$params = $curr_request->getArgs();
			$route = $route.(isset($params[0]) ?'/'.$params[0]:'');
		}
		
		if (!in_array($route, $ignore) && !$this->oAuth->hasPermission("access",$route)) 
		{
			// echo "access deny<pre>";
			// print_r($this->oAuth->currentUser());
			// echo "</pre>";
			// exit();
			// return $this->forward('common/error/error-deny');
			throw new \Exception("Permission denied", 500);
		}

	}

    public function loginAction()
    {
        $authentication_error = null;
        // user : ducbui - pw : 123456 => ma hoa sha1
        if (Input::isPost())
        {
            $username = Input::post('username');
            $password = Input::post('password');

            if(UserAuth::login($username ,$password))
            {
                $user = UserAuth::currentUser();

//                echo "<pre>";
//                print_r($user);
//                echo "</pre>";
//                exit();

                redirect('dashboard/show');
            }
            else
            {
                $authentication_error = true;
            }
        }else
        {
            if (UserAuth::isLoggedIn())
            {
                redirect('dashboard/show');
            }
        }

        $viewData = array(
            'link_url' => site_url('common/login'),
            'authentication_error' => $authentication_error,
        );
        $this->renderView('common/login' ,$viewData ,"admin/layout_login");

    }

    public function logoutAction()
    {
        UserAuth::logout();
        redirect();

// 		redirect('dashboard/member/login');
    }

	
}
