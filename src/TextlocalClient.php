<?php

namespace NotificationChannels\Textlocal;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use GuzzleHttp\Exception\SeekException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use NotificationChannels\Textlocal\Exceptions\CouldNotSendNotification;
use NotificationChannels\Textlocal\Exceptions\CouldNotAuthenticateAccount;

class TextlocalClient
{
    /**
     * The form params to be sent with the request to the api.
     *
     * @var array
     */
    protected $params;

    /**
     * Guzzle http client.
     *
     * @var GuzzleHttp\Client
     */
    protected $http;

    /**
     * Api endpoint to send the request for sms.
     *
     * @var string
     */
    protected $url;

    /**
     * Create new TextlocalClient instance.
     *
     * @param \GuzzleHttp\Client $client
     */
    public function __construct(Client $client)
    {
        $this->http = $client;
        $this->url = config('services.textlocal.url');
    }

    /**
     * Set the Account to be used as Transactional Account.
     *
     * @return TextlocalClient
     */
    public function transactional()
    {
        $apiKey = config('services.textlocal.transactional.apiKey');
        if (empty($apiKey)) {
            throw CouldNotAuthenticateAccount::apiKeyMissing('transactional');
        }
        $this->addParam('apiKey', urlencode($apiKey));
        $this->addParam('sender', config('services.textlocal.transactional.from'), 'TXTLCL');

        return $this;
    }

    /**
     * Set the Account to be used as Promotional Account.
     *
     * @return TextlocalClient
     */
    public function promotional()
    {
        $apiKey = config('services.textlocal.promotional.apiKey');
        if (empty($apiKey)) {
            throw CouldNotAuthenticateAccount::apiKeyMissing('promotional');
        }
        $this->addParam('apiKey', urlencode($apiKey));
        $this->addParam('sender', config('services.textlocal.promotional.from'), 'TXTLCL');

        return $this;
    }

    /**
     * Add parameter to the request parameters to be sent to the api endpoint.
     *
     * @param string $key
     * @param string|number $value
     * @return TextlocalClient
     */
    public function addParam($key, $value)
    {
        $this->params[$key] = $value;

        return $this;
    }

    /**
     * Set the time in future at which the message should be sent.
     * @param string|number $schedule
     * @return TextlocalClient
     */
    public function at($schedule)
    {
        if ($schedule) {
            $time = is_numeric($schedule) ? $schedule : Carbon::parse($schedule)->timestamp;
            $this->addParam('schedule_time', $time);
        }

        return $this;
    }

    /**
     * If provided, set the sender from which the message should be sent.
     * Otherwise let the sender be the default provided in the config.
     *
     * @param $sender
     * @return TextlocalClient
     */
    public function from($sender)
    {
        if ($sender) {
            $this->addParam('sender', $sender);
        }

        return $this;
    }

    /**
     * Prepare comma separated list of numbers to which the message is to be sent.
     * @param $numbers
     * @return TextlocalClient
     */
    public function to($numbers)
    {
        $this->addParam(
            'numbers',
            implode(
                ',',
                is_string($numbers) ? explode(',', $numbers) : $numbers
            )
        );

        return $this;
    }

    /**
     * Prepare the params from the received message object and make the api request to send sms.
     *
     * @param string $to
     * @param TexlocalMessage $message
     * @return object
     */
    public function message($to, $message)
    {
        $numbers = array_merge([$to], $message->cc);

        return $this->{$message->account}()
            ->to($numbers)
            ->from($message->from)
            ->at($message->at)
            ->test($message->test)
            ->send($message->content);
    }

    /**
     * Send the message, making a request to the api endpoint.
     *
     * @param string $message
     * @return object
     */
    public function send($message)
    {
        $this->addParam('message', rawurlencode($message));

        return $this->post();
    }

    /**
     * Send the message after setting the flag for test.
     *
     * @param bool $test
     * @return $this
     */
    public function test($test)
    {
        if ($test) {
            $this->addParam('test', true);
        }

        return $this;
    }

    /**
     * Make the request to the api endpoint for sending the message.
     *
     * @return object
     * @throws \Exception
     */
    public function post()
    {
        $params = ['form_params' => $this->params];
        try {
            $response = $this->http->request('POST', $this->url, $params, ['verify' => false, 'timeout' => 60]);
            $data = json_decode($response->getBody()->getContents());
            // var_dump('TextlocalClient post response');
            // var_dump(json_encode($data));

            return $this->handleResponse($data);
        } catch (RequestException | SeekException | TransferException $e) {
            throw $e;
        }
    }

    /**
     * Handle the response from the api endpoint.
     *
     * @param object $data
     * @return object
     * @throws CouldNotSendNotification
     */
    public function handleResponse($data)
    {
        if ($data->status === 'failure') {
            foreach ($data->errors as $error) {
                throw CouldNotSendNotification::serviceRespondedWithAnError($error);
            }
        }

        return $data;
    }
}
