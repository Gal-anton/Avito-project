<?php

require_once "db_config.php";

/**
 * Class RecordMonitor
 */
class RecordMonitor
{
    /**
     * Client's name
     * @var string
     */
    private $_name;

    /**
     * Client's email
     * @var string
     */
    private $_email;

    /**
     * Product identifier to monitor
     * @var
     */
    private $_idProduct;

    /**
     * @var mysqli
     */
    private $_link;

    /**
     * @param string $name  Client's name
     * @param string $email Client's email
     * @param string $url   Url of advertisement to monitor
     */
    public function __construct($name, $email, $url) {
        $urlArray         = explode("_", $url);
        $this->_idProduct = array_pop($urlArray);
        $this->_name      = substr($name,0,50);
        $this->_email     = $email;
    }

    public function save()
    {
        $this->_link = mysqli_connect(DB_HOST,DB_USER, DB_PASSWORD, DB_NAME);
        $this->_saveClient();
    }

    private function _saveClient()
    {
        $this->_link->query("INSERT INTO `Client` (`name`, `email`) VALUE (" .
                                    $this->_sqlStr($this->_name) . "," .
                                    $this->_sqlStr($this->_email) . ")");
    }

    /**
     * @param $str string
     * @return string
     */
    private function _sqlStr($str) {
        return "\"" . $this->_link->real_escape_string($str) . "\"";
    }
}