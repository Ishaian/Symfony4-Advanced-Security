Custom Security for Symfony 4.0
=====================

Requirements
------------

  * Php ^7.2    http://php.net/manual/fr/install.php;
  * Composer    https://getcomposer.org/download/;
  * Mysql 5.7 or MariaDB 10.2.7  or greater

Installation
------------

1 . Clone the current repository.

2 . Move in and create a `.env.local` file. 
**This one is not committed to the shared repository.**
Set `db_name`
 
3 . Execute commands below into your working folder to install the project:

```
$ composer install
$ bin/console d:d:c (create your DB)
$ bin/console d:m:m (execute migrations and create tables)
$ bin/console s:r
```



Entity
------------


- Simple User Entity : Unique Email per user

- Desabled account function for USER_ROLE own account (in order to join it with a cron script and delete all ROLE_DESABLE in db)


Registration
------------

- Registration form 

- Repeated / Encoded Passwords with constraints and requirements if empty on bdd or not

```
http://yourdomainname/register
```

Login
------------ 

- Login Form auto redirection if not connected

```
http://yourdomainname/login
```

Swift Mailer
------------
- MailManager service: on created/deleted accounts

- Parameters configured with some examples

Admin Features
------------

- Promote a user to admin / Demote an admin to user

- Crud user for admin

- Access denied for users

Next evolutions
------------

- 404 view

- Desable account function improvement 

- Forms improvements

- Flashes messages on login/logout

- SuperAdmin Creation for demote/promote security

