# Redmine issue tracker

Simple issue tracker for Redmine with text message notifications

Requirements:
* [terenaa\SmsGateway](https://github.com/terenaa/sms-gateway)

## Installation

```bash
git clone https://github.com/terenaa/redmine-issue-tracker.git RedmineIssueTracker
cd RedmineIssueTracker/
composer update
```

Copy an example config file (config/config.ini.example) and fill with correct values

## Examples

```php
require_once __DIR__ . '/vendor/autoload.php';

use terenaa\IssueTracker\IssueTracker;

$it = new IssueTracker();
$it->run();
```