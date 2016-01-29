<?php
/**
 * Simple issue tracker for Redmine with text message notifications
 *
 * PHP version 5
 *
 * @category Utils
 * @author Krzysztof Janda <terenaa@the-world.pl>
 * @license https://opensource.org/licenses/MIT MIT
 * @version 1.0
 * @link https://www.github.com/terenaa/redmine-issue-tracker
 */

namespace terenaa\IssueTracker;

use terenaa\SmsGateway\SmsGateway;
use terenaa\SmsGateway\SmsGatewayException;


/**
 * Class IssueTracker
 * @package terenaa\IssueTracker
 */
class IssueTracker
{
    protected $config;

    /**
     * IssueTracker constructor.
     */
    public function __construct()
    {
        try {
            $this->getConfig();
        } catch (IssueTrackerException $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }

    /**
     * Runs tracker in infinite loop with specified intervals
     */
    public function run()
    {
        while (true) {
            try {
                $entry = $this->getLastEntry();

                echo date('Y-m-d H:i:s ');

                if ($this->getCache() != $entry['number']) {
                    exec("notify-send --urgency=critical --expire-time=5000 --icon=/usr/share/icons/Numix-Circle/scalable/apps/apport.svg '{$entry['title']}'");
                    $this->setCache($entry['number']);

                    echo $entry['title'] . PHP_EOL;

                    if ($this->config['notify']) {
                        try {
                            $sms = new SmsGateway();
                            $sms->send($this->config['phone'], $entry['title']);
                        } catch (SmsGatewayException $e) {
                            echo $e->getMessage() . PHP_EOL;
                        }
                    }
                }
            } catch (IssueTrackerException $e) {
                echo $e->getMessage() . PHP_EOL;
            }

            sleep($this->config['refresh']);
        }
    }

    /**
     * Gets atom feed from Redmine
     *
     * @return \SimpleXMLElement
     * @throws \terenaa\IssueTracker\IssueTrackerException
     */
    protected function getAtomFeed()
    {
        if (!($feed = simplexml_load_file("{$this->config['protocol']}://{$this->config['domain']}/projects/{$this->config['project']}/issues.atom?key={$this->config['auth_key']}"))) {
            throw new IssueTrackerException('Cannot load atom feed.');
        }

        return $feed;
    }

    /**
     * Gets last entry from Redmine atom feed
     *
     * @return array
     * @throws \terenaa\IssueTracker\IssueTrackerException
     */
    protected function getLastEntry()
    {
        $entry = $this->getAtomFeed()->entry[0];
        $fullTitle = preg_replace('/\((.*?)\)/', "({$entry->author->name})", $entry->title) . ' (' . date('Y-m-d H:i:s', strtotime($entry->updated)) . ')';
        $issueNumber = substr($entry->id, strpos($entry->id, '/') + 1);

        return array(
            'title' => $fullTitle,
            'number' => $issueNumber
        );
    }

    /**
     * Gets configuration settings from file
     *
     * @throws IssueTrackerException
     */
    protected function getConfig()
    {
        if (!$this->config = parse_ini_file(__DIR__ . '/../../config/config.ini')) {
            throw new IssueTrackerException('Config.ini file missing.');
        };
    }

    /**
     * Gets last issue id from cache file
     *
     * @return null|string
     */
    protected function getCache()
    {
        $file = __DIR__ . '/../../config/issue.cache';

        if (file_exists($file)) {
            return file_get_contents($file);
        }

        return null;
    }

    /**
     * Saves last issue id into cache file
     *
     * @param $issue
     */
    protected function setCache($issue)
    {
        file_put_contents(__DIR__ . '/../../config/issue.cache', $issue);
    }
}
