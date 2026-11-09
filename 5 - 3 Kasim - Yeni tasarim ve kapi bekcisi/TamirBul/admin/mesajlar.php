<?php
/*
SAYFA: Mesajlar
DOSYA: admin/mesajlar.php
KİM: Kişi 4

BU SAYFADA BUNLAR OLACAK:
- İletişim formundan gelen mesajlar
- Okunmamış mesajda Okundu düğmesi (mesaj-okundu.php)
*/

// Sekme başlığı.
$sayfa_basligi = "Mesajlar";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece yönetici.
sadece_rol("admin");

// Bütün mesajlar, yeniden eskiye.
$mesajlar = satirlar("SELECT * FROM mesajlar ORDER BY id DESC");

// Üst menü.
include "../ortak/baslik.php";
?>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Başlık. -->
        <h1>Mesajlar</h1>
        <!-- Açıklama. -->
        <p class="giris-metni">İletişim sayfasından gelen yazılar. Ziyaretçi bu listeyi göremez.</p>
        <!-- Tablo kutusu. -->
        <div class="tablo-kutu">
            <!-- Tablo. -->
            <table>
                <!-- Başlık satırı. -->
                <tr><th>Kim</th><th>Konu</th><th>Mesaj</th><th>Tarih</th><th></th></tr>
                <!-- Her mesaj bir satır. -->
                <?php foreach ($mesajlar as $mesaj) { ?>
                    <!-- Satır. -->
                    <tr>
                        <!-- Ad ve e-posta. -->
                        <td><?php echo temiz_yaz($mesaj["ad"]); ?><br><?php echo temiz_yaz($mesaj["eposta"]); ?></td>
                        <!-- Konu. -->
                        <td><?php echo temiz_yaz($mesaj["konu"]); ?></td>
                        <!-- Mesaj. -->
                        <td><?php echo temiz_yaz($mesaj["mesaj"]); ?></td>
                        <!-- Tarih. -->
                        <td><?php echo tarih_yaz($mesaj["tarih"]); ?></td>
                        <!-- Okundu düğmesi veya yazısı. -->
                        <td>
                            <!-- Okunmadıysa düğme. -->
                            <?php if ($mesaj["okundu"] == 0) { ?>
                                <!-- Form mesaj-okundu.php dosyasına gider. -->
                                <form method="post" action="mesaj-okundu.php">
                                    <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
                                    <?php echo guvenlik_alani(); ?>
                                    <!-- Mesaj numarası. -->
                                    <input type="hidden" name="id" value="<?php echo (int) $mesaj["id"]; ?>">
                                    <!-- Düğme. -->
                                    <button class="btn btn-sm btn-primary" type="submit">Okundu</button>
                                </form>
                            <!-- Okunduysa yazı. -->
                            <?php } else { ?>
                                Okundu
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
