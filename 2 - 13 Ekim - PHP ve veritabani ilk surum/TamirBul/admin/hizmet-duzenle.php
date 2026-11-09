<?php
/*
SAYFA: Hizmet duzenle
DOSYA: admin/hizmet-duzenle.php
KIM: Kisi 4

BU SAYFADA BUNLAR OLACAK:
- Adres cubugundaki id ile hizmet okunur
- Form kutulari mevcut bilgiyle dolu gelir
- Kaydet deyince UPDATE calisir
*/

$kok = "../";
$sayfa_basligi = "Hizmet düzenle";

include "../ortak/baglanti.php";
include "../ortak/guvenlik.php";
sadece_admin();

$id = 0;
if (isset($_GET["id"])) {
    $id = (int) $_GET["id"];
}
if (isset($_POST["id"])) {
    $id = (int) $_POST["id"];
}

$hata = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && $veritabani_hazir) {
    $ad = "";
    $cihaz_turu = "";
    $aciklama = "";
    $fiyat = 0;
    $aktif = 0;

    if (isset($_POST["ad"])) {
        $ad = trim($_POST["ad"]);
    }
    if (isset($_POST["cihaz_turu"])) {
        $cihaz_turu = $_POST["cihaz_turu"];
    }
    if (isset($_POST["aciklama"])) {
        $aciklama = trim($_POST["aciklama"]);
    }
    if (isset($_POST["baslangic_fiyat"])) {
        $fiyat = (float) $_POST["baslangic_fiyat"];
    }
    if (isset($_POST["aktif"])) {
        $aktif = 1;
    }

    if ($id < 1) {
        $hata = "Hizmet numarası yok.";
    } else if ($cihaz_turu != "bilgisayar" && $cihaz_turu != "telefon" && $cihaz_turu != "tablet") {
        $hata = "Cihaz türü seçin.";
    } else if ($ad == "" || $aciklama == "") {
        $hata = "Ad ve açıklama boş bırakılamaz.";
    } else {
        $ad_temiz = mysqli_real_escape_string($baglanti, $ad);
        $aciklama_temiz = mysqli_real_escape_string($baglanti, $aciklama);
        $sorgu = "UPDATE hizmetler SET ad = '$ad_temiz', cihaz_turu = '$cihaz_turu', aciklama = '$aciklama_temiz', baslangic_fiyat = $fiyat, aktif = $aktif WHERE id = $id";
        $calisti = mysqli_query($baglanti, $sorgu);
        if ($calisti) {
            header("Location: hizmetler.php");
            exit;
        } else {
            $hata = "Güncellenemedi: " . mysqli_error($baglanti);
        }
    }
}

$hizmet = null;
if ($veritabani_hazir && $id > 0) {
    $sonuc = mysqli_query($baglanti, "SELECT * FROM hizmetler WHERE id = $id LIMIT 1");
    if ($sonuc && mysqli_num_rows($sonuc) == 1) {
        $hizmet = mysqli_fetch_assoc($sonuc);
    }
}

include "../ortak/baslik.php";
include "menu.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <h1>Hizmeti düzenle</h1>
        <?php if ($hata != "") { ?>
            <p class="hata"><?php echo temiz_yaz($hata); ?></p>
        <?php } ?>

        <?php if ($hizmet) { ?>
            <form class="form-kutu" method="post" action="hizmet-duzenle.php">
                <input type="hidden" name="id" value="<?php echo (int) $hizmet["id"]; ?>">

                <label for="ad">Ad</label>
                <input id="ad" name="ad" type="text" value="<?php echo temiz_yaz($hizmet["ad"]); ?>">

                <label for="cihaz_turu">Cihaz türü</label>
                <select id="cihaz_turu" name="cihaz_turu">
                    <option value="bilgisayar" <?php if ($hizmet["cihaz_turu"] == "bilgisayar") { echo "selected"; } ?>>Bilgisayar</option>
                    <option value="telefon" <?php if ($hizmet["cihaz_turu"] == "telefon") { echo "selected"; } ?>>Telefon</option>
                    <option value="tablet" <?php if ($hizmet["cihaz_turu"] == "tablet") { echo "selected"; } ?>>Tablet</option>
                </select>

                <label for="aciklama">Açıklama</label>
                <textarea id="aciklama" name="aciklama"><?php echo temiz_yaz($hizmet["aciklama"]); ?></textarea>

                <label for="baslangic_fiyat">Başlangıç fiyatı (TL)</label>
                <input id="baslangic_fiyat" name="baslangic_fiyat" type="number" min="0" step="1" value="<?php echo (float) $hizmet["baslangic_fiyat"]; ?>">

                <label><input type="checkbox" name="aktif" <?php if ((int) $hizmet["aktif"] == 1) { echo "checked"; } ?>> Sitede görünsün</label>

                <p><button class="buton" type="submit">Kaydet</button></p>
            </form>
        <?php } else { ?>
            <p>Hizmet bulunamadı.</p>
        <?php } ?>
    </div>
</section>

<?php include "../ortak/altlik.php"; ?>
