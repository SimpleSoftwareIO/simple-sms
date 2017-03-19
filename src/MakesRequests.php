<?php

namespace SimpleSoftwareIO\SMS;

trait MakesRequests
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
     * Has the call been built yet.
     *
     * @var bool
     */
    protected $callBuilt = false;

    /**
     * Builds the API call address.
     *
     * @param $url
     */
    protected function buildCall($url)
    {
        if (! $this->callBuilt) {
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
     * @param array|string $values
     * @param null         $key
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
     * @param string $username
     */
    public function setUser($username)
    {
        $this->auth['username'] = $username;
    }

    /**
     * Sets the password for auth.
     *
     * @param string $password
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
    }

    /**
     * Creates and sends a POST request to the requested URL.
     *
     * @return mixed
     */
    protected function postRequest()
    {
        $response = $this->client->post($this->buildUrl(),
            [
                'auth'        => $this->getAuth(),
                'form_params' => $this->getBody(),
            ]);

        if ($response->getStatusCode() != 201 && $response->getStatusCode() != 200) {
            $this->throwNotSentException('Unable to request from API.');
        }

        return $response;
    }

    /**
     * Creates and sends a GET request to the requested URL.
     *
     * @return mixed
     */
    protected function getRequest()
    {
        $url = $this->buildUrl($this->getBody());

        $response = $this->client->get($url, ['auth' => $this->getAuth()]);

        if ($response->getStatusCode() != 201 && $response->getStatusCode() != 200) {
            $this->throwNotSentException('Unable to request from API.');
        }

        return $response;
    }
}
