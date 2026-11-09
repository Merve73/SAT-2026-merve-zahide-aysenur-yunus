<?php
/*
SAYFA: Talep oluştur
DOSYA: talep-olustur.php
KİM: Kişi 3

BU SAYFADA BUNLAR OLACAK:
- Giriş yapmamış kişiye "Giriş Yap / Kayıt Ol" kutusu (css: .giris-gerekli)
- Müşteriye form: kategori, cihaz türü, hizmet (isteğe bağlı), marka, model, arıza, fotoğraf, il, ilçe
- Kayıt olunca talep detayına gider, orada takip kodu yazar. Örnek: TB1004
*/

// Sekme başlığı.
$sayfa_basligi = "Talep oluştur";
// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "ortak/hazirla.php";

// Giriş yapan kişi müşteri mi?
$musteri_mi = giris_yapildi_mi() && $_SESSION["rol"] == "musteri";

// Seçili kategori. Formdan kategori_id, ana sayfa kartından ?kategori=2 gelir.
$secili_kategori = gelen_sayi("kategori_id");
// Formdan gelmediyse:
if ($secili_kategori == 0) {
    // Adres çubuğuna bakarız.
    $secili_kategori = gelen_sayi("kategori");
}
// Seçili hizmet. Hizmet detayından ?hizmet_id=3 gelir.
$secili_hizmet = gelen_sayi("hizmet_id");
// Hizmet sitede var ve açık mı? Formu kurcalayıp olmayan hizmet yazılamasın.
$hizmet = tek_satir("SELECT kategori_id FROM hizmetler WHERE aktif = 1 AND id = $secili_hizmet");
// Yoksa:
if (!$hizmet) {
    // Hizmet seçilmemiş sayılır.
    $secili_hizmet = 0;
// Varsa ve kategori seçilmediyse:
} else if ($secili_kategori == 0) {
    // Hizmetin kategorisi seçili gelsin.
    $secili_kategori = $hizmet["kategori_id"];
}
// Cihaz türü.
$secili_cihaz = formdan_al("cihaz_turu");
// Marka.
$marka = formdan_al("marka");
// Model.
$model = formdan_al("model");
// Arıza açıklaması.
$ariza = formdan_al("ariza");
// İl.
$secili_il = formdan_al("sehir");
// İlçe.
$secili_ilce = formdan_al("ilce");

// Sayfa ilk açıldıysa il ve ilçe müşterinin kendi adresiyle dolu gelsin.
if (!form_geldi_mi() && $musteri_mi) {
    // Müşterinin il ve ilçesi.
    $ben = tek_satir("SELECT sehir, ilce FROM kullanicilar WHERE id = " . (int) $_SESSION["kullanici_id"]);
    // İl.
    $secili_il = $ben["sehir"];
    // İlçe.
    $secili_ilce = $ben["ilce"];
}

// HARİTA: Talep oluşturma (kaydet ve takip kodu)
// Müşteri formu gönderdiyse:
if (form_geldi_mi() && $musteri_mi) {
    // Fotoğrafın adı. Aşağıda yüklenince dolar.
    $resim = "";
    // Kategori yoksa:
    if (!kategori_var_mi($secili_kategori)) {
        // Hata.
        $hata = "Ana kategori seçin.";
    // Cihaz türü bu kategoride yoksa:
    } else if (!cihaz_turu_dogru_mu($secili_kategori, $secili_cihaz)) {
        // Hata.
        $hata = "Cihaz türü seçin.";
    // Marka veya arıza boşsa:
    } else if ($marka == "" || $ariza == "") {
        // Hata.
        $hata = "Marka ve arıza açıklaması boş bırakılamaz.";
    // İl-ilçe yanlışsa:
    } else if (!il_ilce_dogru_mu($secili_il, $secili_ilce)) {
        // Hata.
        $hata = "Tamirin yapılacağı il ve ilçeyi seçin.";
    } else {
        // Fotoğrafı yükleriz: dosya yoksa "", hatalıysa false, başarılıysa dosya adı.
        $resim = resim_yukle("cihaz_resmi", "talep");
    }
    // Fotoğraf hatalıysa:
    if ($hata == "" && $resim === false) {
        // Hata.
        $hata = "Fotoğraf yüklenemedi. jpg, png, webp veya gif olmalı, en fazla 3 MB.";
    }
    // Hata yoksa kaydederiz:
    if ($hata == "") {
        // Hizmet seçilmediyse NULL yazılır.
        $hizmet_id = null;
        // Seçildiyse:
        if ($secili_hizmet > 0) {
            // Numarası yazılır.
            $hizmet_id = $secili_hizmet;
        }
        // Talebi kaydederiz. Müşteri numarası oturumdan gelir, başkası adına talep açılamaz.
        $yeni_id = kaydet("talepler", array("musteri_id" => $_SESSION["kullanici_id"], "hizmet_id" => $hizmet_id, "kategori_id" => $secili_kategori, "cihaz_turu" => $secili_cihaz, "marka" => $marka, "model" => $model, "ariza" => $ariza, "resim" => $resim, "sehir" => $secili_il, "ilce" => $secili_ilce, "durum" => "beklemede", "takip_kodu" => "", "olusturma_tarihi" => simdi()));
        // Takip kodu: TB + (1000 + numara). Örnek: numara 4 -> TB1004
        guncelle("talepler", array("takip_kodu" => "TB" . (1000 + $yeni_id)), "id = $yeni_id");
        // Talep detayına gideriz. F5'e basınca talep ikinci kez açılmasın diye yönlendiririz.
        yonlendir("musteri/talep-detay.php?id=$yeni_id&yeni=1");
    }
}

