<?php

namespace NotificationChannels\Textlocal\Tests;

use GuzzleHttp\Client;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Textlocal\TextlocalClient;
use NotificationChannels\Textlocal\TextlocalChannel;
use NotificationChannels\Textlocal\TextlocalMessage;

/** @group integration */
class TextlocalChannelIntegrationTest extends TestCase
{
    /** @test */
    public function test_can_send_sms_via_textlocal()
    {
        $notification = new IntegrationTestNotification;
        $notifiable   = new IntegrationTestNotifiable;

        $channel = new TextlocalChannel(new TextlocalClient(new Client()));

        $response = $channel->send($notifiable, $notification);
        // dd($response);
        $this->assertTrue($response->test_mode);
        $this->assertCount(2, $response->messages);
        $this->assertEquals('My test message', $response->message->content);
        $this->assertEquals('TXTLCL', $response->message->sender);
        $this->assertEquals($notifiable->mobile, $response->messages[0]->recipient);
        $this->assertEquals(config('test.textlocal.transactional.cc'), $response->messages[1]->recipient);
    }

    /** @test */
    public function it_can_send_transactional_sms_via_textlocal()
    {
        $notification = new IntegrationTestNotificationTransactional;
        $notifiable   = new IntegrationTestNotifiable;

        $channel = new TextlocalChannel(new TextlocalClient(new Client()));

        $messageContent = sprintf(config('test.textlocal.transactional.template'), '334588');

        $response = $channel->send($notifiable, $notification);
        // var_dump($response);
        $this->assertTrue($response->test_mode);
        $this->assertCount(2, $response->messages);
        $this->assertEquals($messageContent, $response->message->content);
        $this->assertEquals('TXTLCL', $response->message->sender);
        $this->assertEquals($notifiable->mobile, $response->messages[0]->recipient);
        $this->assertEquals(config('test.textlocal.transactional.cc'), $response->messages[1]->recipient);
    }
}

class IntegrationTestNotifiable
{
    use Notifiable;

    public $mobile = '';

    public function routeNotificationForTextlocal($notification)
    {
        $this->mobile = config('test.textlocal.transactional.cc');

        return $this->mobile;
    }
}

class IntegrationTestNotification extends Notification
{
    public function toTextlocal($notifiable)
    {
        return (new TextlocalMessage())
            ->content('My test message')
            // ->from('TXTLCL')
            ->cc(config('test.textlocal.transactional.cc'))
            ->test();
    }
}

class IntegrationTestNotificationTransactional extends Notification
{
    public function toTextlocal($notifiable)
    {
        return (new TextlocalMessage())
            ->transactional()
            ->content(sprintf(config('test.textlocal.transactional.template'), '334588'))
            ->from('TXTLCL')
            ->cc(config('test.textlocal.transactional.cc'))
            ->test();
    }
}
