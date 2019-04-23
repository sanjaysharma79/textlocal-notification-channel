<?php

namespace NotificationChannels\Textlocal\Tests;

use Mockery as m;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Textlocal\TextlocalClient;
use NotificationChannels\Textlocal\TextlocalChannel;
use NotificationChannels\Textlocal\TextlocalMessage;

class TextlocalChannelTest extends TestCase
{
    /** @test */
    public function text_sms_is_sent_via_textlocal()
    {
        $notification = new TestNotification;
        $notifiable   = new TestNotifiable;

        $channel = new TextlocalChannel(
            $textlocal = m::mock(TextlocalClient::class)
        );

        $message          = new TextlocalMessage('My test message');
        $message->promotional()->cc('914545454545')->from('TXTLCL');

        $textlocal->shouldReceive('message')
                ->once()
                ->andReturnUsing(function ($to, $msg) use ($message, $notifiable) {
                    return !!(
                        $to === $notifiable->mobile &&
                        $msg->account === $message->account &&
                        $msg->content === $message->content &&
                        $msg->cc === $message->cc &&
                        $msg->account === 'promotional'
                    );
                });

        $this->assertTrue($channel->send($notifiable, $notification));
        $this->assertEquals('promotional', $message->account);
    }

    /** @test */
    public function sms_is_sent_via_textlocal_transactional_account()
    {
        $notification = new TestNotificationTransactional;
        $notifiable   = new TestNotifiable;

        $channel = new TextlocalChannel(
            $textlocal = m::mock(TextlocalClient::class)
        );

        $message          = new TextlocalMessage('My test message');
        $message->transactional()->cc('915454545454');

        $textlocal->shouldReceive('message')
                ->once()
                ->andReturnUsing(function ($to, $msg) use ($message, $notifiable) {
                    // var_dump('msg:', $msg);
                    // var_dump('message:', $message);

                    return !!(
                        $to === $notifiable->mobile &&
                        $msg->from === $message->from &&
                        $msg->account === $message->account &&
                        $msg->content === $message->content &&
                        $msg->cc === $message->cc &&
                        $msg->account === 'transactional'
                    );
                });

        $this->assertTrue($channel->send($notifiable, $notification));
        $this->assertEquals('transactional', $message->account);
    }
}

class TestNotifiable
{
    use Notifiable;

    public $mobile = '919898989898';

    public function routeNotificationForTextlocal($notification)
    {
        return $this->mobile;
    }
}

class TestNotification extends Notification
{
    public function toTextlocal($notifiable)
    {
        return (new TextlocalMessage())
            ->content('My test message')
            ->from('TXTLCL')
            ->cc('914545454545');
    }
}

class TestNotificationTransactional extends Notification
{
    public function toTextlocal($notifiable)
    {
        return (new TextlocalMessage())
            ->transactional()
            ->content('My test message')
            ->cc('915454545454');
    }
}