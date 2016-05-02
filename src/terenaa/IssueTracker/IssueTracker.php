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


use terenaa\TrackerScaffold\Tracker;

/**
 * Class IssueTracker
 * @package terenaa\IssueTracker
 */
class IssueTracker extends Tracker
{
    /**
     * Gets last entry from Redmine atom feed
     *
     * @return array
     * @throws \terenaa\TrackerScaffold\TrackerException
     */
    protected function getLastEntry()
    {
        $feed = $this->getAtomFeed();
        $return = array_fill_keys(array('guid', 'title'), '');

        if (!$feed) {
            return $return;
        }

        $entry = $feed->entry[0];
        $return['guid'] = substr($entry->id, strpos($entry->id, '/') + 1);
        $return['title'] = preg_replace('/\((.*?)\)/', "({$entry->author->name})", $entry->title) . ' (' . date('Y-m-d H:i:s', strtotime($entry->updated)) . ')';

        return $return;
    }
}
