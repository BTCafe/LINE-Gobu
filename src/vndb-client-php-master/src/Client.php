<?php

namespace VndbClient;

class Client
{
    private $fp;
    
    public function __construct()
    {
    }
    
    public function isConnected()
    {
        if ($this->fp) {
            return true;
        }
        return false;
    }
    
    public function connect()
    {
        $this->fp = fsockopen("api.vndb.org", 19534, $errno, $errstr, 10);
        
        if (!$this->fp) {
            echo "ERROR: $errstr ($errno)<br />\n";
        }
    }
    
    public function login($username, $password)
    {
        $data = array(
            'protocol' => 1,
            'client' => 'vndb-client-php',
            'clientver' => 0.1,
            'username' => $username,
            'password' => $password
        );
        $response = $this->sendCommand('login', $data);
        if ($response->getType() == 'ok') {
            //echo "Login OK\n";
        } else {
            //echo "Login failed..\n";
        }
    }

    public function sendCommand($command, $data = null)
    {
        $packet = $command;
        if ($data) {
            $packet .= ' ' . json_encode($data);
        }
        //echo "SENDING: [$packet]";
        fwrite($this->fp, $packet . chr(0x04));
        
        $res = $this->getResponse();
        $response = new Response();
        
        if ($res=='ok') {
            $response->setType('ok');
        } else {
            $p = strpos($res, '{');
            if ($p>0) {
                $type = substr($res, 0, $p - 1);
                $response->setType($type);

                $json = substr($res, $p);
                $data = json_decode($json, true);
                $response->setData($data);
            }
        }
        return $response;
    }

    public function getResponse()
    {
        //echo "Waiting for response...\n";
        $buffer = '';
        while (!feof($this->fp)) {
            $c = fgets($this->fp, 2);
            if (ord($c)==0x04) {
                //echo "Received: [$buffer]\n\n";
                return $buffer;
            } else {
                $buffer .= $c;
            }
        }
        return null;
    }
    
    public function getVisualNovelDataById($id)
    {
        $res = $this->sendCommand('get vn basic,anime,details,relations,stats (id = ' . (int)$id . ')');
        return $res;
    }

    public function getReleaseDataById($id)
    {
        $res = $this->sendCommand('get release basic,details,vn,producers (id = ' . (int)$id . ')');
        return $res;
    }
    
    public function getProducerDataById($id)
    {
        $res = $this->sendCommand('get producer basic,details,relations (id = ' . (int)$id . ')');
        return $res;
    }
    
    public function getCharacterDataById($id)
    {
        $res = $this->sendCommand('get character basic,details,meas,traits (id = ' . (int)$id . ')');
        return $res;
    }
}
