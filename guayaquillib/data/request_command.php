<?php

class RequestCommand {

    protected $command;
    protected $params;
    protected $command_text;

    function __construct($command, $params)
    {
        $this->command = $command;
        $this->params = $params;
    }

    /**
     * @param mixed $command
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }

    /**
     * @return mixed
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param mixed $command_text
     */
    public function setCommandText($command_text)
    {
        $this->command_text = $command_text;
    }

    /**
     * @return mixed
     */
    public function getCommandText()
    {
        return $this->command_text;
    }

    /**
     * @param mixed $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

} 