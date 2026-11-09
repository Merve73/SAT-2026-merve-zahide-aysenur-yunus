<?php
/*
SAYFA: Teklif ver
DOSYA: tamirci/teklif-ver.php
KİM: Kişi 2

BU SAYFADA BUNLAR OLACAK:
- Seçilen talebin özeti ve fotoğrafı
- Fiyat, süre, açıklama formu. Kayıt teklifler tablosuna "bekliyor" durumuyla yazılır.
- Panel sayfasındaki küçük teklif formu da buraya gönderir
KONTROLLER: tamirci onaylı olmalı, talep ona uygun olmalı, aynı talebe ikinci teklif olmaz.
*/

// Sekme başlığı.
$sayfa_basligi = "Teklif ver";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece tamirci.
sadece_rol("tamirci");

// Tamircinin numarası.
$benim_id = (int) $_SESSION["kullanici_id"];
// Talep numarası (adresten veya formdan).
$talep_id = gelen_sayi("talep_id");
// Tamircinin onayı ve ili.
$ben = tek_satir("SELECT onayli, sehir FROM kullanicilar WHERE id = $benim_id");
// İl temizlenir.
$benim_sehir = temizle($ben["sehir"]);
// Talep: beklemede, aynı ilde ve tamircinin yetkinliğinde olmalı. Uymazsa bulunmaz.
$talep = tek_satir("SELECT talepler.*, kategoriler.ad AS kategori_adi, kullanicilar.ad AS musteri_ad, kullanicilar.soyad AS musteri_soyad FROM talepler INNER JOIN kategoriler ON kategoriler.id = talepler.kategori_id INNER JOIN kullanicilar ON kullanicilar.id = talepler.musteri_id INNER JOIN tamirci_yetkinlikleri ON tamirci_yetkinlikleri.kategori_id = talepler.kategori_id AND tamirci_yetkinlikleri.tamirci_id = $benim_id WHERE talepler.id = $talep_id AND talepler.durum = 'beklemede' AND talepler.sehir = '$benim_sehir'");
// Bu talebe daha önce teklif verdim mi?
$onceki_teklif = sayi_getir("SELECT COUNT(*) FROM teklifler WHERE talep_id = $talep_id AND tamirci_id = $benim_id");
// Formdaki fiyat. (float): ondalıklı sayıya çevirir.
$fiyat = (float) formdan_al("fiyat");
// Süre.
$sure = formdan_al("sure");
// Not.
$aciklama = formdan_al("aciklama");

// HARİTA: Teklif verme (kontroller ve INSERT)
// Form gönderildiyse:
if (form_geldi_mi()) {
    // Onaylı değilse:
    if ($ben["onayli"] != 1) {
        // Hata.
        $hata = "Hesabınız onaylı değil. Teklif veremezsiniz.";
    // Talep uygun değilse:
    } else if (!$talep) {
        // Hata.
        $hata = "Bu talep artık teklif almıyor ya da size uygun değil.";
    // Daha önce teklif verdiyse:
    } else if ($onceki_teklif > 0) {
        // Hata.
        $hata = "Bu talebe zaten teklif verdiniz.";
    // Fiyat 0 veya eksiyse:
    } else if ($fiyat <= 0) {
        // Hata.
        $hata = "Teklif fiyatı 0'dan büyük olmalı.";
    // Çok büyük fiyat veritabanına sığmaz, yanlış yazılmış da olabilir:
    } else if ($fiyat > 1000000) {
        // Hata.
        $hata = "Teklif fiyatı en fazla 1.000.000 TL olabilir.";
    // Süre boşsa:
    } else if ($sure == "") {
        // Hata.
        $hata = "Tahmini süreyi yazın.";
    } else {
        // Teklifi kaydederiz. Durum "bekliyor": müşteri daha karar vermedi.
        kaydet("teklifler", array("talep_id" => $talep_id, "tamirci_id" => $benim_id, "fiyat" => $fiyat, "sure" => $sure, "aciklama" => $aciklama, "durum" => "bekliyor", "tarih" => simdi()));
        // Teklifi kayda yazarız.
        log_yaz("Teklif verildi", "Talep numarası " . $talep_id . ", " . fiyat_yaz($fiyat));
        // Panele döneriz.
        yonlendir("panel.php?mesaj=teklif");
    }
}

