<?php

class SSLSoapException extends Exception {

}

class SSLSoapClient extends SoapClient
{

    private $cookies = array();
    /**
     * @var string The XML SOAP request.
     */
    private $lastRequest;
    /**
     * @var string The XML SOAP response.
     */
    private $lastResponse;
    /**
     *
     * @var array
     */
    private $options = array();
    /**
     * @var Resource
     */
    private $curlHandle;


    /**
     * (PHP 5 &gt;= 5.0.1)<br/>
     * SoapClient constructor
     *
     * @link http://php.net/manual/en/soapclient.soapclient.php
     *
     * @param $wsdl
     * @param $options [optional] optional = array(..., sslCertPath=>file.pem, sslKeyPath=>file.key, ...)
     *
     */
    public function __construct($wsdl, $options = array())
    {
        $this->options = $options;

        parent::__construct($wsdl, $this->options);
        $this->initCurl();
    }


    /**
     *
     */
    public function __destruct()
    {
        curl_close($this->curlHandle);
    }


    /**
     * Call a url using curl with ntlm auth
     *
     * @param string $url
     *
     * @return string
     * @throws SoapFault on curl connection error
     */
    protected function callCurl()
    {
        curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, $this->lastRequest);

        if ($cookies = $this->getCookieAsString()) {
            curl_setopt($this->curlHandle, CURLOPT_COOKIE, $cookies);
        }

        $this->lastResponse = curl_exec($this->curlHandle);
		
