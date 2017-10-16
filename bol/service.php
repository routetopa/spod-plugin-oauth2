<?php

require_once OW_DIR_PLUGIN.'spodoauth2connect'.DS.'lib'.DS.'httpcurl.php';

class SPODOAUTH2CONNECT_BOL_Service
{
    private static $classInstance;

    /*
     *
     * Returns class instance
     *
     * @return SPODOAUTH2CONNECT_BOL_Service
     *
     */
    public static function getInstance()
    {
        if ( null === self::$classInstance )
            {
                self::$classInstance = new self();
            }
            return self::$classInstance;
        }

        private $httpcurl;
        public $props;

        protected function __construct ()
        {
           $this->httpcurl = new HTTPCurl();
           $this->props = $this->getProperties ();
           $this->httpcurl->setUserAgent ('(SPOD OAuth2 Connect/Oxwall)');
           $this->httpcurl->setSSLVerify (false);
           $this->httpcurl->setCache (false);
           $this->httpcurl->setHeaderBody (false);
       }

       public function findValue ($scan_array, $find_key)
       {
        $result = null;
        foreach ( $scan_array as $key => $val )
        {
            if (!strcasecmp($find_key,$key))
            {
              $result = $val;
              break;
          }
          else
          {
              if (is_array($val)) $result = $this->findValue ($val,$find_key);
          }
      }
      return $result;
  }



  public function getProperties ()
  {
    $owconfig = OW::getConfig();
    $props = new SPODOAUTH2CONNECT_BOL_Config ();
    $props->client_id = $owconfig->getValue ('spodoauth2connect','client_id');
    $props->client_secret = $owconfig->getValue ('spodoauth2connect','client_secret');
    $props->redirect_uri = OW::getRouter()->urlForRoute('spodoauth2connect_oauth');

    $props->grant_type = $owconfig->getValue ('spodoauth2connect','grant_type');
    $props->scope = $owconfig->getValue ('spodoauth2connect','scope');

    $props->base_url = $owconfig->getValue ('spodoauth2connect','base_url');

    $props->endpoint = $owconfig->getValue ('spodoauth2connect','endpoint');
    $props->tokenpoint = $owconfig->getValue ('spodoauth2connect','tokenpoint');
    $props->userinfopoint = $owconfig->getValue ('spodoauth2connect','userinfopoint');

    return $props;
}

public function saveProperties (SPODOAUTH2CONNECT_BOL_Config $props)
{
    $owconfig = OW::getConfig();
    $owconfig->saveConfig ('spodoauth2connect','client_id',$props->client_id);
    $owconfig->saveConfig ('spodoauth2connect','client_secret',$props->client_secret);

    $owconfig->saveConfig ('spodoauth2connect','grant_type',$props->grant_type);
    $owconfig->saveConfig ('spodoauth2connect','scope',$props->scope);

    $owconfig->saveConfig ('spodoauth2connect','base_url',$props->base_url);

    $owconfig->saveConfig ('spodoauth2connect','endpoint',$props->endpoint);
    $owconfig->saveConfig ('spodoauth2connect','tokenpoint',$props->tokenpoint);
    $owconfig->saveConfig ('spodoauth2connect','userinfopoint',$props->userinfopoint);

    return true;
}

public function generateOAuthUri ()
{
    $data = array (
        'scope'=>$this->getScope(),
        'redirect_uri'=>$this->props->redirect_uri,
        'response_type'=>'code',
        'client_id'=>$this->props->client_id,
        'state' => sha1(session_id() . 'spodoauth2connect'),
    );
    return $this->props->endpoint.'?'.http_build_query ($data);
}

private function getToken ($data)
{
    $this->httpcurl->setUrl ($this->props->tokenpoint);
    $this->httpcurl->setPostData ($data);
    $this->httpcurl->execute();
    return json_decode ($this->httpcurl->content,true);
}

public function getUserInfo ($data)
{
    $response_data = $this->getToken($data);
    $token = $response_data['access_token'];
    $this->httpcurl->setUrl ($this->props->userinfopoint);
    $this->httpcurl->setHeader( [ "Authorization: Bearer {$token}" ]);
    $this->httpcurl->setPostMethod (false);
    $this->httpcurl->execute();
    return json_decode ($this->httpcurl->content,true);
}

public function getScope()
{
    $owconfig = OW::getConfig();
    $scope = $owconfig->getValue ('spodoauth2connect','scope');
    return $scope;
}


}