// Üst menü.
include "../ortak/baslik.php";
// Tamircinin onay durumu ($onayli) ve onay uyarısı. Menü artık üstteki açılır menüde.
include "onay-durumu.php";
?>

<!-- Sayfa bölümü. -->
<section class="auth-section">
    <!-- Kapsayıcı. -->
    <div class="container auth-container">
        <!-- Talep bulunduysa özet kartı. -->
        <?php if ($talep) { ?>
            <!-- Özet kartı. -->
            <div class="request-item-card dar-kart">
                <!-- Kategori. -->
                <span class="badge badge-info"><?php echo temiz_yaz($talep["kategori_adi"]); ?></span>
                <!-- Cihaz. -->
                <h4><?php echo temiz_yaz($talep["marka"] . " " . $talep["model"] . " - " . $talep["cihaz_turu"]); ?></h4>
                <!-- Müşteri ve konum. -->
                <p class="request-date">Müşteri: <?php echo temiz_yaz(kisa_ad($talep["musteri_ad"], $talep["musteri_soyad"])); ?> | <?php echo temiz_yaz($talep["sehir"] . " / " . $talep["ilce"]); ?></p>
                <!-- Arıza. -->
                <div class="request-item-body"><strong>Arıza:</strong> <?php echo temiz_yaz($talep["ariza"]); ?></div>
                <!-- Fotoğraf varsa. -->
                <?php if ($talep["resim"] != "") { ?>
                    <!-- Fotoğraf. -->
                    <div class="image-preview-frame"><img class="buyutulebilir" src="<?php echo temiz_yaz(resim_adresi($talep["resim"])); ?>" alt="Arıza fotoğrafı" title="Büyütmek için tıklayın"></div>
                <?php } ?>
            </div>
        <?php } ?>
        <!-- Form kartı. -->
        <div class="auth-card">
            <!-- Başlık. -->
            <h2>Arıza Talebine Teklif Gönder</h2>
            <!-- Açıklama. -->
            <p class="auth-subtitle">Net fiyat ve tahmini teslim süresi yazın.</p>
            <!-- Hata kutusu. -->
            <?php include "../ortak/mesajlar.php"; ?>
            <!-- Teklif verilebiliyorsa form. -->
            <?php if ($talep && $onceki_teklif == 0 && $ben["onayli"] == 1) { ?>
                <!-- Teklif formu. -->
                <form method="post" action="teklif-ver.php">
                    <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
                    <?php echo guvenlik_alani(); ?>
                    <!-- Talep numarası gizli gider. -->
                    <input type="hidden" name="talep_id" value="<?php echo $talep_id; ?>">
                    <!-- Fiyat. -->
                    <div class="form-group">
                        <!-- Kutunun adı. -->
                        <label for="txtFiyat">Teklif Tutarı (TL)</label>
                        <!-- Fiyat kutusu. -->
                        <input id="txtFiyat" name="fiyat" type="number" min="1" placeholder="Örn: 1500" required>
                    </div>
                    <!-- Süre. -->
                    <div class="form-group">
                        <!-- Kutunun adı. -->
                        <label for="txtSure">Tahmini Tamir Süresi</label>
                        <!-- Süre kutusu. -->
                        <input id="txtSure" name="sure" maxlength="50" type="text" placeholder="Örn: 1 İş Günü" required>
                    </div>
                    <!-- Not. -->
                    <div class="form-group">
                        <!-- Kutunun adı. -->
                        <label for="txtNot">Açıklama / Garanti Detayı</label>
                        <!-- Not kutusu. -->
                        <textarea id="txtNot" name="aciklama" placeholder="Orijinal parça, 6 ay garanti..."></textarea>
                    </div>
                    <!-- Gönder düğmesi. -->
                    <button class="btn btn-primary btn-block" type="submit">Teklifi Müşteriye Gönder</button>
                </form>
            <!-- Teklif verilemiyorsa bilgi. -->
            <?php } else { ?>
                <!-- Bilgi. -->
                <p class="bos-liste">Bu talebe teklif verilemiyor (zaten teklif verdiniz, talep kapandı veya size uygun değil). <a href="gelen-talepler.php">Uygun taleplere dönün</a>.</p>
            <?php } ?>
        </div>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
