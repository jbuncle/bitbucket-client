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
        $json = $this->curlGetJson($requestUrl);
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
        $json = $this->curlGetJson($requestUrl);
        if (array_key_exists('type', $json) && $json['type'] === 'error') {
            throw new Exception($json['error']['message']);
        }

        return $json;
    }

    public function getRaw(string $path): string {
        return $this->curlGetRaw($path);
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
        $json = $this->curlPostJson($requestUrl, $data);
        if (array_key_exists('type', $json) && $json['type'] === 'error') {
            throw new Exception($json['error']['message']);
        }

        return $json;
    }

    /**
     *
     * @param  string $path
     * @param  array<string> $headers
     *
     * @return string
     *
     * @throws Exception
     */
    private function curlGetRaw(string $path, array $headers = []): string {
        $ch = curl_init();
        if ($ch === false) {
            throw new Exception('Failed to initialise CURL');
        }

        $url = $this->getUrl($path);

        // echo $url . "\n";

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('Error:' . curl_error($ch));
        }

        $responseStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (!in_array($responseStatusCode, [200])) {
            throw new Exception("Bad response code '$responseStatusCode' for URL '$url'");
        }

        if (!is_string($result)) {
            throw new Exception("Bad response for URL '$url'");
        }

        curl_close($ch);

        return $result;
    }

    /**
     *
     * @param  string $path
     * @param  string $postData
     * @param  array<string> $headers
     * @return string
     * @throws Exception
     */
    private function curlPostRaw(string $path, string $postData, array $headers = []): string {
        $ch = curl_init();
        if ($ch === false) {
            throw new Exception('Failed to initialise CURL');
        }

        $url = $this->getUrl($path);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('Error:' . curl_error($ch));
        }

        $responseStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (!in_array($responseStatusCode, [200])) {
            throw new Exception('Bad response code ', $responseStatusCode);
        }

        if (!is_string($result)) {
            throw new Exception('Bad response');
        }

        curl_close($ch);

        return $result;
    }

    /**
     *
     * @param  string $path
     * @return array<mixed>
     * @throws Exception
     */
    private function curlGetJson(string $path): array {
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
        ];

        $result = $this->curlGetRaw($path, $headers);

        $decoded = json_decode($result, true);
        if (!is_array($decoded)) {
            throw new Exception('Failed to decode JSON');
        }

        return $decoded;
    }

    private function curlPostJson(string $path, array $data = []): array {
        $postData = json_encode($data);

        if ($postData === false) {
            throw new Exception('Failed to JSON encode POST data');
        }

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
        ];

        $result = $this->curlPostRaw($path, $postData, $headers);

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
