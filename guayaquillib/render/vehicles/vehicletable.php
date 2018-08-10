<?php

require_once dirname(__FILE__) . '/../template.php';

class GuayaquilVehiclesList extends GuayaquilTemplate
{
    var $priorColumns = array();
    var $columns = array();
    var $vehicles = NULL;
    var $datacolumns = NULL;
    var $catalog = NULL;

    function __construct(IGuayaquilExtender $extender)
    {
        parent::__construct($extender);

        $this->AppendJavaScript(dirname(__FILE__) . '/../jquery.tooltip.js');
        $this->AppendJavaScript(dirname(__FILE__) . '/vehicletable.js');
    }

    function Draw($catalog, $vehicles)
    {
        $this->vehicles = $vehicles;
        $this->catalog = $catalog;
        $data_columns = array();

        $columns = array('brand' => $this->GetLocalizedString('ColumnVehicleBrand'), 'name' => $this->GetLocalizedString('ColumnVehicleName'));

        foreach ($vehicles->row as $row) {
            foreach ($row->attributes() as $key => $value) {
                $data_columns[strtolower($key)] = (string)$value;
            }

            foreach ($row->attribute as $attr) {
                $data_columns[strtolower($attr->attributes()->key)] = (string)$attr->attributes()->value;
                $columns[strtolower($attr->attributes()->key)] = (string)$attr->attributes()->name;
            }
        }

        $this->datacolumns = $data_columns;
        $this->priorColumns = $this->columns;
        $this->columns = $columns;
        $html = '<table class="guayaquil_table" border=1 width="100%">';
        $html .= $this->DrawHeader();

        foreach ($vehicles->row as $row)
            $html .= $this->DrawRow($row, $catalog);

        $html .= '</table>';

        return $html;
    }

    function DrawHeader()
    {
        $html = '<tr>';

        foreach ($this->columns as $key => $column) {
            if (isset($this->datacolumns[$key]) && (in_array($key, $this->priorColumns))) {
                $html .= $this->DrawHeaderCell(strtolower($column));
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
        return $column; //$this->GetLocalizedString('ColumnVehicle' . (string)$column);
    }

    function PrepareVehicleInfo($row)
    {
        $info = "";

        foreach ($this->columns as $key => $col_name)
            if (isset($row[$key])) {
                $c = @$row[$col_name];
                if ($info) $info .= "; ";
                $col_title = $col_name; //$this->GetLocalizedString('ColumnVehicle' . (string)$col_name);
                $info .= $col_title . ": " . $c;
            }

        return $info;
    }

    function DrawRow($row, $catalog)
    {
        $row->addAttribute('vehicle_info', $this->PrepareVehicleInfo($row));

        $link = $this->FormatLink('vehicle', $row, $catalog);

        $html = '<tr onmouseout="this.className=\'\';" onmouseover="this.className=\'over\';" onclick="window.location=\'' . $link . '\'">';

        $tooltip = '';

        foreach ($this->columns as $key => $column) {
            if (isset($this->datacolumns[$key])) {
                if (in_array($key, $this->priorColumns))
                    $html .= $this->DrawCell($row, strtolower($key), $link);
                $tooltip .= $this->DrawToolTipValue($row, strtolower($key), (string)$column);
            }
        }
        $html .= '<td class = "ttp" style = "display:none;">' . $tooltip . '</td>';
        $html .= '</tr>';
        return $html;
    }

    function DrawCell($row, $column, $link)
    {

        return '<td>' . $this->DrawCellValue($row, $column, $link) . '</td>';

    }

    function DrawToolTipValue($row, $column, $name)
    {
        foreach ($row->attributes() as $key => $value) {
            if (strtolower($key) == $column) {
                return '<span class = "item">' . (string)$name . ':' . '<span style="display:inline-block; max-width:300px; float:right">' . (string)$value . '</span></span>';
            }
        }
        foreach ($row->attribute as $attr) {
            if (strtolower($attr->attributes()->key) == $column) {
                return '<span class = "item">' . (string)$name . ':' . '<span style=" display:inline-block; max-width:300px; float:right">' . (string)$attr->attributes()->value . '</span></span>';
            }
        }
    }

    function DrawCellValue($row, $column, $link)
    {
        foreach ($row->attributes() as $key => $value)
            if (strtolower($key) == $column)
                return '<a href="' . $link . '">' . (string)$value . '</a>';
        foreach ($row->attribute as $attr)
            if (strtolower($attr->attributes()->key) == $column)
                return '<a href="' . $link . '">' . (string)$attr->attributes()->value . '</a>';
    }
}

?>