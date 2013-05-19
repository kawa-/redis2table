# redis2table

Simple table based viewer of Redis. You can monitor every redis keys like Key, List, Set, Sorted Set, Hash.

## Demo site

[redis2table](http://mitsuakikawamorita.com/software/redis2table/)

## Screenshot

## Install

### Ubuntu / Debian

First, please install PHP, Apache, Redis, phpredis.

```
$ git clone git://github.com/kawa-/redis2table.git
$ cd -R redis2table/ /var/www/
or using built-in webserver… (PHP >= 5.4)
$ cd redis2table
$ php54 -S localhost:8080
```

After that, visit [http://localhost:8080/redis2table](http://localhost:80/redis2table) (by Apache et al.) or [http://localhost:8080/](http://localhost:8080/) (by built-in webserver)

### CentOS

similar to Ubuntu / Debian.

## ToDo

- auto reload
- more document
