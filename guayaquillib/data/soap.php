<?php

require_once 'SSLSoapClient.php';

class GuayaquilSoapWrapper
{
    ///////////////////////////// Configuration

    private $authMethod;

    private $certificateFileName;
    private $certificateKeyFileName;
    private $certificatePassword;

    private $userLogin;
    private $userSecretKey;

    //////////////////////////// Results

    // If error != '' then request processing error occured
    private $error = '';

    // SimpleXML object
    private $data;

    public function setCertificateAuthorizationMethod($certificateFolder = false, $certificatePassword = false)
    {
        if (!$certificateFolder) {
            $certificateFolder = dirname(__FILE__).DIRECTORY_SEPARATOR.'cert';
        }

        $this->authMethod = 'certificate';
        $this->certificateFileName = $certificateFolder . '/client.pem';
        $this->certificateKeyFileName = $certificateFolder . '/client.key';
        $this->certificatePassword = $certificatePassword ? $certificatePassword : "123ertGHJ";
    }

    public function setUserAuthorizationMethod($login, $key)
    {
        $this->authMethod = 'login';
        $this->userLogin = $login;
        $this->userSecretKey = $key;
    }

    function getSoapClient($laximoOem = true)
    {
        $options = array(
            'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
        );

        if ($laximoOem) {
            $options['uri'] = 'http://WebCatalog.Kito.ec';
            $options['location'] = ($this->authMethod == 'certificate' ? 'https' : 'http').'://ws.avtosoft.net/ec.Kito.WebCatalog/services/Catalog.CatalogHttpSoap11Endpoint/';
        } else {
            $options['uri'] = 'http://Aftermarket.Kito.ec';
            $options['location'] = ($this->authMethod == 'certificate' ? 'https' : 'http').'://aws.laximo.net/ec.Kito.Aftermarket/services/Catalog.CatalogHttpSoap11Endpoint/';
        }

        if ($this->authMethod == 'certificate') {
            $options['sslCertPath'] = $this->certificateFileName;
            $options['sslKeyPath'] = $this->certificateKeyFileName;
            $options['passphrase'] = $this->certificatePassword;
            $options['sslcertpasswd'] = $this->certificatePassword;
            $options['verifypeer'] = 0;
            $options['verifyhost'] = 0;
        }

        $client = new SSLSoapClient(null, $options);

        return $client;
    }

    function queryData($request, $oem_service = true)
    {
        try {
            $client = $this->getSoapClient($oem_service);
            if ($this->authMethod == 'certificate') {
                $this->data = $client->QueryData($request);
            } else {
                $this->data = $client->QueryDataLogin($request, $this->userLogin, md5($request . $this->userSecretKey));
            }

            return $this->data;
        } catch (Exception $ex) {
            $this->error = $this->parseError($ex->getMessage());
        }
    }

    function parseError($err)
    {
        if (strpos($err, "cURL ERROR: 35"))
            return 'Not Connected';

        if (strpos($err, "cURL ERROR: 58"))
            return 'No Certificate';

        if (strpos($err, "400 Bad Request"))
            return 'Certificate expired';

        $e = explode("<br>", $err, 2);
        $err = $e[0];
        $pos = strrpos($err, 'E_');
        if ($pos === false)
            $pos = strrpos($err, ':') + 1;

        return substr($err, $pos);
    }

    public function getError()
    {
        return $this->error;
    }

    public function getData()
    {
        return $this->data;
    }
}

?>
