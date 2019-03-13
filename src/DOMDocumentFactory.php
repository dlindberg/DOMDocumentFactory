<?php

declare(strict_types=1);

namespace dlindberg\DOMDocumentFactory;

class DOMDocumentFactory
{
    private $config;

    public function __construct(DOMDocumentFactoryConfig $config = null)
    {
        $this->config = $config ?? new DOMDocumentFactoryConfig();
    }

    public function __invoke(string $blob): \DOMNode
    {
        return $this->getNode($blob);
    }

    public function getDocument(string $blob): \DOMDocument
    {
        $doc = new \DOMDocument($this->config->version, $this->config->encoding);
        $doc->recover = $this->config->recover;
        $doc->formatOutput = $this->config->formatOutput;
        $doc->preserveWhiteSpace = $this->config->preserveWhiteSpace;
        $doc->loadHTML($this->config->loadString($blob), $this->config->DOMOptions);

        return $doc;
    }

    public static function getDomNode(string $blob, DOMDocumentFactoryConfig $config = null): \DOMNode
    {
        return (new DOMDocumentFactory($config))->getNode($blob);
    }

    public static function stringifyNode(\DOMNode $node, DOMDocumentFactoryConfig $config = null): string
    {
        return (new DOMDocumentFactory($config))->stringify($node);
    }

    public static function stringifyNodeList(\DOMNodeList $nodes, DOMDocumentFactoryConfig $config = null): array
    {
        return (new DOMDocumentFactory($config))->stringifyFromList($nodes);
    }

    public function getNode(string $blob): \DOMNode
    {
        return $this->getDocument($blob)->getElementsByTagName('body')->item(0);
    }

    public function stringify(\DOMNode $input): string
    {
        return $this->config->outputString($input->ownerDocument->saveXML($input));
    }

    public function stringifyFromList(\DOMNodeList $input): array
    {
        return $this->stringifyWalkSiblings($input->item(0));
    }

    private function stringifyWalkSiblings(\DOMNode $node, array $carry = []): array
    {
        $carry[] = $this->stringify($node);

        return null === $node->nextSibling ? $carry : $this->stringifyWalkSiblings($node->nextSibling, $carry);
    }
}
