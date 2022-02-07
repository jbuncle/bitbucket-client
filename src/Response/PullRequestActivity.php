<?php declare(strict_types=1);
namespace JBuncle\BitbucketClient\Response;

use DateTime;
use Exception;

class PullRequestActivity {

    public const TYPE_COMMENT = 'comment';
    public const TYPE_APPROVAL = 'approval';
    public const TYPE_UPDATE = 'update';
    public const TYPE_CHANGES_REQUESTED = 'changes_requested';

    private string $username;
    private DateTime $date;
    private string $type;
    private string $update;

    public static function fromJsonResponse(array $response): \Generator {
        foreach ($response['values'] as $value) {
            yield self::fromJsonValue($value);
        }
    }

    private static function fromJsonValue(array $value): PullRequestActivity {
        if (isset($value['comment'])) {
            $user = $value['comment']['user']['nickname'];
            $comment = trim($value['comment']['content']['raw']);
            $time = new DateTime($value['comment']['created_on']);

            return new PullRequestActivity($user, $time, self::TYPE_COMMENT, $comment);
        } else if (isset($value['approval'])) {
            $user = $value['approval']['user']['nickname'];
            $time = new DateTime($value['approval']['date']);

            return new PullRequestActivity($user, $time, self::TYPE_APPROVAL, '');
        } else if (isset($value['changes_requested'])) {
            $user = $value['changes_requested']['user']['nickname'];
            $time = new DateTime($value['changes_requested']['date']);

            return new PullRequestActivity($user, $time, self::TYPE_CHANGES_REQUESTED, '');
        } else if (isset($value['update'])) {
            $user = $value['update']['author']['nickname'];
            $time = new DateTime($value['update']['date']);
            $state = $value['update']['state'];

            return new PullRequestActivity($user, $time, self::TYPE_UPDATE, $state);
        } else {
            throw new Exception("Unexpected activity" . var_export($value, true));
        }
    }

    public function __construct(string $username, DateTime $date, string $type, string $update) {
        $this->username = $username;
        $this->date = $date;
        $this->type = $type;
        $this->update = $update;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getDate(): DateTime {
        return $this->date;
    }

    public function getType(): string {
        return $this->type;
    }

    public function getUpdate(): string {
        return $this->update;
    }

}
