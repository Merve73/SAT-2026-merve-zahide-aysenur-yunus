<?php
/*
SAYFA: Site yazıları
DOSYA: admin/ayarlar.php
KİM: Kişi 4

BU SAYFADA BUNLAR OLACAK:
- site_ayarlari tablosundaki her satır için bir kutu. Kutunun adı etiket sütunundan gelir.
- Uzun yazılar (60 harften uzun) büyük kutuda gösterilir
- Ana sayfa başlığı, düğme yazıları, bölüm başlıkları, telefon, adres böyle değişir
- Yeni yazı eklemek için SQL dosyasına satır eklenir, bu sayfa onu kendiliğinden gösterir
*/

// Sekme başlığı.
$sayfa_basligi = "Site yazıları";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece yönetici.
sadece_rol("admin");


// HARİTA: Site yazılarını değiştirme (ayarlar)
// Form gönderildiyse:
if (isset($_POST["ayar"])) {
    // Her kutu ayar[anahtar] adıyla gelir. foreach ile tek tek gezeriz.
    foreach ($_POST["ayar"] as $anahtar => $deger) {
        // Değeri kaydederiz.
        guncelle("site_ayarlari", array("deger" => trim($deger)), "anahtar = '" . temizle($anahtar) . "'");
    }
    // Başarı yazısı.
    $tamam = "Kaydedildi. Siteyi yenileyince yeni yazılar görünür.";
}

// Bütün ayarlar.
$ayarlar = satirlar("SELECT * FROM site_ayarlari ORDER BY id");

// Üst menü.
include "../ortak/baslik.php";
// Yönetim menüsü.
include "menu.php";
?>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Başlık. -->
        <h1>Site yazıları</h1>
        <!-- Açıklama. -->
        <p class="giris-metni">Bu yazılar ana sayfada, menüde, sayfa açıklamalarında ve sayfanın altında kullanılır.</p>
        <!-- Başarı kutusu. -->
        <?php include "../ortak/mesajlar.php"; ?>
        <!-- Ayar formu. -->
        <form class="form-kutu" method="post" action="ayarlar.php">
            <!-- Her ayar bir kutu. -->
            <?php foreach ($ayarlar as $ayar) { ?>
                <!-- Kutunun adı (etiket sütunu). -->
                <label for="<?php echo temiz_yaz($ayar["anahtar"]); ?>"><?php echo temiz_yaz($ayar["etiket"]); ?></label>
                <!-- Uzun yazıysa büyük kutu. -->
                <?php if (mb_strlen($ayar["deger"], "UTF-8") > 60) { ?>
                    <!-- Büyük kutu. name="ayar[anahtar]" PHP'ye dizi olarak gider. -->
                    <textarea id="<?php echo temiz_yaz($ayar["anahtar"]); ?>" name="ayar[<?php echo temiz_yaz($ayar["anahtar"]); ?>]" rows="3"><?php echo temiz_yaz($ayar["deger"]); ?></textarea>
                <!-- Kısa yazıysa tek satır kutu. -->
                <?php } else { ?>
                    <!-- Tek satır kutu. -->
                    <input id="<?php echo temiz_yaz($ayar["anahtar"]); ?>" name="ayar[<?php echo temiz_yaz($ayar["anahtar"]); ?>]" type="text" value="<?php echo temiz_yaz($ayar["deger"]); ?>">
                <?php } ?>
            <?php } ?>
            <!-- Kaydet. -->
            <p><button class="btn btn-primary" type="submit">Kaydet</button></p>
        </form>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
