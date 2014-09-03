<?php namespace SimpleSoftwareIO\SMS;

/**
 * Simple-SMS
 * Simple-SMS is a package made for Laravel to send/receive (polling/pushing) text messages.
 *
 * @link http://www.simplesoftware.io
 * @author SimpleSoftware support@simplesoftware.io
 *
 */

use Illuminate\View\Factory;

class OutgoingMessage
{

    /**
     * The Illuminate view factory.
     *
     * @var \Illuminate\View\Factory
     */
    protected $views;

    /**
     * The view file to be used when composing a message.
     *
     * @var string
     */
    protected $view;

    /**
     * The data that will be passed into the Illuminate View Factory
     *
     * @var array
     */
    protected $data;

    /**
     * The number messages are being sent from.
     *
     * @var string
     */
    protected $from;

    /**
     * Array of numbers a message is being sent to.
     *
     * @var array
     */
    protected $to;

    /**
     * Whether a message is a MMS or SMS
     *
     * @var boolean
     */
    protected $mms = false;

    /**
     * Array of attached images.
     *
     * @var array
     */
    protected $attachImages = array();

    /**
     * Create a Message instance.
     *
     * @param  \Illuminate\View\Factory $views
     * @return void
     */
    public function __construct(Factory $views)
    {
        $this->views = $views;
    }

    /**
     * Constructs a message with its view file
     *
     * @return \Illuminate\View\Factory
     */
    public function composeMessage()
    {
        return $this->views->make($this->view, $this->data)->render();
    }

    /**
     * Sets the numbers messages will be sent from.
     *
     * @param string $number Holds the number that messages
     * @return void
     */
    public function from($number)
    {
        $this->from = $number;
    }

    /**
     * Gets the from address
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Sets the to addresses
     *
     * @param string $number Holds the number that a message will be sent to.
     * @param string $carrier The carrier the number is on.
     * @return $this
     */
    public function to($number, $carrier = null)
    {
        $this->to[] = [
            'number' => $number,
            'carrier' => $carrier
        ];

        return $this;
    }

    /**
     * Returns the To addresses without the carriers
     *
     * @return array
     */
    public function getTo()
    {
        $numbers = array();
        foreach ($this->to as $to) {
            $numbers[] = $to['number'];
        }
        return $numbers;
    }

    /**
     * Returns all numbers that a message is being sent to and includes their carriers.
     *
     * @return array An array with numbers and carriers
     */
    public function getToWithCarriers()
    {
        return $this->to;
    }

    /**
     * Sets the view file to be loaded.
     *
     * @param string $view The desired view file
     * @return void
     */
    public function view($view)
    {
        $this->view = $view;
    }

    /**
     * Sets the data for the view file.
     *
     * @param array $data An array of values to be passed to the View Factory.
     * @return void
     */
    public function data($data)
    {
        $this->data = $data;
    }

    /**
     * Returns the current view file.Returns
     *
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Returns the view data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Attaches an image to a message.
     *
     * @parma string $imageURL The location on the image.
     * @return void
     */
    public function attachImage($image)
    {
        $this->mms = true;

        if (is_array($image)) {
            $this->attachImages = array_merge($this->attachImages, $image);
        } else {
            $this->attachImages[] = $image;
        }
    }

    /**
     * Returns the attached image.
     *
     * @return array
     */
    public function getAttachImages()
    {
        return $this->attachImages;
    }

    /**
     * Returns if a message is a MMS.Returns
     *
     * @return boolean
     */
    public function isMMS()
    {
        return $this->mms;
    }
}