# [Gojek] GopayID Api PHP Class (Un-Official)
Repository Berikut Ini Merupakan Porting Dari [GOJEK](https://github.com/ridwanfathin/gojek) dan (https://github.com/namdevel/gopay-api) Untuk PHP 

[![CodeFactor](https://www.codefactor.io/repository/github/mugypleci/gopay-api/badge)](https://www.codefactor.io/repository/github/mugypleci/gopay-api)
[![MIT license](https://img.shields.io/badge/License-MIT-blue.svg)](https://lbesson.mit-license.org/)
[![PHP Libraries](https://badgen.net/badge/icon/libraries?icon=libraries&label)](https://github.com/mugypleci/gopay-api)
[![Open Source Love svg1](https://badges.frapsoft.com/os/v1/open-source.svg?v=103)](https://github.com/mugypleci/gopay-api)
[![HitCount](http://hits.dwyl.com/mugypleci/gopay-api.svg)](http://hits.dwyl.com/mugypleci/gopay-api)

Compliant with the Nov 10, 2020 Gojek API update.

<b>[Fitur Akun Pengguna GOPAY](#fitur-akun-pengguna-gopay)</b>
------------
- [x] LoginNumberPhone
- [x] getAccessToken
- [x] transferGopayID
- [x] transferBank
- [x] getHistory
- [x] getProfile
- [x] getAccountBalance
- [x] getQrid
- [x] getBankList
- [x] getBankAccountName


### Dokumentasi

#### Langkah Untuk Menjalankan GopayID API
##### Ambil Paket Pada Composer
```php
composer require gyugie/gopayid
```

##### Jika Di Jalankan Dengan Native
```php
require 'vendor/autoload.php';
use Gyugie\GopayID;

$gopay = new GopayID();
```

#### Fitur Akun Pengguna GopayID
##### Login Dengan Nomor Handphone
```php
$loginToken = $gopay->LoginNumberPhone('<mobilePhone>')->getResult();
```
##### Login Pada GopayID Untuk Mendapatkan Access Token
```php
$authToken = $gopay->getAuthToken('<loginToken>', '<OTP>')->getResult();
```
##### Menampilkan Informasi Akun Pengguna
```php
$gopay = new GopayID('<access_token>');
$result = $gopay->getProfile()->getResult();
```
##### Transfer Ke Sesama GOPAY
```php
$gopay = new GopayID('<access_token>');
$result = $gopay->transferGopayID('<phoneNumber>', '<amount>', '<pin>')->getResult();
```
##### Transfer Melalui Bank
```php
$gopay = new GopayID('<access_token>');
$result = $gopay->transferBank('<bankCode>', '<bankNumber>', '<amount>', '<pin>')->getResult();
```
##### Mendapatkan List Bank
```php
$gopay = new GopayID('<access_token>');
$result = $gopay->getBankList()->getResult();
```
##### Mendapatkan History Transaksi
```php
$gopay = new GopayID('<access_token>');
$result = $gopay->getHistoryTransaction()->getResult();
```
##### Menampilkan Profile User
```php
$gopay = new GopayID('<access_token>');
$result = $gopay->getBalance()->getResult();
```
##### Mendapatkan Qrid
```php
$gopay = new GopayID('<access_token>');
$result = $gopay->getQrid('<phoneNumber>')->getResult();
```
##### Mendapatkan Nama Bank Account
```php
$gopay = new GopayID('<access_token>');
$result = $gopay->getBankAccountName('<bankCode>', '<bankNumber>')->getResult();
```

Contributing
------------

All kinds of contributions are welcome - code, tests, documentation, bug reports, new features, etc...

* Send feedbacks.
* Submit bug reports.
* Write/Edit the documents.
* Fix bugs or add new features.
