## phalcon-project
基于phalcon/devtools 3.4 扩展的创建项目工具，扩展了适合自己使用的项目结构


## 环境要求
* PHP>=7.2
* Phalcon >=3.4
* Phalcon/devtools >=3.4
* vlucas/phpdotenv >=2.4
* composer >=2.0

## 通过composer安装
```base
composer require fe/phalcon-project
```
或者创建一个composer.json文件
```json
{
    "require": {
        "phalcon-project": "~1.0"
    }
}
```
然后执行：
```base
composer install
```

## 用法
进到工具src目录，然后执行
```base
php index.php
```

```sh
$ php index.php

Project Version (1.0.0)

Available commands:
  info             (alias of: i)                       -显示环境信息
  commands         (alias of: list, enumerate)         -列举所有支持的命令
  controller       (alias of: create-controller)       -创建controller文件
  module           (alias of: create-module)           -创建一个新模块
  model            (alias of: create-model)            -创建model文件
  all-models       (alias of: create-all-models)       -根据数据库表创建所有的model文件
  project          (alias of: create-project)          -创建新项目
  migration        (alias of: create-migration)        -创建数据库迁移文件
```

## LICENSE 
It is open source software licensed under the <a href="https://github.com/yanggenxin520wgy/phalcon-project/blob/main/LICENSE">MIT</a>.
