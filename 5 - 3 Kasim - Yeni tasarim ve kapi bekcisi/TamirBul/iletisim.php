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
    } else if (!eposta_dogru_mu($eposta)) {
        // Hata.
        $hata = "E-posta adresini doğru yazın. Örnek: ali@ornek.com";
    } else {
        // Mesajı kaydederiz. okundu = 0: yönetici daha okumadı.
        kaydet("mesajlar", array("ad" => $ad, "eposta" => $eposta, "konu" => $konu, "mesaj" => $mesaj, "okundu" => 0, "tarih" => simdi()));
        // Gelen mesajı kayda yazarız.
        log_yaz("İletişim mesajı", $konu);
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
                <!-- Telefon. span: yönetimde seçilen stil sadece yazının kendisine uygulanır. -->
                <p><strong>Telefon:</strong> <span<?php echo yazi_stili("telefon"); ?>><?php echo temiz_yaz(ayar_getir("telefon", "")); ?></span></p>
                <!-- E-posta. -->
                <p><strong>E-posta:</strong> <span<?php echo yazi_stili("eposta"); ?>><?php echo temiz_yaz(ayar_getir("eposta", "")); ?></span></p>
                <!-- Adres. -->
                <p><strong>Adres:</strong> <span<?php echo yazi_stili("adres"); ?>><?php echo temiz_yaz(ayar_getir("adres", "")); ?></span></p>
                <!-- Saat. -->
                <p><strong>Saat:</strong> <span<?php echo yazi_stili("calisma_saatleri"); ?>><?php echo temiz_yaz(ayar_getir("calisma_saatleri", "")); ?></span></p>
            </div>
            <!-- Sağ: iletişim formu. -->
            <form class="form-kutu" method="post" action="iletisim.php">
                <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
                <?php echo guvenlik_alani(); ?>
                <!-- Ad. -->
                <label for="ad">Adınız</label>
                <!-- Ad kutusu. -->
                <input id="ad" name="ad" maxlength="80" type="text" value="<?php echo temiz_yaz($ad); ?>">
                <!-- E-posta. -->
                <label for="eposta">E-posta</label>
                <!-- E-posta kutusu. -->
                <input id="eposta" name="eposta" maxlength="100" type="text" value="<?php echo temiz_yaz($eposta); ?>">
                <!-- Konu. -->
                <label for="konu">Konu</label>
                <!-- Konu kutusu. -->
                <input id="konu" name="konu" maxlength="150" type="text" value="<?php echo temiz_yaz($konu); ?>">
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
