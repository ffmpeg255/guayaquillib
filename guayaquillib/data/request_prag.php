<?php

require_once('request_base.php');

class GuayaquilRequestPrag extends GuayaquilRequestBase
{

    function __construct($locale = 'ru_RU', IGuayquilCache $cache = null)
    {
        parent::__construct($locale, $cache, 'prag');
    }

    public function appendFindOffers($brand, $oem, $resultIndex = 'FindOffers')
    {
        $this->appendCommand('FindOffers', array('Locale'=>$this->locale, 'Brand' => $brand, 'OEM' => $oem), $resultIndex);
    }

}
?>