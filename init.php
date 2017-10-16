<?php

$plugin = OW::getPluginManager()->getPlugin('spodoauth2connect');

// This routes redirects user to the authentication server, thus initializing an authentication workflow
OW::getRouter()->addRoute(new OW_Route(
    'spodoauth2connect_begin', 
    'spodoauth2connect/begin', 
    'SPODOAUTH2CONNECT_CTRL_Connect', 
    'startflow'));

// This route handles the callback from the authentication server
OW::getRouter()->addRoute(new OW_Route(
    'spodoauth2connect_oauth', 
    'spodoauth2connect/oauth', 
    'SPODOAUTH2CONNECT_CTRL_Connect', 
    'oauth'));

// Replace standard Oxwall login route
OW::getRouter()->removeRoute('static_sign_in');
OW::getRouter()->addRoute(new OW_Route(
    'static_sign_in', 
    'sign-in', 
    'SPODOAUTH2CONNECT_CTRL_Connect', 
    'startflow'));

// Replace standard Oxwall "Profile edit" route
OW::getRouter()->removeRoute('base_edit');
OW::getRouter()->addRoute(new OW_Route(
    'base_edit', 
    'profile/edit', 
    'SPODOAUTH2CONNECT_CTRL_Edit', 
    'index'));

// Administration route for configuration
OW::getRouter()->addRoute(new OW_Route(
    'spodoauth2connect_admin_main',
    'admin/plugins/spodoauth2connect',
    'SPODOAUTH2CONNECT_CTRL_Admin', 
    'index'));

// Administration route for saving configuration
OW::getRouter()->addRoute(new OW_Route(
    'spodoauth2connect_app_success_page',
    'admin/plugins/spodoauth2connect',
    'SPODOAUTH2CONNECT_CRTL_Admin', 
    'success'));

$configs = OW::getConfig()->getValues('spodoauth2connect');
if ( !empty($configs['client_id']) && !empty($configs['client_secret']) ) {
	$registry = OW::getRegistry();
	$registry->addToArray(BASE_CTRL_Join::JOIN_CONNECT_HOOK, array(new SPODOAUTH2CONNECT_CMP_ConnectButton(), 'render'));
	$registry->addToArray(BASE_CMP_ConnectButtonList::HOOK_REMOTE_AUTH_BUTTON_LIST, array(new SPODOAUTH2CONNECT_CMP_ConnectButton(), 'render'));
}

function spodoauth2connect_event_add_button( BASE_CLASS_EventCollector $event )
{
    $cssUrl = OW::getPluginManager()->getPlugin('SPODOAUTH2CONNECT')->getStaticCssUrl() . 'spodoauth2connect.css';
    OW::getDocument()->addStyleSheet($cssUrl);
    $button = new SPODOAUTH2CONNECT_CMP_ConnectButton();
    $event->add(array('iconClass' => 'ow_ico_signin_g', 'markup' => $button->render()));
}
OW::getEventManager()->bind(BASE_CMP_ConnectButtonList::HOOK_REMOTE_AUTH_BUTTON_LIST, 'spodoauth2connect_event_add_button');

// Alert user if plugin configuration is not complete
function spodoauth2connect_add_admin_notification( BASE_CLASS_EventCollector $e )
 {
    $language = OW::getLanguage();
    $configs = OW::getConfig()->getValues('spodoauth2connect');
    if ( empty($configs['client_id']) || empty($configs['client_secret']) )
    {
        $e->add($language->text('spodoauth2connect', 'admin_configuration_required_notification', array( 'href' => OW::getRouter()->urlForRoute('spodoauth2connect_admin_main') )));
    }
 }
OW::getEventManager()->bind('admin.add_admin_notification', 'spodoauth2connect_add_admin_notification');


function spodoauth2connect_add_access_exception( BASE_CLASS_EventCollector $e ) {
	$e->add(array('controller' => 'SPODOAUTH2CONNECT_CTRL_Connect', 'action' => 'oauth'));

}

OW::getEventManager()->bind('base.members_only_exceptions', 'spodoauth2connect_add_access_exception');
OW::getEventManager()->bind('base.password_protected_exceptions', 'spodoauth2connect_add_access_exception');
OW::getEventManager()->bind('base.splash_screen_exceptions', 'spodoauth2connect_add_access_exception');

$eventHandler = new SPODOAUTH2CONNECT_CLASS_EventHandler();
$eventHandler->init();