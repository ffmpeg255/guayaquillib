<?php

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'template.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'details'.DIRECTORY_SEPARATOR.'detailslist.php';

?>
<?php

class GuayaquilQuickDetailsList extends GuayaquilTemplate
{
	var $groups = NULL;
	var $vehicleid = NULL;
	var $ssd = NULL;
    var $catalog;

    var $currentcategory;
    var $currentunit;
    var $currentdetail;

    var $foundunit = false;

    var $closedimage = '../details/images/closed.gif';
    var $cartimage = '../details/images/cart.gif';
    var $detailinfoimage = '../details/images/info.gif';
    var $zoom_image = NULL;
    var $size = 175;

    var $drawtoolbar = true;

    function __construct(IGuayaquilExtender $extender)
    {
        parent::__construct($extender);

        $this->detaillistrenderer = $this->CrateDetailListRenderer();
        $this->detaillistrenderer->group_by_filter = 1;

        $this->closedimage = $this->Convert2uri(dirname(__FILE__).DIRECTORY_SEPARATOR.$this->closedimage);
        $this->cartimage = $this->Convert2uri(dirname(__FILE__).DIRECTORY_SEPARATOR.$this->cartimage);
        $this->detailinfoimage = $this->Convert2uri(dirname(__FILE__).DIRECTORY_SEPARATOR.$this->detailinfoimage);
        $this->zoom_image = $this->Convert2uri(dirname(__FILE__).'/../images/zoom.png');

        $this->AppendJavaScript(dirname(__FILE__).'/../jquery.colorbox.js');
        $this->AppendCSS(dirname(__FILE__).'/../colorbox.css');
    }

    protected function CrateDetailListRenderer()
    {
        return new GuayaquilDetailsList($this->extender);
    }

	function Draw($details, $catalog, $vehicle_id, $ssd, $buyButton)
	{
        $this->catalog = $catalog;
        $this->vehicleid = $vehicle_id;
        $this->ssd = $ssd;


        $html = $this->drawtoolbar ? GuayaquilToolbar::Draw() : '';

        foreach ($details->Category as $category)
            $html .= $this->DrawCategory($category, $buyButton);

        if (!$this->foundunit)
            $html .= $this->DrawEmptySet();

		return $html;
	}

    protected function DrawCategory($category, $buyButton)
    {
        $this->currentcategory = $category;

        $html = '<div class="gdCategory">'.
            $this->DrawCategoryContent($category);

        foreach ($category->Unit as $unit)
            $html .= $this->DrawUnit($unit, $buyButton);

        $html .= '</div>';

        return $html;
    }

    protected function DrawCategoryContent($category)
    {
        $link = $this->FormatLink('category', $category, $this->catalog);
        return '<h3>'.$category['name'].'</h3>';
    }

    protected function DrawUnit($unit, $buyButton)
    {
        $this->currentunit = $unit;
        $this->foundunit = true;

        return '<table class="gdUnit">
            <tr>
                <td class="gdImageCol" width="'.($this->size + 4).'" align=center valign=top>
                    '.$this->DrawUnitImage($unit).'
                </td><td class="gdDetailCol" valign=top>
                    '.$this->DrawUnitDetails($unit, $buyButton).'
                </td>
            </tr>
        </table>';
    }

    protected function DrawUnitImage($unit)
    {
/*        $note = (string)$unit['note'];
        if (strlen($note))
            $html .= '<br>Примечание: '.$note;
*/
        $link = $this->FormatLink('unit', $unit, $this->catalog);

        $img = str_replace('%size%', $this->size, $unit['imageurl']);
        if (strlen($img))
            $img = '<img class="img_group" src="'.$img.'">';

        return '
            <div class="gdImage'.(!strlen($img) ? ' gdNoImage' : '').'" style="width:'.(int)$this->size.'px; height:'.(int)$this->size.'px;">
			<a href="'.$link.'" title="'.$unit['code'].': '.$unit['name'].'" >
               '.$img.'
			</a>
            </div>
            <a href="'.$link.'"><b>'.$unit['code'].':</b> '.$unit['name'].'</a>
        ';
    }

    protected function DrawUnitDetails($unit, $buyButton)
    {
        $this->detaillistrenderer->currentunit = $unit;
		$unitlink = $this->FormatLink('unit', $unit, $this->catalog);
        return $this->detaillistrenderer->Draw($this->catalog, $unit->Detail, $unitlink, $buyButton);
    }

    protected function DrawEmptySet()
    {
        $link = $this->FormatLink('vehicle', null, $this->catalog);

        return 'Ничего не найдено, воспользуйтесь <a href="'.$link.'">иллюстрированным каталогом</a> для поиска требуемой детали';
    }
}
