<?php
/*
SAYFA: Randevu kur
DOSYA: tamirci/randevu-ekle.php
KIM: Kisi 2 gorunum, kayit bu dosyada.

BU SAYFADA BUNLAR OLACAK:
- Secilen talebin ozeti
- Tarih, saat ve not kutusu
- Kayit olunca randevular tablosuna satir eklenir
- Talebin durumu randevu_kuruldu olur ve tamirci_id dolar
- Onaysiz tamirci kayit yapamaz
*/

$kok = "../";
$sayfa_basligi = "Randevu kur";

include "../ortak/baglanti.php";
include "../ortak/guvenlik.php";
sadece_tamirci();

$benim_id = (int) $_SESSION["kullanici_id"];
$hata = "";
$tamam = "";

$talep_id = 0;
if (isset($_GET["talep_id"])) {
    $talep_id = (int) $_GET["talep_id"];
}
if (isset($_POST["talep_id"])) {
    $talep_id = (int) $_POST["talep_id"];
}

// Onay kontrolu. 1 degilse randevu yazmayiz.
$onayli = 0;
if ($veritabani_hazir) {
    $ben_sonuc = mysqli_query($baglanti, "SELECT onayli FROM kullanicilar WHERE id = $benim_id LIMIT 1");
    if ($ben_sonuc && mysqli_num_rows($ben_sonuc) == 1) {
        $ben = mysqli_fetch_assoc($ben_sonuc);
        $onayli = (int) $ben["onayli"];
    }
}

$talep = null;
if ($veritabani_hazir && $talep_id > 0) {
    $talep_sonuc = mysqli_query($baglanti, "SELECT * FROM talepler WHERE id = $talep_id LIMIT 1");
    if ($talep_sonuc && mysqli_num_rows($talep_sonuc) == 1) {
        $talep = mysqli_fetch_assoc($talep_sonuc);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tarih = "";
    $saat = "";
    $not_metni = "";

    if (isset($_POST["randevu_tarihi"])) {
        $tarih = trim($_POST["randevu_tarihi"]);
    }
    if (isset($_POST["randevu_saati"])) {
        $saat = trim($_POST["randevu_saati"]);
    }
    if (isset($_POST["not_metni"])) {
        $not_metni = trim($_POST["not_metni"]);
    }

    if ($onayli != 1) {
        $hata = "Hesabınız onaylı değil.";
    } else if (!$talep) {
        $hata = "Talep bulunamadı.";
    } else if ($talep["durum"] != "beklemede") {
        $hata = "Bu talep artık beklemede değil.";
    } else if ($tarih == "" || $saat == "") {
        $hata = "Tarih ve saat boş bırakılamaz.";
    } else {
        $tarih_temiz = mysqli_real_escape_string($baglanti, $tarih);
        $saat_temiz = mysqli_real_escape_string($baglanti, $saat);
        $not_temiz = mysqli_real_escape_string($baglanti, $not_metni);

        $ekle = "INSERT INTO randevular (talep_id, tamirci_id, randevu_tarihi, randevu_saati, durum, not_metni) VALUES ($talep_id, $benim_id, '$tarih_temiz', '$saat_temiz', 'bekliyor', '$not_temiz')";
        $calisti = mysqli_query($baglanti, $ekle);

        if ($calisti) {
            // Talebi bu tamirciye baglariz ki baska tamirci ayni isi almasin.
            mysqli_query($baglanti, "UPDATE talepler SET durum = 'randevu_kuruldu', tamirci_id = $benim_id WHERE id = $talep_id AND durum = 'beklemede'");
            $tamam = "Randevu kaydedildi.";
        } else {
            $hata = "Randevu kaydedilemedi: " . mysqli_error($baglanti);
        }
    }
}

include "../ortak/baslik.php";
include "menu.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <h1>Randevu kur</h1>

        <?php if ($hata != "") { ?>
            <p class="hata"><?php echo temiz_yaz($hata); ?></p>
        <?php } ?>
        <?php if ($tamam != "") { ?>
            <p class="tamam"><?php echo temiz_yaz($tamam); ?></p>
            <p><a href="randevularim.php">Randevularıma git</a></p>
        <?php } ?>

        <?php if ($talep && $tamam == "") { ?>
            <div class="bilgi-kutu">
                <p><strong>Kod:</strong> <?php echo temiz_yaz($talep["takip_kodu"]); ?></p>
                <p><strong>Cihaz:</strong> <?php echo temiz_yaz($talep["marka"] . " " . $talep["model"]); ?></p>
                <p><strong>Arıza:</strong> <?php echo temiz_yaz($talep["ariza"]); ?></p>
            </div>

            <form class="form-kutu" method="post" action="randevu-ekle.php">
                <input type="hidden" name="talep_id" value="<?php echo (int) $talep_id; ?>">

                <label for="randevu_tarihi">Tarih</label>
                <input id="randevu_tarihi" name="randevu_tarihi" type="date">

                <label for="randevu_saati">Saat</label>
                <input id="randevu_saati" name="randevu_saati" type="time">

                <label for="not_metni">Not</label>
                <textarea id="not_metni" name="not_metni"></textarea>

                <p><button class="buton" type="submit">Randevuyu kaydet</button></p>
            </form>
        <?php } else if (!$talep) { ?>
            <p>Talep seçilmedi. <a href="gelen-talepler.php">Gelen taleplere dönün</a>.</p>
        <?php } ?>
    </div>
</section>

<?php include "../ortak/altlik.php"; ?>
