<?php

require_once dirname(__FILE__) . '/../template.php';

class GuayaquilVinSearchForm extends GuayaquilTemplate
{
    var $catalog = NULL;
    var $cataloginfo = NULL;

    function __construct(IGuayaquilExtender $extender)
    {
        parent::__construct($extender);
    }

    function Draw($catalog, $cataloginfo, $prevvin = '')
    {
        $this->cataloginfo = $cataloginfo;
        $this->catalog = $catalog;

        $html = $this->DrawVinCheckScript();
        $html .= $this->DrawVinExample($cataloginfo);
        $html .= $this->DrawVinForm($catalog, $prevvin);

        return $html;
    }

    function DrawVinCheckScript()
    {
        $html = '<script type="text/javascript">
		function checkVinValue(value, submit_btn) {
		    value = value.replace(/[^\da-zA-Z]/g,\'\');
            var expr = new RegExp(\'' . $this->GetVinRegularExpression() . '\', \'i\');
            if (expr.test(value))
            {
                jQuery(submit_btn).attr(\'disabled\', \'1\');
                jQuery(\'#VINInput\').attr(\'class\',\'g_input\');
                window.location = \'' . $this->FormatLink('vehicles', NULL, $this->catalog) . '\'.replace(\'\\$vin\\$\', value);
            } else
            jQuery(\'#VINInput\').attr(\'class\',\'g_input_error\');
        }
		</script> ';

        return $html;
    }

    function GetVinRegularExpression()
    {
        return '\\^[A-z0-9]{12}[0-9]{5}\$';
    }

    function GetVinExample($cataloginfo)
    {
        if ($cataloginfo) {
            foreach ($cataloginfo->features->feature as $feature) {
                if ((string)$feature['name'] == 'vinsearch') {
                    return $feature['example'];
                }
            }
        }

        return 'WAUBH54B11N111054';
    }

    function DrawVinExample($cataloginfo)
    {
        return $this->GetLocalizedString('InputVIN', array($this->GetVinExample($cataloginfo))) . '<br>';
    }

    function DrawVinForm($catalog, $prevvin)
    {
        $html = '
            <form name="findByVIN" onSubmit="checkVinValue(this.vin.value);return false;" id="findByVIN" >
                <div id="VINInput" class="g_input"><input name="vin" type="text" id="vin" size="17" style="width:200px;" value="' . $prevvin . '"/></div>
                <input type="submit" name="vinSubmit" value="' . $this->GetLocalizedString('Search') . '" id="vinSubmit" />
                <input type="hidden" name="option" value="com_guayaquil" />
                <input type="hidden" name="view" value="vehicles" />
                <input type="hidden" name="ft" value="findByVIN" />
		    </form>';

        return $html;
    }
}
