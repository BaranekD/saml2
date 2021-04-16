<?php

declare(strict_types=1);

namespace SimpleSAML\SAML2\XML\md;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\XMLStringElementTrait;

/**
 * Class implementing AffiliateMember.
 *
 * @package simplesamlphp/saml2
 */
final class AffiliateMember extends AbstractMdElement
{
    use XMLStringElementTrait;


    /**
     * Validate the content of the element.
     *
     * @param string $content  The value to go in the XML textContent
     * @throws \Exception on failure
     * @return void
     */
    protected function validateContent(string $content): void
    {
        Assert::notEmpty($content, 'AffiliateMember cannot be empty');
        Assert::maxLength($content, 1024, 'The entityID attribute cannot be longer than 1024 characters.');
    }


    /**
     * Convert XML into a AffiliateMember
     *
     * @param \DOMElement $xml The XML element we should load
     * @return self
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, 'AffiliateMember', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, AffiliateMember::NS, InvalidDOMElementException::class);

        return new self($xml->textContent);
    }
}
