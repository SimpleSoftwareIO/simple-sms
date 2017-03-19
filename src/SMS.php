<?php

namespace SimpleSoftwareIO\SMS;

use Closure;
use Illuminate\Support\Str;
use SuperClosure\Serializer;
use Illuminate\Queue\QueueManager;
use Illuminate\Container\Container;
use SimpleSoftwareIO\SMS\Drivers\DriverInterface;

class SMS
{
    /**
     * The Driver Interface instance.
     *
     * @var \SimpleSoftwareIO\SMS\Drivers\DriverInterface
     */
    protected $driver;

    /**
     * The IOC Container.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * The global from address.
     *
     * @var string
     */
    protected $from;

    /**
     * Holds the queue instance.
     *
     * @var \Illuminate\Queue\QueueManager
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
     * Changes the set SMS driver.
     *
     * @param $driver
     */
    public function driver($driver)
    {
        $this->container['sms.sender'] = $this->container->share(function ($app) use ($driver) {
            return (new DriverManager($app))->driver($driver);
        });

        $this->driver = $this->container['sms.sender'];
    }

    /**
     * Send a SMS.
     *
     * @param string   $view     The desired view.
     * @param array    $data     The data that needs to be passed into the view.
     * @param \Closure $callback The methods that you wish to fun on the message.
     *
     * @return \SimpleSoftwareIO\SMS\OutgoingMessage The outgoing message that was sent.
     */
    public function send($view, $data, $callback)
    {
        $data['message'] = $message = $this->createOutgoingMessage();

        //We need to set the properties so that we can later pass this onto the Illuminate Mailer class if the e-mail gateway is used.
        $message->view($view);
        $message->data($data);

        call_user_func($callback, $message);

        $this->driver->send($message);

        return $message;
    }

    /**
     * Creates a new Message instance.
     *
     * @return \SimpleSoftwareIO\SMS\OutgoingMessage
     */
    protected function createOutgoingMessage()
    {
        $message = new OutgoingMessage($this->container['view']);

        //If a from address is set, pass it along to the message class.
        if (isset($this->from)) {
            $message->from($this->from);
        }

        return $message;
    }

    /**
     * Sets the IoC container.
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
     * Queues a SMS message.
     *
     * @param string          $view     The desired view.
     * @param array           $data     An array of data to fill the view.
     * @param \Closure|string $callback The callback to run on the Message class.
     * @param null|string     $queue    The desired queue to push the message to.
     */
    public function queue($view, $data, $callback, $queue = null)
    {
        $callback = $this->buildQueueCallable($callback);

        $this->queue->push('sms@handleQueuedMessage', compact('view', 'data', 'callback'), $queue);
    }

    /**
     * Queues a SMS message to a given queue.
     *
     * @param null|string     $queue    The desired queue to push the message to.
     * @param string          $view     The desired view.
     * @param array           $data     An array of data to fill the view.
     * @param \Closure|string $callback The callback to run on the Message class.
     */
    public function queueOn($queue, $view, $data, $callback)
    {
        $this->queue($view, $data, $callback, $queue);
    }

    /**
     * Queues a message to be sent a later time.
     *
     * @param int             $delay    The desired delay in seconds
     * @param string          $view     The desired view.
     * @param array           $data     An array of data to fill the view.
     * @param \Closure|string $callback The callback to run on the Message class.
     * @param null|string     $queue    The desired queue to push the message to.
     */
    public function later($delay, $view, $data, $callback, $queue = null)
    {
        $callback = $this->buildQueueCallable($callback);

        $this->queue->later($delay, 'sms@handleQueuedMessage', compact('view', 'data', 'callback'), $queue);
    }

    /**
     * Queues a message to be sent a later time on a given queue.
     *
     * @param null|string     $queue    The desired queue to push the message to.
     * @param int             $delay    The desired delay in seconds
     * @param string          $view     The desired view.
     * @param array           $data     An array of data to fill the view.
     * @param \Closure|string $callback The callback to run on the Message class.
     */
    public function laterOn($queue, $delay, $view, $data, $callback)
    {
        $this->later($delay, $view, $data, $callback, $queue);
    }

    /**
     * Builds the callable for a queue.
     *
     * @param \Closure|string $callback The callback to be serialized
     *
     * @return string
     */
    protected function buildQueueCallable($callback)
    {
        if (! $callback instanceof Closure) {
            return $callback;
        }

        return (new Serializer())->serialize($callback);
    }

    /**
     * Handles a queue message.
     *
     * @param \Illuminate\Queue\Jobs\Job $job
     * @param array                      $data
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
     *
     * @return mixed
     */
    protected function getQueuedCallable(array $data)
    {
        if (Str::contains($data['callback'], 'SerializableClosure')) {
            return unserialize($data['callback'])->getClosure();
        }

        return $data['callback'];
    }

    /**
     * Set the queue manager instance.
     *
     * @param \Illuminate\Queue\QueueManager $queue
     *
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
        $raw = $this->container['Illuminate\Support\Facades\Input'];

        return $this->driver->receive($raw);
    }

    /**
     * Queries the provider for a list of messages.
     *
     * @param array $options The options to pass onto a provider.  See each provider for a list of options.
     *
     * @return array Returns an array of IncomingMessage objects.
     */
    public function checkMessages(array $options = [])
    {
        return $this->driver->checkMessages($options);
    }

    /**
     * Gets a message by it's ID.
     *
     * @param $messageId The requested messageId.
     *
     * @return IncomingMessage
     */
    public function getMessage($messageId)
    {
        return $this->driver->getMessage($messageId);
    }
}
