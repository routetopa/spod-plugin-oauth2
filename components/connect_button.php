<?php

class SPODOAUTH2CONNECT_CMP_ConnectButton extends OW_Component
{

  public function render()
    {
     $this->assign('url',SPODOAUTH2CONNECT_BOL_Service::getInstance()->generateOAuthUri());
     return parent::render();
    }
}