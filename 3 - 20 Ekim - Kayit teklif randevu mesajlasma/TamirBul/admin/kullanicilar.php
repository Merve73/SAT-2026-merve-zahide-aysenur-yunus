<?php
/*
SAYFA: Kullanıcılar
DOSYA: admin/kullanicilar.php
KİM: Kişi 4

BU SAYFADA BUNLAR OLACAK:
- Bütün üyeler: ad, işletme, e-posta, rol, il/ilçe, yetkinlik, onay durumu
- Onay bekleyen tamircide Onayla ve Reddet, reddedilende Onayla düğmesi (kullanici-onay.php)

SIRADAKİ İŞ (Kişi 4):
- Kullanıcı silme. Yönetici kendini silmesin, talebi olan kullanıcı silinmesin.
*/

// Sekme başlığı.
$sayfa_basligi = "Kullanıcılar";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece yönetici.
sadece_rol("admin");

// Bütün kullanicilar, yeniden eskiye.
$kisiler = satirlar("SELECT * FROM kullanicilar ORDER BY id DESC");

// Üst menü.
include "../ortak/baslik.php";
// Yönetim menüsü.
include "menu.php";
?>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Başlık. -->
        <h1>Kullanıcılar</h1>
        <!-- Açıklama. -->
        <p class="giris-metni">Tamirci onaylanınca tamirciler listesinde görünür ve teklif verebilir.</p>
        <!-- Tablo kutusu. -->
        <div class="tablo-kutu">
            <!-- Tablo. -->
            <table>
                <!-- Başlık satırı. -->
                <tr><th>Ad</th><th>E-posta</th><th>Rol</th><th>İl / İlçe</th><th>Yetkinlik</th><th>Onay</th></tr>
                <!-- Her kullanıcı bir satır. -->
                <?php foreach ($kisiler as $kisi) { ?>
                    <!-- Satır. -->
                    <tr>
                        <!-- Ad ve işletme. -->
                        <td><?php echo temiz_yaz($kisi["ad"] . " " . $kisi["soyad"]); ?><br><small><?php echo temiz_yaz($kisi["isletme_adi"]); ?></small></td>
                        <!-- E-posta. -->
                        <td><?php echo temiz_yaz($kisi["eposta"]); ?></td>
                        <!-- Rol. -->
                        <td><?php echo temiz_yaz($kisi["rol"]); ?></td>
                        <!-- Konum. -->
                        <td><?php echo temiz_yaz($kisi["sehir"] . " / " . $kisi["ilce"]); ?></td>
                        <!-- Tamirciyse yetkinlikler. -->
                        <td><?php if ($kisi["rol"] == "tamirci") { echo temiz_yaz(yetkinlik_metni($kisi["id"])); } ?></td>
                        <!-- Onay durumu ve düğmeler. -->
                        <td>
                            <!-- Onaylı kullanıcı. -->
                            <?php if ($kisi["onayli"] == 1) { ?>
                                <!-- Yeşil etiket. -->
                                <span class="badge badge-success">Onaylı</span>
                            <!-- Onaysız tamirci. -->
                            <?php } else { ?>
                                <!-- Reddedildiyse kırmızı etiket. -->
                                <?php if ($kisi["onayli"] == 2) { ?>
                                    <!-- Etiket. -->
                                    <span class="badge badge-danger">Reddedildi</span>
                                <?php } ?>
                                <!-- Onayla formu. -->
                                <form method="post" action="kullanici-onay.php">
                                    <!-- Numara. -->
                                    <input type="hidden" name="id" value="<?php echo (int) $kisi["id"]; ?>">
                                    <!-- İş. -->
                                    <input type="hidden" name="islem" value="onayla">
                                    <!-- Düğme. -->
                                    <button class="btn btn-sm btn-secondary" type="submit">Onayla</button>
                                </form>
                                <!-- Sadece onay bekleyende Reddet. -->
                                <?php if ($kisi["onayli"] == 0) { ?>
                                    <!-- Reddet formu. -->
                                    <form method="post" action="kullanici-onay.php" onsubmit="return confirm('Başvuru reddedilsin mi?');">
                                        <!-- Numara. -->
                                        <input type="hidden" name="id" value="<?php echo (int) $kisi["id"]; ?>">
                                        <!-- İş. -->
                                        <input type="hidden" name="islem" value="reddet">
                                        <!-- Düğme. -->
                                        <button class="btn btn-sm btn-danger" type="submit">Reddet</button>
                                    </form>
                                <?php } ?>
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
