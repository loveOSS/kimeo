# Kimeo: the missing contributions reporter for GitHub

Kimeo is a micro CLI application (and a minimalist web client) that is able to generate a list of merged PR
for a selected list of branches for an interval of dates.

![kimeo_cli](https://cloud.githubusercontent.com/assets/1247388/26287523/b074d7fe-3e7d-11e7-814e-ba3f8c6bfd2e.PNG)
 
 
## Installation
 
The login/password credentials are your GitHub's ones.
 
```bash
 dev@dev:~$ composer global require loveOSS/kimeo
 dev@dev:~$ export PATH="$PATH:$HOME/.composer/vendor/bin"
```

Then configure the application, you need to create and complete a `.env` file.
Use the `.env.dist` file provided:

```
GITHUB_OWNER=mickaelandrieu # the owner of GitHub project
GITHUB_REPOSITORY=kimeo # the name of GitHub project

GITHUB_LOGIN=mickaelandrieu # the name of GitHub account (for authentication)
GITHUB_PASSWORD=XXXXXXXXX # the password of GitHub account

CORE_MEMBERS=mickaelandrieu
```
### What are Core members?

For theses members, the line generated for each contributed will be different.

For Core members (ex):

* [#7839](https://github.com/PrestaShop/PrestaShop/pull/7839): Add sprintf check constraint on translations, by @Quetzacoalt91.

For beloved contributors (ex):

* [#7838](https://github.com/PrestaShop/PrestaShop/pull/7838): Return empty array instead of false.. Thank you @neoteknic!

## How to use the CLI application?

```bash
 dev@dev:~$ kimeo <from> <to> <list> <of> <branches> <...>
```

For instance:

```bash
 dev@dev:~$ kimeo 31-10-2016 9-12-2016 main feat-1
```

## How to use the Web client?

```bash
dev@dev:~$ cd /path/to/kimeo && php -S localhost:1234 # Then access http://localhost:1234
```

![kimeo_web](https://cloud.githubusercontent.com/assets/1247388/26287524/b092e9d8-3e7d-11e7-916d-82dedb6e80f2.PNG)

A file named ``report.md`` will be generated, you can manipulate it using any programming language.

## This is so great, I want to contribute! Where are the tests?

Ahaha, well tried ;) I don't want any contributions. If you like it, star it and use it. If you want to improve it, fork it and create the tool that fits your specific needs.
