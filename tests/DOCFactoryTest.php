<?php

declare(strict_types=1);

namespace dlindberg\DOMDocumentFactory;

use \dlindberg\DOMDocumentFactory\DOMDocumentFactory as DOCFactory;
use \dlindberg\DOMDocumentFactory\DOMDocumentFactoryConfig as DOCFactoryConfig;

final class DOCFactoryTest extends \PHPUnit\Framework\TestCase
{
    public $testString = '<p>test blob<a href="/">link</a> then <em>some text</em> end blob.</p>';
    public $testMultiP = '<p>p one</p><p>p two</p><p>p three</p><p>p four</p><p>p five</p>';

    public function testConstructor()
    {
        $factory = new DOCFactory();
        $this->assertTrue($factory instanceof DOCFactory);

        $factory = new DOCFactory(new DOCFactoryConfig(['strictErrorChecking', true]));
        $this->assertTrue($factory instanceof DOCFactory);
    }

    public function testInvocation()
    {
        $factory = new DOCFactory();
        $doc = $factory($this->testString);
        $this->assertTrue($doc instanceof \DOMNode);
        $this->assertEquals('body', $doc->nodeName);
        $this->assertEquals('p', $doc->firstChild->nodeName);
    }

    public function testStaticDomNode()
    {
        $doc = DOMDocumentFactory::getDomNode($this->testString);
        $this->assertTrue($doc instanceof \DOMNode);
        $this->assertEquals('body', $doc->nodeName);
        $this->assertEquals('p', $doc->firstChild->nodeName);
        $this->assertTrue($doc instanceof \DOMNode);
    }

    public function testGetNode()
    {
        $factory = new DOCFactory();
        $doc = $factory->getNode($this->testString);
        $this->assertTrue($doc instanceof \DOMNode);
        $this->assertEquals('body', $doc->nodeName);
        $this->assertEquals('p', $doc->firstChild->nodeName);
    }

    public function testGetDocument()
    {
        $factory = new DOCFactory();
        $doc = $factory->getDocument($this->testString);
        $this->assertEquals(1, $doc->getElementsByTagName('body')->count());
        $this->assertEquals(1, $doc->getElementsByTagName('p')->count());
        $this->assertEquals(1, $doc->getElementsByTagName('em')->count());
        $this->assertEquals(1, $doc->getElementsByTagName('a')->count());
    }

    public function testStringify()
    {
        $factory = new DOCFactory();
        $doc = $factory->getNode($this->testString);
        $this->assertEquals($this->testString, $factory->stringify($doc->firstChild));
    }

    public function testStringifyNode()
    {
        $doc = DOMDocumentFactory::getDomNode($this->testString);
        $this->assertEquals($this->testString, DOMDocumentFactory::stringifyNode($doc->firstChild));
    }

    public function testStringifyFromList()
    {
        $factory = new DOCFactory();
        $doc = $factory->getNode($this->testMultiP);
        $this->assertTrue($doc instanceof \DOMElement);
        if ($doc instanceof \DOMElement) {
            $this->assertEquals(5, \count($factory->stringifyFromList($doc->getElementsByTagName('p'))));
            $this->assertEquals(
                $this->testMultiP,
                \implode('', $factory->stringifyFromList($doc->getElementsByTagName('p')))
            );
        }
    }

    public function testStringifyNodeList()
    {
        $doc = DOMDocumentFactory::getDomNode($this->testMultiP);
        $this->assertTrue($doc instanceof \DOMElement);
        if ($doc instanceof \DOMElement) {
            $this->assertEquals(5, \count(DOMDocumentFactory::stringifyNodeList($doc->getElementsByTagName('p'))));
            $this->assertEquals(
                $this->testMultiP,
                \implode('', DOMDocumentFactory::stringifyNodeList($doc->getElementsByTagName('p')))
            );
        }
    }
}
