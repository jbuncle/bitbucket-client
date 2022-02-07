<?php declare(strict_types=1);
namespace JBuncle\BitbucketClient\Response;

use DateTime;

/**
 * PullRequest
 *
 * @author James Buncle <jbuncle@hotmail.com>
 */
class PullRequest {

    private array $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function getUrl(): string {
        return '/' . $this->getOrganisation() .'/' . $this->getRepository() . '/pull-requests/' . $this->getId();
    }

    public function getOrganisation(): string {

        $fullRepoName = $this->getSource()['repository']['full_name'];
        $parts = explode('/', $fullRepoName, 2);

        return $parts[0];
    }

    public function getRepository(): string {

        $fullRepoName = $this->getSource()['repository']['full_name'];
        $parts = explode('/', $fullRepoName, 2);

        return $parts[count($parts) - 1];
    }

    public function getDescription(): string {
        return $this->data['description'];
    }

    public function getTitle(): string {
        return $this->data['title'];
    }

    public function getCloseSourceBranch(): bool {
        return $this->data['close_source_branch'];
    }

    public function getType(): string {
        return $this->data['type'];
    }

    public function getId(): int {
        return $this->data['id'];
    }

    public function getDestination(): PullRequestDestination {
        return new PullRequestDestination($this->data['destination']);
    }

    public function getCreatedOn(): DateTime {
        return new DateTime($this->data['created_on']);
    }

    public function getSummary(): string {
        return $this->data['description'];
    }

    public function getCommentCount(): int {
        return $this->data['comment_count'];
    }

    public function getState(): string {
        return $this->data['state'];
    }

    public function getTaskCount(): int {
        return $this->data['task_count'];
    }

    public function getReason(): string {
        return $this->data['reason'];
    }

    public function getUpdateOn(): DateTime {
        return new DateTime($this->data['updated_on']);
    }

    public function getAuthor(): array {
        return $this->data['author'];
    }

    public function getMergeCommit(): array {
        return $this->data['merge_commit'];
    }

    public function getClosedBy(): array {
        return $this->data['closed_by'];
    }

    public function getSource(): array {
        return $this->data['source'];
    }

}
