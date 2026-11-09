<?php
/*
SAYFA: Giriş
DOSYA: giris.php
KİM: Kişi 3

BU SAYFADA BUNLAR OLACAK:
- E-posta ve şifre kutusu, Beni hatırla kutusu
- Bilgiler doğruysa role göre panele gider: admin, tamirci, müşteri
- Şifremi unuttum linki iletişim sayfasına gider (yönetici şifreyi yeniler)
GÜVENLİK:
- E-posta yanlış da olsa şifre yanlış da olsa AYNI hata yazısı çıkar. Böylece kötü niyetli biri
  "bu e-posta sitede kayıtlı mı?" diye deneyerek üye listesi çıkaramaz.
- 5 yanlış denemeden sonra 5 dakika bekletir (şifre tahmin etmeyi zorlaştırır).
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

// HARİTA: Giriş denemesi sınırı (5 yanlış, 5 dakika bekleme)
// Yanlış deneme sayısı oturumda tutulur. Hiç yoksa 0.
if (!isset($_SESSION["yanlis_giris"])) {
    // Sayaç sıfırdan başlar.
    $_SESSION["yanlis_giris"] = 0;
}
// Bekleme bitiş zamanı. 0 ise bekleme yok.
if (!isset($_SESSION["giris_bekleme"])) {
    // Bekleme yok.
    $_SESSION["giris_bekleme"] = 0;
}
// Kaç saniye daha beklemesi gerekiyor? time(): şimdiki zaman (saniye).
$kalan_saniye = $_SESSION["giris_bekleme"] - time();

// HARİTA: Giriş yapma (şifre kontrolü, password_verify)
// Form gönderildiyse:
if (form_geldi_mi()) {
    // Şifreyi alırız.
    $sifre = formdan_al("sifre");
    // E-postayı sorgu için temizleriz.
    $eposta_temiz = temizle($eposta);
    // Bu e-postadaki kullanıcıyı buluruz.
    $kullanici = tek_satir("SELECT * FROM kullanicilar WHERE eposta = '$eposta_temiz'");

    // Kontrol edilecek şifre özeti (hash). Kullanıcı varsa onunki.
    // Kullanıcı yoksa da sahte bir özetle (rastgele bir yazının özeti) kontrol yaparız. Böylece cevap süresi aynı olur,
    // süreye bakarak "bu e-posta var mı" anlaşılamaz.
    $kontrol_ozeti = '$2y$10$L4.BbxhYIK03X8ZLF1CocOgB8xX25dpQ0Qiw8uhMggkYd6cNlSDJ6';
    // Kullanıcı varsa:
    if ($kullanici) {
        // Gerçek şifre özeti.
        $kontrol_ozeti = $kullanici["sifre"];
    }
    // password_verify: yazılan şifre, özetle uyuşuyor mu? Kullanıcı yoksa her zaman false.
    $sifre_dogru = password_verify($sifre, $kontrol_ozeti) && $kullanici;

    // Çok fazla yanlış deneme yapıldıysa:
    if ($kalan_saniye > 0) {
        // ceil: yukarı yuvarlar. Saniyeyi dakikaya çeviririz.
        $hata = "Çok fazla yanlış deneme yaptınız. " . ceil($kalan_saniye / 60) . " dakika sonra tekrar deneyin.";
    // E-posta veya şifre yanlışsa (hangisinin yanlış olduğunu SÖYLEMEYİZ):
    } else if (!$sifre_dogru) {
        // Yanlış deneme sayısını 1 artırırız.
        $_SESSION["yanlis_giris"] = $_SESSION["yanlis_giris"] + 1;
        // 5. yanlışta:
        if ($_SESSION["yanlis_giris"] >= 5) {
            // Kilitlenmeyi kayda yazarız. Çok deneme, şifre tahmini olabilir.
            log_yaz("Giriş kilitlendi", "5 yanlış deneme. Son e-posta: " . $eposta);
            // 5 dakika (300 saniye) bekleme koyarız.
            $_SESSION["giris_bekleme"] = time() + 300;
            // Sayacı sıfırlarız.
            $_SESSION["yanlis_giris"] = 0;
        }
        // Hatalı girişi kayda yazarız. Şifre ASLA yazılmaz, sadece denenen e-posta.
        log_yaz("Hatalı giriş", "Denenen e-posta: " . $eposta);
        // Tek, ortak hata yazısı.
        $hata = "E-posta veya şifre hatalı.";
    } else {
        // Doğru giriş: yanlış deneme sayacını sıfırlarız.
        $_SESSION["yanlis_giris"] = 0;
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
            // httponly: JavaScript okuyamaz. samesite Lax: başka site bu çerezle form gönderemez.
            setcookie(session_name(), session_id(), array("expires" => time() + 60 * 60 * 24 * 30, "path" => "/", "httponly" => true, "samesite" => "Lax"));
        }

        // HARİTA: Role göre panele yönlendirme
        // Başarılı girişi kayda yazarız.
        log_yaz("Giriş yapıldı", $kullanici["eposta"]);
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
                <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
                <?php echo guvenlik_alani(); ?>
                <!-- E-posta alanı. -->
                <div class="form-group">
                    <!-- Kutunun adı. -->
                    <label for="txtEposta">E-Posta Adresi</label>
                    <!-- E-posta kutusu. -->
                    <input id="txtEposta" name="eposta" maxlength="100" type="email" placeholder="ornek@email.com" value="<?php echo temiz_yaz($eposta); ?>" required>
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
