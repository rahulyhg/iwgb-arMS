<?php

namespace Action\Frontend\Tools\Roopal;

use Action\Frontend\GenericMemberAction;
use Guym4c\Roopal\Invoice;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Upload extends GenericMemberAction {

    const SPACES_CACHE_DIRECTORY = 'roopal/';

    const SUCCESS_EMAIL_SUBJECT = 'Download your roopal data';

    const SUCCESS_EMAIL_HTML = [
        'content' => [
            'before' => [
                'Hey %name%,',
                'Your roopal data has finished processing and is available for download.',
            ],
            'after' => [
                'This link will remain valid for one hour for your privacy and security.',
                'Deliveroo invoice formats change constantly, and so if you have any issues, drop us a line.',
                '— Your friends at the IWGB',
            ],
            'footer' => [
                'This email was sent regarding your recent invoice upload at [iwgb.org.uk](https://iwgb.org.uk).',
            ],
        ],
        'action' => [
            'href' => '%uri%',
            'display' => 'Download',
        ],
    ];

    const SUCCESS_EMAIL_TEXT = "Hey %name%,\r\n\r\nYour roopal data has finished processing and is available for download.\r\n\r\n%uri%\r\n\r\nThis link will remain valid for one hour for your privacy and security.\r\nDeliveroo invoice formats change constantly, and so if you have any issues, drop us a line.\r\n\r\n— Your friends at the IWGB";

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface {

        $files = $request->getUploadedFiles()['file'];

        /** @var Invoice[] $invoices */
        $invoices = [];

        foreach ($files as $file) {
            /** @var $file UploadedFileInterface */
            $path = APP_ROOT . '/var/upload/' . $file->getClientFilename();
            $file->moveTo($path);
            $invoices[] = new Invoice($path);
        }

        $csv = Invoice::toCsv($invoices);
        $id = uniqid();
        $key = self::SPACES_CACHE_DIRECTORY . 'dl/' . $id . '.csv';

        $this->cdn->putObject([
            'Bucket' => $this->settings['spaces']['bucket'],
            'Key' => $key,
            'ACL' => 'private',
            'ContentType' => 'text/csv',
            'Body' => $csv,
        ]);

        $uri = $this->cdn->createPresignedRequest($this->cdn->getCommand('GetObject', [
            'Bucket' => $this->settings['spaces']['bucket'],
            'Key' => $key,
        ]), '+1 hour')->getUri();

        foreach ($invoices as $invoice) {
            $invoice->setAnonymised(true);
        }

        $csv = Invoice::toCsv($invoices);
        $key = self::SPACES_CACHE_DIRECTORY . $id . '.csv';

        $this->cdn->putObject([
            'Bucket'        => $this->settings['spaces']['bucket'],
            'Key'           => $key,
            'ACL'           => 'private',
            'ContentType'   => 'text/csv',
            'Body'          => $csv,
        ]);

        foreach ($files as $file) {
            unlink(APP_ROOT . '/var/upload' . $file->getClientFilename());
        }

        $this->send->email->transactional($this->member->getEmail(),
            self::SUCCESS_EMAIL_SUBJECT,
            self::SUCCESS_EMAIL_TEXT,
            self::SUCCESS_EMAIL_HTML,
            [
                'name' => $this->member->getFirstName(),
                'uri' => $uri->__toString(),
            ]);

        return $response->withRedirect('/member/tools/roopal?m=Upload complete. We will email you when we have finished processing your invoices');
    }
}