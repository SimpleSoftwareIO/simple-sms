<?php namespace SimpleSoftwareIO\SMS;

class IncomingMessage{

    protected $raw;

    protected $from;

    protected $message;

    public function setRaw($raw)
    {
        $this->raw = $raw;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function setFrom($from)
    {
        $this->from = $from;
    }

    public function getRaw()
    {
        return $this->raw;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getFrom()
    {
        return $this->from;
    }
}