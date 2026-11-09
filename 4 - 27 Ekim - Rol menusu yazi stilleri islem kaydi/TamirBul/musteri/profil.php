<?php
/*
SAYFA: Müşteri profili
DOSYA: musteri/profil.php
KİM: Kişi 2

BU SAYFADA BUNLAR OLACAK:
- Ad, soyad, telefon, il, ilçe formu. E-posta değişmez (giriş adıdır).
- Şifre değiştirme formu
- Güncellenecek kişi formdan değil oturumdan gelir
*/

// Sekme başlığı.
$sayfa_basligi = "Profil";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece müşteri.
sadece_rol("musteri");

// Müşterinin numarası.
$kullanici_id = (int) $_SESSION["kullanici_id"];

// HARİTA: Profil güncelleme (müşteri)
// Bilgi formu gönderildiyse:
if (formdan_al("form_turu") == "bilgi") {
    // Ad.
    $ad = formdan_al("ad");
    // Soyad.
    $soyad = formdan_al("soyad");
    // Telefon.
    $telefon = formdan_al("telefon");
    // İl.
    $sehir = formdan_al("sehir");
    // İlçe.
    $ilce = formdan_al("ilce");
    // Boş kutu varsa:
    if ($ad == "" || $soyad == "" || $telefon == "") {
        // Hata.
        $hata = "Ad, soyad ve telefon boş bırakılamaz.";
    // İl-ilçe yanlışsa:
    // Telefon 10-11 rakam değilse:
    } else if (!telefon_dogru_mu($telefon)) {
        // Hata.
        $hata = "Telefonu 10 veya 11 rakam olarak yazın. Örnek: 0532 111 11 11";
    } else if (!il_ilce_dogru_mu($sehir, $ilce)) {
        // Hata.
        $hata = "İl ve ilçe seçin.";
    } else {
        // Bilgileri güncelleriz.
        guncelle("kullanicilar", array("ad" => $ad, "soyad" => $soyad, "telefon" => $telefon, "sehir" => $sehir, "ilce" => $ilce), "id = $kullanici_id");
        // Üstteki menüde yeni ad görünsün.
        $_SESSION["ad"] = $ad;
        // Soyad da.
        $_SESSION["soyad"] = $soyad;
        // Profil değişikliğini kayda yazarız.
        log_yaz("Profil güncellendi", "");
        // Başarı yazısı.
        $tamam = "Bilgiler kaydedildi.";
    }
}

// Şifre formu gönderildiyse:
if (formdan_al("form_turu") == "sifre") {
    // sifre_degistir hata varsa yazısını, yoksa boş yazı verir.
    $hata = sifre_degistir($kullanici_id, formdan_al("eski"), formdan_al("yeni"), formdan_al("yeni2"));
    // Hata yoksa:
    if ($hata == "") {
        // Şifre değişikliğini kayda yazarız. Şifrenin kendisi yazılmaz.
        log_yaz("Şifre değiştirildi", "");
        // Başarı yazısı.
        $tamam = "Şifre değişti.";
    }
}

// Kullanıcının güncel bilgileri.
$kullanici = tek_satir("SELECT * FROM kullanicilar WHERE id = $kullanici_id");
// İl kutusunun seçili değeri.
$secili_il = $kullanici["sehir"];
// İlçe kutusunun seçili değeri.
$secili_ilce = $kullanici["ilce"];

// Üst menü.
include "../ortak/baslik.php";
?>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Başlık. -->
        <h1>Profil</h1>
        <!-- Açıklama. -->
        <p class="giris-metni">İletişim bilgileriniz ve şifreniz.</p>
        <!-- Hata ve başarı kutuları. -->
        <?php include "../ortak/mesajlar.php"; ?>
        <!-- Bilgi formu. -->
        <form class="form-kutu" method="post" action="profil.php">
            <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
            <?php echo guvenlik_alani(); ?>
            <!-- Hangi form olduğu gizli gider. -->
            <input type="hidden" name="form_turu" value="bilgi">
            <!-- Ad. -->
            <label for="ad">Ad</label>
            <!-- Ad kutusu. -->
            <input id="ad" name="ad" maxlength="50" type="text" value="<?php echo temiz_yaz($kullanici["ad"]); ?>">
            <!-- Soyad. -->
            <label for="soyad">Soyad</label>
            <!-- Soyad kutusu. -->
            <input id="soyad" name="soyad" maxlength="50" type="text" value="<?php echo temiz_yaz($kullanici["soyad"]); ?>">
            <!-- E-posta (değişmez). -->
            <label for="eposta">E-posta</label>
            <!-- disabled: kutu kilitli. -->
            <input id="eposta" type="text" value="<?php echo temiz_yaz($kullanici["eposta"]); ?>" disabled>
            <!-- Telefon. -->
            <label for="telefon">Telefon</label>
            <!-- Telefon kutusu. -->
            <input id="telefon" name="telefon" maxlength="20" type="text" value="<?php echo temiz_yaz($kullanici["telefon"]); ?>">
            <!-- İl ve ilçe (ortak parça). -->
            <?php include "../ortak/konum-secimi.php"; ?>
            <!-- Kaydet düğmesi. -->
            <p><button class="btn btn-primary" type="submit">Bilgileri kaydet</button></p>
        </form>
        <!-- Şifre formu. -->
        <form class="form-kutu" method="post" action="profil.php">
            <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
            <?php echo guvenlik_alani(); ?>
            <!-- Hangi form olduğu gizli gider. -->
            <input type="hidden" name="form_turu" value="sifre">
            <!-- Başlık. -->
            <h2>Şifre değiştir</h2>
            <!-- Eski şifre. -->
            <label for="eski">Eski şifre</label>
            <!-- Eski şifre kutusu. -->
            <input id="eski" name="eski" type="password">
            <!-- Yeni şifre. -->
            <label for="yeni">Yeni şifre</label>
            <!-- Yeni şifre kutusu. -->
            <input id="yeni" name="yeni" type="password">
            <!-- Yeni şifre tekrar. -->
            <label for="yeni2">Yeni şifre tekrar</label>
            <!-- Tekrar kutusu. -->
            <input id="yeni2" name="yeni2" type="password">
            <!-- Kaydet düğmesi. -->
            <p><button class="btn btn-secondary" type="submit">Şifreyi kaydet</button></p>
        </form>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
