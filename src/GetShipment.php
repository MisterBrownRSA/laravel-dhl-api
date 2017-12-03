<?php

    namespace MisterBrownRSA\DHL\API;

    class GetShipment extends APIAbstract
    {
        private $toCountry;
        public function __construct()
        {
            parent::__construct();
        }
    }