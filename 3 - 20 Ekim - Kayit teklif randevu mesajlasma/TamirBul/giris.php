<?php
/*
SAYFA: Giriş
DOSYA: giris.php
KİM: Kişi 3

BU SAYFADA BUNLAR OLACAK:
- E-posta ve şifre kutusu, Beni hatırla kutusu
- Bilgiler doğruysa role göre panele gider: admin, tamirci, müşteri
- Şifremi unuttum linki iletişim sayfasına gider (yönetici şifreyi yeniler)
*/

// Sekme başlığı.
$sayfa_basligi = "Giriş";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "ortak/hazirla.php";

// Zaten giriş yapıldıysa formu göstermeyiz:
if (giris_yapildi_mi()) {
    // Kendi paneline gider. Örnek: musteri/panel.php
    yonlendir($_SESSION["rol"] . "/panel.php");
}

// Kayıt sayfasından gelindiyse adres ?kayit=musteri olur.
if (isset($_GET["kayit"])) {
    // Kayıt tamam yazısı.
    $tamam = "Kayıt tamam. Giriş yapabilirsiniz. Tamirci hesabı, yönetici onayından sonra teklif verebilir.";
}

// Formdaki e-posta. Hatalı girişte kutuya geri yazılır.
$eposta = formdan_al("eposta");

// HARİTA: Giriş yapma (şifre kontrolü, password_verify)
// Form gönderildiyse:
if (form_geldi_mi()) {
    // Şifreyi alırız.
    $sifre = formdan_al("sifre");
    // E-postayı sorgu için temizleriz.
    $eposta_temiz = temizle($eposta);
    // Bu e-postadaki kullanıcıyı buluruz.
    $kullanici = tek_satir("SELECT * FROM kullanicilar WHERE eposta = '$eposta_temiz'");

    // Kullanıcı yoksa:
    if (!$kullanici) {
        // Hata.
        $hata = "Bu e-posta ile kayıtlı kullanıcı yok.";
    // password_verify: yazılan şifre, veritabanındaki hash ile uyuşuyor mu?
    } else if (!password_verify($sifre, $kullanici["sifre"])) {
        // Hata.
        $hata = "Şifre yanlış.";
    } else {
        // Oturum numarasını yenileriz (güvenlik için).
        session_regenerate_id(true);
        // Kullanıcının numarası oturuma yazılır. Diğer sayfalar bunu okur.
        $_SESSION["kullanici_id"] = $kullanici["id"];
        // Adı.
        $_SESSION["ad"] = $kullanici["ad"];
        // Soyadı.
        $_SESSION["soyad"] = $kullanici["soyad"];
        // Rolü: müşteri, tamirci veya admin.
        $_SESSION["rol"] = $kullanici["rol"];

        // HARİTA: Beni hatırla (30 günlük çerez)
        // Beni hatırla işaretliyse oturum çerezi 30 gün silinmez.
        if (isset($_POST["beni_hatirla"])) {
            // 60 sn * 60 dk * 24 saat * 30 gün sonra silinecek çerez.
            setcookie(session_name(), session_id(), time() + 60 * 60 * 24 * 30, "/");
        }

        // HARİTA: Role göre panele yönlendirme
        // Role göre panele gideriz. Örnek: tamirci -> tamirci/panel.php
        yonlendir($kullanici["rol"] . "/panel.php");
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
            <h2>Hesabınıza Giriş Yapın</h2>
            <!-- Açıklama. -->
            <p class="auth-subtitle">Müşteri, tamirci ve yönetici aynı formdan girer.</p>
            <!-- Hata ve başarı kutuları. -->
            <?php include "ortak/mesajlar.php"; ?>

            <!-- Giriş formu. -->
            <form method="post" action="giris.php">
                <!-- E-posta alanı. -->
                <div class="form-group">
                    <!-- Kutunun adı. -->
                    <label for="txtEposta">E-Posta Adresi</label>
                    <!-- E-posta kutusu. -->
                    <input id="txtEposta" name="eposta" type="email" placeholder="ornek@email.com" value="<?php echo temiz_yaz($eposta); ?>" required>
                </div>
                <!-- Şifre alanı. -->
                <div class="form-group">
                    <!-- Kutunun adı. -->
                    <label for="txtSifre">Şifre</label>
                    <!-- Şifre kutusu. Yazılanlar nokta görünür. -->
                    <input id="txtSifre" name="sifre" type="password" placeholder="******" required>
                </div>
                <!-- Beni hatırla ve şifremi unuttum satırı. -->
                <div class="form-actions-row">
                    <!-- Beni hatırla kutusu. -->
                    <label class="remember-me"><input type="checkbox" name="beni_hatirla" value="1"> Beni Hatırla</label>
                    <!-- Şifremi unuttum: yöneticiye yazılır. -->
                    <a class="forgot-password" href="iletisim.php">Şifremi Unuttum?</a>
                </div>
                <!-- Giriş düğmesi. -->
                <button class="btn btn-primary btn-block" type="submit">Giriş Yap</button>
            </form>

            <!-- Kayıt linkleri. -->
            <div class="auth-footer">
                <!-- Soru. -->
                <p>Hesabınız yok mu?</p>
                <!-- İki link yan yana. -->
                <div class="register-links">
                    <!-- Müşteri kaydı. -->
                    <a class="link-primary" href="musteri-kayit.php">Müşteri Kaydı Aç</a>
                    <!-- Ayraç. -->
                    <span>|</span>
                    <!-- Tamirci kaydı. -->
                    <a class="link-secondary" href="tamirci-kayit.php">Tamirci Olarak Katıl</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Sayfa altı.
include "ortak/altlik.php";
?>
