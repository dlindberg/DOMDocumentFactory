<?php

declare(strict_types=1);

namespace dlindberg\DOMDocumentFactory;

use \dlindberg\DOMDocumentFactory\DOMDocumentFactoryConfig as DOCFactoryConfig;

final class DOCFactoryConfigTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $config = new DOCFactoryConfig();
        $this->assertTrue($config instanceof DOCFactoryConfig);
        $config = new DOCFactoryConfig([
            'version'             => '1.1',
            'encoding'            => 'ISO-8859-1',
            'recover'             => false,
            'preserveWhiteSpace'  => true,
            'formatOutput'        => false,
            'DOMOptions'          => null,
            'optionsDoesntExist'  => true,
        ]);
        $this->assertEquals('1.1', $config->version);
        $this->assertEquals('ISO-8859-1', $config->encoding);
        $this->assertFalse($config->recover);
        $this->assertTrue($config->preserveWhiteSpace);
        $this->assertFalse($config->formatOutput);
        $this->assertNull($config->DOMOptions);
        $string = '<p><a href="/">test string</a></p>';
        $pureConfig = \HTMLPurifier_Config::createDefault();
        $pureConfig->set('HTML.ForbiddenElements', ['a']);
        $purifier = new \HTMLPurifier($pureConfig);
        $config = new DOCFactoryConfig([
            'version'  => '1.1',
            'encoding' => 'ISO-8859-1',
        ], new \HTMLPurifier($pureConfig), new \HTMLPurifier($pureConfig));

        $this->assertEquals(
            '<?xml version="1.1" encoding="ISO-8859-1"?>' . $purifier->purify($string),
            $config->loadString($string)
        );
        $this->assertEquals($purifier->purify($string), $config->outputString($string));
        $this->assertNotEquals(
            $purifier->purify($string),
            $config->setOutputPurifier(null)->outputString($string)
        );
    }

    public function testDocType()
    {
        $config = new DOCFactoryConfig();
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>', $config->getDocType());
    }

    public function testSetOption()
    {
        $config = new DOCFactoryConfig();
        $this->assertTrue($config->recover);
        $config->setOption(false, 'recover');
        $this->assertFalse($config->recover);
    }

    public function testLoadString()
    {
        $config = new DOCFactoryConfig();
        $string = '<p>test string</p>';
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . $string, $config->loadString($string));
    }

    public function testSetInputPurifier()
    {
        $config = new DOCFactoryConfig();
        $string = '<p><a href="/">test string</a></p>';
        $pureConfig = \HTMLPurifier_Config::createDefault();
        $pureConfig->set('HTML.ForbiddenElements', ['a']);
        $purifier = new \HTMLPurifier($pureConfig);
        $this->assertNotEquals(
            '<?xml version="1.0" encoding="UTF-8"?>' . $purifier->purify($string),
            $config->loadString($string)
        );
        $config->setInputPurifier($purifier);
        $this->assertEquals(
            '<?xml version="1.0" encoding="UTF-8"?>' . $purifier->purify($string),
            $config->loadString($string)
        );
    }

    public function testSetOutputPurifier()
    {
        $config = new DOCFactoryConfig();
        $string = '<p><a href="/">test string</a></p>';
        $pureConfig = \HTMLPurifier_Config::createDefault();
        $pureConfig->set('HTML.ForbiddenElements', ['a']);
        $purifier = new \HTMLPurifier($pureConfig);
        $this->assertNotEquals($purifier->purify($string), $config->outputString($string));
        $config->setOutputPurifier($purifier);
        $this->assertEquals($purifier->purify($string), $config->outputString($string));
        $this->assertNotEquals(
            $purifier->purify($string),
            $config->setOutputPurifier(null)->outputString($string)
        );
    }
}
