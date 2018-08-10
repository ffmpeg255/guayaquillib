<?php

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'template.php';

class GuayaquilUnitImage extends GuayaquilTemplate
{
	var $catalog = NULL;
	var $unit = NULL;
	var $imagemap = NULL;

	var $containerwidth = 960;
	var $containerheight = 600;

	var $spacer;
    var $zoom_image = NULL;

	function __construct(IGuayaquilExtender $extender)
	{
		parent::__construct($extender);
	}

	function Draw($catalog, $unit, $imagemap)
	{

		$this->AppendJavaScript(dirname(dirname(__FILE__)).'/dragscrollable.js');
		$this->AppendJavaScript(dirname(dirname(__FILE__)).'/jquery.mousewheel.js');
		$this->AppendJavaScript(dirname(dirname(__FILE__)).'/jquery.colorbox.js');
		$this->AppendJavaScript(dirname(__FILE__).'/unit.js');
		$this->AppendCSS(dirname(__FILE__).'/unit.css');
		$this->AppendCSS(dirname(__FILE__).'/../colorbox.css');

		$this->catalog = $catalog;
		$this->unit = $unit;
        $this->zoom_image = '/guayaquillib/render/images/zoom.png';

		$html = $this->DrawUnitImage($unit, $imagemap);

		return $html;
	}

	function DrawUnitImage($unit, $imagemap)
	{
        $html = $this->DrawMagnifier($unit);
		$html .= '<div id="viewport" class="inline_block" style="position:absolute; border: 1px solid #777; background: white; width:'.($this->containerwidth/2).'px; height:'.$this->containerheight.'px; overflow: auto;">';

		$html .= $this->DrawImageMap($imagemap);
		$html .= $this->DrawImage($unit);

		$html .= '</div>';

		return $html;
	}

	function DrawImageMap($imagemap)
	{
		$this->spacer = '/guayaquillib/render/unit/images/spacer.gif';

		$html = '';

		foreach($imagemap->row as $area)
			$html .= $this->DrawImageMapElement($area);

		return $html;
	}

	function DrawImageMapElement($area)
	{
		$html = '<div name="'.$area['code'].'" class="dragger g_highlight" style="position:absolute; width:'.($area['x2']-$area['x1']).'px; height:'.($area['y2']-$area['y1']).'px; margin-top:'.$area['y1'].'px; margin-left:'.$area['x1'].'px; overflow:hidden;">';
		
		$html .= $this->DrawImageMapElementClickableArea();

		$html .= '</div>';

		return $html;
	}

	function DrawImageMapElementClickableArea()
	{
		return '<img src="'.$this->spacer.'" width="200" height="200"/>';
	}

	function DrawImage($unit)
	{
        $img = $unit['largeimageurl'];
        if (!strlen($img))
            $img = $this->Convert2uri(dirname(__FILE__).'/../images/noimage.png');
        $html = '<img class="dragger" src="' . str_replace('%size%', 'source', $img) . '" height="auto"/>';
        return $html;
	}

    private function DrawMagnifier($unit)
    {
        return '<div class="guayaquil_unit_icons"><div class="guayaquil_zoom" full="'.str_replace('%size%', 'source', $unit['largeimageurl']).'" title="'.$unit['code'].': '.$unit['name'].'"></div></div>';
    }
}

?>