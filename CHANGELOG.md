## 0.7.0 (2025-10-23)
* Feature: add support for PHP 8.1
* Feature: rebuild the entire engine, now it works on top of Nodejs chokidar package.

## 0.6.0 (2020-05-11)
* Fix: don't use child process for resource watching

## 0.5.2 (2019-12-07)
* Fix: use predefined const for PHP binary [#59](https://github.com/seregazhuk/php-watcher/pull/59)
* Fix: increase dependency options [#58](https://github.com/seregazhuk/php-watcher/pull/58) by @mmoreram 

## 0.5.1 (2019-11-03)
* Fix: ability to disable the spinner [#48](https://github.com/seregazhuk/php-watcher/pull/48)

## 0.5.0 (2019-11-01)
* Feature: watching whether the script is alive [#47](https://github.com/seregazhuk/php-watcher/pull/47)

## 0.4.3 (2019-10-28)
* Improved: reused a package for spinner implementation [#45](https://github.com/seregazhuk/php-watcher/pull/45)
* Fix: Output stderr of the underlying script [#44](https://github.com/seregazhuk/php-watcher/pull/44) 
* Improved: Improve file changes watching [#42](https://github.com/seregazhuk/php-watcher/pull/42)

## 0.4.2 (2019-10-18)
* Fix: Make script argument required via cli [#36](https://github.com/seregazhuk/php-watcher/pull/36)
* Fix: Move symfony process to dev dependencies [#34](https://github.com/seregazhuk/php-watcher/pull/34) 
* Fix: improvements in spinner rendering [#32](https://github.com/seregazhuk/php-watcher/pull/32)

## 0.4.1 (2019-10-15)
* Fix: CLI empty options override values from config file [#30](https://github.com/seregazhuk/php-watcher/pull/30)

## 0.4.0 (2019-10-15)
* Fix: allow to listen to signals when running inside the docker container [#27](https://github.com/seregazhuk/php-watcher/pull/27)
* Feature: send custom signals to restart the app [#27](https://github.com/seregazhuk/php-watcher/pull/27)

## 0.3.1 (2019-10-10)
* Fix: autoload path inside watcher.php fixed for composer project
 installation  ([#16](https://github.com/seregazhuk/php-watcher/pull/16)) by [gorbunov](https://github.com/gorbunov)
* Fix: custom spinner implementation ([#19](https://github.com/seregazhuk/php-watcher/pull/19))   
* Fix: restore screen cursor when interrupting the script ([#20](https://github.com/seregazhuk/php-watcher/pull/20)) 

## 0.3.0 (2019-10-08)

* Feature: add spinner to output ([#11](https://github.com/seregazhuk/php-watcher/pull/11))
* Feature / BC break: move to PHP 7.2 ([#11](https://github.com/seregazhuk/php-watcher/pull/11))
* Fix: make it truly async ([#10](https://github.com/seregazhuk/php-watcher/pull/10))

## 0.2.0 (2019-10-04)

* Feature: allow to specify config file via CLI option ([#6](https://github.com/seregazhuk/php-watcher/pull/6))
* Fix: default CLI options override config values ([#2](https://github.com/seregazhuk/php-watcher/pull/4))

## 0.1.0 (2019-10-03)

* First tagged release
