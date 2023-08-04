<?php

namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/npm.php';

set('shared_files', [
    '.env',
    'auth.json'
]);

set('application', 'relocation');
set('repository', 'git@relocation:meetjet/relocation.git');
set('branch', 'main');
set('php_fpm_version', '8.2');
set('ssh_multiplexing', false);

host('prod')
    ->setHostname('89.40.6.27')
    ->set('branch', 'main')
    ->set('labels', ['stage' => 'prod'])
    ->set('remote_user', 'ploi')
    ->set('deploy_path', '/home/ploi/relocation.digital');

task('check', function () {
    writeln('check');
});

task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'artisan:storage:link',
    'artisan:config:clear',
    'artisan:view:cache',
    'artisan:config:cache',
    'artisan:migrate',
    'initialize:listing-categories',
    'npm:install',
    'npm:build',
    'deploy:publish',
    //'artisan:lighthouse:clear-cache',
    'artisan:queue:restart',
]);

task('npm:install', function () {
    cd('{{release_or_current_path}}');
    run('npm install');
});

task('npm:build', function () {
    cd('{{release_or_current_path}}');
    run('npm run build');
});

task('artisan:lighthouse:clear-cache', function () {
    cd('{{release_or_current_path}}');
    run('php artisan lighthouse:clear-cache');
});

task('initialize:listing-categories', function () {
    cd('{{release_or_current_path}}');
    run('php artisan initialize:listing-categories');
});


after('deploy:failed', 'deploy:unlock');
