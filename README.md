# Fizz Buzz Client

```
 __ _          _                   
/ _(_)        | |                  
| |_ _ _______| |__  _   _ ________
|  _| |_  /_  / '_ \| | | |_  /_  /
| | | |/ / / /| |_) | |_| |/ / / /
|_| |_/___/___|_.__/ \__,_/___/___|
```

**Requirements**

The following app is designed to be run  on a host with the following tools
- OS capable of running PHP 7.1.3+
- fizzbuzz-api package running on same machine in default settings (http://localhost:3000) please visit [https://github.com/lnesi/fizzbuzz-api](https://github.com/lnesi/fizzbuzz-api) for details.

**To run source also required:**

- composer [https://getcomposer.org/download/](https://getcomposer.org/download/)

**Required PHP Pacages:**
On Linux hosts the following packages (debian)
```
php-common                                      install
php-xdebug                                      install
php7.2-cli                                      install
php7.2-common                                   install
php7.2-intl                                     install
php7.2-json                                     install
php7.2-mbstring                                 install
php7.2-opcache                                  install
php7.2-readline                                 install
php7.2-xml                                      install
```

**Run build v0.1**

From builds folder. No installation or composer required

```
./fizzbuzz-client start
```
**Install for development or to run source**
```
composer install
```

**Run Source**
```
./fizzbuzz-client start
-- or --
php fizzbuzz-client start
```

**Build release**

From Root of project director
```
./fizzbuzz-client app:build
-- or --
php fizzbuzz-client app:build
```

This will generate a self executable php file inside the build folder.
