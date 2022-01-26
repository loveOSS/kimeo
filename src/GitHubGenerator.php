<?php

namespace Kimeo;

use DateTime;
use Symfony\Component\HttpClient\HttpClient;

class GitHubGenerator
{
    const LAST = '/=([[:digit:]]+)>; rel="last"/';

    private $user;
    private $password;
    private $owner;
    private $project;
    private $report;

    public function __construct($user, $password, $owner, $project)
    {
        $this->user = $user;
        $this->password = $password;
        $this->owner = $owner;
        $this->project = $project;
    }

    public function generate(DateTime $from, DateTime $to, array $branches)
    {
        $allPullRequests = [];
        $authHeaders = ['auth_basic' => [$this->user, $this->password]];
        $client = HttpClient::create(['timeout'  => 15.0,]);

        $endPoint = $this->generateEndPoint();

        foreach ($branches as $branch) {
            $requestUri = str_replace('{branch}', $branch, $endPoint);
            $response = $client->request('GET', $requestUri, $authHeaders);

            if ('application/json; charset=utf-8' === $response->getHeaders()['content-type'][0]) {
                $allPullRequestsForBranch = [];
                if(array_key_exists('link', $response->getHeaders())){
                    $headerValue = $response->getHeaders()['link'][0];
                    preg_match(self::LAST, $headerValue, $matches);
                    $nbPages = min($matches[1], 5);  

                    for ($i = 1; $i <= $nbPages; $i++) {
                        $pullRequests = [];
                        $currentResponse = $client->request('GET', $requestUri . '&page=' . $i, $authHeaders);
                        
                        $allPullRequestsForBranch = array_merge($allPullRequestsForBranch, $this->getPullRequests($currentResponse));
                    }

                    $allPullRequests[$branch] = $allPullRequestsForBranch;
                }
            }

            $allPullRequests[$branch] = $this->getPullRequests($response);
        }

        $report = '';
        $coreMembers = $this->getCoreMembers();
        
        foreach ($allPullRequests as $branch => $pullRequests) {
            $report .= '# '.$branch . PHP_EOL;
            
            foreach ($pullRequests as $pullRequest) {
                $mergedAt = new DateTime($pullRequest['merged_at']);
                if ($mergedAt !== '') {
                    if ($this->isDateBetweenDates($mergedAt, $from, $to)) {
                        $report .= "* [#". $pullRequest['number']. "](".$pullRequest['html_url']."): ". $pullRequest['title'];
                        $author = $pullRequest['login'];
                  
                        if (in_array($author, $coreMembers)) {
                            $report .= ", by @" . $author . "." . PHP_EOL;
                        }else {
                            $report .= ". Thank you @" . $author . "!" . PHP_EOL;
                        }
                    }
                }
            }
        }
        $this->report = $report;
    }

    public function getReport()
    {
        return $this->report;
    }

    private function isDateBetweenDates(DateTime $date, DateTime $startDate, DateTime $endDate) {
        return $date > $startDate && $date < $endDate;
    }

    private function reducePullRequest($pullRequest)
    {
        return [
            'merged_at' => $pullRequest['merged_at'],
            'html_url' => $pullRequest['html_url'],
            'number' => $pullRequest['number'],
            'title' => $pullRequest['title'],
            'login' => $pullRequest['user']['login']
        ];
    }

    private function generateEndPoint()
    {
        $apiRoot = 'https://api.github.com/repos/';

        return $apiRoot . $this->owner . '/' . $this->project . '/pulls?base={branch}&state=closed';
    }

    private function getCoreMembers()
    {
        $coreMembers = explode(' ', $_ENV['CORE_MEMBERS']);

        return empty($coreMembers) ? [] : $coreMembers;
    }

    private function getPullRequests($response)
    {
        $pullRequestsAsObjects = json_decode($response->getContent(), true);
                      
        foreach ($pullRequestsAsObjects as $prObject) {
            $pullRequests[$prObject['number']] = $this->reducePullRequest($prObject);
        }

        return $pullRequests;
    }
}
