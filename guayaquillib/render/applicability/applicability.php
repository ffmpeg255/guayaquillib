<?php

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'template.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'details'.DIRECTORY_SEPARATOR.'detailslist.php';

class GuayaquilApplicability extends GuayaquilTemplate
{
    var $priorColumns = array();
    var $columns = array();
    var $vehicles = NULL;
    var $datacolumns = NULL;
    var $catalog = NULL;
    public $oem;
    private $vehicleNo = 0;

    function __construct(IGuayaquilExtender $extender, $oem) {
        parent::__construct($extender);
        $this->AppendJavaScript(dirname(__FILE__) . '/../jquery.tooltip.js');
        $this->AppendJavaScript(dirname(__FILE__).'/applicability.js');
        $this->oem = $oem;
    }

    function Draw($data) {
        $dataType = (string)$data['type'];

        if ($dataType == 'brand') {
            $html = $this->DrawBrandSelector($data);
        } else {
            $html = $this->DrawVehicles($data);
        }

        $html .= '<div style="visibility: visible; display: block; height: 20px; text-align: right;"><a href="http://dev.laximo.ru" rel="follow" style="visibility: visible; display: inline; font-size: 10px; font-weight: normal; text-decoration: none;">guayaquil</a></div>';

        return $html;
    }

    function DrawBrandSelector($brandOems) {
        $html = $this->getBrandSelectorTitle();
        $html .= '<table border="0">';

        foreach ($brandOems as $brandOem) {
            $brand = (string)$brandOem['brand'];
            $oem = (string)$brandOem['oem'];

            $link = $this->FormatLink('brand', $brandOem, null);

            $html .= '<tr><td><a href="'.$link.'">'.$brand.'</a></td><td><a href="'.$link.'">'.$oem.'</a></td></tr>';
        }

        $html .= '</table>';

        return $html;
    }

    function getBrandSelectorTitle() {
        return $this->GetLocalizedString("ApplicabilityBrandSelectorTitle");
    }

    function DrawVehicles($vehicles) {
        $this->vehicles = $vehicles;
        $data_columns = array();

        $columns = array('brand'=>$this->GetLocalizedString('ColumnVehicleBrand'),'name'=>$this->GetLocalizedString('ColumnVehicleName'));

        foreach ($vehicles->row->attributes() as $key => $value) {
            $data_columns[strtolower($key)] = (string)$value;
            $columns[strtolower($key)] = $this->GetLocalizedString('ColumnVehicle'.$key);
        }

        foreach ($vehicles->row->attribute as $attr) {
            $data_columns[strtolower($attr->attributes()->key)] = (string)$attr->attributes()->value;
            $columns[strtolower($attr->attributes()->key)] = (string) $attr->attributes()->name;
        }

        $this->datacolumns = $data_columns;
        $this->priorColumns = $this->columns;
        $this->columns = $columns;
        $html = '<table class="guayaquil_table" border=1 width="100%">';
        $html .= $this->DrawHeader();

        foreach ($vehicles->row as $row) {
            $html .= $this->DrawVehicle($row, (string)$row->attributes()->catalog);
        }

        $html .= '</table>';

        return $html;
    }

    function DrawHeader()
    {
        $html = '<tr>';

        foreach ($this->columns as $key=>$column){
            if (isset($this->datacolumns[$key])&&(in_array($key, $this->priorColumns))){
                $html .= $this->DrawHeaderCell($column);
            }
        }
        $html .= '<th style = "display:none">tooltip</th>';
        $html .= '</tr>';
        return $html;
    }

    function DrawHeaderCell($column)
    {
        return '<th>' . $this->DrawHeaderCellValue($column) . '</th>';
    }

    function DrawHeaderCellValue($column)
    {
        return $column;//$this->GetLocalizedString('ColumnVehicle' . (string)$column);
    }

    function PrepareVehicleInfo($row)
    {
        $info = "";

        foreach ($this->columns as $key=>$col_name)
            if (isset($row[$key])) {
                $c = @$row[$col_name];
                if ($info) $info .= "; ";
                $col_title = $col_name;//$this->GetLocalizedString('ColumnVehicle' . (string)$col_name);
                $info .= $col_title . ": " . $c;
            }

        return $info;
    }

    function DrawVehicle($vehicle)
    {
        $this->vehicleNo ++;
        $rowСode = "grow_" . $this->vehicleNo;
        $catalogCode = (string)$vehicle['catalog'];

        $html = '<tr onmouseout="this.className=\'\';" onmouseover="this.className=\'over\';" onclick="jQuery(\'tr[name='.$rowСode.']\').toggle()" class="g_vehicle">';

        $tooltip = '';

        foreach ($this->columns as $key=>$column)
        {
            if (isset($this->datacolumns[$key])){
                if (in_array($key, $this->priorColumns))
                    $html .= $this->DrawVehicleCell($vehicle, strtolower($key));
                $tooltip.=$this->DrawVehicleToolTipValue($vehicle, strtolower($key), (string)$column);
            }
        }
        $html .= '<td class = "ttp" style = "display:none;>'.$tooltip.'</td>';
        $html .= '</tr>';

        $html .= $this->DrawUnits($vehicle, $rowСode, $catalogCode);

        return $html;
    }

    function DrawVehicleCell($row, $column)
    {
        return '<td>' . $this->DrawVehicleCellValue($row, $column) . '</td>';
    }

    function DrawVehicleToolTipValue($row, $column, $name)
    {
        foreach ($row->attributes() as $key => $value){
            if (strtolower($key) == $column){
                return '<span class = "item">'. (string)$name . ':'  . (string)$value . '</span>';
            }
        }
        foreach ($row->attribute as $attr){
            if (strtolower($attr->attributes()->key) == $column){
                return '<span class = "item">'. (string)$name . ':' . (string)$attr->attributes()->value . '</span>';
            }
        }
    }

    function DrawVehicleCellValue($row, $column)
    {
        foreach ($row->attributes() as $key => $value)
            if (strtolower($key) == $column)
                return $value;

        foreach ($row->attribute as $attr)
            if (strtolower($attr->attributes()->key) == $column)
                return (string)$attr->attributes()->value;
    }

    function DrawUnits($vehicle, $code, $catalog) {
        $html = '';

        foreach ($vehicle->Unit as $unit) {
            $html .= $this->DrawUnit($unit, $code, $catalog);
        }

        return $html;
    }

    function DrawUnit($unit, $code, $catalog)
    {
        $html = '<tr name="'.$code.'" style="display:none" class="g_unit"><td colspan="100">';

        $link = $this->FormatLink('unit', $unit, $catalog);

        $html .= '<a href="'.$link.'">'.$unit['name'].'</a>';

        $html .= '<div class="g_hint" style="display:none;">';
        foreach ($unit->attribute as $attr)
            $html .= '<b>'.(string) ($attr->attributes()->name) .'</b>: ' .(string) $attr->attributes()->value.'<br/>';
        $html .= '</div>';

        $html .= '</td></tr>';

        return $html;
    }

}
