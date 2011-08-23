<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_sql.php 4.0
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

/**
 * Wowhead SQL Helper
 * @package Wowhead Tooltips
 */
class wowhead_sql
{
	/**
	 * DB Connection
	 * @var object $db
	 */
	public $db;
	
	/**
	 * Connected boolean
	 * @var bool $connected
	 */
	public $connected = false;
	
	/**
	 * Query ID holder
	 * @var int $query_id
	 */
	public $query_id = 0;
	
	/**
	 * Constructor
	 * @access public
	 * @param string $host MySQL Hostname
	 * @param string $user MySQL Username
	 * @param string $pass MySQL Password
	 * @param string $db Database Name
	 * @return null
	 */
	public function __construct($host, $dbname, $user, $pass)
	{
		$this->connect($host, $user, $pass, $dbname);
	}
	
	/**
	 * Attempts to connect to the database 
	 * @access public
	 * @param string $host MySQL Hostname
	 * @param string $user MySQL Username
	 * @param string $pass MySQL Password
	 * @param string $db Database Name
	 * @return null
	 */
	public function connect($host, $user, $pass, $dbname)
	{
		if ($this->connected == true)
			return false;
			
		$this->db = mysql_connect($host, $user, $pass);
		
		if (!$this->db || is_null($this->db))
		{
			unset($this->db);
			trigger_error('Failed to connect to MySQL host ' . $host . '.', E_USER_ERROR);
			return false;
		}
		
		if ($this->db && !@mysql_select_db($dbname))
		{
			@mysql_close($this->db);
			unset($this->db);
			trigger_error('Failed to select database ' . $dbname . '.', E_USER_ERROR);
			return false;
		}
		$this->connected = true;
	}
	
	/**
	 * Closes the connection to the database
	 * @access public
	 */
	public function close()
	{
		if (!$this->connected);
			return false;
		
		if (isset($this->db) && $this->db)
		{
			if (isset($this->query_id) && $this->query_id)
				@mysql_free_result($this->query_id);	
		}
		
		@mysql_close($this->db);
		$this->connected = false;
		unset($this->db, $this->query_id);	
	}
	
	/**
	 * Grabs an error message from MySQL
	 * @access public
	 */
	public function error()
	{
        $result['message'] = @mysql_error();
        $result['code'] = @mysql_errno();
        return $result;	
	}
	
	/**
	 * Queries MySQL
	 * @access public
	 * @param string $query
	 * @return int
	 */
	public function query($query)
    {
        if ($this->connected == false)
            return (false);

        // Remove pre-existing query resources
        unset($this->query_id);

        if ($query != '')
            $this->query_id = @mysql_query($query, $this->db);
        if (!empty($this->query_id))
            return ($this->query_id);
        else
            return (false);
    }

	/**
	 * Fetch a result from the MySQL query
	 * @access public
	 * @param string $query_id [optional]
	 * @return array
	 */
    public function fetch_record($query_id = 0)
    {
        if (!$query_id)
            $query_id = $this->query_id;
        if ($query_id)
        {
            $this->record = @mysql_fetch_array($query_id);
            return $this->record;
        }
        else
        {
            return false;
        }
    }

	/**
	 * MySQL Num Rows
	 * @access public
	 * @param int $query_id [optional]
	 * @return int
	 */
    public function num_rows($query_id = 0)
    {
    	if (!$query_id)
    		$query_id = $this->query_id;

    	if ($query_id)
    	{
    		return @mysql_num_rows($query_id);
    	}
    	else
    	{
    		return false;
    	}
    }

	/**
	 * MySQL Free Result
	 * @access public
	 * @param int $query_id [optional]
	 * @return null
	 */
    public function free_result($query_id = 0)
    {
        if ($query_id == $this->query_id)
            unset($this->query_id);
        if (!$query_id && isset($this->query_id))
        {
            $query_id = $this->query_id;
            unset($this->query_id);
        }
        if (is_resource($query_id))
        {
            @mysql_free_result($query_id);
            return true;
        }
        else
        {
            return false;
        }
    }
}
?>