# Cool User Extras

## Overview
A Drupal module that adds some extra user functionalities
 - **Import users from other database**
   - This utility helps you to import users (name and mail) from other database (MySQL/MariaDB).
   - Passwords won't be imported. It will generate a random password for all imported users.
   - Each time the cron executes, it tries to sync/import users.
 - **Load default user**
   - Loads the owner user by default on user entity reference field when you create a new entity.
   - If you enable this option, the field also will become read only when you add or edit.

## Requirements
This module requires no modules outside of Drupal core.

It works with Drupal 10. Developed and tested on an environment with: 
- Drupal core: 10.0.7
- PHP: 8.1.18

## Installation
Install as you would normally install a contributed Drupal module. 
For further information, see [Installing Drupal Modules](https://www.drupal.org/docs/extending-drupal/installing-drupal-modules).

## More information
[Check the Drupal module page](https://www.drupal.org/project/cool_user_extras)
