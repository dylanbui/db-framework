<?php
/**
 * SessionManager Class
 *
 * Class for adding extra session security protection as well as new ways to
 * store sessions (such as databases).
 *
  	CREATE TABLE IF NOT EXISTS `sessions` 
	(
   		`session_id` VARCHAR(40) NOT NULL,
   		`last_activity` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   		`data` text NOT NULL,
   		PRIMARY KEY (`session_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 * 
 */

namespace App\Lib;

use App\Lib\Core\Config;
//use App\Lib\Core\Database\Connection;
use App\Lib\Core\DbConnection;

final class Session 
{
	public $match_ip			= FALSE;			//Require user IP to match?
	public $match_fingerprint	= TRUE;				//Require user agent fingerprint to match?
	public $match_token			= FALSE;			//Require this token to match?
	public $session_name		= 'site_session';	//What should the session be called?
	public $session_id			= NULL;				//Specify a custom ID to use instead of default cookie ID
	private $encryption_key		= '~!@#$%^&*()1234567890';
	
	public $session_database	= FALSE;			
	private $_conn = NULL;
	public $table_name	= 'sessions';
	public $primary_key	= 'session_id';
	
	public $cookie_path			= NULL;				//Path to set in session_cookie
	public $cookie_domain		= NULL;				//The domain to set in session_cookie
	public $cookie_secure		= NULL;				//Should cookies only be sent over secure connections?
	public $cookie_httponly		= NULL;				//Only accessible through the HTTP protocol?

	public $regenerate			= 300;				//Update the session every five minutes
	public $expiration			= 7200;				//The session expires after 2 hours of non-use
	public $gc_probability		= 100;				//Chance (in 100) that old sessions will be removed

	// Store $_SESSION
	public $userdata = array();
	
	var $flashdata_key			= 'flash';
	
	/**
	 * Configure some default session setting and then start the session.
	 * @param	array	$config
	 * @return	void
	 */
	public function __construct($params = array()) 
	{
		//Set the params
		$config = Config::getInstance();
		
		foreach (array('match_ip', 'match_fingerprint', 'match_token', 'session_name', 'cookie_path', 'cookie_domain', 'cookie_secure', 'cookie_httponly', 'regenerate', 'expiration', 'gc_probability', 'session_database', 'table_name', 'primary_key') as $key)
		{
			$this->$key = (isset($params[$key])) ? $params[$key] : $config->config_values['session'][$key];
		}		

		// Configure garbage collection
		ini_set('session.gc_probability', $this->gc_probability);
		ini_set('session.gc_divisor', 100);
		ini_set('session.gc_maxlifetime', $this->expiration);
		ini_set('session.use_cookies', 'On');
		ini_set('session.use_trans_sid', 'Off');

		// Set the session cookie parameters
		session_set_cookie_params(
			$this->expiration + time(),
			$this->cookie_path,
			$this->cookie_domain,
			$this->cookie_secure,
			$this->cookie_httponly
		);

		// Name the session, this will also be the name of the cookie
		session_name($this->session_name);

		//If we were told to use a specific ID instead of what PHP might find
		if($this->session_id) {
			session_id($this->session_id);
		}

		//Create a session (or get existing session)
		$this->create();

		$this->userdata =& $_SESSION;
		
		// Delete 'old' flashdata (from last request)
		$this->_flashdata_sweep();
		
		// Mark all new flashdata as old (data will be deleted before next request)
		$this->_flashdata_mark();
		
	}

    /**
     * Destructor
     *
     * @access public 
     * @return void
     */
    public function __destruct()
    {
        // Close session
        session_write_close();
    }	

	/**
	 * Start the current session, if already started - then destroy and create a new session!
	 * @return void
	 */
	function create() 
	{
		//If this was called to destroy a session (only works after session started)
		$this->clear();

		//If there is a class to handle CRUD of the sessions
		if($this->session_database) 
		{
			// Register non-native driver as the session handler
	        session_set_save_handler( 
	            array( &$this, "open" ), 
	            array( &$this, "close" ),
	            array( &$this, "read" ),
	            array( &$this, "write"),
	            array( &$this, "destroy"),
	            array( &$this, "gc" )
	        );
	        
			// Create connect to database
            $this->_conn = DbConnection::getInstance();
		}
		
		// Start the session!
//		session_start();
        try {
            session_start();
        } catch(\Exception $e) {
            session_regenerate_id();
            session_start();
        }

		//Check the session to make sure it is valid
		if( ! $this->check()) 
		{
			//Destroy invalid session and create a new one
			return $this->create();
		}
	}


