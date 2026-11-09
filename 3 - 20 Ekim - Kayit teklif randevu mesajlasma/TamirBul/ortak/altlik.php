<?php
/*
DOSYA: ortak/altlik.php
KİM: Kişi 1
NE YAPAR: Her sayfanın en altı. baslik.php'nin açtığı <main> burada kapanır.
Yazıların hepsi site_ayarlari tablosundan gelir.
*/

// Sayfa altındaki yazılar. İkinci değer veritabanı kapalıysa görünen yedek yazıdır.
$alt_site_adi = ayar_getir("site_adi", "TamirBul");
// Kısa tanıtım yazısı.
$alt_aciklama = ayar_getir("altlik_aciklama", "");
// Telefon.
$alt_telefon = ayar_getir("telefon", "");
// E-posta.
$alt_eposta = ayar_getir("eposta", "");
// Adres.
$alt_adres = ayar_getir("adres", "");
// Çalışma saatleri.
$alt_saat = ayar_getir("calisma_saatleri", "");
// En alttaki küçük not.
$alt_not = ayar_getir("altlik_not", "");
?>
    </main>

    <!-- Sayfanın en altındaki koyu şerit. -->
    <footer class="footer">
        <!-- Üç sütun: tanıtım, sayfalar, iletişim. -->
        <div class="container footer-container">
            <!-- 1. sütun: site adı ve tanıtım. -->
            <div>
                <!-- Site adı. -->
                <strong><?php echo temiz_yaz($alt_site_adi); ?></strong>
                <!-- Tanıtım yazısı. -->
                <p><?php echo temiz_yaz($alt_aciklama); ?></p>
            </div>
            <!-- 2. sütun: sayfa linkleri. -->
            <div>
                <!-- Sütun başlığı. -->
                <strong>Sayfalar</strong>
                <!-- Hakkımızda linki. -->
                <p><a href="<?php echo $kok; ?>hakkimizda.php">Hakkımızda</a></p>
                <!-- Hizmetler linki. -->
                <p><a href="<?php echo $kok; ?>hizmetler.php">Hizmetler</a></p>
                <!-- Tamirciler linki. -->
                <p><a href="<?php echo $kok; ?>tamirciler.php">Tamirciler</a></p>
                <!-- Talep takip linki. -->
                <p><a href="<?php echo $kok; ?>talep-takip.php">Talep takip</a></p>
                <!-- Tamirci kayıt linki. -->
                <p><a href="<?php echo $kok; ?>tamirci-kayit.php">Tamirci olarak katıl</a></p>
            </div>
            <!-- 3. sütun: iletişim bilgileri. -->
            <div>
                <!-- Sütun başlığı. -->
                <strong>İletişim</strong>
                <!-- Telefon. -->
                <p><?php echo temiz_yaz($alt_telefon); ?></p>
                <!-- E-posta. -->
                <p><?php echo temiz_yaz($alt_eposta); ?></p>
                <!-- Adres. -->
                <p><?php echo temiz_yaz($alt_adres); ?></p>
                <!-- Çalışma saati. -->
                <p><?php echo temiz_yaz($alt_saat); ?></p>
            </div>
        </div>
        <!-- En alttaki küçük not. -->
        <div class="container alt-not">
            <!-- Not yazısı. -->
            <p><?php echo temiz_yaz($alt_not); ?></p>
        </div>
    </footer>

    <!-- Menü, bağlı listeler, slayt ve sohbet için tek javascript dosyası. -->
    <script src="<?php echo $kok; ?>js/site.js"></script>
</body>
</html>
