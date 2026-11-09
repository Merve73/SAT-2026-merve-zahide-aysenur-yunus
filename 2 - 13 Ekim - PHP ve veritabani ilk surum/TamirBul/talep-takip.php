<?php
/*
SAYFA: Talep takip
DOSYA: talep-takip.php
KIM: Kisi 3

BU SAYFADA BUNLAR OLACAK:
- Takip kodu kutusu. Giris gerekmez.
- Kod bulunursa: cihaz, ariza, durum, tarih
- Tamirci atandiysa tamircinin adi
*/

$kok = "";
$sayfa_basligi = "Talep takip";

include "ortak/baglanti.php";
include "ortak/guvenlik.php";

$hata = "";
$talep = null;
$tamirci_adi = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kod = "";
    if (isset($_POST["takip_kodu"])) {
        $kod = trim($_POST["takip_kodu"]);
    }

    if (!$veritabani_hazir) {
        $hata = "Veritabanı kapalı.";
    } else if ($kod == "") {
        $hata = "Takip kodunu yazın.";
    } else {
        $kod_temiz = mysqli_real_escape_string($baglanti, $kod);
        $sorgu = "SELECT * FROM talepler WHERE takip_kodu = '$kod_temiz' LIMIT 1";
        $sonuc = mysqli_query($baglanti, $sorgu);

        if ($sonuc && mysqli_num_rows($sonuc) == 1) {
            $talep = mysqli_fetch_assoc($sonuc);

            // Tamirci atandiysa adini ayri bir sorgu ile aliriz.
            if ($talep["tamirci_id"] != "" && (int) $talep["tamirci_id"] > 0) {
                $tamirci_id = (int) $talep["tamirci_id"];
                $tamirci_sonuc = mysqli_query($baglanti, "SELECT ad, soyad FROM kullanicilar WHERE id = $tamirci_id LIMIT 1");
                if ($tamirci_sonuc && mysqli_num_rows($tamirci_sonuc) == 1) {
                    $tamirci = mysqli_fetch_assoc($tamirci_sonuc);
                    $tamirci_adi = $tamirci["ad"] . " " . $tamirci["soyad"];
                }
            }
        } else {
            $hata = "Bu kod ile talep yok.";
        }
    }
}

include "ortak/baslik.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <h1>Talep takip</h1>
        <p class="giris-metni">Talep açınca verilen kodu yazın. Örnek deneme kodu: TB1001</p>

        <?php if ($hata != "") { ?>
            <p class="hata"><?php echo temiz_yaz($hata); ?></p>
        <?php } ?>

        <form class="form-kutu" method="post" action="talep-takip.php">
            <label for="takip_kodu">Takip kodu</label>
            <input id="takip_kodu" name="takip_kodu" type="text">
            <p><button class="buton" type="submit">Sorgula</button></p>
        </form>

        <?php if ($talep) { ?>
            <!-- Bu bolum: sorgunun sonucu. -->
            <div class="bilgi-kutu">
                <h2><?php echo temiz_yaz($talep["takip_kodu"]); ?></h2>
                <p><strong>Durum:</strong> <?php echo temiz_yaz(durum_adi($talep["durum"])); ?></p>
                <p><strong>Cihaz:</strong> <?php echo temiz_yaz(cihaz_adi($talep["cihaz_turu"]) . " / " . $talep["marka"] . " " . $talep["model"]); ?></p>
                <p><strong>Arıza:</strong> <?php echo temiz_yaz($talep["ariza"]); ?></p>
                <p><strong>Tarih:</strong> <?php echo temiz_yaz($talep["olusturma_tarihi"]); ?></p>
                <?php if ($tamirci_adi != "") { ?>
                    <p><strong>Tamirci:</strong> <?php echo temiz_yaz($tamirci_adi); ?></p>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</section>

<?php include "ortak/altlik.php"; ?>
