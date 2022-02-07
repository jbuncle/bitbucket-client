<?php declare(strict_types=1);
namespace JBuncle\BitbucketClient\Response;

class PullRequestDestination {

    private array $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function getCommit(): array {
        return $this->data['commit'];
    }

    public function getRepository(): array {
        return $this->data['repository'];
    }

    public function getBranch(): string {
        return $this->data['branch']['name'];
    }

}
