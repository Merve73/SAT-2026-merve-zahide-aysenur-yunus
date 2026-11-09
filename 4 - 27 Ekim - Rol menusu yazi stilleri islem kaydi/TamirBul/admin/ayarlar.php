<?php
/*
SAYFA: Site yazıları
DOSYA: admin/ayarlar.php
KİM: Kişi 4

BU SAYFADA BUNLAR OLACAK:
- site_ayarlari tablosundaki her satır için bir kutu. Kutunun adı etiket sütunundan gelir.
- Uzun yazılar (60 harften uzun) büyük kutuda gösterilir
- Ana sayfa başlığı, düğme yazıları, bölüm başlıkları, telefon, adres böyle değişir
- Her yazının üstünde Word benzeri araç çubuğu (ortak/stil-araclari.php), altında önizleme (ortak/stil-onizleme.php)
- Yeni yazı eklemek için SQL dosyasına satır eklenir, bu sayfa onu kendiliğinden gösterir
*/

// Sekme başlığı.
$sayfa_basligi = "Site yazıları";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece yönetici.
sadece_rol("admin");
// Stil tablosu eski veritabanında yoksa oluştururuz.
yazi_stili_tablosunu_hazirla();

// Bu ayarlar yazı değil sayıdır. Ekranda yazı olarak görünmedikleri için stil kutusu çıkmaz.
$stilsiz_ayarlar = array("slayt_suresi", "anasayfa_hizmet_sayisi");

// HARİTA: Site yazılarını değiştirme (ayarlar)
// Form gönderildiyse:
if (isset($_POST["ayar"])) {
    // Her kutu ayar[anahtar] adıyla gelir. foreach ile tek tek gezeriz.
    foreach ($_POST["ayar"] as $anahtar => $deger) {
        // Değeri kaydederiz.
        guncelle("site_ayarlari", array("deger" => trim($deger)), "anahtar = '" . temizle($anahtar) . "'");
    }
    // HARİTA: Yazı stillerini kaydetme
    // Stil kutuları stil[anahtar][renk] adıyla gelir. Geldiyse:
    if (isset($_POST["stil"])) {
        // Her yazının seçimleri için:
        foreach ($_POST["stil"] as $anahtar => $secimler) {
            // Seçimleri kaydederiz. Listede olmayan değerleri fonksiyon kendisi atar.
            yazi_stili_kaydet($anahtar, $secimler);
        }
    }
    // Değişikliği kayda yazarız.
    log_yaz("Site yazıları kaydedildi", "");
    // Başarı yazısı.
    $tamam = "Kaydedildi. Siteyi yenileyince yeni yazılar görünür.";
}

// Bütün ayarlar.
$ayarlar = satirlar("SELECT * FROM site_ayarlari ORDER BY id");

// Üst menü.
include "../ortak/baslik.php";
?>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Başlık. -->
        <h1>Site yazıları</h1>
        <!-- Açıklama. -->
        <p class="giris-metni">Bu yazılar ana sayfada, menüde, sayfa açıklamalarında ve sayfanın altında kullanılır. Her yazının üstünde Word'deki gibi bir araç çubuğu var: yazı tipi, boyut, <b>K</b> (kalın), <i>T</i> (eğik), renk ve hizalama. Seçtiğiniz anda alttaki önizleme değişir. "Temizle" sitenin kendi görünüşüne döndürür. Bitince Kaydet'e basın.</p>
        <!-- Başarı kutusu. -->
        <?php include "../ortak/mesajlar.php"; ?>
        <!-- Ayar formu. -->
        <form class="form-kutu form-genis" method="post" action="ayarlar.php">
            <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
            <?php echo guvenlik_alani(); ?>
            <!-- Her ayar bir kutu. -->
            <?php foreach ($ayarlar as $ayar) { ?>
                <?php
                // Bu ayar yazı mı? Sayı olan ayarlarda (slayt süresi gibi) araç çubuğu çıkmaz.
                $stil_olsun_mu = !in_array($ayar["anahtar"], $stilsiz_ayarlar);
                // Araç çubuğu ve önizleme hangi yazı için çizilecek.
                $stil_anahtar = $ayar["anahtar"];
                // Önizlemede yazının kendisi görünür.
                $stil_ornek_yazi = $ayar["deger"];
                ?>
                <!-- Bir yazının satırı. stil-alani: js/site.js araç çubuğunu, yazı kutusunu ve önizlemeyi bu kutuda arar. -->
                <div class="ayar-satiri <?php if ($stil_olsun_mu) { echo "stil-alani"; } ?>">
                    <!-- Kutunun adı (etiket sütunu). -->
                    <label for="<?php echo temiz_yaz($ayar["anahtar"]); ?>"><?php echo temiz_yaz($ayar["etiket"]); ?></label>
                    <!-- Yazıysa Word gibi araç çubuğu (yazı kutusunun üstünde). -->
                    <?php if ($stil_olsun_mu) { ?>
                        <!-- Araç çubuğu: yazı tipi, boyut, K, T, renk, hizalama, temizle. -->
                        <?php include "../ortak/stil-araclari.php"; ?>
                    <?php } ?>
                    <!-- Uzun yazıysa büyük kutu. -->
                    <?php if (mb_strlen($ayar["deger"], "UTF-8") > 60) { ?>
                        <!-- Büyük kutu. name="ayar[anahtar]" PHP'ye dizi olarak gider. stil-hedef: önizleme bu kutunun yazısını gösterir. -->
                        <textarea class="stil-hedef" id="<?php echo temiz_yaz($ayar["anahtar"]); ?>" name="ayar[<?php echo temiz_yaz($ayar["anahtar"]); ?>]" rows="3"><?php echo temiz_yaz($ayar["deger"]); ?></textarea>
                    <!-- Kısa yazıysa tek satır kutu. -->
                    <?php } else { ?>
                        <!-- Tek satır kutu. -->
                        <input class="stil-hedef" id="<?php echo temiz_yaz($ayar["anahtar"]); ?>" name="ayar[<?php echo temiz_yaz($ayar["anahtar"]); ?>]" type="text" value="<?php echo temiz_yaz($ayar["deger"]); ?>">
                    <?php } ?>
                    <!-- Yazıysa önizleme (yazı kutusunun altında). -->
                    <?php if ($stil_olsun_mu) { ?>
                        <!-- Koyu ve açık zeminde önizleme. -->
                        <?php include "../ortak/stil-onizleme.php"; ?>
                    <?php } ?>
                </div>
            <?php } ?>
            <!-- Kaydet. Sayfanın altında sabit durur, uzun sayfada aşağı inmeye gerek kalmaz. -->
            <p class="sabit-kaydet"><button class="btn btn-primary" type="submit">Kaydet</button></p>
        </form>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
