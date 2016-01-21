[![License](https://img.shields.io/badge/license-GPLv3-blue.svg)](http://www.gnu.org/licenses/gpl.txt)
[![Build Status](https://travis-ci.org/etraxis/etraxis.svg?branch=master)](https://travis-ci.org/etraxis/etraxis)
[![Code Coverage](https://scrutinizer-ci.com/g/etraxis/etraxis/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/etraxis/etraxis/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/etraxis/etraxis/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/etraxis/etraxis/?branch=master)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/0b93b04a-7ba8-49eb-b768-f1d4d1fa970c.svg)](https://insight.sensiolabs.com/projects/0b93b04a-7ba8-49eb-b768-f1d4d1fa970c)

### What is eTraxis

eTraxis is an issues tracking system with ability to set up unlimited number of fully customizable workflows.
eTraxis can be used for tracking almost anything, but the most popular cases are *bugs tracker* and *helpdesk system*.

### Key features

* Custom workflow templates
* Flexible permissions management
* MySQL, PostgreSQL, MSSQL and Oracle support
* Active Directory (LDAP) support
* OS independence
* Localization ability (a lot of different translations are already available)
* Browser-independent web interface (tested with Internet Explorer, Firefox, Opera, Safari, Chrome)
* Graphical project metrics
* Multilingual support (any record can contain text on several different languages)
* Customizable UI
* Dependencies between records
* History of events and changes
* Forum-like user comments with BBCode ability
* Filters and views
* Email notifications, subscriptions, and reminders
* Binary attachments
and more...

### Documentation

All the documentation can be found at **[etraxis.github.io](http://etraxis.github.io/)**.
Also there is a "[Support Forum](https://forum.etraxis.com/)" available.

### Install

```bash
composer.phar install
app/console doctrine:database:create
app/console doctrine:schema:create
```

### Development

```bash
gulp
./bin/php-cs-fixer fix
./bin/phpunit --coverage-html=vendor/coverage
```
