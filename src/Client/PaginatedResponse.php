<?php declare(strict_types=1);

namespace JBuncle\BitbucketClient\Client;

class PaginatedResponse {

    private int $pagelen;
    private int $size;
    private array $values;
    private int $page;
    private ?string $next;

    public static function fromJson(string $requestUrl, array $data): PaginatedResponse {

        return new PaginatedResponse(
                $data['pagelen'],
                $data['size'],
                $data['values'],
                $data['page'],
                self::getNext($requestUrl, $data),
        );
    }

    private static function getNext(string $requestUrl, array $data): ?string {
        if (!isset($data['next'])) {
            return null;
        }

        $components = parse_url($data['next']);
        if ($components === false) {
            return $data['next'];
        }

        $path = (isset($components['path'])) ? $components['path'] : '';

        if (strpos($path, '/api/2.0') === 0) {
            $path = substr($path, strlen('/api/2.0'));
        }

        $next = '';
        $next .= $path;
        $next .= (isset($components['query'])) ? '?' . $components['query'] : '';

        if (isset($components['fragment'])) {
            $next .= '#' . $components['fragment'];
        }

        if ($requestUrl === $next) {
            return null;
        }

        return $next;
    }

    public function __construct(int $pagelen, int $size, array $values, int $page, ?string $next) {
        $this->pagelen = $pagelen;
        $this->size = $size;
        $this->values = $values;
        $this->page = $page;
        $this->next = $next;
    }

    public function getPagelen(): int {
        return $this->pagelen;
    }

    public function getSize(): int {
        return $this->size;
    }

    public function getValues(): array {
        return $this->values;
    }

    public function getPage(): int {
        return $this->page;
    }

    public function next(): ?string {
        return $this->next;
    }

}
