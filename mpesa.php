<?php
class Mpesa {
    private $consumerKey = 'rG4sKTCYZVCcL32SROVSNK1OWxliu1n8BiG0xgXUtUdSg1Ap';
    private $consumerSecret = 'AnnjSAEOewu9Pc0DEutNrM0NAkk5BJrP5qYTkX0ZwVbCXdW1WCFbfxbf3GuvwJC6';
    private $shortcode = '174379';
    private $passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
    private $baseUrl = 'https://api.postman.com/collections/4395533-1a8f1c81-0502-4f9d-8699-d45551834b7d?access_key=PMAT-01J8R72MBSHP5CJ4J9Q46TG6G9';

    private function getAccessToken() {
        $url = $this->baseUrl . "/oauth/v1/generate?grant_type=client_credentials";
        $credentials = base64_encode($this->consumerKey . ':' .$this->consumerSecret);

        $curl =curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: Basic " . $credentials,
            "Content-Type: application/json"
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        $result = json_decode($response, true);

        return $result['access_token'] ?? null;
    }

    public function stkPush($phone, $amount, $callbackUrl) {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) return false;

        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        $url = $this->baseUrl . "/mpesa/stkpush/v1/processrequest";
        $payload = [
            "BusinessShortCode" => $this->shortcode,
            "Password" => $password,    
            "Timestamp" => $timestamp,    
            "TransactionType" => "CustomerPayBillOnline",    
            "Amount" => $amount,
            "PartyA" => $phone,    
            "PartyB" => $this->shortcode,    
            "PhoneNumber" => $phone,    
            "CallBackURL" => $callbackUrl,    
            "AccountReference" => "Hotel Booking",    
            "TransactionDesc" => "Room Booking Payment"
        ];

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer ". $accessToken,
            "Content-Type: application/json"
        ]);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        return json_decode($response, true);
    }
}
?>