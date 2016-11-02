<?php
/**
* Auth class. Used for all login/logout stuff.
*/

namespace Admin\Lib;

use App\Model\Admin\User;
use TinyFw\Support\Session;

class UserAuth extends User
{
    var $userNameField, $passField, $miscFields, $lastLoggedInField;
    
    var $loggedIn;
    var $homePageUrl, $loginPageUrl, $membersAreaUrl;
    
    var $_session_current_name = "session_current_user";
    
    var $_permission = array(); 

    function __construct()
    {
        parent::__construct();

        //The fields below should be columns in the table above, which are used to
        //authenticate the user's credentials.
        $this->userNameField = 'username';
        $this->passField = 'password';

        //The following are general columns in the database which are
        //stored in the Session, for easily displaying some information
        //about the user:
        $this->miscFields='id,group_id,is_admin,display_name,first_name,last_name,email,username,password,data,acl_resources,active,last_login,create_at,last_update'; 

        /* If there is a no lastLoggedIn field in the table which is updated
               to the current DATETIME whenever the user logs in, set the next
              variable to blank to disable this feature. */

        $this->lastLoggedInField = 'last_login';

        $this->homePageUrl = site_url();
        $this->loginPageUrl = site_url('common/home/login');
        $this->membersAreaUrl = site_url();

        //All data passed on from a form to this class must be already escaped to prevent SQL injection.
        //However, all data stored in sessions is escaped by the class.
//        if ($this->isLoggedIn())
//            $this->refreshInfo();
    }
    
    function isSessLoggedIn()
    {
        if ($this->loggedIn == true)
        	return true;
        
        $user = $this->escapeStr($this->getUserSession('user'));
        $pass = $this->escapeStr($this->getUserSession('pass'));

        $this->loggedIn = false;
        if ($this->checkLogin($this->escapeStr($user),$this->escapeStr($pass)))        	
			$this->loggedIn = true;

        return $this->loggedIn;
    }

    function isCookieLoggedIn()
    {
        if (! array_key_exists('user',$_COOKIE) || ! array_key_exists('pass',$_COOKIE))
                return false;
        
        $user = $this->escapeStr($_COOKIE['user']);
        $pass = $this->escapeStr($_COOKIE['pass']);

        $loggedIn = false;
        if ($this->checkLogin($user,$pass))
            $loggedIn = true;

        if ($loggedIn && ! $this->isSessLoggedIn())
        {
        	$row = $this->getRow('username = ?',array($user));
        	$pass = $row[$this->passField];
        	$this->login($user,$pass);
        }
        
        return $loggedIn;
    }

    function isLoggedIn()
    {
        return ($this->isSessLoggedIn() || $this->isCookieLoggedIn());
    }

    function login($user, $pass ,$remember = false)
    {
    	// Ma hoa
    	$pass = encryption($pass);

        if ($this->isSessLoggedIn())
        	return false;

        $rowUser = $this->checkLogin($user ,$pass);
        if (empty($rowUser))
            return false;

        $this->setUserSession('user',$user);        
        $this->setUserSession('pass',$pass);

        foreach (explode(',',$this->miscFields) as $k => $v)
        {
            $this->setUserSession($v ,$rowUser[$v]);
        }

        if ($this->lastLoggedInField != '')
        {
        	$condition = "{$this->userNameField} = ? && {$this->passField} = ?";
        	$value = array($this->lastLoggedInField => now_to_mysql());        	
        	$this->updateWithCondition($condition, array($user, $pass), $value);
        }

//        echo "<pre>";
//        print_r($rowUser);
//        echo "</pre>";
//        exit();

        // Check is group admin
//        $arrAcl = $this->getAclFromGroup($rowUser['group_id']);
//        $this->setUserSession('acl_resources', $arrAcl);
//        $this->_permission = $arrAcl;

        if ($remember)
			$this->setCookies();
        
        return true;
    }

