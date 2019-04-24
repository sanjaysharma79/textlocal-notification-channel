# Textlocal Notification Channel for Laravel 5.6+.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/thinkstudeo/textlocal-notification-channel.svg?style=flat-square)](https://packagist.org/packages/thinkstudeo/textlocal-notification-channel)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/thinkstudeo/textlocal-notification-channel/master.svg?style=flat-square)](https://travis-ci.org/thinkstudeo/textlocal-notification-channel)
[![StyleCI](https://styleci.io/repos/183053426/shield)](https://styleci.io/repos/183053426)
[![SymfonyInsight](https://insight.symfony.com/projects/5a7f8ac7-e224-4794-87c4-256cdb5d7ba1/mini.svg)](https://insight.symfony.com/projects/5a7f8ac7-e224-4794-87c4-256cdb5d7ba1)
[![Quality Score](https://img.shields.io/scrutinizer/g/thinkstudeo/textlocal-notification-channel.svg?style=flat-square)](https://scrutinizer-ci.com/g/thinkstudeo/textlocal-notification-channel)
[![Total Downloads](https://img.shields.io/packagist/dt/thinkstudeo/textlocal-notification-channel.svg?style=flat-square)](https://packagist.org/packages/thinkstudeo/textlocal-notification-channel)

This package makes it easy to send notifications using [Textlocal](https://textlocal.com) with Laravel 5.6+

Supports using both Transactional and Promotional accounts with Textlocal at the same time.

## Contents

- [Textlocal Notification Channel for Laravel 5.6+.](#textlocal-notification-channel-for-laravel-56)
  - [Contents](#contents)
  - [Installation](#installation)
    - [Setting up the Textlocal service](#setting-up-the-textlocal-service)
  - [Usage](#usage)
    - [Using Textlocal Promotional Account](#using-textlocal-promotional-account)
    - [Using Textlocal Transactional Account](#using-textlocal-transactional-account)
  - [Changelog](#changelog)
  - [Testing](#testing)
  - [Security](#security)
  - [Contributing](#contributing)
  - [Credits](#credits)
  - [License](#license)

## Installation

```bash
composer require thinkstudeo/textlocal-notification-channel
```

Taking advantage of automatic package discovery available since Laravel 5.5, the service provider will be registered automatically.

### Setting up the Textlocal service

Add your textlocal accounts, api url and credentials in the `config/services.php` file.
The url is required to be set in the config file because, textlocal has different urls for different countries.
Atleast for India, its different. `https://api/textlocal.in/send/`

```php
...
'textlocal' => [
    'url' => 'https://api.textlocal.com/send/'	//or 'https://api.textlocal.in/send/ - for India

    //Textlocal Transactional Account
    'transactional' => [
        'apiKey' => env('TEXTLOCAL_TRANSACTIONAL_KEY'),
        'from' => env('TEXTLOCAL_TRANSACTIONAL_FROM', 'TXTLCL')
	],

    //Textlocal Promotional Account
    'promotional' => [
        'apiKey' => env('TEXTLOCAL_PROMOTIONAL_KEY'),
        'from' => env('TEXTLOCAL_PROMOTIONAL_FROM', 'TXTLCL')
    ]
],
...
```

Don't forget to add the keys to your `.env` file

```
...
TEXTLOCAL_TRANSACTIONAL_KEY= <Your Textlocal Transactional Account API KEY>
TEXTLOCAL_TRANSACTIONAL_FROM= <Registered/Approved sender for your Textlocal Transactional Account>
TEXTLOCAL_PROMOTIONAL_KEY= <Your Textlocal Promotional Account API KEY>
TEXTLOCAL_PROMOTIONAL_FROM= <Registered/Approved sender for your Textlocal Promotional Account>
...
```

## Usage

To use the channel, include the `NotificationChannels\Textlocal\TextlocalChannel` class in the `via()` method of your notification class.

### Using Textlocal Promotional Account

```php
use Illuminate\Notifications\Notification;
use NotificationChannels\Textlocal\TextlocalChannel;
use NotificationChannels\Textlocal\TextlocalMessage;

class SendBlackFridaySaleAnnouncement extends Notification
{
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TextlocalChannel::class];
    }

    /**
     * Get the Textlocal / SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return NexmoMessage
     */
    public function toTextlocal($notifiable)
    {
        return (new TextlocalMessage())
            //Required
            // To send sms via your Textlocal promotional account
            //or transactional() to sent via Textlocal transactional account
            ->promotional()

            //Optional
            //If you don't provide a from, it will pick up the value from the config
            ->from('TXTLCL')

            //Optional
            //If you want to send a copy of the sms to another number eg an Admin
            ->cc('914545454545')

            //Required
            ->content('We are running a BlackFriday sale from tomorrow for 3 days with 40% off. Hurry !!! Grab the opportunity!');
    }
}
```

### Using Textlocal Transactional Account

```php
use Illuminate\Notifications\Notification;
use NotificationChannels\Textlocal\TextlocalChannel;
use NotificationChannels\Textlocal\TextlocalMessage;

class SendLoginOtp extends Notification
{
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TextlocalChannel::class];
    }

    /**
     * Get the Textlocal / SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return NexmoMessage
     */
    public function toTextlocal($notifiable)
    {
        return (new TextlocalMessage())
            //Required
            // To send sms via your Textlocal transactional account
            //or promotional() to sent via Textlocal promotional account
            ->transactional()

            //Optional
            //If you don't provide a from, it will pick up the value from the config
            ->from('TXTLCL')

            //Optional
            //If you want to send a copy of the sms to another number eg an Admin
            ->cc('914545454545')

            //Required
            //When sending through Textlocal transactional account, the content must conform to one of your approved templates.
            ->content('Your OTP for Application is 234567. It is valid for the next 10 minutes only.');
    }
}
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

Fill in the `env` values in the `phpunit.xml.dist`. The tests depends on these values.

```xml

<php>
    <env name="APP_ENV" value="Testing"/>
    <env name="TEXTLOCAL_TRANSACTIONAL_KEY" value="API_KEY for transactional account"/>
    <env name="TEXTLOCAL_TRANSACTIONAL_FROM" value="TXTLCL"/>
    <env name="TEXTLOCAL_PROMOTIONAL_KEY" value="API_KEY for transactional account"/>
    <env name="TEXTLOCAL_PROMOTIONAL_FROM" value="TXTLCL"/>

    <env name="TEST_TEXTLOCAL_TRANSACTIONAL_TEMPLATE" value="Your approved template for Textlocal transactional account"/>
    <env name="TEST_TEXTLOCAL_TRANSACTIONAL_MOBILE" value="Valid phone number"/>
    <env name="TEST_TEXTLOCAL_TRANSACTIONAL_CC" value="Another Valid Phone number"/>
</php>
```

```bash
$ composer test
```

## Security

If you discover any security related issues, please email neerav@thinkstudeo.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

-   [Neerav Pandya](https://github.com/neeravp)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
