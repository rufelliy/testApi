<?php

class Api
{
    public static string $apiUrl = 'https://dev-api.rafinita.com/post';
    protected string $pass = 'd0ec0beca8a3c30652746925d5380cf3';
    protected string $clientKey = '5b6492f0-f8f5-11ea-976a-0242c0a85007';
    public array $params = [];

    /**
     * Api constructor.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $this->getDefaultParams();
        if (!empty($params)) {
            $this->params = array_merge($this->params, $params);
        }
    }

    /**
     * @return mixed
     */
    public function sendPost() : mixed
    {
        $hash = $this->getHash();
        $data = array_merge($this->params, ['hash' => $hash]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }

    /**
     * @return array
     */
    public function getDefaultParams() : array
    {
        return [
            'action' => 'SALE',
            'order_id' => 'ORDER-' . rand(0, 100),
            'order_amount' => 1.99,
            'order_currency' => 'USD',
            'order_description' => 'Product',
            'card_number' => '4111111111111111',
            'card_exp_month' => '02',
            'card_exp_year' => '2025',
            'card_cvv2' => '000',
            'payer_first_name' => 'John',
            'payer_last_name' => 'Doe',
            'payer_address' => 'BigStreet',
            'payer_country' => 'US',
            'payer_state' => 'CA',
            'payer_city' => 'City',
            'payer_zip' => '123456',
            'payer_email' => 'test@gmail.com',
            'payer_phone' => '199999999',
            'payer_ip' => '194.31.236.146',
            'term_url_3ds' => 'https://testApi/checkResponse.php',
            'client_key' => $this->clientKey,
        ];
    }

    /**
     * @return string
     */
    protected function getHash() : string
    {
        return md5(strtoupper(strrev($this->params['payer_email'])
            . $this->pass . strrev(substr($this->params['card_number'],0,6)
            . substr($this->params['card_number'],-4))));
    }

    /**
     * @param $response
     */
    public function checkResponse($response) : void
    {
        if ($response['result'] == 'ERROR') {
            $errorMessage = '';
            foreach ($response['errors'] as $error) {
                $errorMessage .=  $error['error_message'];
            }
            echo $errorMessage;
        }else {
            switch ($response['result']) {
                case 'SUCCESS':
                    echo 'PAYMENT SUCCESS';
                    break;
                case 'DECLINED':
                    echo 'PAYMENT DECLINED';
                    break;
                case 'REDIRECT':
                    echo '3DS REDIRECT';
                    break;
            }
        }
    }

}