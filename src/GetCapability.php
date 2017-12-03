<?php

    namespace MisterBrownRSA\DHL\API;

    class GetCapability extends APIAbstract
    {
        private $fromCountryCode;
        private $fromPostalCode;
        private $fromCity;

        private $toCountryCode;
        private $toPostalCode;
        private $toCity;

        private $timeZone;
        private $dimensionUnit;
        private $weightUnit;

        public function __construct()
        {
            parent::__construct();
            $this->fromCountryCode = getenv('DHL_COUNTRYCODE') ?: config('dhl.tas.DHL_COUNTRYCODE');
            $this->fromPostalCode = getenv('DHL_POSTALCODE') ?: config('dhl.tas.DHL_POSTALCODE');
            $this->fromCity = getenv('DHL_CITY') ?: config('dhl.tas.DHL_CITY');
            $this->timeZone = "+02:00";
            $this->dimensionUnit = 'CM';
            $this->weightUnit = 'KG';
        }

        public function toXML()
        {
            $xml = new \XmlWriter();
            $xml->openMemory();
            $xml->setIndent(TRUE);
            $xml->setIndentString("  ");
            $xml->startDocument('1.0', 'UTF-8');

            $xml->startElement('p:DCTRequest');
            $xml->writeAttribute('xmlns:p', "http://www.dhl.com");
            $xml->writeAttribute('xmlns:p1', "http://www.dhl.com/datatypes");
            $xml->writeAttribute('xmlns:p2', "http://www.dhl.com/DCTRequestdatatypes");
            $xml->writeAttribute('xmlns:xsi', "http://www.w3.org/2001/XMLSchema-instance");
            $xml->writeAttribute('xsi:schemaLocation', "http://www.dhl.com DCT-req.xsd");
            $xml->startElement('GetCapability');
            $xml->startElement('Request');
            $xml->startElement('ServiceHeader');
            $xml->writeElement('MessageTime', date('Y-m-d') . "T" . date('H:i:s') . ".000+02:00");
            $xml->writeElement('MessageReference', "1234567890123456789012345678901"); //
            $xml->writeElement('SiteID', $this->username);
            $xml->writeElement('Password', $this->password);
            $xml->endElement();
            $xml->endElement();

            $xml->startElement('From');
            $xml->writeElement('CountryCode', $this->fromCountryCode); //
            $xml->writeElement('Postalcode', $this->fromPostalCode); //
            $xml->writeElement('City', $this->fromCity); //
            $xml->endElement();

            $xml->startElement('BkgDetails');
            $xml->writeElement('PaymentCountryCode', $this->fromCountryCode);
            $xml->writeElement('Date', date('Y-m-d'));
            $xml->writeElement('ReadyTime', 'PT12H00M');
            $xml->writeElement('ReadyTimeGMTOffset', $this->timeZone);
            $xml->writeElement('DimensionUnit', $this->dimensionUnit);
            $xml->writeElement('WeightUnit', $this->weightUnit);
            $xml->writeElement('NumberOfPieces', '1'); //#Optional - use when no pieces are registered
            //removing pieces since we're just testing address with capability
//                $xml->startElement('Pieces');
//                    $xml->startElement('Piece');
//                    $xml->writeElement('PieceID', '1'); //
//                    $xml->writeElement('Height', '30'); //
//                    $xml->writeElement('Depth', '20'); //
//                    $xml->writeElement('Width', '10'); //
//                    $xml->writeElement('Weight', '1.6'); //
//                    $xml->endElement();
//                $xml->endElement();
            $xml->endElement();

            $xml->startElement('To');
            $xml->writeElement('CountryCode', $this->toCountryCode); //
            if ($this->toPostalCode != NULL) {
                $xml->writeElement('Postalcode', $this->toPostalCode()); //
            }
            $xml->writeElement('City', $this->toCity); //
            $xml->endElement();
            $xml->endElement();
            $xml->endElement();
            $xml->endDocument();

            return $this->document = $xml->outputMemory();
        }

        public function validate()
        {
            if (empty($this->results)) {
                $this->doCurlPost();
            }

            return (!isset($this->results->GetCapabilityResponse->Note));
        }

        public function toCountryCode($value = NULL)
        {
            if (empty($value)) {
                return $this->toCountryCode;
            }

            $this->toCountryCode = $value;

            return $this;
        }

        public function toPostalCode($value = NULL)
        {
            if (empty($value)) {
                return $this->toPostalCode;
            }

            $this->toPostalCode = $value;

            return $this;
        }

        public function toCity($value = NULL)
        {
            if (empty($value)) {
                return $this->toCity;
            }

            $this->toCity = $value;

            return $this;
        }

        public function fromCountryCode($value = NULL)
        {
            if (empty($value)) {
                return $this->fromCountryCode;
            }

            $this->fromCountryCode = $value;

            return $this;
        }

        public function fromPostalCode($value = NULL)
        {
            if (empty($value)) {
                return $this->toPostalCode;
            }

            $this->toPostalCode = $value;

            return $this;
        }

        public function fromCity($value = NULL)
        {
            if (empty($value)) {
                return $this->fromCity;
            }

            $this->fromCity = $value;

            return $this;
        }

        public function from($values = NULL)
        {

            if (empty($values)) {
                return [
                    'COUNTRYCODE' => $this->fromCountryCode,
                    'POSTALCODE'  => $this->fromPostalCode,
                    'CITY'        => $this->fromCity,
                ];
            }

            /*TODO:: validation*/

            $this->fromCountryCode = $values['COUNTRYCODE'];
            $this->fromPostalCode = $values['POSTALCODE'];
            $this->fromCity = $values['CITY'];

            return $this;
        }

        public function user($user)
        {
            return $this->toCity($user->addresses()->primary()->town)
                ->toCountryCode($user->addresses()->primary()->country->code)
                ->toPostalCode($user->addresses()->primary()->postal_code);
        }

        public function timeZone($value = NULL)
        {
            if (empty($value)) {
                return $this->timeZone;
            }

            $this->timeZone = $value;

            return $this;
        }

        public function dimensionUnit()
        {
            if (empty($value)) {
                return $this->dimensionUnit;
            }

            $this->dimensionUnit = $value;

            return $this;
        }

        public function weightUnit()
        {
            if (empty($value)) {
                return $this->weightUnit;
            }

            $this->weightUnit = $value;

            return $this;
        }
    }