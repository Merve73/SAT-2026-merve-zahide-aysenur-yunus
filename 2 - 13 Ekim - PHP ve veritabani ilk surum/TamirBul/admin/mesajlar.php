<?php
/*
SAYFA: Mesajlar
DOSYA: admin/mesajlar.php
KIM: Kisi 4

BU SAYFADA BUNLAR OLACAK:
- Iletisim formundan gelen mesajlar
- Okundu isaretleme butonu
*/

$kok = "../";
$sayfa_basligi = "Mesajlar";

include "../ortak/baglanti.php";
include "../ortak/guvenlik.php";
sadece_admin();

$mesajlar = false;
if ($veritabani_hazir) {
    $mesajlar = mysqli_query($baglanti, "SELECT * FROM mesajlar ORDER BY id DESC");
}

include "../ortak/baslik.php";
include "menu.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <h1>Mesajlar</h1>
        <p class="giris-metni">İletişim sayfasından gelen yazılar. Ziyaretçi bu listeyi göremez.</p>

        <div class="tablo-kutu">
            <table>
                <tr>
                    <th>Kim</th>
                    <th>Konu</th>
                    <th>Mesaj</th>
                    <th>Tarih</th>
                    <th></th>
                </tr>
                <?php if ($mesajlar && mysqli_num_rows($mesajlar) > 0) { ?>
                    <?php while ($satir = mysqli_fetch_assoc($mesajlar)) { ?>
                        <tr>
                            <td>
                                <?php echo temiz_yaz($satir["ad"]); ?><br>
                                <?php echo temiz_yaz($satir["eposta"]); ?>
                            </td>
                            <td><?php echo temiz_yaz($satir["konu"]); ?></td>
                            <td><?php echo temiz_yaz($satir["mesaj"]); ?></td>
                            <td><?php echo temiz_yaz($satir["tarih"]); ?></td>
                            <td>
                                <?php if ((int) $satir["okundu"] == 0) { ?>
                                    <form method="post" action="mesaj-okundu.php">
                                        <input type="hidden" name="id" value="<?php echo (int) $satir["id"]; ?>">
                                        <button class="buton buton-kucuk" type="submit">Okundu</button>
                                    </form>
                                <?php } else { ?>
                                    Okundu
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="5">Mesaj yok.</td></tr>
                <?php } ?>
            </table>
        </div>
    </div>
</section>

<?php include "../ortak/altlik.php"; ?>
