<?php
/*
SAYFA: Cikis
DOSYA: cikis.php
KIM: Kisi 3

BU SAYFADA EKRAN YOK.
- Oturumdaki giris bilgisini siler
- Kullaniciyi ana sayfaya gonderir
*/

include "ortak/guvenlik.php";

// Oturum dizisini bosaltiriz.
$_SESSION = array();

// Tarayicidaki oturum dosyasini da kapatiriz.
session_destroy();

header("Location: index.php");
exit;
