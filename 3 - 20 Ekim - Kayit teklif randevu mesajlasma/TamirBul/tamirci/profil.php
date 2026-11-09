<?php
/*
SAYFA: Tamirci profili
DOSYA: tamirci/profil.php
KİM: Kişi 2

BU SAYFADA BUNLAR OLACAK:
- Ad, soyad, işletme, telefon, il, ilçe, adres, tanıtım, yetkinlikler
- Şifre değiştirme
- Bu bilgiler tamirciler listesinde ve müşteriye giden tekliflerde görünür
*/

// Sekme başlığı.
$sayfa_basligi = "Profil";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece tamirci.
sadece_rol("tamirci");

// Tamircinin numarası.
$kullanici_id = (int) $_SESSION["kullanici_id"];

// HARİTA: Profil güncelleme (tamirci, yetkinlikler)
// Bilgi formu gönderildiyse:
if (formdan_al("form_turu") == "bilgi") {
    // Ad.
    $ad = formdan_al("ad");
    // Soyad.
    $soyad = formdan_al("soyad");
    // İşletme.
    $isletme_adi = formdan_al("isletme_adi");
    // Telefon.
    $telefon = formdan_al("telefon");
    // İl.
    $sehir = formdan_al("sehir");
    // İlçe.
    $ilce = formdan_al("ilce");
    // Adres.
    $adres = formdan_al("adres");
    // Tanıtım.
    $hakkinda = formdan_al("hakkinda");
    // Yetkinlikler. Hiç işaretlenmediyse boş dizi.
    $yetkinlikler = array();
    // İşaretlendiyse:
    if (isset($_POST["yetkinlikler"])) {
        // Dizi.
        $yetkinlikler = $_POST["yetkinlikler"];
    }
    // Boş kutu varsa:
    if ($ad == "" || $soyad == "" || $isletme_adi == "" || $telefon == "") {
        // Hata.
        $hata = "Ad, soyad, işletme adı ve telefon boş bırakılamaz.";
    // İl-ilçe yanlışsa:
    } else if (!il_ilce_dogru_mu($sehir, $ilce)) {
        // Hata.
        $hata = "İl ve ilçe seçin.";
    // Yetkinlik yoksa:
    } else if (count($yetkinlikler) == 0) {
        // Hata.
        $hata = "En az bir yetkinlik seçin.";
    } else {
        // Bilgileri güncelleriz.
        guncelle("kullanicilar", array("ad" => $ad, "soyad" => $soyad, "isletme_adi" => $isletme_adi, "telefon" => $telefon, "sehir" => $sehir, "ilce" => $ilce, "adres" => $adres, "hakkinda" => $hakkinda), "id = $kullanici_id");
        // Yetkinlikleri baştan yazarız.
        yetkinlikleri_kaydet($kullanici_id, $yetkinlikler);
        // Menüdeki ad.
        $_SESSION["ad"] = $ad;
        // Menüdeki soyad.
        $_SESSION["soyad"] = $soyad;
        // Başarı yazısı.
        $tamam = "Bilgiler kaydedildi.";
    }
}

// Şifre formu gönderildiyse:
if (formdan_al("form_turu") == "sifre") {
    // Hata varsa yazısı, yoksa boş yazı.
    $hata = sifre_degistir($kullanici_id, formdan_al("eski"), formdan_al("yeni"), formdan_al("yeni2"));
    // Hata yoksa:
    if ($hata == "") {
        // Başarı yazısı.
        $tamam = "Şifre değişti.";
    }
}

// Güncel bilgiler.
$kullanici = tek_satir("SELECT * FROM kullanicilar WHERE id = $kullanici_id");
// İl kutusunun seçili değeri.
$secili_il = $kullanici["sehir"];
// İlçe kutusunun seçili değeri.
$secili_ilce = $kullanici["ilce"];
// İşaretli yetkinlikler.
$secili_yetkinlikler = array();
// Tablodaki yetkinlikleri okuruz.
foreach (satirlar("SELECT kategori_id FROM tamirci_yetkinlikleri WHERE tamirci_id = $kullanici_id") as $satir) {
    // Numarasını diziye ekleriz.
    $secili_yetkinlikler[] = (int) $satir["kategori_id"];
}

