## Простая и быстрая реализация ECDSA на PHP используя библиотеку функций openssl

### Введение

Это PHP-реализация алгоритма цифровой подписи с эллиптической кривой. Он совместим с PHP 5.5+. Обратите внимание, что эта библиотека сильно зависит от пакета openssl для PHP, поэтому - в зависимости от вашей установки PHP - вам может потребоваться повторно скомпилировать ее с флагом "–with-openssl".

Модуль обернут вокруг встроенных функций openssl, поэтому все стандартные кривые должны поддерживаться. Значение по умолчанию: `secp256k1`.

### Инсталяция

Для инсталяции ECDSA-PHP используйте composer:

```json
{
    "require": {
        "i-avatar777/service-ecdsa": "*"
    }
}
```

### Скорость

Мы провели тест на MAC Pro i7 2017. Мы пробежали библиотеку 100 раз и получили среднее время, отображаемое ниже:

| Библиотека         | Подпись       | Проверка  |
| ------------------ |:-------------:| ---------:|
| service-ecdsa      |     0.6ms     |  0.4ms    |

### Пример кода

Как подписать json сообщение:

```php
use iAvatar777\services\EllipticCurve\Ecdsa;

# Generate privateKey from PEM string
$privateKey = EllipticCurve\PrivateKey::fromPem("
    -----BEGIN EC PARAMETERS-----
    BgUrgQQACg==
    -----END EC PARAMETERS-----
    -----BEGIN EC PRIVATE KEY-----
    MHQCAQEEIODvZuS34wFbt0X53+P5EnSj6tMjfVK01dD1dgDH02RzoAcGBSuBBAAK
    oUQDQgAE/nvHu/SQQaos9TUljQsUuKI15Zr5SabPrbwtbfT/408rkVVzq8vAisbB
    RmpeRREXj5aog/Mq8RrdYy75W9q/Ig==
    -----END EC PRIVATE KEY-----
");


# Create message from json
$message = array(
    "transfers" => array(
        array(
            "amount" => 100000000,
            "taxId" => "594.739.480-42",
            "name" => "Daenerys Targaryen Stormborn",
            "bankCode" => "341",
            "branchCode" => "2201",
            "accountNumber" => "76543-8",
            "tags" => array("daenerys", "targaryen", "transfer-1-external-id")
        )
    )
);

$message = json_encode($message, JSON_PRETTY_PRINT);

$signature = Ecdsa::sign($message, $privateKey);

# Generate Signature in base64.
echo "\n" . $signature->toBase64();

# To double check if message matches the signature
$publicKey = $privateKey->publicKey();

echo "\n" . Ecdsa::verify($message, $signature, $publicKey);

```

Simple use:

```php
# Generate new Keys
use iAvatar777\services\EllipticCurve\PrivateKey;
use iAvatar777\services\EllipticCurve\Ecdsa;

$privateKey = new PrivateKey;
$publicKey = $privateKey->publicKey();

$message = "My test message";

# Generate Signature
$signature = Ecdsa::sign($message, $privateKey);

# Verify if signature is valid
echo "\n" . Ecdsa::verify($message, $signature, $publicKey);

```

### OpenSSL

Эта библиотека совместима с OpenSSL, поэтому вы можете использовать ее для генерации ключей:

```
openssl ecparam -name secp256k1 -genkey -out privateKey.pem
openssl ec -in privateKey.pem -pubout -out publicKey.pem
```

Создайте message.txt файл и подпишите его:

```
openssl dgst -sha256 -sign privateKey.pem -out signatureDer.txt message.txt
```

А теперь подпишем:

```php
use iAvatar777\services\EllipticCurve\Utils\File;
use iAvatar777\services\EllipticCurve\PublicKey;
use iAvatar777\services\EllipticCurve\Signature;
use iAvatar777\services\EllipticCurve\Ecdsa;

$publicKeyPem = Utils\File::read("publicKey.pem");
$signatureDer = Utils\File::read("signatureDer.txt");
$message = Utils\File::read("message.txt");

$publicKey = PublicKey::fromPem($publicKeyPem);
$signature = Signature::fromDer($signatureDer);

echo "\n" . Ecdsa::verify($message, $signature, $publicKey);

```

Вы можете проверить на своем терминале:

```
openssl dgst -sha256 -verify publicKey.pem -signature signatureDer.txt message.txt
```

```
openssl base64 -in signatureDer.txt -out signatureBase64.txt
```

Вы можете также проверить при помощи этой библиотеки:

```php
use iAvatar777\services\EllipticCurve\Utils\File;
use iAvatar777\services\EllipticCurve\Signature;

$signatureDer = Utils\File::read("signatureDer.txt");

$signature = Signature::fromDer($signatureDer);

echo "\n" . $signature->toBase64();
```

### Запуск UNIT тестов

```sh
php tests/test.php
```

[python-ecdsa]: https://github.com/warner/python-ecdsa
