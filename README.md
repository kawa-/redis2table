# redis2table

Simple table based viewer of Redis. You can monitor every redis keys like Key, List, Set, Sorted Set, Hash and Redis info.

## Demo site

[redis2table](http://mitsuakikawamorita.com/software/redis2table/)

## Screenshot

![image](http://mitsuakikawamorita.com/software/redis2table/redis2table_2013-05-19_16-08-57.png)

## Install

### Ubuntu / Debian

First, please install PHP, Apache, Redis, [phpredis](https://github.com/nicolasff/phpredis). For detail, [Here](http://anton.logvinenko.name/en/blog/how-to-install-redis-and-redis-php-client.html) is very usefull.

```
$ git clone git://github.com/kawa-/redis2table.git
$ cd -R redis2table/ /var/www/
or using built-in webserver… (PHP >= 5.4)
$ cd redis2table
$ php54 -S localhost:8080
```

After that, visit [http://localhost:8080/index.php?host=localhost&port=6379](http://localhost:8080/index.php?host=localhost&port=6379) (by Apache et al.).

### CentOS

similar to Ubuntu / Debian.

## ToDo

- auto reload by ajax
- more document
- cool design

