<?php

require_once "src/ellipticcurve.php";


$success = 0;
$failure = 0;

function assertEqual($a, $b) {
    if ($a != $b) {
        global $failure;
        $failure ++;
        echo "\n      FAIL: '" . $a . "' != '" . $b . "'";
    } else {
        global $success;
        $success ++;
        echo "\n      success";
    }
}

function printHeader($text) {
    echo "\n  " . $text . " test:";
}

function printSubHeader($text) {
    echo "\n    " . $text . ":";
}


echo "\n\nRunning ECDSA tests:";

printHeader("ECDSA");

printSubHeader("testVerifyRightMessage");
$privateKey = new iAvatar777\services\EllipticCurve\PrivateKey;
$publicKey = $privateKey->publicKey();
$message = "This is the right message";
$signature = iAvatar777\services\EllipticCurve\Ecdsa::sign($message, $privateKey);
assertEqual(iAvatar777\services\EllipticCurve\Ecdsa::verify($message, $signature, $publicKey), true);

printSubHeader("testVerifyWrongMessage");
$privateKey = new iAvatar777\services\EllipticCurve\PrivateKey;
$publicKey = $privateKey->publicKey();
$message1 = "This is the right message";
$message2 = "This is the wrong message";
$signature = iAvatar777\services\EllipticCurve\Ecdsa::sign($message1, $privateKey);
assertEqual(iAvatar777\services\EllipticCurve\Ecdsa::verify($message2, $signature, $publicKey), false);


printHeader("openSSL");

printSubHeader("testAssign");
// Generated by: openssl ecparam -name secp256k1 -genkey -out privateKey.pem
$privateKeyPem = iAvatar777\services\EllipticCurve\Utils\File::read("tests/privateKey.pem");
$privateKey = iAvatar777\services\EllipticCurve\PrivateKey::fromPem($privateKeyPem);
$message = iAvatar777\services\EllipticCurve\Utils\File::read("tests/message.txt");
$signature = iAvatar777\services\EllipticCurve\Ecdsa::sign($message, $privateKey);
$publicKey = $privateKey->publicKey();
assertEqual(iAvatar777\services\EllipticCurve\Ecdsa::verify($message, $signature, $publicKey), true);

printSubHeader("testVerifySignature");
// openssl ec -in privateKey.pem -pubout -out publicKey.pem
$publicKeyPem = iAvatar777\services\EllipticCurve\Utils\File::read("tests/publicKey.pem");
// openssl dgst -sha256 -sign privateKey.pem -out signature.binary message.txt
$signatureDer = iAvatar777\services\EllipticCurve\Utils\File::read("tests/signatureDer.txt");
$message = iAvatar777\services\EllipticCurve\Utils\File::read("tests/message.txt");
$publicKey = iAvatar777\services\EllipticCurve\PublicKey::fromPem($publicKeyPem);
$signature = iAvatar777\services\EllipticCurve\Signature::fromDer($signatureDer);
assertEqual(iAvatar777\services\EllipticCurve\Ecdsa::verify($message, $signature, $publicKey), true);


printHeader("PrivateKey");

printSubHeader("testPemConversion");
$privateKey1 = new iAvatar777\services\EllipticCurve\PrivateKey;
$pem = $privateKey1->toPem();
$privateKey2 = iAvatar777\services\EllipticCurve\PrivateKey::fromPem($pem);
assertEqual($privateKey1->toPem, $privateKey2->toPem);

printSubHeader("testDerConversion");
$privateKey1 = new iAvatar777\services\EllipticCurve\PrivateKey;
$der = $privateKey1->toDer();
$privateKey2 = iAvatar777\services\EllipticCurve\PrivateKey::fromDer($der);
assertEqual($privateKey1->toPem(), $privateKey2->toPem());

printSubHeader("testStringConversion");
$privateKey1 = new iAvatar777\services\EllipticCurve\PrivateKey;
$str = $privateKey1->toString();
$privateKey2 = iAvatar777\services\EllipticCurve\PrivateKey::fromString($str);
assertEqual($privateKey1->toPem(), $privateKey2->toPem());


printHeader("PublicKey");

printSubHeader("testPemConversion");
$privateKey = new iAvatar777\services\EllipticCurve\PrivateKey;
$publicKey1 = $privateKey->publicKey();
$pem = $publicKey1->toPem();
$publicKey2 = iAvatar777\services\EllipticCurve\PublicKey::fromPem($pem);
assertEqual($publicKey1->toPem(), $publicKey2->toPem());

printSubHeader("testDerConversion");
$privateKey = new iAvatar777\services\EllipticCurve\PrivateKey;
$publicKey1 = $privateKey->publicKey();
$der = $publicKey1->toDer();
$publicKey2 = iAvatar777\services\EllipticCurve\PublicKey::fromDer($der);
assertEqual($publicKey1->toPem(), $publicKey2->toPem());


printSubHeader("testStringConversion");
$privateKey = new iAvatar777\services\EllipticCurve\PrivateKey;
$publicKey1 = $privateKey->publicKey();
$str = $publicKey1->toString();
$publicKey2 = iAvatar777\services\EllipticCurve\PublicKey::fromString($str);
assertEqual($publicKey1->toPem, $publicKey2->toPem);


printHeader("Signature");

printSubHeader("testDerConversion");
$privateKey = new iAvatar777\services\EllipticCurve\PrivateKey;
$message = "This is a text message";
$signature1 = iAvatar777\services\EllipticCurve\Ecdsa::sign($message, $privateKey);
$der = $signature1->toDer();
$signature2 = iAvatar777\services\EllipticCurve\Signature::fromDer($der);
assertEqual($signature1->r, $signature2->r);
assertEqual($signature1->s, $signature2->s);

printSubHeader("testBase64Conversion");
$privateKey = new iAvatar777\services\EllipticCurve\PrivateKey;
$message = "This is a text message";
$signature1 = iAvatar777\services\EllipticCurve\Ecdsa::sign($message, $privateKey);
$base64 = $signature1->toBase64();
$signature2 = iAvatar777\services\EllipticCurve\Signature::fromBase64($base64);
assertEqual($signature1->r, $signature2->r);
assertEqual($signature1->s, $signature2->s);


if ($failure == 0) {
    echo "\n\nALL " . $success . " TESTS SUCCESSFUL\n\n";
} else {
    echo "\n\n" . $failure . "/" . ($failure + $success) . " FAILURES OCCURRED\n\n";
}

?>
