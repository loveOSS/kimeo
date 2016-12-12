# Core Weekly report generator

Traces is a micro CLI application that is able to generate a list of merged PR
for a selected list of branches in an interval of dates.
 
 
## Installation
 
The authentication is a basic login/password for GitHub.
 
```bash
 $ composer install prestashop/core-weekly-report
 
 $ ./vendor/bin/core-weekly-generator <login> <password> <from> <to> <list> <of> <branches> <...>
```
 
For instance:

```bash
./core-weekly-generator mickaelandrieu XXXXXXXXX 31-10-2016 9-12-2016 1.7.0.x develop 1.6.1.x
```

A file named ``weekly-report.md`` will be generated, you can manipulate it using any programming language.

