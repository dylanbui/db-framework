<?php

namespace App\Controller;

use TinyFw\Core\Controller;
use TinyFw\Helper\Captcha;
use App\Lib\Email;
use TinyFw\Support\Cookie;
use TinyFw\Support\Input;
use TinyFw\Support\Response;

class CookieController extends Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function addCookieAction()
	{
	    Cookie::set('my_cookie_1', 'Gia tri 1');
        Cookie::set('my_cookie_2', 'Gia tri 2');
        Cookie::set(Cookie::make('my_cookie_obj_1', 'Object 1'));
        Cookie::set(Cookie::make('my_cookie_obj_2', 'Thoi gian song 1h', time() + 3600));
        $this->renderView('site-index/cookie/index');
	}

    public function removeCookieAction()
    {
        Cookie::clear('my_cookie_obj_1');
        Cookie::clear('my_cookie_obj_2');
        $this->renderView('site-index/cookie/index');
    }

    public function addCookieByAjaxAction()
    {
        if (Input::isPost())
        {
            $cname = Input::post('cname');
            $cvalue = Input::post('cvalue');

            $returnVal = array(
                'cname' => $cname,
                'cvalue' => $cvalue,
            );

            Response::addHeader("Set-Cookie: cookie_11=gia tri 11,cookie_22=gia tri 22");
//            Response::addHeader("Set-Cookie: cookie_22=gia tri 22");

            return Response::setOutputJson($returnVal);

//            return Response::setOutputJson($returnVal)
//                ->withCookie(Cookie::make($cname, $cvalue))
//                ->withCookie(Cookie::make('cookie_ajax_withCookie', 'Set cookie by ajax withCookie'));
//            Response::setOutputJson()->withCookie(Cookie::make('cookie_ajax_1', 'Set cookie by ajax 1'));
        }

        $this->renderView('site-index/cookie/index-ajax');
    }

    public function removeCookieByAjaxAction()
    {
        Cookie::clear('my_cookie_obj_1');
        Cookie::clear('my_cookie_obj_2');
        $this->renderView('site-index/cookie/index');
    }


	
	
}
