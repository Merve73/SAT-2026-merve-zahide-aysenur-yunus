<?php
/*
SAYFA: Tamirciler
DOSYA: tamirciler.php
KIM: Kisi 1

BU SAYFADA BUNLAR OLACAK:
- Uzmanliga gore filtre: hepsi, bilgisayar, telefon, tablet
- Onayli tamircilerin kartlari: ad, sehir, uzmanlik, kisa tanitim
- Onayi bekleyen tamirci bu listede gorunmez
*/

$kok = "";
$sayfa_basligi = "Tamirciler";

include "ortak/baglanti.php";
include "ortak/guvenlik.php";

$secilen = "";
if (isset($_GET["uzmanlik"])) {
    $secilen = $_GET["uzmanlik"];
}

$tamirciler = false;
if ($veritabani_hazir) {
    if ($secilen == "bilgisayar" || $secilen == "telefon" || $secilen == "tablet") {
        $uzmanlik_temiz = mysqli_real_escape_string($baglanti, $secilen);
        $sorgu = "SELECT * FROM kullanicilar WHERE rol = 'tamirci' AND onayli = 1 AND uzmanlik = '$uzmanlik_temiz' ORDER BY ad";
    } else {
        $sorgu = "SELECT * FROM kullanicilar WHERE rol = 'tamirci' AND onayli = 1 ORDER BY ad";
    }
    $tamirciler = mysqli_query($baglanti, $sorgu);
}

include "ortak/baslik.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <h1>Tamirciler</h1>
        <p class="giris-metni">Yönetici onayından geçmiş tamirciler. Onay, yönetim panelindeki kullanıcılar sayfasından verilir.</p>

        <div class="filtre">
            <a href="tamirciler.php">Hepsi</a>
            <a href="tamirciler.php?uzmanlik=bilgisayar">Bilgisayar</a>
            <a href="tamirciler.php?uzmanlik=telefon">Telefon</a>
            <a href="tamirciler.php?uzmanlik=tablet">Tablet</a>
        </div>

        <div class="kart-alani">
            <?php if ($tamirciler && mysqli_num_rows($tamirciler) > 0) { ?>
                <?php while ($satir = mysqli_fetch_assoc($tamirciler)) { ?>
                    <article class="kart">
                        <p class="etiket"><?php echo temiz_yaz(cihaz_adi($satir["uzmanlik"])); ?></p>
                        <h2><?php echo temiz_yaz($satir["ad"] . " " . $satir["soyad"]); ?></h2>
                        <p><?php echo temiz_yaz($satir["sehir"]); ?></p>
                        <p><?php echo temiz_yaz($satir["hakkinda"]); ?></p>
                        <a class="buton" href="tamirci-detay.php?id=<?php echo (int) $satir["id"]; ?>">Profil</a>
                    </article>
                <?php } ?>
            <?php } else { ?>
                <p>Bu filtrede onaylı tamirci yok.</p>
            <?php } ?>
        </div>
    </div>
</section>

<?php include "ortak/altlik.php"; ?>
