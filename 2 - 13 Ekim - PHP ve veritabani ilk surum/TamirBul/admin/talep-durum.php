<?php
/*
SAYFA: Talep durum kaydi
DOSYA: admin/talep-durum.php
KIM: Kisi 4

BU SAYFA EKRAN BASMAZ.
- Gelen durum listede var mi diye bakar
- Varsa talepler tablosunda gunceller
*/

include "../ortak/baglanti.php";
include "../ortak/guvenlik.php";
sadece_admin();

if ($_SERVER["REQUEST_METHOD"] == "POST" && $veritabani_hazir) {
    $id = 0;
    $durum = "";

    if (isset($_POST["id"])) {
        $id = (int) $_POST["id"];
    }
    if (isset($_POST["durum"])) {
        $durum = $_POST["durum"];
    }

    $kabul = false;
    if ($durum == "beklemede" || $durum == "tamirci_atandi" || $durum == "randevu_kuruldu" || $durum == "tamirde" || $durum == "tamamlandi" || $durum == "iptal") {
        $kabul = true;
    }

    if ($id > 0 && $kabul) {
        $durum_temiz = mysqli_real_escape_string($baglanti, $durum);
        mysqli_query($baglanti, "UPDATE talepler SET durum = '$durum_temiz' WHERE id = $id");
    }
}

header("Location: talepler.php");
exit;
