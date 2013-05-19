# redis2table

Simple table based viewer of Redis. You can monitor every redis keys like Key, List, Set, Sorted Set, Hash.

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

After that, visit [http://localhost:8080/redis2table](http://localhost:80/redis2table) (by Apache et al.) or [http://localhost:8080/](http://localhost:8080/) (by built-in webserver)

### CentOS

similar to Ubuntu / Debian.

## Edit

First, go to index.php…

```
$ cd redis2table
$ vi index.php
```

Next, edit index.php line #5 on your Redis env.

```
$redis->connect("localhost", 6379, 2.5);
// "2.5" means timeout. So let it be. 
```

If you redis data is empty, please uncomment "Testing data".

```
/* Testing data
$redis->flushAll();

$redis->set(uniqid("key_"), uniqid("key_value_1_"));

// ~ omitted ~ //

$redis->zAdd($key_zset_03, time(), uniqid("zset_value_22_"));
$redis->zAdd($key_zset_03, time(),uniqid("zset_value_23_"));
*/
```


## ToDo

- auto reload
- more document


