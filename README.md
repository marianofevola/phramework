# phramework
Phalcon framework
Spins up a new skeleton component-repository-model application that handles:
- config
    - yaml
- database
    - mySql
- login
    - failed login
    - success login
    - remember me token
- signup
- assets management
    - css
    - js

Every application is installed in the root. Example:

app/
- config
    - frontend
        - default
        - login #module specific assests
    default.yaml
    dev.yaml
    prod.yaml
- frontend
    - public
    - src
        - Common
            - View
                - Layout
                    FrontendLayout.html
        - Modules
            - Login
                - Controllers
                - Views
- vvendor
    - marianofevola
        - phramework
            - phramework

## Phinx Database migrator
Add alias in ~/.bash_profile
```bash
alias phinx="./vendor/bin/phinx"
```
Create Migration: 
```bash
phinx create MyMigrationName
```
Execute Migration
```bash
phinx migrate -e dev
```
Rollback Migration
```bash
phinx rollback --dry-run
phinx rollback -e dev
```
Seeding https://book.cakephp.org/phinx/0/en/seeding.html
Create seeder:
```bash
phinx seed:create UserSeeder 
```
Execute all seeds:
```bash
php vendor/bin/phinx seed:run -e dev
```
Execute specific seeds:
```bash
php vendor/bin/phinx seed:run -s UserSeeder
