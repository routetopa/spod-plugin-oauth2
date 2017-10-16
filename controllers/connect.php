<?php

class SPODOAUTH2CONNECT_CTRL_Connect extends OW_ActionController
{
    /**
     *
     * @var SPODOAUTH2CONNECT_BOL_Service
     */
    private $service;

    public function init()
    {
        $this->service = SPODOAUTH2CONNECT_BOL_Service::getInstance();
    }

    public function startflow()
    {
      $auth_uri = $this->service->generateOAuthUri();
      $this->redirect($auth_uri);
    }

    public function oauth()
    {
     $language = OW::getLanguage();
     if (!empty ($_GET['code']))
     {
       $data = array (
         'code'=>$_GET['code'],
         'client_id'=>$this->service->props->client_id,
         'client_secret'=>$this->service->props->client_secret,
         'redirect_uri'=>$this->service->props->redirect_uri,
         'grant_type'=>'authorization_code'
        );
       $userinfo = $this->service->getUserInfo ($data);
     }
     else
     {
        OW::getFeedback()->error($language->text('spodoauth2connect', 'login_failure_msg'));
        $this->redirect(OW::getRouter()->urlForRoute('static_sign_in'));
     }
     $result = $this->login ($userinfo);
     if ($result) $this->redirect(OW::getRouter()->getBaseUrl());
     else $this->redirect(OW::getRouter()->urlForRoute('static_sign_in'));
    }


  public function login( $params )
    {
      $language = OW::getLanguage();
      // Register or login
      $user = BOL_UserService::getInstance()->findByEmail($params['email']);
      if (!empty($user))
      {
        // LOGIN
        OW::getUser()->login($user->id);
        OW::getFeedback()->info($language->text('spodoauth2connect', 'login_success_msg'));
        return true;
      }
      else
      {
        //REGISTER
        $authAdapter = new SPODOAUTH2CONNECT_CLASS_AuthAdapter($params['email']);
        $username = 'oauth2_'.$params ['id'];
        $password = uniqid();
        try
        {
          $user = BOL_UserService::getInstance()->createUser($username, $password, $params['email'], null, true);
        }
        catch ( Exception $e )
        {
          switch ( $e->getCode() )
          {
           case BOL_UserService::CREATE_USER_DUPLICATE_EMAIL:
             OW::getFeedback()->error($language->text('spodoauth2connect', 'join_dublicate_email_msg'));
             return false;
             break;
          case BOL_UserService::CREATE_USER_INVALID_USERNAME:
             OW::getFeedback()->error($language->text('spodoauth2connect', 'join_incorrect_username'));
             return false;
             break;
          default:
             OW::getFeedback()->error($language->text('spodoauth2connect', 'join_incomplete'));
             return false;
             break;
         }
      } //END TRY-CATCH
      $user->username = "oauth2_" . $user->id;
      BOL_UserService::getInstance()->saveOrUpdate($user);
      BOL_QuestionService::getInstance()->saveQuestionsData(array('realname' => $params['name']), $user->id);
      BOL_AvatarService::getInstance()->setUserAvatar ($user->id, $params['picture']);

      switch ($params['gender'])
      {
        case 'male'   :  BOL_QuestionService::getInstance()->saveQuestionsData(array('sex' => 1), $user->id);break;
        case 'female' :  BOL_QuestionService::getInstance()->saveQuestionsData(array('sex' => 2), $user->id);break;
      }

      $authAdapter->register($user->id);
      $authResult = OW_Auth::getInstance()->authenticate($authAdapter);
      if ( $authResult->isValid() )
      {
        $event = new OW_Event(OW_EventManager::ON_USER_REGISTER, array('method' => 'auth2', 'userId' => $user->id));
        OW::getEventManager()->trigger($event);
        OW::getFeedback()->info($language->text('spodoauth2connect', 'join_success_msg'));
        OW::getUser()->login($user->id);
      }
      else
      {
        OW::getFeedback()->error($language->text('spodoauth2connect', 'join_failure_msg'));
      }
      return $authResult->isValid();
    }
   }
}