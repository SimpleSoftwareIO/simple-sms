<?php namespace SimpleSoftwareIO\SMS;

/**
 * Simple-SMS
 * Simple-SMS is a package made for Laravel to send/receive (polling/pushing) text messages.
 *
 * Part of this file is based on the Illuminate\Mail system.
 *
 * @link http://www.simplesoftware.io
 * @author SimpleSoftware support@simplesoftware.io
 *
 */

use SimpleSoftwareIO\SMS\Drivers\DriverInterface;
use Illuminate\Container\Container;
use Illuminate\Queue\QueueManager;
use Illuminate\Log\Writer;
use Illuminate\Support\SerializableClosure;
use Closure;

class SMS
{

    /**
     * The Driver Interface instance.
     *
     * @var \SimpleSoftwareIO\SMS\DriverInterface
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
     * Holds the queue instance.
     *
     * @var Illuminate\Queue\QueueManager
     */
    protected $queue;

    /**
     * Creates the SMS instance.
     *
     * @param DriverInterface $driver
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
        $data['message'] = $message = $this->createOutgoingMessage();

        //We need to set the properties so that we can later pass this onto the Illuminate Mailer class if the e-mail gateway is used.
        $message->view($view);
        $message->data($data);

        call_user_func($callback, $message);

        if (!$this->pretending)
        {
            $this->driver->send($message);
        }
        elseif (isset($this->logger))
        {
            $this->logMessage($message);
        }
    }

    /**
     * Logs that a message was sent.
     *
     * @param $message
     */
    protected function logMessage($message)
    {
        $numbers = implode(" , ", $message->getTo());

        $this->logger->info("Pretending to send SMS message to: $numbers");
    }

    /**
     * Creates a new Message instance.
     *
     * @return SimpleSoftwareIO\SMS\Message
     */
    protected function createOutgoingMessage()
    {
        $message = new OutgoingMessage($this->container['view']);

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
     * Fake sending a SMS
     *
     * @param $view The desired view
     * @param $data The data to fill the view
     * @param $callback The message callback
     */
    public function pretend($view, $data, $callback)
    {
        $this->setPretending(true);
        $this->send($view, $data, $callback);
    }

    /**
     * Sets if SMS should be fake send a SMS
     *
     * @param bool $pretend
     */
    public function setPretending($pretend = false)
    {
        $this->pretending = $pretend;
    }

    /**
     * Sets the IoC container
     *
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Sets the number that message should always be sent from.
     *
     * @param $number
     */
    public function alwaysFrom($number)
    {
        $this->from = $number;
    }

    /**
     * Set the log writer instance.
     *
     * @param  \Illuminate\Log\Writer $logger
     * @return $this
     */
    public function setLogger(Writer $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Queues a SMS message.
     *
     * @param string $view The desired view.
     * @param array $data An array of data to fill the view.
     * @param  \Closure|string $callback The callback to run on the Message class.
     * @param null|string $queue The desired queue to push the message to.
     * @return void
     */
    public function queue($view, $data, $callback, $queue = null)
    {
        $callback = $this->buildQueueCallable($callback);

        $this->queue->push('sms@handleQueuedMessage', compact('view', 'data', 'callback'), $queue);
    }

    /**
     * Queues a SMS message to a given queue.
     *
     * @param null|string $queue The desired queue to push the message to.
     * @param string $view The desired view.
     * @param array $data An array of data to fill the view.
     * @param  \Closure|string $callback The callback to run on the Message class.
     * @return void
     */
    public function queueOn($queue, $view, array $data, $callback)
    {
        $this->queue($view, $data, $callback, $queue);
    }

    /**
     * Queues a message to be sent a later time.
     *
     * @param int $delay The desired delay in seconds
     * @param string $view The desired view.
     * @param array $data An array of data to fill the view.
     * @param  \Closure|string $callback The callback to run on the Message class.
     * @param null|string $queue The desired queue to push the message to.
     * @return void
     */
    public function later($delay, $view, array $data, $callback, $queue = null)
    {
        $callback = $this->buildQueueCallable($callback);

        $this->queue->later($delay, 'mailer@handleQueuedMessage', compact('view', 'data', 'callback'), $queue);
    }

    /**
     * Queues a message to be sent a later time on a given queue.
     *
     * @param null|string $queue The desired queue to push the message to.
     *  @param int $delay The desired delay in seconds
     * @param string $view The desired view.
     * @param array $data An array of data to fill the view.
     * @param  \Closure|string $callback The callback to run on the Message class.
     * @return void
     */
    public function laterOn($queue, $delay, $view, array $data, $callback)
    {
        $this->later($delay, $view, $data, $callback, $queue);
    }

    /**
     * Builds the callable for a queue.
     *
     * @param \Clousure|string $callback The callback to be serialized
     * @return string
     */
    protected function buildQueueCallable($callback)
    {
        if ( ! $callback instanceof Closure) return $callback;

        return serialize(new SerializableClosure($callback));
    }

    /**
     * Handles a queue message.
     *
     * @param \Illuminate\Queue\Jobs\Job $job
     * @param array $data
     * @return void
     */
    public function handleQueuedMessage($job, $data)
    {
        $this->send($data['view'], $data['data'], $this->getQueuedCallable($data));

        $job->delete();
    }

    /**
     * Gets the callable for a queued message.
     *
     * @param array $data
     * @return mixed
     */
    protected function getQueuedCallable(array $data)
    {
        if (str_contains($data['callback'], 'SerializableClosure'))
        {
            return with(unserialize($data['callback']))->getClosure();
        }

        return $data['callback'];
    }

    /**
     * Set the queue manager instance.
     *
     * @param  \Illuminate\Queue\QueueManager  $queue
     * @return $this
     */
    public function setQueue(QueueManager $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Receives a SMS via a push request.
     *
     * @return IncomingMessage
     */
    public function receive()
    {
        //Passes all of the request onto the driver.
        $raw = $this->container['Input'];
        return $this->driver->receive($raw);
    }

    /**
     * Queries the provider for a list of messages.
     *
     * @param array $options The options to pass onto a provider.  See each provider for a list of options.
     * @return array Returns an array of IncomingMessage objects.
     */
    public function checkMessages(Array $options = array())
    {
        return $this->driver->checkMessages($options);
    }

    /**
     * Gets a message by it's ID.
     *
     * @param $messageId The requested messageId.
     * @return IncomingMessage
     */
    public function getMessage($messageId)
    {
        return $this->driver->getMessage($messageId);
    }
}