// Üst menü.
include "../ortak/baslik.php";
// Tamirci menüsü.
include "menu.php";
?>

<!-- Sayfa bölümü. -->
<section class="auth-section">
    <!-- Kapsayıcı. -->
    <div class="container auth-container">
        <!-- Geniş kart. -->
        <div class="auth-card auth-card-wide">
            <!-- Başlık. -->
            <h2>Servis Profili</h2>
            <!-- Açıklama. -->
            <p class="auth-subtitle">Yetkinlikleriniz panelinize hangi taleplerin düşeceğini belirler.</p>
            <!-- Hata ve başarı kutuları. -->
            <?php include "../ortak/mesajlar.php"; ?>
            <!-- Bilgi formu. -->
            <form method="post" action="profil.php">
                <!-- Hangi form olduğu gizli gider. -->
                <input type="hidden" name="form_turu" value="bilgi">
                <!-- Ad ve soyad yan yana. -->
                <div class="form-row">
                    <!-- Ad. -->
                    <div class="form-group"><label for="ad">Yetkili Adı</label><input id="ad" name="ad" type="text" value="<?php echo temiz_yaz($kullanici["ad"]); ?>"></div>
                    <!-- Soyad. -->
                    <div class="form-group"><label for="soyad">Yetkili Soyadı</label><input id="soyad" name="soyad" type="text" value="<?php echo temiz_yaz($kullanici["soyad"]); ?>"></div>
                </div>
                <!-- İşletme adı. -->
                <div class="form-group"><label for="isletme_adi">İşletme / Servis Adı</label><input id="isletme_adi" name="isletme_adi" type="text" value="<?php echo temiz_yaz($kullanici["isletme_adi"]); ?>"></div>
                <!-- E-posta ve telefon yan yana. -->
                <div class="form-row">
                    <!-- E-posta (kilitli). -->
                    <div class="form-group"><label for="eposta">E-posta (değişmez)</label><input id="eposta" type="text" value="<?php echo temiz_yaz($kullanici["eposta"]); ?>" disabled></div>
                    <!-- Telefon. -->
                    <div class="form-group"><label for="telefon">Telefon</label><input id="telefon" name="telefon" type="text" value="<?php echo temiz_yaz($kullanici["telefon"]); ?>"></div>
                </div>
                <!-- İl ve ilçe (ortak parça). -->
                <?php include "../ortak/konum-secimi.php"; ?>
                <!-- Adres. -->
                <div class="form-group"><label for="adres">Dükkan Adresi</label><textarea id="adres" name="adres" rows="2"><?php echo temiz_yaz($kullanici["adres"]); ?></textarea></div>
                <!-- Yetkinlik kutuları (ortak parça). -->
                <?php include "../ortak/yetkinlik-secimi.php"; ?>
                <!-- Tanıtım. -->
                <div class="form-group"><label for="hakkinda">Kısa Tanıtım</label><textarea id="hakkinda" name="hakkinda"><?php echo temiz_yaz($kullanici["hakkinda"]); ?></textarea></div>
                <!-- Kaydet düğmesi. -->
                <button class="btn btn-secondary btn-block" type="submit">Bilgileri Kaydet</button>
            </form>
        </div>
        <!-- Şifre kartı. -->
        <div class="auth-card bosluklu">
            <!-- Başlık. -->
            <h2>Şifre Değiştir</h2>
            <!-- Şifre formu. -->
            <form method="post" action="profil.php">
                <!-- Hangi form olduğu gizli gider. -->
                <input type="hidden" name="form_turu" value="sifre">
                <!-- Eski şifre. -->
                <div class="form-group"><label for="eski">Eski şifre</label><input id="eski" name="eski" type="password"></div>
                <!-- Yeni şifre. -->
                <div class="form-group"><label for="yeni">Yeni şifre</label><input id="yeni" name="yeni" type="password"></div>
                <!-- Yeni şifre tekrar. -->
                <div class="form-group"><label for="yeni2">Yeni şifre tekrar</label><input id="yeni2" name="yeni2" type="password"></div>
                <!-- Kaydet düğmesi. -->
                <button class="btn btn-primary btn-block" type="submit">Şifreyi Kaydet</button>
            </form>
        </div>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
