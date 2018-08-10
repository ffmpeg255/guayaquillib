<?php

require_once('soap.php');
require_once('request_base.php');

class GuayaquilRequest extends GuayaquilRequestBase
{
    var $catalog;
    var $ssd;

    function __construct($catalog = false, $ssd = false, $locale = 'ru_RU', IGuayquilCache $cache = null)
    {
        parent::__construct($locale, $cache, 'oem');
        $this->catalog = $catalog;
        $this->ssd = $ssd;
    }


    function appendGetCatalogInfo()
    {
		$this->appendCommand('GetCatalogInfo', array('Locale' => $this->locale, 'Catalog' => $this->catalog, 'ssd' => $this->ssd));
    }	

    function appendListCatalogs()
    {
		$this->appendCommand('ListCatalogs', array('Locale' => $this->locale, 'ssd' => $this->ssd));
    }	

    function appendFindVehicleByVIN($vin)
    {
		$this->appendCommand('FindVehicleByVIN', array('Locale' => $this->locale, 'Catalog' => $this->catalog, 'VIN' => $this->checkParam($vin), 'ssd' => $this->ssd, 'Localized' => 'true'));
    }	

    function appendFindVehicleByFrame($frame, $frameNo)
    {
        $this->appendCommand('FindVehicleByFrame', array('Locale' => $this->locale, 'Catalog' => $this->catalog, 'Frame' => $this->checkParam($frame), 'FrameNo' => $this->checkParam($frameNo), 'ssd' => $this->ssd, 'Localized' => 'true'));
    }

    /**
     * @deprecated
     */
    function appendFindVehicleByWizard($wizardid)
    {
        $this->appendCommand('FindVehicleByWizard', array('Locale' => $this->locale, 'Catalog' => $this->catalog, 'WizardId' => $this->checkParam($wizardid), 'ssd' => $this->ssd, 'Localized' => 'true'));
    }

    function appendFindVehicleByWizard2($ssd)
    {
        $this->appendCommand('FindVehicleByWizard2', array('Locale' => $this->locale, 'Catalog' => $this->catalog, 'ssd' => $this->checkParam($ssd), 'Localized' => 'true'));
    }

    function appendGetVehicleInfo($vehicleid)
    {
        $this->appendCommand('GetVehicleInfo', array('Locale' => $this->locale, 'Catalog' => $this->catalog, 'VehicleId' => $this->checkParam($vehicleid), 'ssd' => $this->ssd, 'Localized' => 'true'));
    }

    function appendListCategories($vehicleid, $categoryid)
    {
        $this->appendCommand('ListCategories', array('Locale' => $this->locale, 'Catalog' => $this->catalog, 'VehicleId' => $this->checkParam($vehicleid), 'CategoryId' => $this->checkParam($categoryid), 'ssd' => $this->ssd));
    }

    function appendListUnits($vehicleid, $categoryid)
    {
        $this->appendCommand('ListUnits', array('Locale' => $this->locale, 'Catalog' => $this->catalog, 'VehicleId' => $this->checkParam($vehicleid), 'CategoryId' => $this->checkParam($categoryid), 'ssd' => $this->ssd, 'Localized' => 'true'));
    }

    function appendGetUnitInfo($unitid)
    {
        $this->appendCommand('GetUnitInfo', array('Locale' => $this->locale, 'Catalog' => $this->catalog, 'UnitId' => $this->checkParam($unitid), 'ssd' => $this->ssd, 'Localized' => 'true'));
    }

    function appendListImageMapByUnit($unitid)
    {
        $this->appendCommand('ListImageMapByUnit', array('Catalog' => $this->catalog, 'UnitId' => $this->checkParam($unitid), 'ssd' => $this->ssd));
    }

    function appendListDetailByUnit($unitid)
    {
        $this->appendCommand('ListDetailByUnit', array('Locale' => $this->locale, 'Catalog' => $this->catalog, 'UnitId' => $this->checkParam($unitid), 'ssd' => $this->ssd, 'Localized' => 'true'));
    }

    /**
     * @deprecated
     */
    function appendGetWizard($wizardid = '', $valueid = '')
    {
        $this->appendCommand('GetWizard', array('Locale' => $this->locale, 'Catalog' => $this->catalog, 'WizardId' => $this->checkParam($wizardid), 'ValueId' => $this->checkParam($valueid)));
    }

    function appendGetWizard2($ssd = false)
    {
        $this->appendCommand('GetWizard2', array('Locale' => $this->locale, 'Catalog' => $this->catalog, 'ssd' => $this->checkParam($ssd)));
    }

    function appendGetWizardNextStep2($ssd = false)
    {
        $this->appendCommand('GetWizardNextStep2', array('Locale' => $this->locale, 'Catalog' => $this->catalog, 'ssd' => $this->checkParam($ssd)));
    }

    function appendGetFilterByUnit($filter, $vehicle_id, $unit_id)
    {
        $this->appendCommand('GetFilterByUnit', array('Locale' => $this->locale, 'Catalog' => $this->catalog, 'Filter' => $this->checkParam($filter), 'VehicleId' => $this->checkParam($vehicle_id), 'UnitId' => $this->checkParam($unit_id), 'ssd' => $this->ssd));
    }

    function appendGetFilterByDetail($filter, $vehicle_id, $unit_id, $detail_id)
    {
        $this->appendCommand('GetFilterByDetail', array('Locale' => $this->locale, 'Catalog' => $this->catalog, 'Filter' => $this->checkParam($filter), 'VehicleId' => $this->checkParam($vehicle_id), 'UnitId' => $this->checkParam($unit_id), 'DetailId' => $this->checkParam($detail_id), 'ssd' => $this->ssd));
    }

    function appendListQuickGroup($vehicle_id)
    {
        $this->appendCommand('ListQuickGroup', array('Locale' => $this->locale, 'Catalog' => $this->catalog, 'VehicleId' => $this->checkParam($vehicle_id), 'ssd' => $this->ssd));
    }

    function appendListQuickDetail($vehicle_id, $group_id, $all = 0)
    {
        $params = array('Locale' => $this->locale, 'Catalog' => $this->catalog, 'VehicleId' => $this->checkParam($vehicle_id), 'QuickGroupId' => $group_id, 'ssd' => $this->ssd, 'Localized' => 'true');

        if ($all)
            $params['All'] = 1;

        $this->appendCommand('ListQuickDetail', $params);
    }

    function appendFindDetailApplicability($oem, $brand = '')
    {
        $this->appendCommand('FindDetailApplicability', array('Locale' => $this->locale, 'OEM' => $this->checkParam($oem), 'Brand' => $brand, 'Localized' => 'true'));
    }

}
?>
