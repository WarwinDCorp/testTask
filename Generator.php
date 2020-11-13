<?php


namespace Classes;


class Generator {
    private $generator;
    public function __construct() {
        $this->generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
    }

    public function getBarcode($barcode) {
        return base64_encode($this->generator->getBarcode("$barcode", $this->generator::TYPE_CODE_128));
    }
}