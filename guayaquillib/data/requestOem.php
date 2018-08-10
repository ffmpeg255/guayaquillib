<?php

require_once('soap.php');

interface IGuayquilCache
{
    function GetCachedData($request);
    function PutCachedData($request, $data);
}


class GuayaquilRequestOEM
{
	//	Function parameters
    protected $locale;
    protected $catalog;
    protected $ssd;
    protected $cache;

	// Temporary varibles
    protected $queries = array();

	// soap wrapper object
    /** @var \GuayaquilSoapWrapper */
	private $soap;

	//	Results
	public $error;
    public $data;

    function __construct($catalog = '', $ssd = '', $locale = 'ru_RU', IGuayquilCache $cache = null)
    {
        $this->locale = $this->checkParam($locale);
        $this->catalog = $this->checkParam($catalog);
        $this->ssd = $this->checkParam($ssd);
        $this->cache = $cache;
        $this->soap = new GuayaquilSoapWrapper();
        $this->soap->setCertificateAuthorizationMethod();
    }

    public function setUserAuthorizationMethod($login, $key)
    {
        $this->soap->setUserAuthorizationMethod($login, $key);
    }

    function checkParam($value)
	{
		return $value;
	}

	function appendCommand($command, $params)
	{
        $item = new stdClass();
        $item->command = $command;
        $item->params = $params;
        if (isset($params) && is_array($params))
        {
            $command .= ':';
            $first = true;
            foreach ($params as $key=>$value)
            {
                if ($first)
                    $first = false;
                else
                    $command .= '|';

                $command .= $key.'='.$value;
            }

            $item->command_text = $command;
        }
        else
            $item->command_text = $command;

        $this->queries[] = $item;
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

    function appendGetWizard2($ssd = false)
    {
        $this->appendCommand('GetWizard2', array('Locale' => $this->locale, 'Catalog' => $this->catalog, 'ssd' => $this->checkParam($ssd)));
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

    function appendFindVehicleCustom($searchType, $searchParams)
    {
        $params = array('Locale' => $this->locale, 'Catalog' => $this->catalog, 'Code' => $this->checkParam($searchType));
        $this->appendCommand('FindVehicleCustom', $searchParams && is_array($searchParams) ? array_merge($params, $searchParams) : $params);
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


    public function appendExecCustomOperation($operation, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }

        $this->appendCommand('ExecCustomOperation', array_merge(array('Locale' => $this->locale, 'Catalog' => $this->catalog, 'operation' => $this->checkParam($operation)), $data));
    }

    function query()
	{
        $result = array();
        $request = array();
        $count = count($this->queries);

        for ($index = 0; $index < $count; $index++)
            if ($this->cache)
            {
                // Try get data from local cache
                $data = $this->cache->GetCachedData($this->queries[$index]);
                if ($data)
                {
                    if (!is_object($data))
                        $data = simplexml_load_string($data);

                    $result[$index] = $data;
                    $request[$index] = null;
                }
                else
                {
                    $request[$index] = $this->queries[$index];
                    $result[$index] = null;
                }
            }
            else
            {
                $result[$index] = null;
                $request[$index] = $this->queries[$index];
            }

        $commands_index = 0;
        $query = '';
        $indexes = array();
        for ($index = 0; $index < $count; $index ++)
            if ($request[$index])
            {
                if ($query)
                    $query .= "\n";
                $query .= $request[$index]->command_text;
                $indexes[] = $index;

                if ($commands_index == 5)
                {
                    if (!$this->_query($query, $indexes, $result))
                        return false;
                    
                    $commands_index = 0;
                    $query = '';
                    $indexes = array();
                }
                
                $commands_index ++;
            }

        if ($commands_index > 0)
            (!$this->_query($query, $indexes, $result));

        $this->data = $result;

        $this->queries = array();

		return $result;
	}

    function _query($query, $indexes, &$result)
    {
        $data = $this->soap->queryData($query);
        if ($this->soap->getError())
        {
            $this->error = $this->soap->getError();
            return false;
        }

        $data = simplexml_load_string($data);
        $index = 0;

        //  Merge results
        foreach ($data->children() as $row)
        {
            $result[$indexes[$index]] = $row;

            if ($this->cache)   // Put in cache
                $this->cache->PutCachedData($this->queries[$indexes[$index]], $row->asXML());

            $index ++;
        }
    }
}
?>
