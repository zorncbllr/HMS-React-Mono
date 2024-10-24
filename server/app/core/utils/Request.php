<?php

class Request
{
    public array $body, $headers, $query, $param, $cookies, $form_data, $sessions;
    public string $base_uri;

    public function __construct($param = [])
    {
        $this->setQuery();
        $this->setBody();
        $this->param = $param;
        $this->headers = getallheaders();
        $this->cookies = $_COOKIE;
        $this->sessions = $_SESSION;
        $this->form_data = [...$_POST, ...$_GET];
        $this->base_uri = $_SERVER["HTTP_HOST"];
    }

    protected function setQuery()
    {
        if (!empty($_SERVER['QUERY_STRING'])) {
            $queries = explode("&", $_SERVER['QUERY_STRING']);

            foreach ($queries as $query) {
                $pairs = explode("=", $query);
                $this->query[$pairs[0]] = $pairs[1];
            }
            return;
        }

        $this->query = [];
    }

    protected function setBody()
    {
        $rawData = file_get_contents("php://input");
        $this->body = json_decode($rawData, associative: true) ?? [];
    }
}