	/**
	 * Check the current session to make sure the user is the same (or else create a new session)
	 * @return unknown_type
	 */
	function check() 
	{
		//On creation store the useragent fingerprint
		if(empty($_SESSION['fingerprint'])) 
		{
			$_SESSION['fingerprint'] = $this->generate_fingerprint();
		} 
		//If we should verify user agent fingerprints (and this one doesn't match!)
		elseif($this->match_fingerprint && $_SESSION['fingerprint'] != $this->generate_fingerprint()) 
		{
			return FALSE;
		}

		//If an IP address is present and we should check to see if it matches
		if(isset($_SESSION['ip_address']) && $this->match_ip) 
		{
			//If the IP does NOT match
			if($_SESSION['ip_address'] != ip_address()) 
			{
				return FALSE;
			}
		}

		//Set the users IP Address
		$_SESSION['ip_address'] = ip_address();

		//If a token was given for this session to match
		if($this->match_token) 
		{
			if(empty($_SESSION['token']) OR $_SESSION['token'] != $this->match_token) 
			{
				//Remove token check
				$this->match_token = FALSE;
				return FALSE;
			}
		}

		//Set the session start time so we can track when to regenerate the session
		if(empty($_SESSION['last_activity'])) 
		{
			$_SESSION['last_activity'] = time();
		} 
		//Check to see if the session needs to be regenerated
		elseif($_SESSION['last_activity'] + $this->expiration < time()) 
		{
			//Generate a new session id and a new cookie with the updated id
			session_regenerate_id();

			//Store new time that the session was generated
			$_SESSION['last_activity'] = time();

		}
		return TRUE;
	}


	/**
	 * Destroys the current session and user agent cookie
	 * @return  void
	 */
	function clear() 
	{
		//If there is no session to delete (not started)
		if (session_id() === '') return;

		// Get the session name
		$name = session_name();

		// Destroy the session
		session_destroy();

		// Delete the session cookie (if exists)
		if (isset($_COOKIE[$name])) 
		{
			//Get the current cookie config
			$params = session_get_cookie_params();

			// Delete the cookie from globals
			unset($_COOKIE[$name]);

			//Delete the cookie on the user_agent
			setcookie($name, '', time()-43200, $params['path'], $params['domain'], $params['secure']);
		}
	}


	/**
	 * Generates key as protection against SessionManager Hijacking & Fixation. This
	 * works better than IP based checking for most sites due to constant user
	 * IP changes (although this method is not as secure as IP checks).
	 * @return string
	 */
	function generate_fingerprint()  
	{
		//We don't use the ip-adress, because it is subject to change in most cases
// 		foreach(array('ACCEPT_CHARSET', 'ACCEPT_ENCODING', 'ACCEPT_LANGUAGE', 'USER_AGENT') as $name) {
// 			$key[] = empty($_SERVER['HTTP_'. $name]) ? NULL : $_SERVER['HTTP_'. $name];
// 		}
// 		//Create an MD5 has and return it
// 		return md5(implode("\0", $key));
		$secure_word = 'a39ccdef11305d5999dbccddcf4';
		return md5($secure_word.$_SERVER['HTTP_USER_AGENT']);		
	}


	/**
 	* Default session handler for storing sessions in the database.
 	**/ 
	
	/**
	 * Record the current sesion_id for later
	 * @return boolean
	 */
	public function open() 
	{
		//Store the current ID so if it is changed we will know!
		$this->session_id = session_id();
		return TRUE;
	}


	/**
	 * Superfluous close function
	 * @return boolean
	 */
	public function close() 
	{
		return TRUE;
	}


	/**
	 * Attempt to read a session from the database.
	 * @param	string	$id
	 */
	public function read($id = NULL) 
	{
		$time = date('Y-m-d H:i:s', time() - $this->expiration);
		
		//Select the session
		$row = $this->_conn->query("SELECT * FROM {$this->table_name} WHERE {$this->primary_key} = '{$id}' AND last_activity > '{$time}' ");
		return (!empty($row)) ? $row[0]['data'] : '';
	}

