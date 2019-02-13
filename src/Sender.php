<?php

class Sender {

    /** @var $emailer \Emailer */
    public $email;

    /** @var $mailgun \Mailgun\Mailgun */
    public $mailgun;

    /** @var $twilio \Twilio\Rest\Client */
    public $twilio;

    public $twilioSettings;

    /**
     * Sender constructor.
     * @param Emailer $emailer
     * @param \Mailgun\Mailgun $mailgun
     * @param \Twilio\Rest\Client $twilio
     * @param array $twilioSettings
     */
    public function __construct(Emailer $emailer, \Mailgun\Mailgun $mailgun, \Twilio\Rest\Client $twilio, array $twilioSettings) {
        $this->email = $emailer;
        $this->mailgun = $mailgun;
        $this->twilio = $twilio;
        $this->twilioSettings = $twilioSettings;
    }

}