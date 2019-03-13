<?php

declare(strict_types=1);

namespace dlindberg\DOMDocumentFactory;

use \HTMLPurifier;

class DOMDocumentFactoryConfig
{
    public $version             = '1.0';
    public $encoding            = 'UTF-8';
    public $recover             = true;
    public $preserveWhiteSpace  = false;
    public $formatOutput        = true;
    public $DOMOptions          = LIBXML_NOERROR | LIBXML_NOWARNING;

    /**
     * @var \HTMLPurifier
     */
    private $inputPurifier;

    /**
     * @var \HTMLPurifier | null
     */
    private $outputPurifier;

    public function __construct(
        array $settings = [],
        \HTMLPurifier $inputPurifier = null,
        \HTMLPurifier $outputPurifier = null
    ) {
        $this->inputPurifier = $inputPurifier ?? new HTMLPurifier();
        $this->outputPurifier = $outputPurifier;
        \array_filter($settings, [$this, 'setOption'], ARRAY_FILTER_USE_BOTH);
    }

    public function setOption($value, $option): DOMDocumentFactoryConfig
    {
        if (isset($this->$option)) {
            $this->$option = $value;
        }

        return $this;
    }

    public function setInputPurifier(\HTMLPurifier $purifier): DOMDocumentFactoryConfig
    {
        $this->inputPurifier = $purifier;

        return $this;
    }

    public function setOutputPurifier(?\HTMLPurifier $purifier): DOMDocumentFactoryConfig
    {
        $this->outputPurifier = $purifier;

        return $this;
    }

    public function loadString(string $blob): string
    {
        return $this->getDocType() . $this->inputPurifier->purify($blob);
    }

    public function outputString(string $blob): string
    {
        return $this->outputPurifier ? $this->outputPurifier->purify($blob) : $blob;
    }

    public function getDocType(): string
    {
        return \trim((new \DOMDocument($this->version, $this->encoding))->saveXML());
    }
}
