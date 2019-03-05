<?php

namespace Action\Backend\Media;

trait SpacesActionTrait {

    private $DEFAULT_PATH = 'bucket/';

    protected static function getFileType($file) {
        return strtolower(pathinfo($file, PATHINFO_EXTENSION));
    }

    protected static function isImage($file) {
        return in_array(self::getFileType($file), ['jpg', 'png']);
    }

    private function getEncodedRoot(): string {
        return base64_encode($this->settings['spaces']['publicRoot']);
    }

    private function getRoot(): string {
        return $this->settings['spaces']['publicRoot'];
    }


}