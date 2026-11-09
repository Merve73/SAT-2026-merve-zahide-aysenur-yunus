<?php
/*
SAYFA: Yönetici şifresi
DOSYA: admin/sifre.php
KİM: Kişi 4

BU SAYFADA BUNLAR OLACAK:
- Yönetici kendi şifresini değiştirir: eski şifre, yeni şifre, yeni şifre tekrar.
- Örnek verideki yönetici şifresi 123456'dır. Site yayına çıkmadan önce MUTLAKA buradan değiştirilir.
- Şifre kontrolü ve kaydı ortak/guvenlik.php içindeki sifre_degistir() fonksiyonundadır (müşteri ve tamirci de onu kullanır).
*/

// Sekme başlığı.
$sayfa_basligi = "Şifre değiştir";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece yönetici.
sadece_rol("admin");

// Yöneticinin numarası.
$kullanici_id = (int) $_SESSION["kullanici_id"];

// HARİTA: Yönetici şifre değiştirme
// Form gönderildiyse:
if (form_geldi_mi()) {
    // sifre_degistir hata varsa yazısını, yoksa boş yazı verir.
    $hata = sifre_degistir($kullanici_id, formdan_al("eski"), formdan_al("yeni"), formdan_al("yeni2"));
    // Hata yoksa:
    if ($hata == "") {
        // Şifre değişikliğini kayda yazarız. Şifrenin kendisi yazılmaz.
        log_yaz("Şifre değiştirildi", "Yönetici");
        // Başarı yazısı.
        $tamam = "Şifre değişti. Bir sonraki girişte yeni şifreyi kullanın.";
    }
}

// Üst menü.
include "../ortak/baslik.php";
?>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Başlık. -->
        <h1>Şifre değiştir</h1>
        <!-- Açıklama. -->
        <p class="giris-metni">Örnek verideki yönetici şifresi herkesçe bilinir (123456). Site yayına çıkmadan önce buradan değiştirin.</p>
        <!-- Hata ve başarı kutuları. -->
        <?php include "../ortak/mesajlar.php"; ?>
        <!-- Şifre formu. -->
        <form class="form-kutu" method="post" action="sifre.php">
            <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
            <?php echo guvenlik_alani(); ?>
            <!-- Eski şifre. -->
            <label for="eski">Eski şifre</label>
            <!-- Eski şifre kutusu. autocomplete: tarayıcı şu anki şifreyi önerebilir. -->
            <input id="eski" name="eski" type="password" autocomplete="current-password" required>
            <!-- Yeni şifre. -->
            <label for="yeni">Yeni şifre (en az 6 karakter)</label>
            <!-- Yeni şifre kutusu. autocomplete: tarayıcı güçlü şifre önerebilir. -->
            <input id="yeni" name="yeni" type="password" minlength="6" autocomplete="new-password" required>
            <!-- Yeni şifre tekrar. -->
            <label for="yeni2">Yeni şifre tekrar</label>
            <!-- Tekrar kutusu. -->
            <input id="yeni2" name="yeni2" type="password" minlength="6" autocomplete="new-password" required>
            <!-- Kaydet düğmesi. -->
            <p><button class="btn btn-primary" type="submit">Şifreyi kaydet</button></p>
        </form>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
