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

    public function paginatedRequest(string $requestUrl): PaginatedResponse {
        $json = $this->curl($requestUrl);
        if (array_key_exists('type', $json) && $json['type'] === 'error') {
            throw new Exception($json['error']['message'] . " '$requestUrl'");
        }

        return PaginatedResponse::fromJson($requestUrl, $json);
    }

    public function request(string $requestUrl) {
        $json = $this->curl($requestUrl);
        if (array_key_exists('type', $json) && $json['type'] === 'error') {
            throw new Exception($json['error']['message']);
        }

        return $json;
    }

    /**
     *
     * @param string $path
     * @return mixed
     * @throws Exception
     */
    private function curl(string $path) {
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

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('Error:' . curl_error($ch));
        }

        if (!is_string($result)) {
            throw new Exception('Bad response');
        }

        curl_close($ch);

        $decoded = json_decode($result, true);
        if ($decoded === null) {
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
