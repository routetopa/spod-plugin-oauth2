<?php

$plugin = OW::getPluginManager()->getPlugin('spodoauth2connect');

BOL_LanguageService::getInstance()->addPrefix('spodoauth2connect', 'SPOD OAuth2 Connect');
OW::getPluginManager()->addPluginSettingsRouteName('spodoauth2connect', 'spodoauth2connect_admin_main');

//ow_base_config
OW::getConfig()->addConfig('spodoauth2connect', 'client_id', '', 'Client ID');
OW::getConfig()->addConfig('spodoauth2connect', 'client_secret', '', 'Client Secret');
OW::getConfig()->addConfig('spodoauth2connect', 'grant_type', '', 'Grant type');
OW::getConfig()->addConfig('spodoauth2connect', 'scope', '', 'Scope');

OW::getConfig()->addConfig('spodoauth2connect', 'base_url', '', 'Base URL');

OW::getConfig()->addConfig('spodoauth2connect', 'endpoint', '', 'Authorization endpoint');
OW::getConfig()->addConfig('spodoauth2connect', 'tokenpoint', '', 'Token endpoint');
OW::getConfig()->addConfig('spodoauth2connect', 'userinfopoint', '', 'UserInfo endpoint');


$path = OW::getPluginManager()->getPlugin('spodoauth2connect')->getRootDir() . 'langs.zip';
OW::getLanguage()->importPluginLangs($path, 'spodoauth2connect');