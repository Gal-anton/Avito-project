<?php

require_once "db_config.php";

/**
 * Class Record
 */
class Record
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
     * @param string $name  Client's name
     * @param string $email Client's email
     * @param string $url   Url of advertisement to monitor
     */
    public function __construction($name, $email, $url) {
        $urlArray         = explode("_", $url);
        $this->_idProduct = array_pop($urlArray);
        $this->_name      = substr($name,0,50);
        $this->_email     = $email;
    }

    public function save()
    {
        $link = mysqli_connect(DB_HOST,DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
    return $link;
    }
}