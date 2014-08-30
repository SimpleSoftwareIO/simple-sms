<?php namespace SimpleSoftwareIO\SMS;

class IncomingMessage{

    protected $raw;

    protected $from;

    protected $message;

    protected $id;

    protected $to;

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

    public function raw()
    {
        return $this->raw;
    }

    public function message()
    {
        return $this->message;
    }

    public function from()
    {
        return $this->from;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function id()
    {
        return $this->id;
    }

    public function setTo($to)
    {
        $this->to = $to;
    }

    public function to()
    {
        return $this->to;
    }
}