// Formdaki hizmet listesi.
$hizmetler = satirlar("SELECT id, ad FROM hizmetler WHERE aktif = 1 ORDER BY ad");

// Üst menü.
include "ortak/baslik.php";
?>

<!-- Ortalanmış form bölümü. -->
<section class="request-section">
    <!-- Kapsayıcı. -->
    <div class="container request-container">
        <!-- Geniş beyaz kart. -->
        <div class="request-card">
            <!-- Başlık. -->
            <h2>Arızalı Cihazınız İçin Talep Oluşturun</h2>
            <!-- Açıklama (site yazılarından). -->
            <p class="request-subtitle"><?php echo temiz_yaz(ayar_getir("talep_formu_aciklama", "")); ?></p>
            <!-- Hata kutusu. -->
            <?php include "ortak/mesajlar.php"; ?>

            <!-- Giriş yapılmadıysa: giriş ve kayıt düğmeleri (ortak css: .giris-gerekli, .btn). -->
            <?php if (!giris_yapildi_mi()) { ?>
                <!-- Giriş gerekli kutusu. -->
                <div class="giris-gerekli">
                    <!-- Yazı. -->
                    <p>Talep oluşturmak için müşteri hesabınızla giriş yapın.</p>
                    <!-- Düğmeler. -->
                    <div class="kart-dugmeleri">
                        <!-- Giriş yap. -->
                        <a class="btn btn-primary" href="giris.php">Giriş Yap</a>
                        <!-- Kayıt ol. -->
                        <a class="btn btn-secondary" href="musteri-kayit.php">Kayıt Ol</a>
                    </div>
                </div>
            <!-- Tamirci veya yönetici giriş yaptıysa: bu form onlar için değil. -->
            <?php } else if (!$musteri_mi) { ?>
                <!-- Bilgi kutusu. -->
                <div class="giris-gerekli">
                    <!-- Yazı. -->
                    <p>Bu formu müşteriler doldurur. Siz <?php echo temiz_yaz($_SESSION["rol"]); ?> hesabıyla girdiniz.</p>
                </div>
            <!-- Müşteriyse form. -->
            <?php } else { ?>
                <!-- enctype: dosya gönderen formda zorunlu, yoksa fotoğraf gitmez. -->
                <form method="post" action="talep-olustur.php" enctype="multipart/form-data">
                    <!-- Ara başlık. -->
                    <div class="form-section-title">Cihaz Türü ve Kategorisi</div>
                    <!-- Kategori ve cihaz kutuları (ortak parça). -->
                    <?php include "ortak/cihaz-secimi.php"; ?>
                    <!-- Hizmet seçimi. -->
                    <div class="form-group">
                        <!-- Kutunun adı. -->
                        <label for="cmbHizmet">Hizmet (zorunlu değil)</label>
                        <!-- Hizmet kutusu. -->
                        <select id="cmbHizmet" name="hizmet_id">
                            <!-- Seçmeme seçeneği. -->
                            <option value="0">Seçmek istemiyorum</option>
                            <!-- Her hizmet bir seçenek. -->
                            <?php foreach ($hizmetler as $satir) { ?>
                                <!-- Seçili hizmet "selected" gelir. -->
                                <option value="<?php echo $satir["id"]; ?>" <?php if ($secili_hizmet == $satir["id"]) { echo "selected"; } ?>><?php echo temiz_yaz($satir["ad"]); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <!-- Ara başlık. -->
                    <div class="form-section-title">Marka ve Model Bilgisi</div>
                    <!-- Marka ve model yan yana. -->
                    <div class="form-row">
                        <!-- Marka. -->
                        <div class="form-group">
                            <!-- Kutunun adı. -->
                            <label for="txtMarka">Marka</label>
                            <!-- Marka kutusu. -->
                            <input id="txtMarka" name="marka" type="text" placeholder="Örn: Apple, Bosch, Dyson" value="<?php echo temiz_yaz($marka); ?>" required>
                        </div>
                        <!-- Model. -->
                        <div class="form-group">
                            <!-- Kutunun adı. -->
                            <label for="txtModel">Model (Varsa)</label>
                            <!-- Model kutusu. -->
                            <input id="txtModel" name="model" type="text" placeholder="Örn: iPhone 13, V15" value="<?php echo temiz_yaz($model); ?>">
                        </div>
                    </div>
                    <!-- Ara başlık. -->
                    <div class="form-section-title">Arıza Detayı ve Görsel</div>
                    <!-- Arıza. -->
                    <div class="form-group">
                        <!-- Kutunun adı. -->
                        <label for="txtAriza">Arıza Açıklaması / Sorun Nedir?</label>
                        <!-- Arıza kutusu. -->
                        <textarea id="txtAriza" name="ariza" rows="4" required><?php echo temiz_yaz($ariza); ?></textarea>
                    </div>
                    <!-- Fotoğraf. -->
                    <div class="form-group">
                        <!-- Kutunun adı. -->
                        <label for="fileCihazResmi">Arıza Fotoğrafı (İsteğe Bağlı, en fazla 3 MB)</label>
                        <!-- Dosya seçme kutusu. Sadece resimler. -->
                        <input id="fileCihazResmi" name="cihaz_resmi" type="file" accept="image/*">
                    </div>
                    <!-- Ara başlık. -->
                    <div class="form-section-title">Tamir Konumu</div>
                    <!-- İl ve ilçe (ortak parça). -->
                    <?php include "ortak/konum-secimi.php"; ?>
                    <!-- Gönder düğmesi. -->
                    <button class="btn btn-primary btn-block" type="submit">Talebi Yayınla ve Teklif Topla</button>
                </form>
            <?php } ?>
        </div>
    </div>
</section>

<?php
// Sayfa altı.
include "ortak/altlik.php";
?>