	/**
	 * Attempt to create or update a session in the database.
	 * The $data is already serialized by PHP.
	 *
	 * @param	string	$id
	 * @param	string 	$data
	 */
	public function write($id = NULL, $data = '') 
	{
		$time = date('Y-m-d H:i:s', time());
		$this->_conn->query("REPLACE `{$this->table_name}` (`{$this->primary_key}`,`last_activity`,`data`) VALUES('{$id}','{$time}','{$data}')");
	}

	/**
	 * Delete a session from the database
	 * @param	string	$id
	 * @return	boolean
	 */
	public function destroy($id) 
	{
		$this->_conn->query("DELETE FROM {$this->table_name} WHERE {$this->primary_key} = '{$id}'");
		return TRUE;
	}

	/**
	 * Garbage collector method to remove old sessions
	 */
	public function gc() 
	{
		//The max age of a session
		$time = date('Y-m-d H:i:s', time() - $this->expiration);
		
		//Remove all old sessions
		$this->_conn->query("DELETE FROM {$this->table_name} WHERE last_activity < '{$time}'");
		return TRUE;
	}	
	
	private function _set_cookie($cookie_data = NULL)
	{
		if (is_null($cookie_data))
		{
			$cookie_data = $this->userdata;
		}

		// Serialize the userdata for the cookie
		$cookie_data = serialize($cookie_data);

		// if encryption is not used, we provide an md5 hash to prevent userside tampering
		$cookie_data = $cookie_data.md5($cookie_data.$this->encryption_key);

		// Set the cookie
		setcookie(
					$this->session_name,
					$cookie_data,
					$this->expiration + time(),
					$this->cookie_path,
					$this->cookie_domain,
					0
				);
	}
	
	/*
	 * CodeIgniter supports "flashdata", or session data that will only be available for the next server request, 
	 * and are then automatically cleared. These can be very useful, and are typically used for informational 
	 * or status messages (for example: "record 2 deleted").
	 * */
	
