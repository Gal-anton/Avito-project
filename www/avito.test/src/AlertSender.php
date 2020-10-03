<?php


class AlertSender
{

    /**
     * Check thr product's price by its Id
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
}