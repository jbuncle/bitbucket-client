<?php declare(strict_types=1);

namespace JBuncle\BitbucketClient;

use Exception;
use JBuncle\BitbucketClient\Client\PaginatedResponse;

class BitbucketApi {

    private string $baseUrl;
    private string $username;
    private string $password;

    public function __construct(string $baseUrl, string $username, string $password) {
        $this->baseUrl = $baseUrl;
        $this->username = $username;
        $this->password = $password;
    }

    public function paginatedGet(string $requestUrl): PaginatedResponse {
        $json = $this->curlGet($requestUrl);
        if (array_key_exists('type', $json) && $json['type'] === 'error') {
            throw new Exception($json['error']['message'] . " '$requestUrl'");
        }

        return PaginatedResponse::fromJson($requestUrl, $json);
    }

    /**
     * Send a GET request to the Bitbucket API.
     *
     * @param string $requestUrl
     *
     * @return array<mixed>
     * @throws Exception
     */
    public function get(string $requestUrl): array {
        $json = $this->curlGet($requestUrl);
        if (array_key_exists('type', $json) && $json['type'] === 'error') {
            throw new Exception($json['error']['message']);
        }

        return $json;
    }

    /**
     * Send a POST Request to the Bitbucket API.
     *
     * @param string $requestUrl
     * @param array<mixed> $data
     *
     * @return array<mixed>
     *
     * @throws Exception
     */
    public function post(string $requestUrl, array $data): array {
        $json = $this->curlPost($requestUrl, $data);
        if (array_key_exists('type', $json) && $json['type'] === 'error') {
            throw new Exception($json['error']['message']);
        }

        return $json;
    }

    /**
     * 
     * @param string $path
     * @return array<mixed>
     * @throws Exception
     */
    private function curlGet(string $path): array {
        $ch = curl_init();
        if ($ch === false) {
            throw new Exception("Failed to initialise CURL");
        }

        $url = $this->getUrl($path);

        // echo $url . "\n";

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);

        $headers = [];
        $headers[] = 'Accept: application/json';
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('Error:' . curl_error($ch));
        }

        if (!is_string($result)) {
            throw new Exception('Bad response');
        }

        curl_close($ch);

        $decoded = json_decode($result, true);
        if (!is_array($decoded)) {
            throw new Exception('Failed to decode JSON');
        }

        return $decoded;
    }

    private function curlPost(string $path, array $data = []): array {
        $ch = curl_init();
        if ($ch === false) {
            throw new Exception("Failed to initialise CURL");
        }

        $url = $this->getUrl($path);

        $postData = json_encode($data);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);

        $headers = [];
        $headers[] = 'Accept: application/json';
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('Error:' . curl_error($ch));
        }

        if (!is_string($result)) {
            throw new Exception('Bad response');
        }

        curl_close($ch);

        $decoded = json_decode($result, true);
        if (!is_array($decoded)) {
            throw new Exception('Failed to decode JSON');
        }

        return $decoded;
    }

    private function getUrl(string $path): string {
        $baseUrl = rtrim($this->baseUrl, '/');
        $cleanPath = ltrim($path, '/');
        return $baseUrl . '/' . $cleanPath;
    }

}
