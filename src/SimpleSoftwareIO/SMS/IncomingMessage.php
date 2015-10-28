<?php namespace SimpleSoftwareIO\SMS;

/**
 * Simple-SMS
 * Simple-SMS is a package made for Laravel to send/receive (polling/pushing) text messages.
 *
 * @link http://www.simplesoftware.io
 * @author SimpleSoftware support@simplesoftware.io
 *
 */

class IncomingMessage
{
    /**
     * Holds the raw data from a provider.
     *
     * @var mixed
     */
    protected $raw;

    /**
     * Holds who a message came from.
     *
     * @var string
     */
    protected $from;

    /**
     * Holds the message body.
     *
     * @var string
     */
    protected $message;

    /**
     * Holds the messasge ID.
     *
     * @var string
     */
    protected $id;

    /**
     * Holds the to address.
     *
     * @var string
     */
    protected $to;

    /**
     * Sets the raw data.
     *
     * @param mixed $raw
     */
    public function setRaw($raw)
    {
        $this->raw = $raw;
    }

    /**
     * Sets the message body.
     *
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Sets the from address.
     *
     * @param string $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * Returns the raw data.
     *
     * @return mixed
     */
    public function raw()
    {
        return $this->raw;
    }

    /**
     * Returns the message body.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }

    /**
     * Returns the from address.
     *
     * @return string
     */
    public function from()
    {
        return $this->from;
    }

    /**
     * Sets the message id.
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns the message id.
     *
     * @return string
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * Sets the to address.
     *
     * @param string $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }

    /**
     * Returns the to address.
     *
     * @return string
     */
    public function to()
    {
        return $this->to;
    }
}
