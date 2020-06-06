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

Folder structure example:
```bash
app/
config/
    frontend/
        defaults.yaml
        login.yaml #module specific assests
    default.yaml
    dev.yaml
    prod.yaml
frontend/
    public/
    src/
        Common/
            View/
                Layout/
                    FrontendLayout.html
                partials/
        Modules/
            Login/
                Controllers/
                Views/
vendor/
    marianofevola/
        phramework/
```
## View
To use common partials use:
```php
$this->view->partialCommon();
```
# Configuration
## Session handling
- create config/default.yaml and add your session folder, example:
```yaml
application:
  sessionSavePath: var/cache/session
```

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
Rollback all migrations
```bash
phinx rollback -e dev -t 0
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
