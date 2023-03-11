<?php

namespace Deployer;

require 'recipe/common.php';

// Config

//set('repository', 'https://github.com/rendix2/alfred');
//set('branch', 'main');

add('shared_files', []);
add('shared_dirs', ['log']);
add('writable_dirs', ['log', 'temp']);
set('keep_releases', 5);
set('source_path', __DIR__);

// Hosts

host('rozarka.net')
    ->set('hostname', 'rozarka.net')
    ->set('remote_user', 'docker')
    ->set('port', '22')
    ->set('identity_file', __DIR__ . '/config/deploy/docker')
    ->set('deploy_path', '/opt/bitnami/apache/htdocs/rozarka.net');

// Hooks

desc('Upload web dir');
task('deploy:copy_web', function () {
    upload(__DIR__ . DIRECTORY_SEPARATOR, get('release_path'));
});

desc('Set owner docker');
task('deploy:before:owner', function () {
    run('sudo chown -R docker:docker /opt/bitnami/apache/htdocs');
});

desc('Set owner bitnami');
task('deploy:after:owner', function () {
    run('sudo chown -R bitnami:bitnami /opt/bitnami/apache/htdocs');
});

desc('Run migrations');
task('deploy:migrations', function () {
    run('php ' . get('release_path') . '/bin/console migrations:migrate --no-interaction');
});

desc('Set .htaccess');
task('deploy:htaccess', function () {
    run('sudo rm ' . get('release_path') . '/.htaccess');
    run('sudo mv ' . get('release_path') . '/_.htaccess ' . get('release_path') . '/.htaccess');
});

desc('Set neon file');
task('deploy:neon', function () {
    run('sudo rm ' . get('release_path') . '/config/local.neon');
});

task('deploy:update_code', function () {
});

desc('Remove cached files');
task('deploy:remove_cache', function () {
    run('sudo rm ' . get('release_path') . '/temp/cache -r -f');
    run('sudo rm ' . get('release_path') . '/temp/proxies -r -f');
});

desc('Deploy Alfred');
task('deploy', [
        //'deploy:check_remote',
        'deploy:before:owner',
        'deploy:setup',
        'deploy:lock',
        'deploy:release',

        'deploy:copy_web',

        'deploy:shared',
        'deploy:writable',
        'deploy:clear_paths',
        'deploy:htaccess',
        'deploy:neon',
        'deploy:remove_cache',
        'deploy:migrations',
        'deploy:symlink',
        'deploy:unlock',
        'deploy:cleanup',
        'deploy:after:owner',
    ]
);

after('deploy:failed', 'deploy:unlock');
