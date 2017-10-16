<?php

class SPODOAUTH2CONNECT_CTRL_Admin extends ADMIN_CTRL_Abstract
{
	public function __construct() {
        parent::__construct();
    }
	public function index(){	

		$form = new SPODOAUTH2CONNECT_AccessForm();
		$this->addForm($form);
	

		if ( OW::getRequest()->isPost() && $form->isValid($_POST) ){
	
			if ( $form->process() ){
				OW::getFeedback()->info(OW::getLanguage()->text('spodoauth2connect', 'register_app_success'));
				$this->redirect(OW::getRouter()->urlForRoute('spodoauth2connect_app_success_page'));
			}
		
            OW::getFeedback()->error(OW::getLanguage()->text('spodoauth2connect', 'register_app_failed'));
			$this->redirect();
		}  
		$this->assign('returnUrl', OW::getRouter()->urlForRoute('spodoauth2connect_oauth'));
        $this->assign('beginUrl', OW::getRouter()->urlForRoute('spodoauth2connect_begin'));
		OW::getDocument()->setHeading(OW::getLanguage()->text('spodoauth2connect', 'heading_configuration'));
        OW::getDocument()->setHeadingIconClass('ow_ic_friends');
	}
	
	public function success() {
		OW::getDocument()->setHeading(OW::getLanguage()->text('spodoauth2connect', 'heading_configuration'));
		$success_text = OW::getLanguage()->text('spodoauth2connect','register_success_msg');
		$this->assign('text', $success_text);
	}
    
}


class SPODOAUTH2CONNECT_AccessForm extends Form {

  public function __construct()
  {
    parent::__construct('SPODOAUTH2CONNECT_AccessForm');
    $service = SPODOAUTH2CONNECT_BOL_Service::getInstance();
    $conf = $service->getProperties();

    $field = new TextField('client_id');
    $field->setRequired(true);
    $field->setValue($conf->client_id);
    $this->addElement($field);

    $field = new TextField('client_secret');
    $field->setRequired(false);
    $field->setValue($conf->client_secret);
    $this->addElement($field);

    $field = new TextField('grant_type');
    $field->setRequired(true);
    $field->setValue($conf->grant_type);
    $this->addElement($field);

    $field = new TextField('scope');
    $field->setRequired(true);
    $field->setValue($conf->scope);
    $this->addElement($field);
    
    $field = new TextField('base_url');
    $field->setRequired(true);
    $field->setValue($conf->base_url);
    $this->addElement($field);

    $field = new TextField('endpoint');
    $field->setRequired(true);
    $field->setValue($conf->endpoint);
    $this->addElement($field);

    $field = new TextField('tokenpoint');
    $field->setRequired(true);
    $field->setValue($conf->tokenpoint);
    $this->addElement($field);

    $field = new TextField('userinfopoint');
    $field->setRequired(true);
    $field->setValue($conf->userinfopoint);
    $this->addElement($field);

    $submit = new Submit('save');
    $submit->setValue(OW::getLanguage()->text('spodoauth2connect', 'save_btn_label'));
    $this->addElement($submit);
  }

  public function process()
  {
    $values = $this->getValues();
    $service = SPODOAUTH2CONNECT_BOL_Service::getInstance();
    $conf = new SPODOAUTH2CONNECT_BOL_Config();
    $conf->client_id = trim($values['client_id']);
    $conf->client_secret = trim($values['client_secret']);

    $conf->grant_type = trim($values['grant_type']);
    $conf->scope = trim($values['scope']);

    $conf->base_url = trim($values['base_url']);

    $conf->endpoint = trim($values['endpoint']);
    $conf->tokenpoint = trim($values['tokenpoint']);
    $conf->userinfopoint = trim($values['userinfopoint']);

    return $service->saveProperties($conf);
  }
}