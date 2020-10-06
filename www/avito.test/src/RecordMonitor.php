<?php

require_once __DIR__ . "/../config/db_config.php";

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
    private string $_email;

    /**
     * Product identifier to monitor
     * @var string
     */
    private $_idProductFromUrl;
    /**
     * DB connection
     * @var mysqli
     */
    private mysqli $_link;

    /**
     * @param null|string $name Client's name
     * @param null|string $email Client's email
     * @param null|string $url Url of advertisement to monitor
     */
    public function __construct($name = null, $email = null, $url = null) {
        if (is_null($name)  !== true &&
            is_null($email) !== true &&
            is_null($url)   !== true) {
                $urlArray = explode("_", $url);
                $this->_idProductFromUrl = array_pop($urlArray);
                $this->_name = substr($name, 0, 50);
                $this->_email = $email;
        }

    }

    /**
     * BD connection creation
     */
    public function createDBConnection() {
        $this->_link = mysqli_connect(DB_HOST,DB_USER, DB_PASSWORD, DB_NAME);
    }

    /**
     * Save Client, Product adn relationship in DB
     * @return bool
     */
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

    /**
     * Save client into database
     * @return mixed
     */
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

    /**
     * Save product into database
     * @return mixed
     */
    private function _saveProduct()
    {
        $price  = $this->getPriceById($this->_idProductFromUrl);
        $result = $this->_link->query("INSERT INTO `Product` (`id_from_url`, `price`) VALUE (" .
                                    $this->_sqlStr($this->_idProductFromUrl) . "," .
                                    $this->_sqlStr($price) . ")");
        $id = $this->_link->insert_id;
        if ($result === false) {
            $id = $this->_link->query("SELECT `id_product` FROM `Product` WHERE " .
                                                "`id_from_url` = " . $this->_sqlStr($this->_idProductFromUrl));
            $row = $id->fetch_row();
            $id = array_pop($row);
        }

        return $id;
    }

    /**
     * Save relationship between client and product into database
     * @param $id_client
     * @param $id_product
     * @return false|mixed
     */
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
    public static function getPriceById(string $id) {

        if (is_numeric($id) === true) {
            $path = 'https://m.avito.ru/api/1/rmp/show/' . $id .
                '?key=' . KEY_AVITO;
            $ch = curl_init();
            $timeout = 5;
            curl_setopt ($ch, CURLOPT_URL, $path);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $response = curl_exec($ch);
            curl_close($ch);
            //$response = file_get_contents($path);

            if ($response !== false) {
                $response = (array) json_decode($response);
                $result = (array)$response["result"];
                $dfpTargetings = (array)$result["dfpTargetings"];
                return $dfpTargetings["par_price"];
            }
        }
        return false;
    }


    /**
     * Get price of product by its url
     * @param $url string
     * @return bool|float
     */
    public function getPriceByUrl(string $url) {
        $urlArray = explode("_", $url);
        $id_product = array_pop($urlArray);

        return $this->getPriceById($id_product);
    }


    /**
     * Modify string to put into mysql query
     * @param $str string|integer
     * @return string
     */
    private function _sqlStr($str)
        {
            if (is_null($str) !== true)
                {
                    return "\"" . $this->_link->real_escape_string($str) . "\"";
                }
            return null;
        }

    /**
     * Get recent mysql errors
     * @return array
     */
    public function getDBErrors()
    {
        return $this->_link->error_list;
    }

    /**
     * Close connection before destruct
     */
    public function __destruct()
        {
            return $this->_link->close();
        }


    /**
     * @return mixed
     */
    public function getIdProductFromUrl()
    {
        return $this->_idProductFromUrl;
    }


    /**
     * Get all products from DB
     * @return array
     */
    public function getAllProducts()
    {
        $products = $this->_link->query("SELECT * FROM `Product`");
        $productsArray = array();
        while ($rowProduct = $products->fetch_assoc()) {
            $productsArray[] = $rowProduct;
        }

        return $productsArray;
    }


    /**
     * @param int|string $id_from_url
     * @return array
     */
    public function getSubscribedClient($id_from_url)
        {
            $clients = $this->_link->query("SELECT * 
                                        FROM `Client` JOIN Following F 
                                            on Client.id_client = F.id_client 
                                        WHERE F.id_product = " . $this->_sqlStr($id_from_url));
            $clientsArray = array();
            while ($rowClient = $clients->fetch_assoc()) {
                $clientsArray[] = $rowClient;
            }

            return $clientsArray;
        }

}