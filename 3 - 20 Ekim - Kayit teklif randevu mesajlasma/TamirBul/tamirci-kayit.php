<?php
/*
SAYFA: Tamirci kaydı
DOSYA: tamirci-kayit.php
KİM: Kişi 3

BU SAYFADA BUNLAR OLACAK:
- Yetkili ad, soyad, işletme adı, e-posta, telefon, il, ilçe, adres
- Yetkinlik onay kutuları (en az bir tane)
- Şifre ve tekrarı
- Kayıt olunca onayli = 0 yazılır. Yönetici onaylayana kadar teklif veremez.
*/

// Sekme başlığı.
$sayfa_basligi = "Tamirci kaydı";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "ortak/hazirla.php";

// Formdaki kutular. Hata olursa geri yazılır.
$ad = formdan_al("ad");
// Soyad.
$soyad = formdan_al("soyad");
// İşletme adı.
$isletme_adi = formdan_al("isletme_adi");
// E-posta.
$eposta = formdan_al("eposta");
// Telefon.
$telefon = formdan_al("telefon");
// Dükkan adresi.
$adres = formdan_al("adres");
// İl.
$secili_il = formdan_al("sehir");
// İlçe.
$secili_ilce = formdan_al("ilce");
// İşaretlenen yetkinlikler. Hiç yoksa boş dizi.
$secili_yetkinlikler = array();
// Onay kutuları dizi olarak gelir.
if (isset($_POST["yetkinlikler"])) {
    // Her değeri sayıya çeviririz.
    foreach ($_POST["yetkinlikler"] as $kategori_id) {
        // Diziye ekleriz.
        $secili_yetkinlikler[] = (int) $kategori_id;
    }
}

// HARİTA: Tamirci kaydı (onay bekleyen hesap)
// Form gönderildiyse:
if (form_geldi_mi()) {
    // Şifre.
    $sifre = formdan_al("sifre");
    // Şifre tekrarı.
    $sifre_tekrar = formdan_al("sifre_tekrar");
    // Bu e-posta ile kayıt var mı?
    $eposta_var = sayi_getir("SELECT COUNT(*) FROM kullanicilar WHERE eposta = '" . temizle($eposta) . "'");

    // Boş kutu varsa:
    if ($ad == "" || $soyad == "" || $isletme_adi == "" || $eposta == "" || $telefon == "" || $sifre == "") {
        // Hata.
        $hata = "Adres dışındaki bütün alanları doldurun.";
    // E-postada @ yoksa:
    } else if (strpos($eposta, "@") === false) {
        // Hata.
        $hata = "E-posta adresinde @ olmalı.";
    // İl-ilçe yanlışsa:
    } else if (!il_ilce_dogru_mu($secili_il, $secili_ilce)) {
        // Hata.
        $hata = "İl ve ilçe seçin.";
    // Yetkinlik seçilmediyse:
    } else if (count($secili_yetkinlikler) == 0) {
        // Hata.
        $hata = "En az bir yetkinlik seçin. Talepler bu seçime göre gelir.";
    // Şifre kısaysa:
    } else if (strlen($sifre) < 6) {
        // Hata.
        $hata = "Şifre en az 6 karakter olsun.";
    // Şifreler farklıysa:
    } else if ($sifre != $sifre_tekrar) {
        // Hata.
        $hata = "Şifre tekrarı aynı değil.";
    // E-posta kayıtlıysa:
    } else if ($eposta_var > 0) {
        // Hata.
        $hata = "Bu e-posta ile kayıt var. Giriş yapın.";
    } else {
        // Yeni tamirciyi kaydederiz. onayli = 0: yönetici onaylayana kadar teklif veremez.
        $yeni_id = kaydet("kullanicilar", array("ad" => $ad, "soyad" => $soyad, "eposta" => $eposta, "sifre" => password_hash($sifre, PASSWORD_DEFAULT), "telefon" => $telefon, "rol" => "tamirci", "isletme_adi" => $isletme_adi, "sehir" => $secili_il, "ilce" => $secili_ilce, "adres" => $adres, "hakkinda" => "", "onayli" => 0, "kayit_tarihi" => simdi()));
        // Kayıt olduysa:
        if ($yeni_id > 0) {
            // Yetkinliklerini kaydederiz.
            yetkinlikleri_kaydet($yeni_id, $secili_yetkinlikler);
            // Giriş sayfasına gideriz.
            yonlendir("giris.php?kayit=tamirci");
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
        <!-- Geniş beyaz kart. -->
        <div class="auth-card auth-card-wide">
            <!-- Başlık. -->
            <h2>Tamirci / Teknik Servis Hesabı Oluştur</h2>
            <!-- Açıklama. -->
            <p class="auth-subtitle">İşletmenizi kaydedin, teklif vermeye başlayın. (Kayıt sonrası yönetici onayı gereklidir)</p>
            <!-- Hata kutusu. -->
            <?php include "ortak/mesajlar.php"; ?>

            <!-- Kayıt formu. -->
            <form method="post" action="tamirci-kayit.php">
                <!-- Ad ve soyad yan yana. -->
                <div class="form-row">
                    <!-- Ad. -->
                    <div class="form-group">
                        <!-- Kutunun adı. -->
                        <label for="txtAd">Yetkili Adı</label>
                        <!-- Ad kutusu. -->
                        <input id="txtAd" name="ad" type="text" value="<?php echo temiz_yaz($ad); ?>" required>
                    </div>
                    <!-- Soyad. -->
                    <div class="form-group">
                        <!-- Kutunun adı. -->
                        <label for="txtSoyad">Yetkili Soyadı</label>
                        <!-- Soyad kutusu. -->
                        <input id="txtSoyad" name="soyad" type="text" value="<?php echo temiz_yaz($soyad); ?>" required>
                    </div>
                </div>
                <!-- İşletme adı. -->
                <div class="form-group">
                    <!-- Kutunun adı. -->
                    <label for="txtIsletmeAdi">İşletme / Servis Adı</label>
                    <!-- İşletme kutusu. -->
                    <input id="txtIsletmeAdi" name="isletme_adi" type="text" placeholder="Örn: Güven Teknik Servis" value="<?php echo temiz_yaz($isletme_adi); ?>" required>
                </div>
                <!-- E-posta ve telefon yan yana. -->
                <div class="form-row">
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
                </div>
                <!-- İl ve ilçe (ortak parça). -->
                <?php include "ortak/konum-secimi.php"; ?>
                <!-- Adres. -->
                <div class="form-group">
                    <!-- Kutunun adı. -->
                    <label for="txtAcikAdres">Açık Adres / Dükkan Adresi</label>
                    <!-- Adres kutusu. -->
                    <textarea id="txtAcikAdres" name="adres" rows="2"><?php echo temiz_yaz($adres); ?></textarea>
                </div>
                <!-- Yetkinlik kutuları (ortak parça). -->
                <?php include "ortak/yetkinlik-secimi.php"; ?>
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
                <button class="btn btn-secondary btn-block" type="submit">Tamirci Başvurusunu Tamamla</button>
            </form>

            <!-- Alt link. -->
            <div class="auth-footer">
                <!-- Giriş linki. -->
                <p>Zaten hesabınız var mı? <a class="link-secondary" href="giris.php">Giriş Yap</a></p>
            </div>
        </div>
    </div>
</section>

<?php
// Sayfa altı.
include "ortak/altlik.php";
?>
