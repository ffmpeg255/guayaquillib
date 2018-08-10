<?php

require_once('soap.php');
require_once('request_command.php');

interface IGuayquilCache
{
    function GetCachedData($request);

    function PutCachedData($request, $data);
}

class GuayaquilRequestBase
{
    //	Function parameters
    var $locale;
    var $cache;

    // Temporary varibles
    var $queries = array();

    //RequestParams
    var $certificatePath = false;
    var $login = false;
    var $password = false;

    // soap wrapper object
    var $soap;

    //	Results
    var $error;
    var $data;

    protected $service_type;

    function __construct($locale = 'ru_RU', IGuayquilCache $cache = null, $serviceType)
    {
        $this->locale = $locale;
        $this->cache = $cache;
        $this->soap = new GuayaquilSoapWrapper();
        $this->service_type = $serviceType;
    }

    function query()
    {
        if ($this->certificatePath) {
            $this->soap->certificatePath = $this->certificatePath;
        }

        $result = array();
        $commands_to_request = array();
        $count = count($this->queries);

        foreach ($this->queries as $key => $queery) {
            if ($this->cache) {
                // Try get data from local cache
                $data = $this->cache->GetCachedData($queery);
                if ($data) {
                    if (!is_object($data)) {
                        $data = simplexml_load_string($data);
                    }

                    $result[$key] = $data;
                    $commands_to_request[$key] = null;

                    continue;
                }
            }

            $result[$key] = null;
            $commands_to_request[$key] = $queery;
        }

        $commands_index = 0;
        $query_to_request = '';
        $indexes = array();
        //for ($index = 0; $index < $count; $index ++)
        foreach ($commands_to_request as $key => $queery) {
            /**
             * @var $queery RequestCommand
             */
            if ($query_to_request) {
                $query_to_request .= "\n";
            }

            $query_to_request .= $queery->getCommandText();
            $indexes[] = $key;

            if ($commands_index == 5) {
                if (!$this->_query($query_to_request, $indexes, $result))
                    return false;

                $commands_index = 0;
                $query_to_request = '';
                $indexes = array();
            }

            $commands_index++;
        }

        if ($commands_index > 0)
            (!$this->_query($query_to_request, $indexes, $result));

        $this->data = $result;

        $this->queries = array();

        return $result;
    }

    function _query($query, $indexes, &$result)
    {
        $data = $this->soap->queryData($query, $this->service_type, $this->login, $this->password);
        if ($this->soap->error) {
            $this->error = $this->soap->error;
            return false;
        }

        $data = simplexml_load_string($data);
        $index = 0;

        if ($data) {
            //  Merge results
            foreach ($data->children() as $row) {
                $result[$indexes[$index]] = $row;

                if ($this->cache) // Put in cache
                    $this->cache->PutCachedData($this->queries[$indexes[$index]], $row->asXML());

                $index++;
            }
        } else {

        }
    }

    protected function appendCommand($command_name, $params, $command_index = false)
    {
        $command = new RequestCommand($command_name, $params);
        if (isset($params) && is_array($params)) {
            $command_text = $command_name . ':';
            $first = true;
            foreach ($params as $key => $value) {
                if ($first) {
                    $first = false;
                } else {
                    $command_text .= '|';
                }

                $command_text .= $key . '=' . $value;
            }

            $command->setCommandText($command_text);
        } else {
            $command->setCommandText($command_name);
        }

        if ($command_index) {
            $this->queries[] = $command;
        } else {
            $this->queries[$command_index] = $command;
        }
    }

    public function checkParam($param)
    {
        return $param;
    }
} 