        $httpCode = curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);
        if ($httpCode >= 400) {
            $xml = simplexml_load_string($this->lastResponse, 'SimpleXMLElement', 0, 'soapenv', true);
            if ($xml)
            {
                $fault = $xml->Body->Fault->children('');
                throw new \SoapFault((string)$fault->faultcode, (string)$fault->faultstring);
            }
            else
            {
                throw new \SoapFault("Error code $httpCode", $this->lastResponse);
            }
        }
    }


    /**
     * If value is null cookie will be deleted.
     *
     * @param string $name
     * @param null $value
     */
    public function __setCookie($name, $value = null)
    {
        if (is_null($value)) {
            unset($this->cookies[$name]);
        } else {
            $this->cookies[$name] = $value;
        }
    }


    /**
     * @return null|string
     */
    private function getCookieAsString()
    {
        $out = null;
        if ($this->cookies) {
            $buf = array();
            foreach ($this->cookies as $name => $val) {
                $buf[] = urlencode($name) . "=" . urlencode($val);
            }
            $out = implode('; ', $buf);

        }

        return $out;
    }


    /**
     *
     */
    protected function initCurl()
    {
        $this->curlHandle = curl_init();

        curl_setopt($this->curlHandle, CURLOPT_URL, $this->location);

        curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlHandle, CURLOPT_HEADER, false);

        curl_setopt($this->curlHandle, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($this->curlHandle, CURLOPT_FORBID_REUSE, true);
        curl_setopt($this->curlHandle, CURLOPT_FRESH_CONNECT, true);

        curl_setopt($this->curlHandle, CURLOPT_ENCODING, 'gzip,deflate');

        curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, array("Content-Type: text/xml; charset=utf-8", 'Expect:',));
        curl_setopt($this->curlHandle, CURLOPT_POST, true);

        if (in_array('sslCertPath', $this->options)) {
            $this->setSslCert($this->options['sslCertPath']);
        }

        if (in_array('sslKeyPath', $this->options)) {
            $this->setSslKey($this->options['sslKeyPath']);
        }

        curl_setopt($this->curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curlHandle, CURLOPT_SSL_VERIFYHOST, false);
    }


    /**
     * @param string $sslCertPath
     *
     * @throws SSLSoapException
     */
    public function setSslCert($sslCertPath)
    {
        if (file_exists($sslCertPath)) {
            $this->setCurlOption(CURLOPT_SSLCERT, $sslCertPath);
        } else {
            throw new SSLSoapException("SSLCERT file {$sslCertPath} not found.");
        }
    }


    /**
     * @param string $sslKeyPath
     *
     * @throws SSLSoapException
     */
    public function setSslKey($sslKeyPath)
    {
        if (file_exists($sslKeyPath)) {
            $this->setCurlOption(CURLOPT_SSLKEY, $sslKeyPath);
        } else {
            throw new SSLSoapException("SSLKEY file {$sslKeyPath} not foud.");
        }
    }


    /**
     * @param int $key
     * @param mixed $value
     */
    public function setCurlOption($key, $value)
    {
        curl_setopt($this->curlHandle, $key, $value);
    }


    /**
     * (PHP 5 &gt;= 5.0.1)<br/>
     * Calls a SOAP function (deprecated)
     *
     * @link http://php.net/manual/en/soapclient.call.php
     *
     * @param string $functionName
     * @param string $arguments
     *
     * @return mixed
     */
    public function __call($functionName, $arguments)
    {
        $this->lastResponse = null;

        return $this->__soapCall($functionName, $arguments);
    }


    /**
     * (PHP 5 &gt;= 5.0.1)<br/>
     * Returns last SOAP request
     *
     * @link http://php.net/manual/en/soapclient.getlastrequest.php
     * @return string The last SOAP request, as an XML string.
     */
    public function __getLastRequest()
    {
        return $this->lastRequest;
    }


    /**
     * (PHP 5 &gt;= 5.0.1)<br/>
     * Returns last SOAP response
     *
     * @link http://php.net/manual/en/soapclient.getlastresponse.php
     * @return string The last SOAP response, as an XML string.
     */
    public function __getLastResponse()
    {
        return $this->lastResponse;
    }


    /**
     * (PHP 5 &gt;= 5.0.1)<br/>
     * Calls a SOAP function
     *
     * @link http://php.net/manual/en/soapclient.soapcall.php
     *
     * @param string $functionName <p>
     * The name of the SOAP function to call.
     * </p>
     * @param array $arguments <p>
     * An array of the arguments to pass to the function. This can be either
     * an ordered or an associative array.
     * </p>
     * @param array $options [optional] <p>
     * An associative array of options to pass to the client.
     * </p>
     * <p>
     * The location option is the URL of the remote Web service.
     * </p>
     * <p>
     * The uri option is the target namespace of the SOAP service.
     * </p>
     * <p>
     * The soapaction option is the action to call.
     * </p>
     * @param mixed $inputHeaders [optional] <p>
     * An array of headers to be sent along with the SOAP request.
     * </p>
     * @param array $outputHeaders [optional] <p>
     * If supplied, this array will be filled with the headers from the SOAP response.
     * </p>
     *
     * @return mixed SOAP functions may return one, or multiple values. If only one value is returned
     * by the SOAP function, the return value of __soapCall will be
     * a simple value (e.g. an integer, a string, etc). If multiple values are
     * returned, __soapCall will return
     * an associative array of named output parameters.
     * </p>
     * <p>
     * On error, if the SoapClient object was constructed with the trace
     * option set to false, a SoapFault object will be returned.
     */
    public function __soapCall(
        $function_name, $arguments, $options = NULL, $input_headers = NULL, &$output_headers = NULL
    )
    {
        try {
            return parent::__soapCall($function_name, $arguments, $options, $input_headers, $outputHeaders);
        } catch (SSLSoapException $e) { }

        $this->callCurl();

        return parent::__soapCall($function_name, $arguments, $options, $input_headers, $outputHeaders);
    }


    /**
     * (PHP 5 &gt;= 5.0.1)<br/>
     * Performs a SOAP request
     *
     * @link http://php.net/manual/en/soapclient.dorequest.php
     *
     * @param string $request <p>
     * The XML SOAP request.
     * </p>
     * @param string $location <p>
     * The URL to request.
     * </p>
     * @param string $action <p>
     * The SOAP action.
     * </p>
     * @param int $version <p>
     * The SOAP version.
     * </p>
     * @param int $one_way [optional] <p>
     * If one_way is set to 1, this method returns nothing.
     * Use this where a response is not expected.
     * </p>
     *
     * @return string The XML SOAP response.
     */
    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        if (!is_null($this->lastResponse)) {
// We forced a response, so return it.
            return $this->lastResponse;
        }

        $this->lastRequest = '' . $request;

        throw new SSLSoapException();
    }
}