<?php

declare(strict_types=1);

namespace SimpleSAML\SAML2\XML\xenc;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML2\Exception\InvalidDOMElementException;
use SimpleSAML\SAML2\Utils;

/**
 * Class representing <xenc:CipherData>.
 *
 * @package simplesamlphp/saml2
 */
class CipherData extends AbstractXencElement
{
    /** @var string|null */
    protected $cipherValue = null;

    /** @var \SAML2\XML\xenc\CipherReference|null */
    protected $cipherReference = null;


    /**
     * CipherData constructor.
     *
     * @param string|null $cipherValue
     * @param \SAML2\XML\xenc\CipherReference|null $cipherReference
     */
    public function __construct(?string $cipherValue, ?CipherReference $cipherReference = null)
    {
        Assert::oneOf(
            null,
            [$cipherValue, $cipherReference],
            'Can only have one of CipherValue/CipherReference'
        );

        if (!is_null($cipherValue)) {
            $this->setCipherValue($cipherValue);
        } else {
            $this->setCipherReference($cipherReference);
        }
    }


    /**
     * Get the string value of the <xenc:CipherValue> element inside this CipherData object.
     *
     * @return string
     */
    public function getCipherValue(): string
    {
        return $this->cipherValue;
    }


    /**
     * @param string $cipherValue
     */
    protected function setCipherValue(string $cipherValue): void
    {
        Assert::regex($cipherValue, '/[a-zA-Z0-9_\-=\+\/]/', 'Invalid data in <xenc:CipherValue>.');
        $this->cipherValue = $cipherValue;
    }


    /**
     * Get the CipherReference element inside this CipherData object.
     *
     * @return \SAML2\XML\xenc\CipherReference
     */
    public function getCipherReference(): CipherReference
    {
        return $this->cipherReference;
    }


    /**
     * @param \SAML2\XML\xenc\CipherReference $cipherReference
     */
    protected function setCipherReference(CipherReference $cipherReference): void
    {
        $this->cipherReference = $cipherReference;
    }


    /**
     * @inheritDoc
     *
     * @throws \SimpleSAML\SAML2\Exception\InvalidDOMElementException if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, 'CipherData', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, CipherData::NS, InvalidDOMElementException::class);

        $cv = Utils::xpQuery($xml, './xenc:CipherValue');
        Assert::maxCount($cv, 1, 'More than one CipherValue element in <xenc:CipherData');

        $cr = CipherReference::getChildrenOfClass($xml);
        Assert::maxCount($cr, 1, 'More than one CipherReference element in <xenc:CipherData');

        return new self(
            empty($cv) ? null : $cv[0]->textContent,
            empty($cr) ? null : array_pop($cr)
        );
    }


    /**
     * @inheritDoc
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        /** @psalm-var \DOMDocument $e->ownerDocument */
        $e = $this->instantiateParentElement($parent);

        if ($this->cipherValue != null) {
            Utils::addString($e, $this::NS, 'CipherValue', $this->cipherValue);
        }

        if ($this->cipherReference != null) {
            $this->cipherReference->toXML($e);
        }

        return $e;
    }
}
