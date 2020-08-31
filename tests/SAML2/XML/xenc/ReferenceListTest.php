<?php

declare(strict_types=1);

namespace SimpleSAML\SAML2\XML\xenc;

use PHPUnit\Framework\TestCase;
use RobRichards\XMLSecLibs\XMLSecurityDsig;
use SimpleSAML\SAML2\Constants;
use SimpleSAML\SAML2\DOMDocumentFactory;
use SimpleSAML\SAML2\XML\Chunk;

/**
 * Class \SimpleSAML\SAML2\XML\xenc\ReferenceListTest
 *
 * @covers \SimpleSAML\SAML2\XML\xenc\ReferenceList
 *
 * @author Tim van Dijen, <tvdijen@gmail.com>
 * @package simplesamlphp/saml2
 */
final class ReferenceListTest extends TestCase
{
    /** @var \DOMDocument $document */
    private $document;

    /** @var \SAML2\Chunk $dataReference */
    private $dataReference;

    /** @var \SAML2\Chunk $keyReference */
    private $keyReference;


    /**
     * @return void
     */
    public function setup(): void
    {
        $dsNamespace = XMLSecurityDSig::XMLDSIGNS;

        $this->document = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/resources/xml/xenc_ReferenceList.xml'
        );

        $this->dataReference = new Chunk(DOMDocumentFactory::fromString(<<<XML
    <ds:Transforms xmlns:ds="{$dsNamespace}">
      <ds:Transform Algorithm="http://www.w3.org/TR/1999/REC-xpath-19991116">
        <ds:XPath xmlns:xenc="http://www.w3.org/2001/04/xmlenc#">
          self::xenc:EncryptedData[@Id="example1"]
        </ds:XPath>
      </ds:Transform>
    </ds:Transforms>
XML
        )->documentElement);

        $this->keyReference = new Chunk(DOMDocumentFactory::fromString(<<<XML
    <ds:Transforms xmlns:ds="{$dsNamespace}">
      <ds:Transform Algorithm="http://www.w3.org/TR/1999/REC-xpath-19991116">
        <ds:XPath xmlns:xenc="http://www.w3.org/2001/04/xmlenc#">
          self::xenc:EncryptedKey[@Id="example1"]
        </ds:XPath>
      </ds:Transform>
    </ds:Transforms>
XML
        )->documentElement);
    }


    // marshalling


    /**
     * @return void
     */
    public function testMarshalling(): void
    {
        $referenceList = new ReferenceList(
            [new DataReference('#Encrypted_DATA_ID', [$this->dataReference])],
            [new KeyReference('#Encrypted_KEY_ID', [$this->keyReference])]
        );

        $dataReferences = $referenceList->getDataReferences();
        $this->assertCount(1, $dataReferences);

        $keyReferences = $referenceList->getKeyReferences();
        $this->assertCount(1, $keyReferences);

        $this->assertEquals([$this->dataReference], $dataReferences[0]->getReferences());
        $this->assertEquals([$this->keyReference], $keyReferences[0]->getReferences());

        $this->assertEquals(
            $this->document->saveXML($this->document->documentElement),
            strval($referenceList)
        );
    }


    // unmarshalling


    /**
     * @return void
     */
    public function testUnmarshalling(): void
    {
        $referenceList = ReferenceList::fromXML($this->document->documentElement);

        $dataReferences = $referenceList->getDataReferences();
        $this->assertCount(1, $dataReferences);

        $keyReferences = $referenceList->getKeyReferences();
        $this->assertCount(1, $keyReferences);

        $this->assertEquals([$this->dataReference], $dataReferences[0]->getReferences());
        $this->assertEquals([$this->keyReference], $keyReferences[0]->getReferences());

        $this->assertEquals(
            $this->document->saveXML($this->document->documentElement),
            strval($referenceList)
        );
    }


    /**
     * Test serialization / unserialization
     */
    public function testSerialization(): void
    {
        $this->assertEquals(
            $this->document->saveXML($this->document->documentElement),
            strval(unserialize(serialize(ReferenceList::fromXML($this->document->documentElement))))
        );
    }
}
