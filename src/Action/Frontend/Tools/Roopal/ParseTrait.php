<?php

namespace Action\Frontend\Tools\Roopal;

use Aws\S3\S3Client;
use Guym4c\Roopal\Invoice;
use Psr\Http\Message\UploadedFileInterface;

trait ParseTrait {

    private $SPACES_CACHE_DIRECTORY = 'roopal/';

    /**
     * @param $files UploadedFileInterface[]
     * @return string
     * @throws \League\Csv\CannotInsertRecord
     * @throws \Exception
     */
    public function parseInvoices(array $files): string {
        /** @var S3Client $cdn */
        $cdn = $this->cdn;

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
        $key = $this->SPACES_CACHE_DIRECTORY . 'dl/' . $id . '.csv';

        $cdn->putObject([
            'Bucket' => $this->settings['spaces']['bucket'],
            'Key' => $key,
            'ACL' => 'private',
            'ContentType' => 'text/csv',
            'Body' => $csv,
        ]);

        $uri = $cdn->createPresignedRequest($cdn->getCommand('GetObject', [
            'Bucket' => $this->settings['spaces']['bucket'],
            'Key' => $key,
        ]), '+1 hour')->getUri();

        foreach ($invoices as $invoice) {
            $invoice->setAnonymised(true);
        }

        $csv = Invoice::toCsv($invoices);
        $key = $this->SPACES_CACHE_DIRECTORY . $id . '.csv';

        $cdn->putObject([
            'Bucket'        => $this->settings['spaces']['bucket'],
            'Key'           => $key,
            'ACL'           => 'private',
            'ContentType'   => 'text/csv',
            'Body'          => $csv,
        ]);

        foreach ($files as $file) {
            unlink(APP_ROOT . '/var/upload' . $file->getClientFilename());
        }

        return (string) $uri;
    }
}