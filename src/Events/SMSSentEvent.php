<?php namespace SimpleSoftwareIO\SMS\Events;

use SimpleSoftwareIO\SMS\OutgoingMessage;

class SMSSentEvent
{
    /**
     * @var OutgoingMessage
     */
    protected $outgoingMessage;

    /**
     * SMSSentEvent constructor.
     * @param OutgoingMessage $outgoingMessage
     */
    public function __construct(OutgoingMessage $outgoingMessage)
    {
        $this->outgoingMessage = $outgoingMessage;
    }

    /**
     * @return OutgoingMessage
     */
    public function getOutgoingMessage()
    {
        return $this->outgoingMessage;
    }
}