<?php

namespace SimpleSoftwareIO\SMS\Drivers;

use SimpleSoftwareIO\SMS\IncomingMessage;

abstract class AbstractSMS
{
    /**
     * Holds the arguments for the body.
     *
     * @var array
     */
    protected $body = [];

    /**
     * Holds the username and password for requests.
     *
     * @var array
     */
    protected $auth = [];

    /**
     * Has the call been built yet
     *
     * @var        boolean
     */
    protected $callBuilt = false;

    /**
     * Creates a new IncomingMessage instance.
     *
     * @return IncomingMessage
     */
    protected function createIncomingMessage()
    {
        return new IncomingMessage();
    }

    /**
     * Builds the API call address.
     *
     * @param $url
     */
    protected function buildCall($url)
    {
        if ( ! $this->callBuilt )
        {
            $this->apiBase .= $url;    
            $this->callBuilt = true;
        }
    }

    /**
     * Builds a URL.
     *
     * @param array $segments
     *
     * @return string
     */
    protected function buildUrl(array $segments = [])
    {
        //Get the base URL and add a ?
        $url = $this->apiBase.'?';

        if (isset($this->apiEnding)) {
            $segments = array_merge($segments, $this->apiEnding);
        }

        foreach ($segments as $key => $value) {
            $url = $url."$key=$value&";
        }

        //Remove the final &
        $url = substr($url, 0, -1);

        return $url;
    }

    /**
     * Builds the body part of the request and adds it to the body array.
     *
     * @param array|string $values Provides the data to be merged into the array. If a string, the key must be provided.
     * @param null         $key    Holds the key in which a string will be merged into the array.
     */
    public function buildBody($values, $key = null)
    {
        if (is_array($values)) {
            $this->body = array_merge($this->body, $values);
        } else {
            $this->body[$key] = $values;
        }
    }

    /**
     * Returns the body array.
     *
     * @return array
     */
    protected function getBody()
    {
        return $this->body;
    }

    /**
     * Sets the username for auth.
     *
     * @param $username
     */
    public function setUser($username)
    {
        $this->auth['username'] = $username;
    }

    /**
     * Sets the password for auth.
     *
     * @param $password
     */
    public function setPassword($password)
    {
        $this->auth['password'] = $password;
    }

    /**
     * Returns the auth array for requests.  If not set, will return null.
     *
     * @return array|null
     */
    protected function getAuth()
    {
        if (isset($this->auth['username']) && isset($this->auth['password'])) {
            return [$this->auth['username'], $this->auth['password']];
        }

        return;
    }

    /**
     * Creates and sends a POST request to the requested URL.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function postRequest()
    {
        $response = $this->client->post($this->buildUrl(),
            [
                'auth' => $this->getAuth(),
                'form_params' => $this->getBody(),
            ]);

        if ($response->getStatusCode() != 201 && $response->getStatusCode() != 200) {
            throw new \Exception('Unable to request from API.');
        }

        return $response;
    }

    /**
     * Creates and sends a GET request to the requested URL.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function getRequest()
    {
        $url = $this->buildUrl($this->getBody());

        $response = $this->client->get($url, ['auth' => $this->getAuth()]);

        if ($response->getStatusCode() != 201 && $response->getStatusCode() != 200) {
            throw new \Exception('Unable to request from API.');
        }

        return $response;
    }

    /**
     * Creates many IncomingMessage objects.
     *
     * @param $rawMessages
     *
     * @return array
     */
    protected function makeMessages($rawMessages)
    {
        $incomingMessages = [];
        foreach ($rawMessages as $rawMessage) {
            $incomingMessages[] = $this->processReceive($rawMessage);
        }

        return $incomingMessages;
    }

    /**
     * Creates a single IncomingMessage object.
     *
     * @param $rawMessage
     *
     * @return mixed
     */
    protected function makeMessage($rawMessage)
    {
        return $this->processReceive($rawMessage);
    }

    /**
     * Creates many IncomingMessage objects and sets all of the properties.
     *
     * @param $rawMessage
     *
     * @return mixed
     */
    abstract protected function processReceive($rawMessage);
}
