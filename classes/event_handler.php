<?php

class SPODOAUTH2CONNECT_CLASS_EventHandler
{
    public function afterUserRegistered( OW_Event $event )
    {
        $params = $event->getParams();

        if ( $params['method'] != 'oauth2' )
        {
            return;
        }

        $userId = (int) $params['userId'];

        $event = new OW_Event('feed.action', array(
                'pluginKey' => 'base',
                'entityType' => 'user_join',
                'entityId' => $userId,
                'userId' => $userId,
                'replace' => true,
                ), array(
                'string' => OW::getLanguage()->text('spodoauth2connect', 'feed_user_join'),
                'view' => array(
                    'iconClass' => 'ow_ic_user'
                )
            ));
        OW::getEventManager()->trigger($event);
    }

    public function afterUserSynchronized( OW_Event $event )
    {
        $params = $event->getParams();

        if ( !OW::getPluginManager()->isPluginActive('activity') || $params['method'] !== 'oauth2' )
        {
            return;
        }
        $event = new OW_Event(OW_EventManager::ON_USER_EDIT, array('method' => 'native', 'userId' => $params['userId']));
        OW::getEventManager()->trigger($event);
    }

    public function collectAuthLinkItems( BASE_CLASS_ConsoleItemCollector $event )
    {
        // Hide the LOGIN button
        $a = $event->getData()[0];
        $item = $a['item'];
        if ($item instanceof BASE_CMP_ConsoleItem) {
            /*
            $urlAuthPage = OW::getRouter()->urlForRoute('spodoauth2connect_begin');
            $login = OW::getLanguage()->text('spodoauth2connect', 'connect_btn_label');
            $item->setControl('<a href="' . $urlAuthPage . '"><span class="ow_signin_label">' . $login . '</span></a>');
            $unbindClick = "$('#".$item->getUniqId()."').unbind('click');";
            OW::getDocument()->addOnloadScript($unbindClick);
            */
            $item->setVisible(false);
        }
    }

    public function genericInit()
    {
        OW::getEventManager()->bind(OW_EventManager::ON_USER_REGISTER, array($this, "afterUserRegistered"));
        OW::getEventManager()->bind(OW_EventManager::ON_USER_EDIT, array($this, "afterUserSynchronized"));
    }

    public function init()
    {
        $this->genericInit();

        //It binds to the class which shows the Sign in/Sign up links in Oxwall.
        OW::getEventManager()->bind('console.collect_items', array($this, 'collectAuthLinkItems'));
    }
}