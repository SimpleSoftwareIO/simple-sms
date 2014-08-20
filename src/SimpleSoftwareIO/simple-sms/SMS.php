<?php namespace SimpleSoftwareIO\SMS;

use SimpleSoftwareIO\SMS\Drivers\DriverInterface;
use Illuminate\Container\Container;
use Illuminate\Log\Writer;

class SMS {

  /**
   * The Driver Interface instance.
   *
   * @var \SimpleSoftwareIO\SMS\DenderInterface
   */
  protected $driver;

  /**
	 * The log writer instance.
	 *
	 * @var \Illuminate\Log\Writer
	 */
  protected $logger;

  /**
   * Determines if a message should be sent or faked.
   *
   * @var boolean
   */
  protected $pretending = false;

  /**
   * The IOC Container
   *
   * @var \Illuminate\Container\Container
   */
  protected $container;

  /**
   * The global from address
   *
   * @var string
   */
  protected $from;

  /**
   * Creates the SMS instance.
   *
   * @param SimpleSoftwareIO\SMS\Drivers\DriverInterface $driver The desired driver to send the SMS messsages.
   * @return void
   */
  public function __construct(DriverInterface $driver)
  {
    $this->driver = $driver;
  }

  /**
   * Send a SMS
   *
   * @parma string $view The desired view.
   * @parma array $data The data that needs to be passed into the view.
   * @parma \Clousure $callback The methods that you wish to fun on the message.
   */
  public function send($view, $data, $callback)
  {
    $data['message'] = $message = $this->createMessage();

    //We need to set the properties so that we can later pass this onto the Illuminate Mailer class if the e-mail gateway is used.
    $message->view($view);
    $message->data($data);

    call_user_func($callback, $message);

    if ( ! $this->pretending)
    {
      $this->driver->send($message);
    }
    elseif (isset($this->logger))
    {
      $this->logMessage($message);
    }
  }

  /**
   * Logs that a message was sent.Logs
   *
   * @parma SimpleSoftwareIO\SMS\Message $message An instance of the message.
   * @return void
   */
  protected function logMessage($message)
  {
    foreach($message->getTo() as $number)
    {
      $numbers[] = $number['number'];
    }

    $numbers = implode(",", $numbers);

    $this->logger->info("Pretending to send SMS message to: {$numbers}");
  }

  /**
   * Creates a new Message instance.
   *
   * @return SimpleSoftwareIO\SMS\Message
   */
  protected function createMessage()
  {
    $message = new Message($this->container['view']);

    //If a from address is set, pass it along to the messasge class.
    if (isset($this->from)) $message->from($this->from);

    return $message;
  }

  /**
   * Returns if the message should be faked when sent or not.
   *
   * @return boolean
   */
  public function isPretending()
  {
    return $this->pretending;
  }

  /**
   * Sets if a message should be pretend sent or not.
   *
   * @parma boolean $pretend
   * @return void
   */
  public function pretend($pretend = false)
  {
    $this->pretending = $pretend;
  }

  /**
   * Sets the contrainer.
   *
   * @parma Illuminate\Container\Container $container The IoC container.
   * @return void
   */
  public function setContainer(Container $container)
  {
    $this->container = $container;
  }

  /**
   * Sets the number that message should always be sent from.
   *
   * @parma string $number The desired number to send a message from.
   * @return void
   */
  public function alwaysFrom($number)
  {
    $this->from = $number;
  }

  /**
	 * Set the log writer instance.
	 *
	 * @param  \Illuminate\Log\Writer  $logger
	 * @return $this
	 */
  public function setLogger(Writer $logger)
  {
    $this->logger = $logger;

    return $this;
  }

  /**
   * Coming Soon
   */
  public function queue()
  {

  }

  /**
   * Coming Soon
   */
  public function receive()
  {

  }

  /**
   * Coming Soon
   */
  public function checkMessages()
  {

  }
  /**
   * Coming Soon
   */
  public function getMessage($messageId)
  {

  }
}