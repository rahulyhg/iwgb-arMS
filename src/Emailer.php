<?php

use Mailgun\Model\Message\SendResponse;

class Emailer {

    /** @var $mailgun \Mailgun\Mailgun */
    private $mailgun;

    /** @var $domain string */
    private $domain;

    /** @var $from string */
    private $from;

    /** @var $replyTo string */
    private $replyTo;

    /** @var $view \Slim\Views\Twig */
    private $view;

    /**
     * Emailer constructor.
     * @param \Mailgun\Mailgun $mailgun
     * @param \Slim\Views\Twig $view
     * @param string $domain
     * @param string $from
     * @param string $replyTo
     */
    public function __construct(\Mailgun\Mailgun $mailgun, \Slim\Views\Twig $view, string $domain, string $from, string $replyTo) {
        $this->mailgun = $mailgun;
        $this->view = $view;
        $this->domain = $domain;
        $this->from = $from;
        $this->replyTo = $replyTo;
    }

    public function send(array $params): SendResponse {

        return $this->mailgun->messages()->send($this->domain,
            array_merge([
                'from'      => $this->from,
                'h:Reply-To'=> $this->replyTo,
            ], $params)
        );
    }

    public function sendTransactional(string $to, string $subject, string $text, array $htmlVars, array $params): SendResponse {
        $email = $this->view->getEnvironment()->load('/email/transaction.html.twig');
        $html = $email->render(array_merge([$subject], $htmlVars));
        return $this->send([
            'to'        => $params['to'],
            'subject'   => $params['subject'],
            'text'      => self::processEmailText($text, $params),
            'html'      => self::processEmailText($html, $params),
        ]);
    }

    private static function processEmailText($body, $params): string {
        foreach ($params as $key => $value) {
            $body = str_replace("%$key%", $value, $body);
        }
        return $body;
    }


}