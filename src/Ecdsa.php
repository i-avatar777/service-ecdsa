<?php

namespace iAvatar777\services\EllipticCurve;


class Ecdsa
{

    public static function sign($message, $privateKey)
    {
        $signature = null;
        openssl_sign($message, $signature, $privateKey->openSslPrivateKey, OPENSSL_ALGO_SHA256);

        return new Signature($signature);
    }

    /**
     * @param $message
     * @param Signature $signature
     * @param PublicKey $publicKey
     * @return bool
     */
    public static function verify($message, Signature $signature, PublicKey $publicKey)
    {
        $success = openssl_verify($message, $signature->toDer(), $publicKey->openSslPublicKey, OPENSSL_ALGO_SHA256);
        if ($success == 1) {
            return true;
        }

        return false;
    }
}

?>