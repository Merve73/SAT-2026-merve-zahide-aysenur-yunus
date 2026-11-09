<?php
/*
SAYFA: İletişim
DOSYA: iletisim.php
KİM: Kişi 1

BU SAYFADA BUNLAR OLACAK:
- Solda telefon, e-posta, adres, saat (site yazılarından)
- Sağda form: ad, e-posta, konu, mesaj. mesajlar tablosuna yazar, yönetici okur.
*/

// Sekme başlığı.
$sayfa_basligi = "İletişim";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "ortak/hazirla.php";

// Mesaj gönderildiyse adres ?gonderildi=1 olur.
if (isset($_GET["gonderildi"])) {
    // Teşekkür yazısı.
    $tamam = "Mesajınız alındı. Yönetici panelden okuyacak.";
}
// Formdaki ad.
$ad = formdan_al("ad");
// E-posta.
$eposta = formdan_al("eposta");
// Konu.
$konu = formdan_al("konu");
// Mesaj.
$mesaj = formdan_al("mesaj");

// HARİTA: İletişim formu (mesajlar tablosuna kayıt)
// Form gönderildiyse:
if (form_geldi_mi()) {
    // Boş kutu varsa:
    if ($ad == "" || $eposta == "" || $konu == "" || $mesaj == "") {
        // Hata.
        $hata = "Bütün kutuları doldurun.";
    // E-postada @ yoksa:
    } else if (strpos($eposta, "@") === false) {
        // Hata.
        $hata = "E-posta adresinde @ olmalı.";
    } else {
        // Mesajı kaydederiz. okundu = 0: yönetici daha okumadı.
        kaydet("mesajlar", array("ad" => $ad, "eposta" => $eposta, "konu" => $konu, "mesaj" => $mesaj, "okundu" => 0, "tarih" => simdi()));
        // Sayfayı boş formla yeniden açarız, üstte teşekkür yazısı çıkar.
        yonlendir("iletisim.php?gonderildi=1");
    }
}

// Üst menü.
include "ortak/baslik.php";
?>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Başlık. -->
        <h1>İletişim</h1>
        <!-- Açıklama. -->
        <p class="giris-metni">Sorunuzu yazın, yönetici panelden okuyup size dönsün. Şifrenizi unuttuysanız da buradan yazın.</p>
        <!-- Hata ve başarı kutuları. -->
        <?php include "ortak/mesajlar.php"; ?>
        <!-- İki kutu yan yana. -->
        <div class="yan-yana">
            <!-- Sol: iletişim bilgileri (site yazılarından). -->
            <div class="bilgi-kutu">
                <!-- Başlık. -->
                <h2>Bize ulaşın</h2>
                <!-- Telefon. -->
                <p><strong>Telefon:</strong> <?php echo temiz_yaz(ayar_getir("telefon", "")); ?></p>
                <!-- E-posta. -->
                <p><strong>E-posta:</strong> <?php echo temiz_yaz(ayar_getir("eposta", "")); ?></p>
                <!-- Adres. -->
                <p><strong>Adres:</strong> <?php echo temiz_yaz(ayar_getir("adres", "")); ?></p>
                <!-- Saat. -->
                <p><strong>Saat:</strong> <?php echo temiz_yaz(ayar_getir("calisma_saatleri", "")); ?></p>
            </div>
            <!-- Sağ: iletişim formu. -->
            <form class="form-kutu" method="post" action="iletisim.php">
                <!-- Ad. -->
                <label for="ad">Adınız</label>
                <!-- Ad kutusu. -->
                <input id="ad" name="ad" type="text" value="<?php echo temiz_yaz($ad); ?>">
                <!-- E-posta. -->
                <label for="eposta">E-posta</label>
                <!-- E-posta kutusu. -->
                <input id="eposta" name="eposta" type="text" value="<?php echo temiz_yaz($eposta); ?>">
                <!-- Konu. -->
                <label for="konu">Konu</label>
                <!-- Konu kutusu. -->
                <input id="konu" name="konu" type="text" value="<?php echo temiz_yaz($konu); ?>">
                <!-- Mesaj. -->
                <label for="mesaj">Mesaj</label>
                <!-- Mesaj kutusu. -->
                <textarea id="mesaj" name="mesaj"><?php echo temiz_yaz($mesaj); ?></textarea>
                <!-- Gönder düğmesi. -->
                <p><button class="btn btn-primary" type="submit">Gönder</button></p>
            </form>
        </div>
    </div>
</section>

<?php
// Sayfa altı.
include "ortak/altlik.php";
?>
