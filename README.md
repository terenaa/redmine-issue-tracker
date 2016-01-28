# Redmine issue tracker

Simple issue tracker for Redmine with text message notifications

Requirements:
* [terenaa\SmsGateway](https://github.com/terenaa/sms-gateway)

## Installation

```
composer require terenaa/redmine-issue-tracker
```

## Examples

```php
require_once __DIR__ . '/vendor/autoload.php';

use terenaa\IssueTracker\IssueTracker;

$it = new IssueTracker();
$it->run();
```