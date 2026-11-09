<?php
/*
SAYFA: Çıkış
DOSYA: cikis.php
KİM: Kişi 3
BU SAYFADA EKRAN YOK. Oturumu kapatır ve ana sayfaya gönderir.
*/

// Oturumu açarız ki kapatabilelim.
include "ortak/hazirla.php";

// HARİTA: Çıkış yapma (oturumu kapatma)
// Oturumdaki bütün bilgileri sileriz.
$_SESSION = array();
// Oturumu tamamen kapatırız.
session_destroy();
// Ana sayfaya gideriz.
yonlendir("index.php");
