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
// Çıkışı kayda yazarız. Oturum silinmeden önce, kim olduğu belli olsun diye.
log_yaz("Çıkış yapıldı", "");
// Oturumdaki bütün bilgileri sileriz.
$_SESSION = array();
// Tarayıcıdaki oturum çerezini de sileriz ("Beni hatırla" 30 gün saklıyordu). Geçmiş bir zaman verince çerez silinir.
setcookie(session_name(), "", array("expires" => time() - 3600, "path" => "/"));
// Oturumu tamamen kapatırız.
session_destroy();
// Ana sayfaya gideriz.
yonlendir("index.php");
