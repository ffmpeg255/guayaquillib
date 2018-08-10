<?php

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'template.php';

class GuayaquilFilter extends GuayaquilTemplate
{
    var $catalog;
    var $ssd;
    var $unit;
    var $isEmpty = false;

	function Draw($catalog, $filters, $ssd, $unit)
	{
		$this->catalog = $catalog;
		$this->ssd = $ssd;
        $this->unit = $unit;
		$html = $this->DrawJS($filters);
		$html .= $this->DrawHeader($filters);
		$html .= $this->DrawBody($filters);
		$html .= $this->DrawFooter($filters);

		$html .= '<div style="visibility: visible; display: block; height: 20px; text-align: right;"><a href="http://dev.laximo.ru" rel="follow" style="visibility: visible; display: inline; font-size: 10px; font-weight: normal; text-decoration: none;">guayaquil</a></div>';

		return $html;
	}

    private function DrawJS($filters)
    {
        return;
        $html  = '<script type="text/javascript"> ';
        $html .= 'function ProcessFilters(form) { ';
        $html .= ' var ssd = \''.$this->ssd.'\';';
        $html .= ' }';
        $html .= '</script> ';

        return '';
    }

	function DrawHeader($filters)
	{
        return 'Выберите из списка значение условия:<br><br>
            <form onsubmit="ProcessFilters(false); return false;" id="guayaquilFilterForm"><table class="g_filter_table">';
	}

	function DrawBody($filters)
	{
        $html = '';
        $count = 0;
		foreach ($filters as $filter)
        {
            $html .= $this->DrawFilter($filter);
            $count ++;
        }
        if (!$count)
            return $this->DrawEmpty();

		return $html;
	}

	function DrawFilter($filter)
	{
        return '<tr><td valign="top">'.$this->DrawFilterName($filter).'</td><td>'.$this->DrawFilterBox($filter).'</td></tr>';
	}

	function DrawFilterName($filter)
	{
        return $filter['name'];
	}

	function DrawFilterBox($filter)
	{
        if (((string)$filter['type']) == 'list')
            return $this->DrawFilterBoxList($filter);

        return $this->DrawFilterBoxInput($filter);
	}

    function DrawFilterBoxInput($filter)
    {
        return '<input type=text class="g_filter g_filter_box" regexp="'.str_replace('"', '""', $filter['regexp']).'" ssd="'.str_replace('"', '""', $filter['ssdmodification']).'">';
    }

    function GetReturnURL()
    {
    }

	function DrawFilterBoxList($filter)
	{
        $html = '<select class="g_filter_box g_filter">';
        $html .= '<option value="">-- Не указано --</option>';

        $count = 0;
        foreach ($filter->values->row as $value)
        {
            $note = (string)$value;
            if ($note)
                return $this->DrawFilterBoxInputList($filter);

            $html .= '<option value="'.str_replace('"', '""', $value['ssdmodification']).'">'.$value['name'].'</option>';
            $count++;
        }

        if (!$count)
            return $this->DrawEmpty();

        $html .= '</select>';

        return $html;
	}

    private function DrawFilterBoxInputList($filter)
    {
        static $id = 0;
        $html = '';
        $count = 0;

        foreach ($filter->values->row as $value)
        {
            $html .= '<div class="g_filter_label"><label>
                    <input type="radio" name="filter_'.$id.'" class="g_filter g_filter_radio" ssd="'.str_replace('"', '""', $value['ssdmodification']).'"> <span class="g_filter_name">'.$value['name'].'</span><br>
                    <div class="g_filter_note">'.str_replace("\n", '<br>', (string)$value).'</div>
                </label></div>';
            $count ++;
        }

        if ($count > 5)
            $html = '<div class="g_filter_scroller">'.$html.'</div>';

        $html = '<div class="g_filter">'.$html.'</div>';

        $id++;

        return $html;
    }

    private function DrawEmpty()
    {
        $this->isEmpty = true;
        return 'Не найдено ни одного варианта условий, нажмите кнопку "Пропустить выбор" и перейдите на иллюстрацию';
    }

	function DrawFooter($filters)
	{
        $html = '<tr><td colspan="2" align="center">
            <input type="button" value="Пропустить выбор" onclick="window.location=\''.$this->FormatLink('skip', $this->unit, $this->catalog, $this).'\'; return false;">
            <input type="submit" value="Подтвердить" '.($this->isEmpty ? 'disabled="disabled"' : '').'>
            </td></tr>';
        $html .= '</table>';
        return $html;
	}
}