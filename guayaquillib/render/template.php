<?php

interface IGuayaquilExtender
{
	public function GetLocalizedString($name, $params = false, $renderer);

	public function AppendJavaScript($filename, $renderer);
	
	public function AppendCSS($filename, $renderer);

	public function FormatLink($type, $dataItem, $catalog, $renderer);

	public function Convert2uri($filename);
}

class GuayaquilTemplate
{
	var $extender;

	public function __construct(IGuayaquilExtender $extender)
	{
		$this->extender = $extender;

		static $guayaquil_templateinitialized;
		
		if (!isset($guayaquil_templateinitialized))
		{
			$this->AppendCSS(dirname(__FILE__).'/guayaquil.css');
			$this->AppendJavaScript(dirname(__FILE__).'/jquery.js');
			
			$guayaquil_templateinitialized = 1;
		}
	}

	public function GetLocalizedString($name, $params = false)
	{
		if ($this->extender == NULL)
			return $name;

		return $this->extender->GetLocalizedString($name, $params, $this);
	}

	public function FormatLink($type, $dataItem, $catalog)
	{
		if ($this->extender == NULL)
			die('Add IGuayaquilExtender object to template or redefine method FormatLink');

		return $this->extender->FormatLink($type, $dataItem, $catalog, $this);
	}

	public function AppendJavaScript($filename)
	{
		if ($this->extender == NULL)
			die('Add IGuayaquilExtender object to template or redefine method AppendJavaScript');

		return $this->extender->AppendJavaScript($filename, $this);
	}

	public function AppendCSS($filename)
	{
		if ($this->extender == NULL)
			die('Add IGuayaquilExtender object to template or redefine method AppendCSS');

		return $this->extender->AppendCSS($filename, $this);
	}

	public function Convert2uri($filename)
	{
		if ($this->extender == NULL)
			die('Add IGuayaquilExtender object to template or redefine method Convert2uri');

		return $this->extender->Convert2uri($filename, $this);
	}
}

class GuayaquilToolbar
{
    static $toolbar;

    var $buttons;

    public static function AddButton($title, $url, $onclick = null, $alt = null, $img = null, $id = null)
    {
        if (!self::$toolbar)
            self::$toolbar = new GuayaquilToolbar();

        self::$toolbar->buttons[] = array('title' => $title, 'url' => $url, 'onclick' => $onclick, 'alt' => $alt, 'img' => $img, 'id' => $id);
    }

    public static function Draw()
    {
        if (!GuayaquilToolbar::$toolbar)
            return '';

        $html = '';

        $toolbar = GuayaquilToolbar::$toolbar;
        foreach ($toolbar->buttons as $button)
            $html .= $toolbar->DrawButton($button);

        return $toolbar->DrawContainer($html);
    }

    private function DrawContainer($content)
    {
        if ($content)
            return '<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b><div id="guayaquil_toolbar" class="xboxcontent">
                    '.$content.'
                </div><b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b><br>';

        return '';
    }

    private function DrawButton($button)
    {
        return '<span class="g_ToolbarButton" '.($button['id'] ? 'id="'.$button['id'].'"' : '').'>
                <a href="'.$button['url'].'" '.($button['onclick'] ? ' onClick="'.$button['onclick'].'"' : '').'>'.
                   ($button['img'] ? '<img src="'.$button['img'].'" alt="'.$button['alt'].'">' : '').' '.
                   $button['title'].'
               </a>
           </span>';
    }
}