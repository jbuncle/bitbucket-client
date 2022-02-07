<?php declare(strict_types=1);
use JBuncle\BitbucketClient\Client\BitbucketClient;
use JBuncle\BitbucketClient\BitbucketClientFactory;

$username = "myusername";
$password = "mypassword";

$clientFactory = new BitbucketClientFactory($username, $password);

$workspace = 'my-workspace';
$repository = 'my-repository';
$pullRequestId = 1;

/** @var BitbucketClient $client */
$client = $clientFactory->getInstanceForWorkspace($workspace);
$client->getPullRequestActivity($repository, $pullRequestId);
