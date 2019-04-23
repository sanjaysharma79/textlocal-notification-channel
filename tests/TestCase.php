<?php

namespace NotificationChannels\Textlocal\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('services.textlocal.url', 'https://api.textlocal.in/send/');
        $app['config']->set('services.textlocal.transactional.apiKey', env('TEXTLOCAL_TRANSACTIONAL_KEY'));
        $app['config']->set('services.textlocal.transactional.from', env('TEXTLOCAL_TRANSACTIONAL_FROM'));
        // $app['config']->set('services.textlocal.transactional.apiKey', env('TEXTLOCAL_TRANSACTIONAL_KEY'));
        $app['config']->set('services.textlocal.promotional.apiKey', env('TEXTLOCAL_PROMOTIONAL_KEY'));
        $app['config']->set('services.textlocal.promotional.from', env('TEXTLOCAL_PROMOTIONAL_FROM'));
        // $app['config']->set('services.textlocal.promotional.apiKey', env('TEXTLOCAL_PROMOTIONAL_KEY'));
        $app['config']->set(
            'test.textlocal.transactional.template',
            env('TEST_TEXTLOCAL_TRANSACTIONAL_TEMPLATE')
        );
        $app['config']->set(
            'test.textlocal.transactional.mobile',
            env('TEST_TEXTLOCAL_TRANSACTIONAL_MOBILE')
        );
        $app['config']->set(
            'test.textlocal.transactional.cc',
            env('TEST_TEXTLOCAL_TRANSACTIONAL_CC')
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            'NotificationChannels\Textlocal\TextlocalServiceProvider'
        ];
    }
}
