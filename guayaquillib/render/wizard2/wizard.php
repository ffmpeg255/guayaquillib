<?php

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'template.php';

class GuayaquilWizard extends GuayaquilTemplate
{
	var $wizard = NULL;
	var $catalog = NULL;

	function __construct(IGuayaquilExtender $extender)
	{
		parent::__construct($extender);
	}

	function Draw($catalog, $wizard)
	{
		$this->wizard = $wizard;
		$this->catalog = $catalog;

		$html = '<div class="DrawHeader">';
		$html .= '<script type="text/javascript">';
		$html .= 'function openWizard(ssd) {';
        $html .= 'var url = \''.$this->FormatLink('wizard', null, $catalog).'\'.replace(\'\\$ssd\\$\', ssd);';
        $html .= 'window.location = url;';
        $html .= '}';
		$html .= '</script>';
		$html .= '<form name="findByParameterIdentifocation" id="findByParameterIdentifocation">';
		$html .= '<table border="0" width="100%">';
		
		$html .= $this->DrawHeader();

		foreach($wizard->row as $condition)
			$html .= $this->DrawConditionRow($catalog, $condition); 		
		
		$html .= '</table>';
		$html .= '</form>';

		if ($wizard->row['allowlistvehicles'] == 'true')
			$html .= $this->DrawVehiclesListLink($catalog, $wizard);

		$html .= '</div>';

		return $html;
	}

	function DrawHeader()
	{
		return '';
	}

	function DrawConditionRow($catalog, $condition)
	{
		$html = '<tr width="60%"'.($condition['automatic'] == 'false' ? ' class="guayaquil_SelectedRow"' : '').'>';
		$html .= '<td>'.$this->DrawConditionName($catalog, $condition).'</td>';

		$html .= '<td>';
		if ($condition['determined'] == 'false') {
			$html .= $this->DrawSelector($catalog, $condition);
        } else if ($condition['automatic'] == 'true') {
            $html .= $this->DrawAutomaticSelector($catalog, $condition);
        } else {
            $html .= $this->DrawManualSelector($catalog, $condition);
        }
		
		$html .= '</td></tr>';

		return $html;
	}

	function DrawConditionName($catalog, $condition)
	{
		return $condition['name'];
	}

	function DrawSelector($catalog, $condition)
	{
		$html = '<select style="width:250px" name="Select'.$condition['type'].'" onChange="openWizard(this.options[this.selectedIndex].value); return false;">';

		$html .= '<option value="null">&nbsp;</option>';

		foreach($condition->options->row as $row) {
			$html .= $this->DrawSelectorOption($row);
		}

		$html .= '</select>';

		return $html;
	}

	function DrawAutomaticSelector($catalog, $condition)
	{
		$html = '<select disabled style="width:250px" name="Select'.$condition['type'].'">';
		$html .= $this->DrawDisabledSelectorOption($condition);
		$html .= '</select>';

		return $html;
	}

	function DrawManualSelector($catalog, $condition)
	{
		$html = '<select disabled style="width:250px" name="Select'.$condition['type'].'">';
		$html .= $this->DrawDisabledSelectorOption($condition);
		$html .= '</select>';
        $removeFile = $this->Convert2uri(__DIR__ . DIRECTORY_SEPARATOR . 'images/remove.png');

		return $html;
	}

	function DrawSelectorOption($row)
	{
		return '<option value="'.$row['key'].'">'.$row['value'].'</option>';
	}

	function DrawDisabledSelectorOption($condition)
	{
		return '<option disabled selected value="null">'.$condition['value'].'</option>';
	}

	function DrawVehiclesListLink($catalog, $wizard)
	{
		$html .= '<br/><br/><a href="'.str_replace('$ssd$', $condition['ssd'], $this->FormatLink('wizard', null, $catalog)).'">Сбросить фильтр</a>';

		return $html;
	}
}

?>