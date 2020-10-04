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
        $id_client   = $this->_saveClient();
        $id_product  = $this->_saveProduct();
//        var_dump($id_client);
//        var_dump($id_product);
        if ($id_client !== false && $id_product !== false) {
            if ($this->_saveFollowing($id_client, $id_product) !== false) {
                return true;
            }
        }
        return false;
    }

    private function _saveClient()
    {
        $result = $this->_link->query("INSERT INTO `Client` (`name`, `email`) VALUE (" .
                                    $this->_sqlStr($this->_name) . "," .
                                    $this->_sqlStr($this->_email) . ")");
        $id = $this->_link->insert_id;
        if ($result === false) {
            $id = $this->_link->query("SELECT `id_client` FROM `Client` WHERE " .
                                            "`email` = " . $this->_sqlStr($this->_email));
            $row = $id->fetch_row();
            $id = array_pop($row);
        }
        return $id;
    }

    private function _saveProduct()
    {
        $price  = $this->getPriceById($this->_idProduct);
        $result = $this->_link->query("INSERT INTO `Product` (`id_from_url`, `price`) VALUE (" .
                                    $this->_sqlStr($this->_idProduct) . "," .
                                    $this->_sqlStr($price) . ")");
        $id = $this->_link->insert_id;
        if ($result === false) {
            $id = $this->_link->query("SELECT `id_product` FROM `Product` WHERE " .
                                                "`id_from_url` = " . $this->_sqlStr($this->_idProduct));
            $row = $id->fetch_row();
            $id = array_pop($row);
        }

        return $id;
    }

    private function _saveFollowing($id_client, $id_product)
    {
        $result = $this->_link->query("INSERT INTO `Following` (`id_client`, `id_product`) VALUE (" .
                                    $this->_sqlStr($id_client) . "," .
                                    $this->_sqlStr($id_product) . ")");
        return ($result === false) ? false : $this->_link->insert_id;
    }


    /**
     * Check the product's price by its Id
     * @param $id string
     *
     * @return float|boolean
     */
    public function getPriceById($id) {

        if (is_numeric($id) === true) {
            $filename = 'https://m.avito.ru/api/1/rmp/show/' . $id .
                '?key=af0deccbgcgidddjgnvljitntccdduijhdinfgjgfjir';
            $response = file_get_contents($filename);

            if ($response !== false) {
                $response = (array)json_decode($response);
                $result = (array)$response["result"];
                $dfpTargetings = (array)$result["dfpTargetings"];
                return $dfpTargetings["par_price"];
            }
        }
        return false;
    }


    public function getPriceByUrl($url) {
        $urlArray = explode("_", $url);
        $id_product = array_pop($urlArray);

        return $this->getPriceById($id_product);
    }


    /**
     * @param $str string
     * @return string
     */
    private function _sqlStr($str) {
        return "\"" . $this->_link->real_escape_string($str) . "\"";
    }

    public function getDBErrors() {
        return $this->_link->error_list;
    }

    public function __destruct() {
        return $this->_link->close();
    }
}