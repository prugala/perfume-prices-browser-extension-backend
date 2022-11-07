<?php
namespace Deployer;

require 'recipe/symfony.php';

// Config

set('repository', 'git@github.com:prugala/fragrantica-browser-extension-backend.git');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host('extension.isedo.pl')
    ->stage('prod')
    ->set('remote_user', 'deploy')
    ->set('deploy_path', '~/apps/extension');

// Hooks

after('deploy:failed', 'deploy:unlock');
