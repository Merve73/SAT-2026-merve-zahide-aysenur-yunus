<?php
/*
SAYFA: Tamirci onay kaydi
DOSYA: admin/kullanici-onay.php
KIM: Kisi 4

BU SAYFA EKRAN BASMAZ.
- POST ile gelen id'yi alir
- Sadece rol = tamirci olan satirin onayli degerini 1 yapar
*/

include "../ortak/baglanti.php";
include "../ortak/guvenlik.php";
sadece_admin();

if ($_SERVER["REQUEST_METHOD"] == "POST" && $veritabani_hazir) {
    $id = 0;
    if (isset($_POST["id"])) {
        $id = (int) $_POST["id"];
    }
    if ($id > 0) {
        mysqli_query($baglanti, "UPDATE kullanicilar SET onayli = 1 WHERE id = $id AND rol = 'tamirci'");
    }
}

header("Location: kullanicilar.php");
exit;
