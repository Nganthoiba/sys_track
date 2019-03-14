<?php
Config::set("site_name", "MVC design");
Config::set("languages", array('en','fr'));
Config::set('routes', array(
    'admin'=>'_admin_'
));
Config::set('default_route', 'default');
Config::set('default_controller', 'Default');
Config::set('default_action', 'index');
Config::set('default_language', 'en');
Config::set('host', 'http://localhost/sys_track');

/**** Database configuration setup ****/
Config::set('db_server', 'localhost');
Config::set('db_name', 'sys_track');
Config::set('db_username', 'root');
Config::set('db_password', '');


