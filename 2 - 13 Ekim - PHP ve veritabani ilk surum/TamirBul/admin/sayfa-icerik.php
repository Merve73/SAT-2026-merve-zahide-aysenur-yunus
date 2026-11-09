<?php
/*
SAYFA: Sayfa yazilari
DOSYA: admin/sayfa-icerik.php
KIM: Kisi 4

BU SAYFADA BUNLAR OLACAK:
- sayfa_icerikleri tablosundaki her sayfa icin baslik ve uzun metin
- Su an tabloda hakkimizda var. Hakkımızda sayfasi bunu okur.
- Kaydet deyince UPDATE calisir
*/

$kok = "../";
$sayfa_basligi = "Sayfa yazıları";

include "../ortak/baglanti.php";
include "../ortak/guvenlik.php";
sadece_admin();

$hata = "";
$tamam = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && $veritabani_hazir) {
    $sayfa = "";
    $baslik = "";
    $icerik = "";

    if (isset($_POST["sayfa"])) {
        $sayfa = $_POST["sayfa"];
    }
    if (isset($_POST["baslik"])) {
        $baslik = trim($_POST["baslik"]);
    }
    if (isset($_POST["icerik"])) {
        $icerik = trim($_POST["icerik"]);
    }

    if ($sayfa == "" || $baslik == "" || $icerik == "") {
        $hata = "Başlık ve metin boş bırakılamaz.";
    } else {
        $sayfa_temiz = mysqli_real_escape_string($baglanti, $sayfa);
        $baslik_temiz = mysqli_real_escape_string($baglanti, $baslik);
        $icerik_temiz = mysqli_real_escape_string($baglanti, $icerik);
        $sorgu = "UPDATE sayfa_icerikleri SET baslik = '$baslik_temiz', icerik = '$icerik_temiz' WHERE sayfa = '$sayfa_temiz'";
        $calisti = mysqli_query($baglanti, $sorgu);
        if ($calisti) {
            $tamam = "Sayfa yazısı kaydedildi.";
        } else {
            $hata = "Kaydedilemedi: " . mysqli_error($baglanti);
        }
    }
}

$sayfalar = false;
if ($veritabani_hazir) {
    $sayfalar = mysqli_query($baglanti, "SELECT * FROM sayfa_icerikleri ORDER BY id");
}

include "../ortak/baslik.php";
include "menu.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <h1>Sayfa yazıları</h1>
        <p class="giris-metni">Uzun metinler burada durur. Hakkımızda sayfası bu tablodan okur.</p>

        <?php if ($hata != "") { ?>
            <p class="hata"><?php echo temiz_yaz($hata); ?></p>
        <?php } ?>
        <?php if ($tamam != "") { ?>
            <p class="tamam"><?php echo temiz_yaz($tamam); ?></p>
        <?php } ?>

        <?php if ($sayfalar) { ?>
            <?php while ($satir = mysqli_fetch_assoc($sayfalar)) { ?>
                <form class="form-kutu" method="post" action="sayfa-icerik.php">
                    <input type="hidden" name="sayfa" value="<?php echo temiz_yaz($satir["sayfa"]); ?>">
                    <p class="etiket"><?php echo temiz_yaz($satir["sayfa"]); ?></p>

                    <label>Başlık</label>
                    <input name="baslik" type="text" value="<?php echo temiz_yaz($satir["baslik"]); ?>">

                    <label>Metin</label>
                    <textarea name="icerik"><?php echo temiz_yaz($satir["icerik"]); ?></textarea>

                    <p><button class="buton" type="submit">Kaydet</button></p>
                </form>
            <?php } ?>
        <?php } ?>
    </div>
</section>

<?php include "../ortak/altlik.php"; ?>
