<?php
namespace Deployer;

require 'recipe/laravel.php';

// Config

set('repository', 'https://github.com/gabrielabiah/laravel-angular-authentication/tree/da354ea529474da3995415d69437dba566fc0d57/backend-laravel');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts
host('80.209.233.7')
    //->user('manifest')
    ->set('remote_user', 'manifest')
    ->set('deploy_path', '/var/www/portal.siteshowcase.top');

// Hooks

after('deploy:failed', 'deploy:unlock');
