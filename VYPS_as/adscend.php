<?php

class Adscend_API{

    private $pub_id;
    private $key;

    /**
     * Adscend_API constructor.
     * @param $pub_id string pubID for Adscend
     * @param $key string api key
     */
    public function __construct($pub_id, $key)
    {
        $this->pub_id = $pub_id;
        $this->key = $key;
    }

    /**
     * @param $sub_id string id of user you want to get number of leads
     * @param $adwall_id id of adwall
     * @return double balance of user
     */
	
	/* Some documentation about use. 
	* do $adscend = new Adscend_API("PUBLIC_ID", "API_KEY");
	*  $adscend->get_balance("SUB_ID", "ADWALL_ID"); to get currency total
	*/
    public function get_balance($sub_id, $adwall_id)
    {
        $host = "http://adscendmedia.com/adwall/api/publisher/{$this->pub_id}/profile/{$adwall_id}/user/{$sub_id}/transactions.json";
        $username = $this->pub_id;
        $password = $this->key;
        $process = curl_init($host);
        curl_setopt($process, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        $return = json_decode(curl_exec($process));
        curl_close($process);
        return $return['currency_count'];
    }

}