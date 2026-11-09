<?php
/*
SAYFA: Randevu kur
DOSYA: tamirci/randevu-ekle.php
KİM: Kişi 2

BU SAYFADA BUNLAR OLACAK:
- Seçilen talebin özeti, tarih, saat ve not kutusu
- Kayıt randevular tablosuna yazılır, talebin durumu randevu_kuruldu olur
- Sadece teklifi kabul edilen ve onaylı tamirci randevu kurabilir
*/

// Sekme başlığı.
$sayfa_basligi = "Randevu kur";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece tamirci.
sadece_rol("tamirci");

// Tamircinin numarası.
$benim_id = (int) $_SESSION["kullanici_id"];
// Talep numarası.
$talep_id = gelen_sayi("talep_id");
// Talep: bana verilmiş ve randevu bekleyen olmalı.
$talep = tek_satir("SELECT * FROM talepler WHERE id = $talep_id AND tamirci_id = $benim_id AND durum = 'tamirci_atandi'");

// HARİTA: Randevu kurma
// Form gönderildiyse:
if (form_geldi_mi()) {
    // Tarih. Örnek: 2026-10-25
    $tarih = formdan_al("randevu_tarihi");
    // Saat. Örnek: 14:00
    $saat = formdan_al("randevu_saati");
    // Not.
    $not_metni = formdan_al("not_metni");
    // Talep uygun değilse:
    if (!$talep) {
        // Hata.
        $hata = "Bu talep için randevu kurulamaz.";
    // Tarih veya saat boşsa:
    } else if ($tarih == "" || $saat == "") {
        // Hata.
        $hata = "Tarih ve saat boş bırakılamaz.";
    // Tarih bugünden önceyse: (Yıl-ay-gün yazıları harf harf karşılaştırılabilir.)
    } else if ($tarih < date("Y-m-d")) {
        // Hata.
        $hata = "Geçmiş bir güne randevu kurulamaz.";
    } else {
        // Randevuyu kaydederiz.
        kaydet("randevular", array("talep_id" => $talep_id, "tamirci_id" => $benim_id, "randevu_tarihi" => $tarih, "randevu_saati" => $saat, "durum" => "bekliyor", "not_metni" => $not_metni));
        // Talebin durumunu değiştiririz. Müşteri "Randevu kuruldu" görür.
        guncelle("talepler", array("durum" => "randevu_kuruldu"), "id = $talep_id");
        // Başarı yazısı.
        $tamam = "Randevu kaydedildi.";
    }
}

// Üst menü.
include "../ortak/baslik.php";
// Tamirci menüsü.
include "menu.php";
?>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Başlık. -->
        <h1>Randevu kur</h1>
        <!-- Hata ve başarı kutuları. -->
        <?php include "../ortak/mesajlar.php"; ?>
        <!-- Kaydedildiyse link. -->
        <?php if ($tamam != "") { ?>
            <!-- Randevularıma git. -->
            <p><a class="btn btn-primary" href="randevularim.php">Randevularıma git</a></p>
        <!-- Talep uygunsa form. -->
        <?php } else if ($talep) { ?>
            <!-- Talep özeti. -->
            <div class="bilgi-kutu">
                <!-- Kod. -->
                <p><strong>Kod:</strong> <?php echo temiz_yaz($talep["takip_kodu"]); ?></p>
                <!-- Cihaz. -->
                <p><strong>Cihaz:</strong> <?php echo temiz_yaz($talep["marka"] . " " . $talep["model"]); ?></p>
                <!-- Arıza. -->
                <p><strong>Arıza:</strong> <?php echo temiz_yaz($talep["ariza"]); ?></p>
            </div>
            <!-- Randevu formu. -->
            <form class="form-kutu" method="post" action="randevu-ekle.php">
                <!-- Talep numarası gizli gider. -->
                <input type="hidden" name="talep_id" value="<?php echo $talep_id; ?>">
                <!-- Tarih. -->
                <label for="randevu_tarihi">Tarih</label>
                <!-- Tarih kutusu (takvim açar). -->
                <input id="randevu_tarihi" name="randevu_tarihi" type="date">
                <!-- Saat. -->
                <label for="randevu_saati">Saat</label>
                <!-- Saat kutusu. -->
                <input id="randevu_saati" name="randevu_saati" type="time">
                <!-- Not. -->
                <label for="not_metni">Not</label>
                <!-- Not kutusu. -->
                <textarea id="not_metni" name="not_metni"></textarea>
                <!-- Kaydet düğmesi. -->
                <p><button class="btn btn-primary" type="submit">Randevuyu kaydet</button></p>
            </form>
        <!-- Talep uygun değilse bilgi. -->
        <?php } else { ?>
            <!-- Bilgi. -->
            <p class="bos-liste">Talep bulunamadı. Randevu sadece teklifi kabul edilen işe kurulur. <a href="panel.php">Panele dönün</a>.</p>
        <?php } ?>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
