<?php
/*
SAYFA: Hizmet sil
DOSYA: admin/hizmet-sil.php
KIM: Kisi 4

BU SAYFA EKRAN BASMAZ.
- Sadece POST ile calisir. Adres cubuguna id yazmak silmez.
- Gelen id'yi sayiya cevirip hizmetler tablosundan siler.
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
        mysqli_query($baglanti, "DELETE FROM hizmetler WHERE id = $id");
    }
}

header("Location: hizmetler.php");
exit;
