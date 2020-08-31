<?php

declare(strict_types=1);

namespace SimpleSAML\SAML2\XML\xenc;

use PHPUnit\Framework\TestCase;
use RobRichards\XMLSecLibs\XMLSecurityDsig;
use SimpleSAML\SAML2\Constants;
use SimpleSAML\SAML2\DOMDocumentFactory;
use SimpleSAML\SAML2\XML\Chunk;

/**
 * Class \SAML2\XML\xenc\CipherDataTest
 *
 * @covers \SimpleSAML\SAML2\XML\xenc\CipherData
 *
 * @author Tim van Dijen, <tvdijen@gmail.com>
 * @package simplesamlphp/saml2
 */
final class CipherDataTest extends TestCase
{
    /** @var \DOMDocument $document */
    private $document;


    /**
     * @return void
     */
    public function setup(): void
    {
        $this->document = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/resources/xml/xenc_CipherData.xml'
        );
    }


    // marshalling


    /**
     * @return void
     */
    public function testMarshallingCipherValue(): void
    {
        $cipherData = new CipherData('c29tZSB0ZXh0');

        $this->assertEquals('c29tZSB0ZXh0', $cipherData->getCipherValue());

        $this->assertEquals(
            $this->document->saveXML($this->document->documentElement),
            strval($cipherData)
        );
    }


    // unmarshalling


    /**
     * @return void
     */
    public function testUnmarshalling(): void
    {
        $cipherData = CipherData::fromXML($this->document->documentElement);

        $this->assertEquals('c29tZSB0ZXh0', $cipherData->getCipherValue());
    }


    /**
     * Test serialization / unserialization
     */
    public function testSerialization(): void
    {
        $this->assertEquals(
            $this->document->saveXML($this->document->documentElement),
            strval(unserialize(serialize(CipherData::fromXML($this->document->documentElement))))
        );
    }
}
