<?php
/*
SAYFA: Sayfa yazıları
DOSYA: admin/sayfa-icerik.php
KİM: Kişi 4

BU SAYFADA BUNLAR OLACAK:
- sayfa_icerikleri tablosundaki her sayfa için başlık ve uzun metin formu
- Şu an tabloda hakkımızda var. Hakkımızda sayfası bunu okur.
*/

// Sekme başlığı.
$sayfa_basligi = "Sayfa yazıları";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece yönetici.
sadece_rol("admin");


// HARİTA: Uzun sayfa yazılarını değiştirme (Hakkımızda)
// Form gönderildiyse:
if (form_geldi_mi()) {
    // Hangi sayfa. Örnek: hakkımızda
    $sayfa = formdan_al("sayfa");
    // Başlık.
    $baslik = formdan_al("baslik");
    // Metin.
    $icerik = formdan_al("icerik");
    // Boş kutu varsa:
    if ($baslik == "" || $icerik == "") {
        // Hata.
        $hata = "Başlık ve metin boş bırakılamaz.";
    } else {
        // Kaydederiz.
        guncelle("sayfa_icerikleri", array("baslik" => $baslik, "icerik" => $icerik), "sayfa = '" . temizle($sayfa) . "'");
        // Başarı yazısı.
        $tamam = "Sayfa yazısı kaydedildi.";
    }
}

// Bütün sayfa yazıları.
$sayfalar = satirlar("SELECT * FROM sayfa_icerikleri ORDER BY id");

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
        <h1>Sayfa yazıları</h1>
        <!-- Açıklama. -->
        <p class="giris-metni">Uzun metinler burada durur. Hakkımızda sayfası bu tablodan okur.</p>
        <!-- Hata ve başarı kutuları. -->
        <?php include "../ortak/mesajlar.php"; ?>
        <!-- Her sayfa için bir form. -->
        <?php foreach ($sayfalar as $sayfa) { ?>
            <!-- Form. -->
            <form class="form-kutu" method="post" action="sayfa-icerik.php">
                <!-- Hangi sayfa olduğu gizli gider. -->
                <input type="hidden" name="sayfa" value="<?php echo temiz_yaz($sayfa["sayfa"]); ?>">
                <!-- Sayfanın kodu. -->
                <p class="etiket"><?php echo temiz_yaz($sayfa["sayfa"]); ?></p>
                <!-- Başlık. -->
                <label>Başlık</label>
                <!-- Başlık kutusu. -->
                <input name="baslik" type="text" value="<?php echo temiz_yaz($sayfa["baslik"]); ?>">
                <!-- Metin. -->
                <label>Metin</label>
                <!-- Metin kutusu. -->
                <textarea name="icerik" rows="8"><?php echo temiz_yaz($sayfa["icerik"]); ?></textarea>
                <!-- Kaydet. -->
                <p><button class="btn btn-primary" type="submit">Kaydet</button></p>
            </form>
        <?php } ?>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