    //This function refreshes all the info in the Session, so if a user changed
    //his name, for example, his name in the Session is updated
    function refreshInfo()
    {
        if (! $this->isLoggedIn())
            return false;

        $id = trim($this->getUserSession('id'));
        $rowUser = $this->get($id);

        $this->setUserSession('pass', $rowUser[$this->passField]);
        $this->setUserSession('user', $rowUser[$this->userNameField]);

        foreach (explode(',',$this->miscFields) as $k=>$v)
        {
            $this->setUserSession($v ,$rowUser[$v]);
        }

        // Get is group admin
//        $arrAcl = $this->getAclFromGroup($rowUser['group_id']);
//        $this->setUserSession('acl_resources', $arrAcl);
//        $this->_permission = $arrAcl;

        //The following variables are used to determine wether or not to
        //set the cookies on the users computer. If $origUser matches the
        //cookie value 'user' it means the user had cookies stored on his
        //browser, so the cookies would be re-written with the new value of the
        //username.
        $origUser = $this->getUserSession('user');
        $origPass = $this->getUserSession('pass');

        if (array_key_exists('user',$_COOKIE) && array_key_exists('pass',$_COOKIE))
        {
            if ($_COOKIE['user'] == $origUser && $_COOKIE['pass'] == $origPass)
                $this->setCookies();
        }
        return true;
    }

    function logout($redir = false)
    {
        if (! $this->isLoggedIn())
			return false;

        // -- Clear user session --
        $this->clearUserSession();

        if ($this->isCookieLoggedIn())
        {
			setcookie('user','', time()-36000, '/');
			setcookie('pass','', time()-36000, '/');
        }
        
        if (! $redir)
			return;

		redirect($this->homePageUrl);
        die;
    }

    function restrict($minLevel)
    {
        if (! is_numeric($minLevel) && $minLevel!='ADMIN')
                return false;

        //URL of the page the user was trying to access, so upon logging in
        // he is redirected back to this url.
//         $url=$this->obj->uri->uri_string();
//         if (! $this->isLoggedIn())
//         {
//                 $this->obj->session->set_userdata('redirect_url',$url);
//                 header('location: '.$this->loginPageUrl);
//                 die;
//         }

//         if ($this->obj->session->userdata($this->lvlField) < $minLevel)
//         {
//                 header('location: '.$this->membersAreaUrl);
//                 die;
//         }
        return true;
    }

    function setCookies()
    {
        if (! $this->isSessLoggedIn())
        {
                return false;
        }
        $user = $this->getUserSession('user');
        $pass = $this->getUserSession('pass');

        @setcookie('user',$user, time()+60*60*24*30, '/');
        @setcookie('pass',$pass, time()+60*60*24*30, '/');
        return true;
    }

    function isAdmin()
    {
        if (! $this->isLoggedIn())
                return false;
        
        return $this->getUserSession("is_admin");
    }           


    function isVerified()
    {
//         return ($this->obj->session->userdata('verified')=='1');
    }
    
    function currentUser()
    {
        return Session::get($this->_session_current_name);
    }
    
    public function hasPermission($key, $value) 
    {
    	if ($this->isAdmin())
    		return true;

    	if (isset($this->_permission[$key])) 
    	{
    		return in_array($value, $this->_permission[$key]);
    	} else 
    	{
    		return false;
    	}
    }    

    public function getUserSession($key)
    {
        $arrSession = Session::get($this->_session_current_name);
        if (is_null($arrSession))
            return null;

        if (isset($arrSession[$key]))
            return $arrSession[$key];
        return null;
    }
    
    public function setUserSession($key ,$value)
    {
        $arrSession = Session::get($this->_session_current_name);
        $arrSession[$key] = $value;
        Session::set($this->_session_current_name ,$arrSession);
    }

    public function clearUserSession()
    {
        Session::unset_userdata($this->_session_current_name);
    }

    private function checkLogin($user, $pass)
    {
    	$row = $this->getRow('username = ? AND password = ?',array($user,$pass));
    	return empty($row) ? false : $row;
    }
    
	private function escapeStr($str)
	{
		return $str;
// 		return trim(mysqli_real_escape_string($str));
	}
}


?>