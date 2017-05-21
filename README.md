# Kimeo: the missing commits report for GitHub

Kimeo is a micro CLI application (and a minimalist web client) that is able to generate a list of merged PR
for a selected list of branches for an interval of dates.
 
 
## Installation
 
The login/password credentials are your GitHub's ones.
 
```bash
 dev@dev:~$ composer global require mickaelandrieu/kimeo
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
 dev@dev:~$ kimeo <login> <password> <from> <to> <list> <of> <branches> <...>
```

For instance:

```bash
 dev@dev:~$ kimeo mickaelandrieu XXXXXXXXX 31-10-2016 9-12-2016 1.7.0.x develop 1.6.1.x
```

## How to use the Web client?

```bash
dev@dev:~$ cd kimeo && php -S localhost:1234 # Then access http://localhost:1234
```

A file named ``report.md`` will be generated, you can manipulate it using any programming language.
