<?php

namespace NotificationChannels\Textlocal;

class TextlocalMessage
{
    /**
     * The phone numbers copy of the message should be sent to.
     *
     * @var string
     */
    public $cc = [];

    /**
     * The sender the message should be sent from.
     *
     * @var string
     */
    public $from;

    /**
     * The time at which the message should be sent.
     *
     * @var string|number
     */
    public $at;

    /**
     * The message content.
     *
     * @var string
     */
    public $content;

    /**
     * The Textlocal account through which the message should be sent.
     *
     * @var string
     */
    public $account = 'promotional';

    /**
     * Indication whether it's a test or not.
     *
     * @var string
     */
    public $test = false;

    /**
     * Create a new message instance.
     *
     * @param string $content
     * @return void
     */
    public function __construct($content = '')
    {
        $this->content = $content;
    }

    /**
     * Set the message content.
     *
     * @param string $content
     * @return $this
     */
    public function content($content)
    {
        $this->content = trim($content);

        return $this;
    }

    /**
     * Set the numbers copy of the message should be sent to.
     *
     * @param string|array $cc
     * @return $this
     */
    public function cc($cc)
    {
        $cc = is_string($cc) ? [$cc] : $cc;
        $this->cc = $cc;

        return $this;
    }

    /**
     * Set the sender the message should be sent from.
     *
     * @param string $from
     * @return $this
     */
    public function from($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Set the date and time at which the message should be sent.
     *
     * @param number $at
     * @return $this
     */
    public function at($at)
    {
        $this->at = $at;

        return $this;
    }

    /**
     * Set the flag for test as true, for the message.
     *
     * @return $this
     */
    public function test()
    {
        $this->test = true;

        return $this;
    }

    /**
     * Set Textlocal account from which the message should be sent as transactional.
     *
     * @return $this
     */
    public function transactional()
    {
        $this->account = 'transactional';

        return $this;
    }

    /**
     * Set Textlocal account from which the message should be sent as promotional.
     *
     * @return $this
     */
    public function promotional()
    {
        $this->account = 'promotional';

        return $this;
    }
}
