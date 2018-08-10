<?php

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'template.php';

class GuayaquilWizard2NextStep extends GuayaquilTemplate
{
	var $wizard = NULL;
	var $catalog = NULL;
	var $columnCount = 4;

	function __construct(IGuayaquilExtender $extender)
	{
		parent::__construct($extender);
	}

	function Draw($catalog, $wizard)
	{
		$this->wizard = $wizard;
		$this->catalog = $catalog;

		$html = '<div class="DrawHeader">';
		$html .= '<form name="findByParameterIdentifocation" id="findByParameterIdentifocation">';
		$html .= '<table border="0" width="100%">';

		$html .= $this->DrawHeader();

		foreach($wizard->previousStep->row as $condition) {
			$html .= $this->DrawPreviousConditionRow($catalog, $condition);
        }

        $html .= $this->DrawConditionRow($catalog, $wizard->currentStep);

        $html .= '</table>';
		$html .= '</form>';

		if ($wizard->currentStep['allowlistvehicles'] == 'true')
			$html .= $this->DrawVehiclesListLink($catalog, $wizard);

		$html .= '</div>';

		return $html;
	}

	function DrawHeader()
	{
		return '';
	}

	function DrawPreviousConditionRow($catalog, $condition)
	{
		$html = '<tr><td>'.$this->DrawConditionName($catalog, $condition).'</td>';

		$html .= '<td>';
        $html .= $this->DrawAutomaticSelector($catalog, $condition);
        if ($condition['automatic'] != 'true') {
            $removeFile = $this->Convert2uri(__DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'remove.png');
            $html .= '<a href="'.$this->FormatLink('condition', $condition, $catalog).'"><img src="'.$removeFile.'"></a>';
        }
		$html .= '</td></tr>';

		return $html;
	}

	function DrawConditionRow($catalog, $condition)
	{
		$html = '<tr><td colspan="2" class="guayaqyul_WizardStep2SelectHdr">'.$this->DrawConditionName($catalog, $condition).'</td></tr>';
		$html .= '<tr colspan="2" class="guayaqyul_WizardStep2SelectList"><td>';
        $html .= $this->DrawSelector($catalog, $condition);
		$html .= '</td></tr>';

		return $html;
	}

	function DrawConditionName($catalog, $condition)
	{
		return $condition['name'];
	}

	function DrawAutomaticSelector($catalog, $condition)
	{
		$html = $condition['value'];

		return $html;
	}

	function DrawSelector($catalog, $condition)
	{
        $html = '<table border="0" width="100%">';
        $array = array();

        foreach ($condition->options->row as $row) {
            $array[] = $row;
        }

        $count = count($array);
        $rows = ceil($count / $this->columnCount);

        for ($row = 0; $row < $rows; $row ++) {
            $html .= '<tr>';
            for ($column = 0; $column < $this->columnCount; $column ++) {
                $item = $array[$row * $this->columnCount + $column];
                $html .= '<td>'.$this->DrawSelectorOption($item).'</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</td></tr></table>';

		return $html;
	}

	function DrawSelectorOption($row)
	{
        $link = $this->FormatLink('option', $row, $this->catalog);
		return '<a href="'.$link.'">'.$row['value'].'</a>';
	}

	function DrawVehiclesListLink($catalog, $wizard)
	{
		$html = '<br><a class="gWizardVehicleLink" href="'.$this->FormatLink('vehicles', $wizard, $catalog).'">';
		$html .= $this->GetLocalizedString('List vehicles');
		$html .= '</a>';

		return $html;
	}
}

?>