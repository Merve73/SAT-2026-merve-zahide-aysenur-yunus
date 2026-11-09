<?php
/*
SAYFA: Talep düzenle
DOSYA: musteri/talep-duzenle.php
KİM: Kişi 2

BU SAYFADA BUNLAR OLACAK:
- Sadece teklif bekleyen (beklemede) kendi talebini düzenler
- Kategori, cihaz türü, marka, model, arıza, il, ilçe değişebilir
- Tamirci seçildikten sonra düzenleme kapanır
*/

// Sekme başlığı.
$sayfa_basligi = "Talep düzenle";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece müşteri.
sadece_rol("musteri");

// Talep numarası (adresten veya formdan).
$id = gelen_sayi("id");
// Müşterinin numarası.
$musteri_id = (int) $_SESSION["kullanici_id"];
// Talep: kendisinin ve teklif bekleyen olmalı.
$talep = tek_satir("SELECT * FROM talepler WHERE id = $id AND musteri_id = $musteri_id AND durum = 'beklemede'");

// Talep bulunduysa kutuların ilk değerleri veritabanından gelir.
if ($talep) {
    // Kategori.
    $secili_kategori = (int) $talep["kategori_id"];
    // Cihaz türü.
    $secili_cihaz = $talep["cihaz_turu"];
    // Marka.
    $marka = $talep["marka"];
    // Model.
    $model = $talep["model"];
    // Arıza.
    $ariza = $talep["ariza"];
    // İl.
    $secili_il = $talep["sehir"];
    // İlçe.
    $secili_ilce = $talep["ilce"];
}

// HARİTA: Talep düzenleme (UPDATE)
// Form gönderildiyse ve talep varsa:
if (form_geldi_mi() && $talep) {
    // Formdaki kategori.
    $secili_kategori = gelen_sayi("kategori_id");
    // Cihaz türü.
    $secili_cihaz = formdan_al("cihaz_turu");
    // Marka.
    $marka = formdan_al("marka");
    // Model.
    $model = formdan_al("model");
    // Arıza.
    $ariza = formdan_al("ariza");
    // İl.
    $secili_il = formdan_al("sehir");
    // İlçe.
    $secili_ilce = formdan_al("ilce");

    // Cihaz türü bu kategoride yoksa:
    if (!cihaz_turu_dogru_mu($secili_kategori, $secili_cihaz)) {
        // Hata.
        $hata = "Kategori ve cihaz türü seçin.";
    // Marka veya arıza boşsa:
    } else if ($marka == "" || $ariza == "") {
        // Hata.
        $hata = "Marka ve arıza boş bırakılamaz.";
    // İl-ilçe yanlışsa:
    } else if (!il_ilce_dogru_mu($secili_il, $secili_ilce)) {
        // Hata.
        $hata = "İl ve ilçe seçin.";
    } else {
        // Talebi güncelleriz. musteri_id ve durum koşulu tekrar kontrol eder.
        guncelle("talepler", array("kategori_id" => $secili_kategori, "cihaz_turu" => $secili_cihaz, "marka" => $marka, "model" => $model, "ariza" => $ariza, "sehir" => $secili_il, "ilce" => $secili_ilce), "id = $id AND musteri_id = $musteri_id AND durum = 'beklemede'");
        // Kategori veya konum değiştiyse eski teklifler geçersizdir (o tamirci artık bu işi görmeyebilir).
        if ($secili_kategori != $talep["kategori_id"] || $secili_il != $talep["sehir"] || $secili_ilce != $talep["ilce"]) {
            // Eski teklifleri sileriz.
            sil("teklifler", "talep_id = $id");
        }
        // Detay sayfasına döneriz.
        yonlendir("talep-detay.php?id=" . $id);
    }
}

// Üst menü.
include "../ortak/baslik.php";
// Müşteri menüsü.
include "menu.php";
?>

<!-- Form bölümü. -->
<section class="request-section">
    <!-- Kapsayıcı. -->
    <div class="container request-container">
        <!-- Geniş kart. -->
        <div class="request-card">
            <!-- Başlık. -->
            <h2>Talebi Düzenle</h2>
            <!-- Açıklama. -->
            <p class="request-subtitle">Tamirci seçilene kadar talep bilgilerini değiştirebilirsiniz. Kategori veya il/ilçe değişirse gelen teklifler silinir.</p>
            <!-- Hata kutusu. -->
            <?php include "../ortak/mesajlar.php"; ?>
            <!-- Talep varsa form. -->
            <?php if ($talep) { ?>
                <!-- Düzenleme formu. -->
                <form method="post" action="talep-duzenle.php">
                    <!-- Talep numarası gizli gider. -->
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <!-- Kategori ve cihaz (ortak parça). -->
                    <?php include "../ortak/cihaz-secimi.php"; ?>
                    <!-- Marka ve model yan yana. -->
                    <div class="form-row">
                        <!-- Marka. -->
                        <div class="form-group">
                            <!-- Kutunun adı. -->
                            <label for="txtMarka">Marka</label>
                            <!-- Marka kutusu. -->
                            <input id="txtMarka" name="marka" type="text" value="<?php echo temiz_yaz($marka); ?>" required>
                        </div>
                        <!-- Model. -->
                        <div class="form-group">
                            <!-- Kutunun adı. -->
                            <label for="txtModel">Model (Varsa)</label>
                            <!-- Model kutusu. -->
                            <input id="txtModel" name="model" type="text" value="<?php echo temiz_yaz($model); ?>">
                        </div>
                    </div>
                    <!-- Arıza. -->
                    <div class="form-group">
                        <!-- Kutunun adı. -->
                        <label for="txtAriza">Arıza Açıklaması</label>
                        <!-- Arıza kutusu. -->
                        <textarea id="txtAriza" name="ariza" rows="4" required><?php echo temiz_yaz($ariza); ?></textarea>
                    </div>
                    <!-- İl ve ilçe (ortak parça). -->
                    <?php include "../ortak/konum-secimi.php"; ?>
                    <!-- Kaydet düğmesi. -->
                    <button class="btn btn-primary btn-block" type="submit">Değişiklikleri Kaydet</button>
                </form>
            <!-- Talep yoksa bilgi. -->
            <?php } else { ?>
                <!-- Bilgi. -->
                <p class="bos-liste">Bu talep düzenlenemez. Ya size ait değil ya da tamirci seçildi. <a href="taleplerim.php">Taleplerime dön</a>.</p>
            <?php } ?>
        </div>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
