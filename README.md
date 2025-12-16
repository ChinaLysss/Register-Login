# 🚀 Register&Login
一个轻量级的基于 **PHP + MySQL** 实现的用户注册与登录 API 项目,且支持双登录方式 （用户名+密码 or TOKEN）

![License](https://img.shields.io/badge/License-MIT-green.svg)
![Language](https://img.shields.io/badge/Language-PHP-blue.svg)
![Database](https://img.shields.io/badge/Database-MySQL-orange.svg)

## 📋 项目介绍
本项目提供了简洁高效的用户注册、登录API接口，核心聚焦于用户身份验证的基础功能，同时兼顾安全性和易用性。适用于小型项目、个人开发的接口验证场景，可直接集成或二次开发

## ✨ 功能特点
- 🔑 **双登录方式**：可以使用「用户名+密码」登录，也可以直接使用 TOKEN 登录
- 🛡️ **密码加密**：使用自定义加密方式，让密码安全更上一层楼‘
- 🆔 **TOKEN**：注册时自动生成随机且唯一的用户 TOKEN，方便后续接口的开发

## 📍 环境要求
- PHP 5.6+ / PHP 7.x / PHP 8.x（推荐7.4+）
- MySQL 5.5+ / MariaDB
- PHP开启**PDO_MySQL**扩展（必选，用于数据库操作）
- 任意Web服务器（Apache/Nginx/IIS）

## 📂 目录结构
```
Register&Login/
├── SQL.sql             # 数据库SQL
├── useraction.php      # 核心API文件（注册+登录逻辑）
└── README.md           # 项目说明文档
```

## 🚀 快速开始
### 1. 克隆仓库
```bash
git clone https://github.com/ChinaLysss/Register&Login.git
cd Register&Login
```

### 2. 导入数据库
将项目中的数据库文件（`SQL.sql`）导入到你的MySQL数据库中：
- 方式1：通过phpMyAdmin、Navicat等工具直接导入
- 方式2：使用MySQL命令行导入
  ```bash
  mysql -u root -p 你的数据库名 < SQL.sql
  ```

### 3. 配置数据库
打开`useraction.php`文件，修改第 11 行至第 14 行数据库信息：
```php
$db_host = 'localhost';   // 数据库主机
$db_user = 'aaaaaa';      // 数据库用户名
$db_pass = 'aaaaaa';      // 数据库密码
$db_name = 'aaaaaa';      // 数据库名
```

### 4. 部署运行
将项目文件上传至你的Web服务器根目录

## 📖 使用说明
### 接口请求说明
- 请求方式：**GET/POST**
- 返回格式：JSON
- Action： `register`（注册）、`login`（登录）

### 1. 用户注册
#### 请求参数
| 参数名      | 说明           | 是否必填 |
|-------------|----------------|----------|
| action      | 操作类型，值为`register` | 是       |
| username    | 用户名         | 是       |
| password    | 密码           | 是       |
| repassword  | 重复密码       | 是       |

#### 请求示例（POST）
```bash
curl -X POST http://你的域名/useraction.php \
-d "action=register&username=test123&password=123456&repassword=123456"
```

#### 返回示例（成功）
```json
{
    "code": 200,
    "msg": "注册成功",
    "data": {
        "UID": 1,
        "Username": "test123",
        "TOKEN": "f97e27a993014d19821929e187209112d4c889a7e"
    }
}
```

### 2. 登录（方式1：用户名+密码）
#### 请求参数
| 参数名      | 说明           | 是否必填 |
|-------------|----------------|----------|
| action      | 操作类型，值为`login` | 是       |
| username    | 用户名         | 是       |
| password    | 密码           | 是       |

#### 请求示例（POST）
```bash
curl -X POST http://你的域名/useraction.php \
-d "action=login&username=test123&password=123456"
```

### 3. 登录（方式2：TOKEN）
#### 请求参数
| 参数名      | 说明           | 是否必填 |
|-------------|----------------|----------|
| action      | 操作类型，值为`login` | 是       |
| token       | 注册时生成的TOKEN | 是       |

#### 请求示例（POST）
```bash
curl -X POST http://你的域名/useraction.php \
-d "action=login&token=f97e27a993014d19821929e187209112d4c889a7e"
```

#### 登录成功返回示例
```json
{
    "code": 200,
    "msg": "登录成功",
    "data": {
        "UID": 1,
        "Username": "test123",
        "TOKEN": "f97e27a993014d19821929e187209112d4c889a7e"
    }
}
```



## 📄 许可证
本项目采用 **MIT许可证** 开源 - 详见 [LICENSE](LICENSE) 文件。

## 💬 说明
如果觉得项目对你有帮助，欢迎点个⭐️ Star支持一下～ 如有问题或建议，可提交Issue或PR。