	/**
	 * Add or change flashdata, only available
	 * until the next request
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	void
	 */
	function set_flashdata($newdata = array(), $newval = '')
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => $newval);
		}

		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				$flashdata_key = $this->flashdata_key.':new:'.$key;
				$this->userdata[$flashdata_key] = $val;
			}
		}
	}
		
	// ------------------------------------------------------------------------

	/**
	 * Keeps existing flashdata available to next request.
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	function keep_flashdata($key)
	{
		// 'old' flashdata gets removed.  Here we mark all
		// flashdata as 'new' to preserve it from _flashdata_sweep()
		// Note the function will return FALSE if the $key
		// provided cannot be found
		$old_flashdata_key = $this->flashdata_key.':old:'.$key;
		$value = $this->userdata[$old_flashdata_key];

		$new_flashdata_key = $this->flashdata_key.':new:'.$key;
		$this->userdata[$new_flashdata_key] = $value;
	}
		
	// ------------------------------------------------------------------------
	
	/**
	 * Fetch a specific flashdata item from the session array
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function flashdata($key)
	{
		$flashdata_key = $this->flashdata_key.':old:'.$key;
		if (isset($this->userdata[$flashdata_key]))
		{
			return $this->userdata[$flashdata_key];
		}
		return NULL;
	}

	// ------------------------------------------------------------------------
	
	/**
	 * Identifies flashdata as 'old' for removal
	 * when _flashdata_sweep() runs.
	 *
	 * @access	private
	 * @return	void
	 */
	function _flashdata_mark()
	{
		foreach ($this->userdata as $name => $value)
		{
			$parts = explode(':new:', $name);
			if (is_array($parts) && count($parts) === 2)
			{
				$new_name = $this->flashdata_key.':old:'.$parts[1];
				$this->userdata[$new_name] = $value;
// 				$this->set_userdata($new_name, $value);
				unset($this->userdata[$name]);
// 				$this->unset_userdata($name);
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Removes all flashdata marked as 'old'
	 *
	 * @access	private
	 * @return	void
	 */
	
	function _flashdata_sweep()
	{
// 		$userdata = $this->all_userdata();
		foreach ($this->userdata as $key => $value)
		{
			if (strpos($key, ':old:'))
			{
// 				$this->unset_userdata($key);
				unset($this->userdata[$key]);
			}
		}
	
	}
	
	
	
}

/**
 * CodeIgniter SessionManager Driver Class
 *
 * @package	CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author	Andrey Andreev
 * @link	https://codeigniter.com/user_guide/libraries/sessions.html
 */
abstract class SessionDriver implements SessionHandlerInterface {
    protected $_config;
    /**
     * Data fingerprint
     *
     * @var	bool
     */
    protected $_fingerprint;
    /**
     * Lock placeholder
     *
     * @var	mixed
     */
    protected $_lock = FALSE;
    /**
     * Read session ID
     *
     * Used to detect session_regenerate_id() calls because PHP only calls
     * write() after regenerating the ID.
     *
     * @var	string
     */
    protected $_session_id;
    /**
     * Success and failure return values
     *
     * Necessary due to a bug in all PHP 5 versions where return values
     * from userspace handlers are not handled properly. PHP 7 fixes the
     * bug, so we need to return different values depending on the version.
     *
     * @see	https://wiki.php.net/rfc/session.user.return-value
     * @var	mixed
     */
    protected $_success, $_failure;
    // ------------------------------------------------------------------------
    /**
     * Class constructor
     *
     * @param	array	$params	Configuration parameters
     * @return	void
     */
    public function __construct(&$params)
    {
        $this->_config =& $params;
        if (is_php('7'))
        {
            $this->_success = TRUE;
            $this->_failure = FALSE;
        }
        else
        {
            $this->_success = 0;
            $this->_failure = -1;
        }
    }
    // ------------------------------------------------------------------------
    /**
     * Cookie destroy
     *
     * Internal method to force removal of a cookie by the client
     * when session_destroy() is called.
     *
     * @return	bool
     */
    protected function _cookie_destroy()
    {
        return setcookie(
            $this->_config['cookie_name'],
            NULL,
            1,
            $this->_config['cookie_path'],
            $this->_config['cookie_domain'],
            $this->_config['cookie_secure'],
            TRUE
        );
    }
    // ------------------------------------------------------------------------
    /**
     * Get lock
     *
     * A dummy method allowing drivers with no locking functionality
     * (databases other than PostgreSQL and MySQL) to act as if they
     * do acquire a lock.
     *
     * @param	string	$session_id
     * @return	bool
     */
    protected function _get_lock($session_id)
    {
        $this->_lock = TRUE;
        return TRUE;
    }
    // ------------------------------------------------------------------------
    /**
     * Release lock
     *
     * @return	bool
     */
    protected function _release_lock()
    {
        if ($this->_lock)
        {
            $this->_lock = FALSE;
        }
        return TRUE;
    }
    // ------------------------------------------------------------------------
    /**
     * Fail
     *
     * Drivers other than the 'files' one don't (need to) use the
     * session.save_path INI setting, but that leads to confusing
     * error messages emitted by PHP when open() or write() fail,
     * as the message contains session.save_path ...
     * To work around the problem, the drivers will call this method
     * so that the INI is set just in time for the error message to
     * be properly generated.
     *
     * @return	mixed
     */
    protected function _fail()
    {
        ini_set('session.save_path', config_item('sess_save_path'));
        return $this->_failure;
    }
}

/**
 * CodeIgniter SessionManager Database Driver
 *
 * @package	CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author	Andrey Andreev
 * @link	https://codeigniter.com/user_guide/libraries/sessions.html
 */
class SessionDatabaseDriver extends SessionDriver implements SessionHandlerInterface {

    /**
     * DB object
     *
     * @var	object
     */
    protected $_db;

    /**
     * Row exists flag
     *
     * @var	bool
     */
    protected $_row_exists = FALSE;

    /**
     * Lock "driver" flag
     *
     * @var	string
     */
    protected $_platform;

    // ------------------------------------------------------------------------

    /**
     * Class constructor
     *
     * @param	array	$params	Configuration parameters
     * @return	void
     */
    public function __construct(&$params)
    {
        parent::__construct($params);

        // Create connect to database
        $this->_db = DbConnection::getInstance();
//        $this->_platform = 'mysql';

        // Note: BC work-around for the old 'sess_table_name' setting, should be removed in the future.
//        isset($this->_config['save_path']) OR $this->_config['save_path'] = config_item('sess_table_name');
    }

    // ------------------------------------------------------------------------

    /**
     * Open
     *
     * Initializes the database connection
     *
     * @param	string	$save_path	Table name
     * @param	string	$name		SessionManager cookie name, unused
     * @return	bool
     */
    public function open($save_path, $name)
    {
//        if (empty($this->_db->conn_id) && ! $this->_db->db_connect())
//        {
//            return $this->_fail();
//        }

        return $this->_success;
    }

    // ------------------------------------------------------------------------

    /**
     * Read
     *
     * Reads session data and acquires a lock
     *
     * @param	string	$session_id	SessionManager ID
     * @return	string	Serialized session data
     */
    public function read($session_id)
    {
        if ($this->_get_lock($session_id) !== FALSE)
        {
            // Prevent previous QB calls from messing with our queries
//            $this->_db->reset_query();

            // Needed by write() to detect session_regenerate_id() calls
            $this->_session_id = $session_id;

//            $this->_db
//                ->select('data')
//                ->from($this->_config['save_path'])
//                ->where('id', $session_id);

            $time = date('Y-m-d H:i:s', time() - $this->expiration);
            //Select the session
            $sql = "SELECT * FROM {$this->table_name} WHERE {$this->primary_key} = '{$session_id}' AND last_activity > '{$time}' ";
//            $row = $this->_conn->query("SELECT * FROM {$this->table_name} WHERE {$this->primary_key} = '{$session_id}' AND last_activity > '{$time}' ");


            if ($this->_config['match_ip'])
            {
                $sql .= " AND ip_address = {$_SERVER['REMOTE_ADDR']}";
//                $this->_db->where('ip_address', $_SERVER['REMOTE_ADDR']);
            }

            $result = $this->_db->selectOneRow($sql);
            if (empty($result))
//            if ( ! ($result = $this->_db->get()) OR ($result = $result->row()) === NULL)
            {
                // PHP7 will reuse the same SessionHandler object after
                // ID regeneration, so we need to explicitly set this to
                // FALSE instead of relying on the default ...
                $this->_row_exists = FALSE;
                $this->_fingerprint = md5('');
                return '';
            }

            // PostgreSQL's variant of a BLOB datatype is Bytea, which is a
            // PITA to work with, so we use base64-encoded data in a TEXT
            // field instead.
//            $result = ($this->_platform === 'postgre')
//                ? base64_decode(rtrim($result->data))
//                : $result->data;

            $result = $result->data;
            $this->_fingerprint = md5($result);
            $this->_row_exists = TRUE;
            return $result;
        }

        $this->_fingerprint = md5('');
        return '';
    }

    // ------------------------------------------------------------------------

    /**
     * Write
     *
     * Writes (create / update) session data
     *
     * @param	string	$session_id	SessionManager ID
     * @param	string	$session_data	Serialized session data
     * @return	bool
     */
    public function write($session_id, $session_data)
    {
        // Prevent previous QB calls from messing with our queries
//        $this->_db->reset_query();

        // Was the ID regenerated?
        if ($session_id !== $this->_session_id)
        {
            if ( ! $this->_release_lock() OR ! $this->_get_lock($session_id))
            {
                return $this->_fail();
            }

            $this->_row_exists = FALSE;
            $this->_session_id = $session_id;
        }
        elseif ($this->_lock === FALSE)
        {
            return $this->_fail();
        }

        if ($this->_row_exists === FALSE)
        {
//            $time = date('Y-m-d H:i:s', time());
//            $this->_db->query("REPLACE `{$this->table_name}` (`{$this->primary_key}`,`last_activity`,`data`) VALUES('{$session_id}','{$time}','{$session_data}')");
            $time = time();
            $sql = "INSERT INTO {$this->table_name} SET (session_id, ip_address, last_activity, user_data)";
            $sql .= " VALUES ('{$session_id}','{$_SERVER['REMOTE_ADDR']}','{$time}','{$session_data}')";

            if ($this->_db->insert($sql))
            {
                $this->_fingerprint = md5($session_data);
                $this->_row_exists = TRUE;
                return $this->_success;
            }

            return $this->_fail();
        }

        $sql = "UPDATE {$this->table_name} SET user_data='".$session_data."',last_activity='".time()."'";
        $sql .= " WHERE session_id = '".$session_id."'";
        if ($this->_config['match_ip'])
            $sql .= " AND ip_address = '".$_SERVER['REMOTE_ADDR']."'";

        if ($this->_db->update($sql))
        {
            $this->_fingerprint = md5($session_data);
            return $this->_success;
        }

        return $this->_fail();
    }

    // ------------------------------------------------------------------------

    /**
     * Close
     *
     * Releases locks
     *
     * @return	bool
     */
    public function close()
    {
        return ($this->_lock && ! $this->_release_lock())
            ? $this->_fail()
            : $this->_success;
    }

    // ------------------------------------------------------------------------

    /**
     * Destroy
     *
     * Destroys the current session.
     *
     * @param	string	$session_id	SessionManager ID
     * @return	bool
     */
    public function destroy($session_id)
    {
        if ($this->_lock)
        {
            // Prevent previous QB calls from messing with our queries
//            $this->_db->reset_query();
//            $this->_db->where('id', $session_id);
//            if ($this->_config['match_ip'])
//            {
//                $this->_db->where('ip_address', $_SERVER['REMOTE_ADDR']);
//            }

            $sql = "DELETE FROM {$this->table_name} WHERE session_id = '".$session_id."'";
            if ($this->_config['match_ip'])
                $sql .= " AND ip_address = '".$_SERVER['REMOTE_ADDR']."'";

            if ($this->_db->delete($sql))
            {
                return $this->_fail();
            }
        }

        if ($this->close() === $this->_success)
        {
            $this->_cookie_destroy();
            return $this->_success;
        }

        return $this->_fail();
    }

    // ------------------------------------------------------------------------

    /**
     * Garbage Collector
     *
     * Deletes expired sessions
     *
     * @param	int 	$maxlifetime	Maximum lifetime of sessions
     * @return	bool
     */
    public function gc($maxlifetime)
    {
        // Prevent previous QB calls from messing with our queries
//        $this->_db->reset_query();
        $sql = "DELETE FROM {$this->table_name} WHERE last_activity < ".(time() - $maxlifetime);

//        return ($this->_db->delete($this->_config['save_path'], 'timestamp < '.(time() - $maxlifetime)))
        return ($this->_db->delete($sql)) ? $this->_success : $this->_fail();
    }

    // ------------------------------------------------------------------------

    /**
     * Get lock
     *
     * Acquires a lock, depending on the underlying platform.
     *
     * @param	string	$session_id	SessionManager ID
     * @return	bool
     */
    protected function _get_lock($session_id)
    {
        $arg = $session_id.($this->_config['match_ip'] ? '_'.$_SERVER['REMOTE_ADDR'] : '');
        $sql = "SELECT GET_LOCK('".$arg."', 300) AS ci_session_lock";
        $result = $this->_db->selectOneRow($sql);
        if($result['ci_session_lock'])
        {
            $this->_lock = $arg;
            return TRUE;
        }
        return FALSE;

//        if ($this->_db->query("SELECT GET_LOCK('".$arg."', 300) AS ci_session_lock")->row()->ci_session_lock)
//        {
//            $this->_lock = $arg;
//            return TRUE;
//        }
//        return FALSE;
//        return parent::_get_lock($session_id);
    }

    // ------------------------------------------------------------------------

    /**
     * Release lock
     *
     * Releases a previously acquired lock
     *
     * @return	bool
     */
    protected function _release_lock()
    {
        if ( ! $this->_lock)
            return TRUE;

        $sql = "SELECT RELEASE_LOCK('".$this->_lock."') AS ci_session_lock";
        $result = $this->_db->selectOneRow($sql);
        if($result['ci_session_lock']) {
            $this->_lock = FALSE;
            return TRUE;
        }
        return FALSE;

//        if ($this->_platform === 'mysql')
//        {
//            if ($this->_db->query("SELECT RELEASE_LOCK('".$this->_lock."') AS ci_session_lock")->row()->ci_session_lock)
//            {
//                $this->_lock = FALSE;
//                return TRUE;
//            }
//
//            return FALSE;
//        }
//        elseif ($this->_platform === 'postgre')
//        {
//            if ($this->_db->simple_query('SELECT pg_advisory_unlock('.$this->_lock.')'))
//            {
//                $this->_lock = FALSE;
//                return TRUE;
//            }
//
//            return FALSE;
//        }
//
//        return parent::_release_lock();
    }
}