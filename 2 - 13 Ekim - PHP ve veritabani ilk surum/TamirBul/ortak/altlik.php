<?php
// Her sayfanin en alti. baslik.php icinde acilan <main> burada kapanir.
// Telefon, e-posta ve adres site_ayarlari tablosundan okunur.

// Ayar fonksiyonu yoksa yedek metinler kalsin diye once varsayilanlari yazariz.
$alt_telefon = "0500 000 00 00";
$alt_eposta = "info@tamirbul.test";
$alt_adres = "Okul projesi";
$alt_saat = "Hafta içi 09:00 - 18:00";
$alt_site_adi = "TamirBul";

// Baglanti aciksa yedeklerin yerine veritabanindaki metinleri koyariz.
if (isset($baglanti)) {
    $alt_telefon = ayar_getir($baglanti, "telefon", $alt_telefon);
    $alt_eposta = ayar_getir($baglanti, "eposta", $alt_eposta);
    $alt_adres = ayar_getir($baglanti, "adres", $alt_adres);
    $alt_saat = ayar_getir($baglanti, "calisma_saatleri", $alt_saat);
    $alt_site_adi = ayar_getir($baglanti, "site_adi", $alt_site_adi);
}

// Alt klasorden gelindiyse $kok zaten sayfada tanimli. Degilse bos birakalim.
if (!isset($kok)) {
    $kok = "";
}
?>
    </main>

    <!-- footer: her sayfanin en altindaki bilgi seridi. -->
    <footer class="alt">
        <div class="kapsayici alt-ic">
            <div>
                <strong><?php echo temiz_yaz($alt_site_adi); ?></strong>
                <!-- Kisa aciklama sabit. Site adi yukaridan geldigi icin marka degisir. -->
                <p>Bilgisayar, telefon ve tablet tamiri için müşteri ile tamirciyi buluşturur.</p>
            </div>
            <div>
                <strong>Sayfalar</strong>
                <p><a href="<?php echo $kok; ?>hakkimizda.php">Hakkımızda</a></p>
                <p><a href="<?php echo $kok; ?>hizmetler.php">Hizmetler</a></p>
                <p><a href="<?php echo $kok; ?>tamirciler.php">Tamirciler</a></p>
                <p><a href="<?php echo $kok; ?>talep-takip.php">Talep takip</a></p>
            </div>
            <div>
                <strong>İletişim</strong>
                <p><?php echo temiz_yaz($alt_telefon); ?></p>
                <p><?php echo temiz_yaz($alt_eposta); ?></p>
                <p><?php echo temiz_yaz($alt_adres); ?></p>
                <p><?php echo temiz_yaz($alt_saat); ?></p>
            </div>
        </div>
        <div class="kapsayici alt-not">
            <p>Bilgisayar programcılığı dönem projesi. İçerikler yönetim panelinden değiştirilir.</p>
        </div>
    </footer>

    <!-- Menu dugmesinin calismasi icin tek javascript dosyasi. -->
    <script src="<?php echo $kok; ?>js/site.js"></script>
</body>
</html>
