<?php
/*
SAYFA: Hizmet yonetimi
DOSYA: admin/hizmetler.php
KIM: Kisi 4

BU SAYFADA BUNLAR OLACAK:
- Tum hizmetler, gizli olanlar dahil
- Yeni hizmet linki
- Duzenle linki
- Sil butonu (hizmet-sil.php)
*/

$kok = "../";
$sayfa_basligi = "Hizmetler";

include "../ortak/baglanti.php";
include "../ortak/guvenlik.php";
sadece_admin();

$hizmetler = false;
if ($veritabani_hazir) {
    $hizmetler = mysqli_query($baglanti, "SELECT * FROM hizmetler ORDER BY id DESC");
}

include "../ortak/baslik.php";
include "menu.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <h1>Hizmetler</h1>
        <p class="giris-metni">Buradan eklenen hizmet, sitedeki hizmetler sayfasında görünür. Aktif kutusu kapalıysa listede gizlenir.</p>
        <p><a class="buton" href="hizmet-ekle.php">Yeni hizmet</a></p>

        <div class="tablo-kutu">
            <table>
                <tr>
                    <th>Ad</th>
                    <th>Tür</th>
                    <th>Fiyat</th>
                    <th>Aktif</th>
                    <th></th>
                </tr>
                <?php if ($hizmetler) { ?>
                    <?php while ($satir = mysqli_fetch_assoc($hizmetler)) { ?>
                        <tr>
                            <td><?php echo temiz_yaz($satir["ad"]); ?></td>
                            <td><?php echo temiz_yaz(cihaz_adi($satir["cihaz_turu"])); ?></td>
                            <td><?php echo fiyat_yaz($satir["baslangic_fiyat"]); ?></td>
                            <td><?php if ((int) $satir["aktif"] == 1) { echo "Evet"; } else { echo "Hayır"; } ?></td>
                            <td>
                                <a href="hizmet-duzenle.php?id=<?php echo (int) $satir["id"]; ?>">Düzenle</a>
                                <form method="post" action="hizmet-sil.php" onsubmit="return confirm('Bu hizmet silinsin mi?');">
                                    <input type="hidden" name="id" value="<?php echo (int) $satir["id"]; ?>">
                                    <button class="buton buton-kucuk tehlike" type="submit">Sil</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </table>
        </div>
    </div>
</section>

<?php include "../ortak/altlik.php"; ?>
