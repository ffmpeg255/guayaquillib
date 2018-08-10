<?php

require_once('soap.php');

class GuayaquilRequestAM
{
	//	Function parameters
    protected $locale;

	// Temporery varibles
	protected $query = '';

	// soap wrapper object
    /** @var \GuayaquilSoapWrapper */
    protected $soap;

	//	Results
	public $error;
	public $data;

	function __construct($locale = 'ru_RU')
	{
		$this->locale = $this->checkParam($locale);
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

	function appendCommand($command)
	{
		if ($this->query == '')
			$this->query = $command;
		else
			$this->query .= "\n".$command;
	}

    public function appendFindOEM($oem, $options, $brand = null, $replacementtypes = 'default')
    {
        $this->appendCommand('FindOEM:Locale='.$this->locale.'|OEM='.$oem.'|ReplacementTypes='.$replacementtypes.'|Options='.$options.($brand ? '|Brand='.$brand : ''));
    }

    public function appendFindOEMCorrection($oem)
    {
        $this->appendCommand('FindOEMCorrection:Locale='.$this->locale.'|OEM='.$oem);
    }

    public function appendFindDetail($id, $options, $replacementtypes = 'default')
    {
        $this->appendCommand('FindDetail:Locale='.$this->locale.'|DetailId='.$id.'|ReplacementTypes='.$replacementtypes.'|Options='.$options);
    }

    public function appendManufacturerInfo($id)
    {
        $this->appendCommand('ManufacturerInfo:Locale='.$this->locale.'|ManufacturerId='.$id);
    }

    public function appendListManufacturer()
    {
        $this->appendCommand('ListManufacturer:Locale='.$this->locale);
    }

    public function appendFindReplacements($id)
    {
        $this->appendCommand('FindReplacements:Locale='.$this->locale.'|DetailId='.$id);
    }

	function query()
	{
        $this->data = $this->soap->queryData($this->query, false);
		$this->query = '';
		$this->error = $this->soap->getError();

		return $this->data;
	}
}
?>