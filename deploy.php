<?php

namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'forward-kenya-party');

// Project repository
set('repository', 'git@github.com:wagura-maurice/forward-kenya-party.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);
set('default_timeout', 150000);

// Shared files/dirs between deploys
add('shared_files', ['.env']);
add('shared_dirs', []);

// Writable dirs by web server
add('writable_dirs', []);

// Hosts
host('206.189.120.35')
    ->user('deployer')
    ->port(22)
    ->identityFile('~/.ssh/id_rsa') // ssh on local machine that links to the deployer on vps
    ->set('deploy_path', '/var/www/html/{{application}}');

// Tasks
task('build', function () {
    run('cd {{release_path}} && build');
});

// Update the task sequence
task('release:application', function () {
    // serve the app down
    run('{{bin/php}} {{release_path}}/artisan down');
    // Clear caches
    // run('{{bin/php}} {{release_path}}/artisan cache:clear');
    // Clear expired password reset tokens
    // run('{{bin/php}} {{release_path}}/artisan auth:clear-resets');
    // Clear and cache routes
    run('{{bin/php}} {{release_path}}/artisan route:clear');
    // Clear and cache config
    run('{{bin/php}} {{release_path}}/artisan config:clear');
    // Clear and cache view
    run('{{bin/php}} {{release_path}}/artisan view:clear');
    // create storage link for the release
    run('{{bin/php}} {{release_path}}/artisan storage:link');
    // optimize config and cache
    run('{{bin/php}} {{release_path}}/artisan optimize');
    // Run database migrations
    // run('{{bin/php}} {{release_path}}/artisan migrate:fresh --seed --force');
    // Run custom commands
    // run('{{bin/php}} {{release_path}}/artisan app:optimize');
    // run('{{bin/php}} {{release_path}}/artisan app:debug');
    // serve the app up
    run('{{bin/php}} {{release_path}}/artisan up');
})->once();

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database and other laravel tasks before symlink new release.
before('deploy:symlink', 'release:application');

