<?php
/*
SAYFA: Müşteri kaydı
DOSYA: musteri-kayit.php
KİM: Kişi 3

BU SAYFADA BUNLAR OLACAK:
- Ad, soyad, e-posta, telefon, il, ilçe, şifre, şifre tekrar
- Kayıt olunca giriş sayfasına döner. Müşteri onay beklemez.
*/

// Sekme başlığı.
$sayfa_basligi = "Müşteri kaydı";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "ortak/hazirla.php";

// Formdaki ad. Hata olursa kutuya geri yazılır.
$ad = formdan_al("ad");
// Soyad.
$soyad = formdan_al("soyad");
// E-posta.
$eposta = formdan_al("eposta");
// Telefon.
$telefon = formdan_al("telefon");
// İl. ortak/konum-secimi.php bu değişkenle kutuyu seçili getirir.
$secili_il = formdan_al("sehir");
// İlçe.
$secili_ilce = formdan_al("ilce");

// HARİTA: Müşteri kaydı (INSERT, password_hash)
// Form gönderildiyse:
if (form_geldi_mi()) {
    // Şifre.
    $sifre = formdan_al("sifre");
    // Şifre tekrarı.
    $sifre_tekrar = formdan_al("sifre_tekrar");
    // Bu e-posta ile kayıt var mı diye sayarız.
    $eposta_var = sayi_getir("SELECT COUNT(*) FROM kullanicilar WHERE eposta = '" . temizle($eposta) . "'");

    // Boş kutu varsa:
    if ($ad == "" || $soyad == "" || $eposta == "" || $telefon == "" || $sifre == "") {
        // Hata.
        $hata = "Bütün alanları doldurun.";
    // E-postada @ yoksa:
    } else if (strpos($eposta, "@") === false) {
        // Hata.
        $hata = "E-posta adresinde @ olmalı.";
    // İl ve ilçe tabloda yoksa:
    } else if (!il_ilce_dogru_mu($secili_il, $secili_ilce)) {
        // Hata.
        $hata = "İl ve ilçe seçin.";
    // Şifre 6 harften kısaysa:
    } else if (strlen($sifre) < 6) {
        // Hata.
        $hata = "Şifre en az 6 karakter olsun.";
    // Şifreler farklıysa:
    } else if ($sifre != $sifre_tekrar) {
        // Hata.
        $hata = "Şifre tekrarı aynı değil.";
    // E-posta zaten kayıtlıysa:
    } else if ($eposta_var > 0) {
        // Hata.
        $hata = "Bu e-posta ile kayıt var. Giriş yapın.";
    } else {
        // Yeni müşteriyi kaydederiz. password_hash: şifre geri çözülemeyen yazıya çevrilir. onayli = 1: onay beklemez.
        $yeni_id = kaydet("kullanicilar", array("ad" => $ad, "soyad" => $soyad, "eposta" => $eposta, "sifre" => password_hash($sifre, PASSWORD_DEFAULT), "telefon" => $telefon, "rol" => "musteri", "sehir" => $secili_il, "ilce" => $secili_ilce, "adres" => "", "hakkinda" => "", "onayli" => 1, "kayit_tarihi" => simdi()));
        // Kayıt olduysa giriş sayfasına gideriz.
        if ($yeni_id > 0) {
            // Giriş sayfası.
            yonlendir("giris.php?kayit=musteri");
        }
        // Kayıt olmadıysa:
        $hata = "Kayıt olmadı. Veritabanını kontrol edin.";
    }
}

// Üst menü.
include "ortak/baslik.php";
?>

<!-- Ortalanmış form bölümü. -->
<section class="auth-section">
    <!-- Kapsayıcı. -->
    <div class="container auth-container">
        <!-- Beyaz kart. -->
        <div class="auth-card">
            <!-- Başlık. -->
            <h2>Müşteri Hesabı Oluştur</h2>
            <!-- Açıklama. -->
            <p class="auth-subtitle">Arızalı cihazın için hızlıca teklif topla ve tamirciyi seç.</p>
            <!-- Hata kutusu. -->
            <?php include "ortak/mesajlar.php"; ?>

            <!-- Kayıt formu. -->
            <form method="post" action="musteri-kayit.php">
                <!-- Ad ve soyad yan yana. -->
                <div class="form-row">
                    <!-- Ad. -->
                    <div class="form-group">
                        <!-- Kutunun adı. -->
                        <label for="txtAd">Adınız</label>
                        <!-- Ad kutusu. -->
                        <input id="txtAd" name="ad" type="text" value="<?php echo temiz_yaz($ad); ?>" required>
                    </div>
                    <!-- Soyad. -->
                    <div class="form-group">
                        <!-- Kutunun adı. -->
                        <label for="txtSoyad">Soyadınız</label>
                        <!-- Soyad kutusu. -->
                        <input id="txtSoyad" name="soyad" type="text" value="<?php echo temiz_yaz($soyad); ?>" required>
                    </div>
                </div>
                <!-- E-posta. -->
                <div class="form-group">
                    <!-- Kutunun adı. -->
                    <label for="txtEposta">E-Posta Adresi</label>
                    <!-- E-posta kutusu. -->
                    <input id="txtEposta" name="eposta" type="email" value="<?php echo temiz_yaz($eposta); ?>" required>
                </div>
                <!-- Telefon. -->
                <div class="form-group">
                    <!-- Kutunun adı. -->
                    <label for="txtTelefon">Telefon Numarası</label>
                    <!-- Telefon kutusu. -->
                    <input id="txtTelefon" name="telefon" type="tel" placeholder="05XXXXXXXXX" value="<?php echo temiz_yaz($telefon); ?>" required>
                </div>
                <!-- İl ve ilçe kutuları (ortak parça). -->
                <?php include "ortak/konum-secimi.php"; ?>
                <!-- Şifre ve tekrarı yan yana. -->
                <div class="form-row">
                    <!-- Şifre. -->
                    <div class="form-group">
                        <!-- Kutunun adı. -->
                        <label for="txtSifre">Şifre</label>
                        <!-- Şifre kutusu. -->
                        <input id="txtSifre" name="sifre" type="password" placeholder="En az 6 karakter" required>
                    </div>
                    <!-- Şifre tekrar. -->
                    <div class="form-group">
                        <!-- Kutunun adı. -->
                        <label for="txtSifreTekrar">Şifre Tekrarı</label>
                        <!-- Tekrar kutusu. -->
                        <input id="txtSifreTekrar" name="sifre_tekrar" type="password" required>
                    </div>
                </div>
                <!-- Kayıt düğmesi. -->
                <button class="btn btn-primary btn-block" type="submit">Kayıt Ol</button>
            </form>

            <!-- Alt linkler. -->
            <div class="auth-footer">
                <!-- Giriş linki. -->
                <p>Zaten hesabınız var mı? <a class="link-primary" href="giris.php">Giriş Yap</a></p>
            </div>
        </div>
    </div>
</section>

<?php
// Sayfa altı.
include "ortak/altlik.php";
?>
