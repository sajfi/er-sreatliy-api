<?php
/*
 * Vyžaduje rozšíření XML_RPC
 * http://pear.php.net/package/XML_RPC
 */
require_once 'XML/RPC.php';

/**
 * 
 * 
 * @author Ing. Tomáš Naibrt <tomas.naibrt@expertreality.cz> 
 */
class erSreality
{
    protected $api_key = null;
    protected $api_password = null;
    protected $api_id = null;
    
    const API_PATH = '/srealityApi/RPC2';
    const API_SERVER = 'http://www.expertreality.cz';

    protected $token = null;
    protected $client = null;

    /**
     *
     * @param int $api_id
     * @param string $api_key
     * @param string $api_password 
     */
    public function __construct($api_id, $api_key, $api_password)
    {
        $this->setApiId($api_id);
        $this->setApiKey($api_key);
        $this->setApiPassword($api_password);
        $this->login();
    }

    public function __destruct()
    {
        $this->logout();
    }

    /**
     *
     * @param array $params
     * @return int 
     */
    public function saveAdvert($params = array())
    {
        $result = self::callMethod('addAdvert', array(
            'session_id' => $this->getSessionId(), 
            'params' => $params
            ));
        
        return $result['advert_id'];
    }
    
    /**
     *
     * @param int $sreality_id
     * @param string $sreality_rkid 
     */
    public function delAdvert($sreality_id, $sreality_rkid = null)
    {
        $result = self::callMethod('delAdvert', array(
            'session_id' => $this->getSessionId(), 
            'advert_id' => $sreality_id, 
            'advert_rkid' => $sreality_rkid, 
            ));
    }
    
    /**
     *
     * @return array 
     */
    public function listAdvert()
    {
        $result = self::callMethod('listAdvert', array(
            'session_id' => $this->getSessionId(), 
            ));
        
        return $result;
    }

    /**
     *
     * @param int $sreality_id
     * @param string $sreality_rkid
     * @param array $data
     * @return int 
     */
    public function savePhoto($sreality_id, $sreality_rkid = null, $data = array())
    {
        $result = self::callMethod('addPhoto', array(
            'session_id' => $this->getSessionId(), 
            'advert_id' => $sreality_id, 
            'advert_rkid' => $sreality_rkid, 
            'data' => $data));
        
        return $result['photo_id'];
    }
    
    /**
     *
     * @param int $sreality_id
     * @param string $sreality_rkid
     * @return array 
     */
    public function listPhoto($sreality_id, $sreality_rkid = null)
    {
        $result = self::callMethod('listPhoto', array(
            'session_id' => $this->getSessionId(), 
            'advert_id' => $sreality_id, 
            'advert_rkid' => $sreality_rkid, 
            ));
        
        return $result;
    }
    
    /**
     *
     * @param int $sreality_id
     * @param string $sreality_rkid 
     */
    public function delPhoto($sreality_id, $sreality_rkid = null)
    {
        $result = self::callMethod('delPhoto', array(
            'session_id' => $this->getSessionId(), 
            'photo_id' => $sreality_id, 
            'photo_rkid' => $sreality_rkid, 
            ));
    }
    
    /**
     *
     * @param int $sreality_id
     * @param string $sreality_rkid
     * @param array $data
     * @return int 
     */
    public function saveSeller($sreality_id = null, $sreality_rkid = null, $data = array())
    {
        $result = self::callMethod('addSeller', array(
            'session_id' => $this->getSessionId(), 
            'seller_id' => $sreality_id, 
            'seller_rkid' => $sreality_rkid, 
            'data' => $data));
        return $result['seller_id'];
    }
    
    /**
     *
     * @return array
     */
    public function listSeller()
    {
        $result = self::callMethod('listSeller', array(
            'session_id' => $this->getSessionId(), 
            ));
        
        return $result;
    }
    
    /**
     *
     * @param int $sreality_id
     * @param string $sreality_rkid 
     */
    public function delSeller($sreality_id, $sreality_rkid = null)
    {
        $result = self::callMethod('delSeller', array(
            'session_id' => $this->getSessionId(), 
            'seller_id' => $sreality_id, 
            'seller_rkid' => $sreality_rkid, 
            ));
    }

    /**
     * 
     */
    protected function login()
    {
        self::callMethod('login', array('session_id' => $this->getSessionId()));
    }

    /**
     * 
     */
    public function logout()
    {
        self::callMethod('logout', array('session_id' => $this->getSessionId()));
    }

    protected function getSessionId()
    {
        if (is_null($this->token))
        {
            $result = $this->callMethod('getHash', array('client_id' => $this->getApiId()));
            $this->token = $result['session_id'];
        }

        $fp = substr($this->token, 0, 48);
        $vp = md5($this->token . md5($this->getApiPassword()) . $this->getApiKey());
        $this->token = $fp . $vp;
        
        return $this->token;
    }

    protected function getRpcClient()
    {
        if (is_null($this->client))
        {
            $this->client = new XML_RPC_Client(self::API_PATH, self::API_SERVER);
        }

        return $this->client;
    }
    
    protected function setApiKey($api_key)
    {
        $this->api_key = $api_key;
    }
    
    public function getApiKey()
    {
        return $this->api_key;
    }
    
    protected function setApiPassword($password)
    {
        $this->api_password = $password;
    }
    
    public function getApiPassword()
    {
        return $this->api_password;
    }
    
    protected function setApiId($id)
    {
        $this->api_id = $id;
    }
    
    public function getApiId()
    {
        return $this->api_id;
    }

    /**
     *
     * @param string $method
     * @param array $params
     * @param array $types
     * @return array
     * @throws Exception 
     */
    protected function callMethod($method, $params, $types = array())
    {
        $client = $this->getRpcClient();
//        $client->setDebug(true);
        
        
        $xml_params = array();
        foreach ($params as $key => $param)
        {
//            if($key == 'data')
//            {
//                $XML_RPC_val = new XML_RPC_Value;
//                $XML_RPC_val->addStruct($param);
//                $xml_params[] = $XML_RPC_val;
//            }
//            else
//            {
                $xml_params[] = XML_RPC_encode($param);
//            }
        }

        $msg = new XML_RPC_Message($method, $xml_params);
        $response = $client->send($msg);
        
        if(!$response)
        {
            var_dump($client->errstr);
            throw new Exception('Sreality ERROR: response === false');
        }
        
//        var_dump($response);
        
        $response = XML_RPC_decode($response->value());

        if (!isset($response['status']))
        {
            throw new Exception('Sreality ERROR: Volání XML-RPC skončilo chybou.');
        }

        if ($response['status'] > 299 || $response['status'] < 199)
        {
            throw new Exception('Sreality ERROR:: ' . $response['status'] . '. ' . $response['statusMessage']);
        }

        if (isset($response['output']))
        {
            return $response['output'];
        }
        else
        {
            throw new Exception('Sreality ERROR: Neexistující output.');
        }
    }
}