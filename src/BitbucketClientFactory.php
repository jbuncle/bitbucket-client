<?php declare(strict_types=1);
namespace JBuncle\BitbucketClient;

use JBuncle\BitbucketClient\Client\BitbucketClient;

class BitbucketClientFactory {

    private string $baseUrl;
    private string $username;
    private string $password;

    public function __construct(
            string $username,
            string $password
    ) {
        $this->baseUrl = 'https://bitbucket.org/api/2.0';
        $this->username = $username;
        $this->password = $password;
    }

    public function getInstanceForWorkspace(string $workspace): BitbucketClient {
        $api = new BitbucketApi($this->baseUrl, $this->username, $this->password);
        $utility = new BitbucketClient($api, $workspace);

        return $utility;
    }

}
