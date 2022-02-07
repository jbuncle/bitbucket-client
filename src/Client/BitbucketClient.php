<?php declare(strict_types=1);
namespace JBuncle\BitbucketClient\Client;

use Generator;
use Iterator;
use JBuncle\BitbucketClient\BitbucketApi;
use JBuncle\BitbucketClient\SearchQuery\ConditionI;
use JBuncle\BitbucketClient\Response\PullRequest;
use JBuncle\BitbucketClient\Response\PullRequestActivity;

class BitbucketClient {

    private BitbucketApi $api;
    private string $workspace;

    public function __construct(BitbucketApi $api, string $workspace) {
        $this->api = $api;
        $this->workspace = $workspace;
    }

    public function search(
            string $repo,
            ConditionI $searchCondition
    ): Generator {
        $query = (string) $searchCondition;

        $endpoint = $this->getBaseUrlForRepo($repo) . "/pullrequests?q=" . urlencode($query);

        do {
            $paginatedResponse = $this->api->paginatedRequest($endpoint);

            foreach ($paginatedResponse->getValues() as $value) {
                // TODO: introduce object
                yield new PullRequest($value);
            }

            $endpoint = $paginatedResponse->next();
        } while ($endpoint !== null);
    }

    public function getPullRequestActivity(
            string $repo,
            int $pullRequestId,
            int $pageLen = 50
    ): Iterator {
        $endpoint = $this->getBaseUrlForRepo($repo) . "/pullrequests/{$pullRequestId}/activity?pagelen=" . $pageLen;
        $response = $this->api->request($endpoint);

        // TODO: introduce object
        return PullRequestActivity::fromJsonResponse($response);
    }

    private function getBaseUrlForRepo(string $repo): string {
        return "/repositories/{$this->workspace}/{$repo}";
    }

}
