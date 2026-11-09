<?php
// Her sayfanin ust kismi: baslik cubugu ve menu.
// Sayfa bu dosyayi cagirmadan once $kok ve $sayfa_basligi doldurmalidir.
// $kok ana klasorde bos metindir. Alt klasorde "../" olur ki css yolu bozulmasin.
// DIKKAT: bu dosya <main> etiketini acar. Kapatan dosya ortak/altlik.php dosyasidir.

// Sayfa $kok vermeyi unutursa ana klasorde oldugunu varsayariz.
if (!isset($kok)) {
    $kok = "";
}

// Sayfa baslik vermeyi unutursa genel bir baslik kalsin.
if (!isset($sayfa_basligi)) {
    $sayfa_basligi = "TamirBul";
}

// Site adi veritabanindan gelir. Yonetici admin/ayarlar.php icinden degistirir.
$site_adi = "TamirBul";
if (isset($baglanti)) {
    $site_adi = ayar_getir($baglanti, "site_adi", "TamirBul");
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <!-- Turkce karakterler icin karakter seti. -->
    <meta charset="UTF-8">
    <!-- Telefonda sayfa ekran genisligine gore kuculsun. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tarayici sekmesindeki yazi. -->
    <title><?php echo temiz_yaz($sayfa_basligi . " | " . $site_adi); ?></title>
    <!-- Tek stil dosyasi. $kok alt klasorden cikip css'e ulasir. -->
    <link rel="stylesheet" href="<?php echo $kok; ?>css/stil.css">
</head>
<body>
    <!-- header: sayfanin en ust seridi. -->
    <header class="ust">
        <div class="ust-ic">
            <!-- Logoya basinca ana sayfaya doner. -->
            <a class="marka" href="<?php echo $kok; ?>index.php"><?php echo temiz_yaz($site_adi); ?></a>

            <!-- Telefonda menuyu acip kapatan dugme. js/site.js bunu kullanir. -->
            <button type="button" id="menu-dugmesi" class="menu-dugmesi">Menü</button>

            <!-- nav: sayfa linklerinin durdugu menu. -->
            <nav id="menu" class="menu">
                <a href="<?php echo $kok; ?>index.php">Ana sayfa</a>
                <a href="<?php echo $kok; ?>hizmetler.php">Hizmetler</a>
                <a href="<?php echo $kok; ?>tamirciler.php">Tamirciler</a>
                <a href="<?php echo $kok; ?>talep-olustur.php">Talep oluştur</a>
                <a href="<?php echo $kok; ?>talep-takip.php">Talep takip</a>
                <a href="<?php echo $kok; ?>iletisim.php">İletişim</a>

                <?php if (giris_yapildi_mi()) { ?>
                    <?php if ($_SESSION["rol"] == "admin") { ?>
                        <a href="<?php echo $kok; ?>admin/panel.php">Yönetim</a>
                    <?php } ?>
                    <?php if ($_SESSION["rol"] == "tamirci") { ?>
                        <a href="<?php echo $kok; ?>tamirci/panel.php">Tamirci paneli</a>
                    <?php } ?>
                    <?php if ($_SESSION["rol"] == "musteri") { ?>
                        <a href="<?php echo $kok; ?>musteri/panel.php">Hesabım</a>
                    <?php } ?>
                    <a href="<?php echo $kok; ?>cikis.php">Çıkış</a>
                <?php } else { ?>
                    <a href="<?php echo $kok; ?>giris.php">Giriş</a>
                    <a href="<?php echo $kok; ?>kayit.php">Kayıt ol</a>
                <?php } ?>
            </nav>
        </div>
    </header>

    <?php if (!isset($veritabani_hazir) || $veritabani_hazir == false) { ?>
        <!-- Veritabani yokken ogrenci neden bos gordugunu anlasin diye sari kutu. -->
        <div class="uyari">
            Veritabanına bağlanılamadı. XAMPP içinde MySQL'i başlatın ve veritabani/tamirbul.sql dosyasını phpMyAdmin'den içe aktarın.
            <?php if (isset($veritabani_hata) && $veritabani_hata != "") { ?>
                <br>Ayrıntı: <?php echo temiz_yaz($veritabani_hata); ?>
            <?php } ?>
        </div>
    <?php } ?>

    <!-- main: sayfanin asil icerigi burada baslar. -->
    <main>
