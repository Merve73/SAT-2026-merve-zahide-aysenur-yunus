<?php
/*
SAYFA: Musteri ozeti
DOSYA: musteri/panel.php
KIM: Kisi 2

BU SAYFADA BUNLAR OLACAK:
- Hos geldin yazisi
- Toplam talep sayisi
- Son taleplerin kisa listesi
- Yeni talep ve taleplerim linkleri
*/

$kok = "../";
$sayfa_basligi = "Hesabım";

include "../ortak/baglanti.php";
include "../ortak/guvenlik.php";
sadece_musteri();

$musteri_id = (int) $_SESSION["kullanici_id"];
$adet = 0;
$talepler = false;

if ($veritabani_hazir) {
    $sayim = mysqli_query($baglanti, "SELECT COUNT(*) AS adet FROM talepler WHERE musteri_id = $musteri_id");
    if ($sayim) {
        $satir = mysqli_fetch_assoc($sayim);
        $adet = $satir["adet"];
    }
    $talepler = mysqli_query($baglanti, "SELECT * FROM talepler WHERE musteri_id = $musteri_id ORDER BY id DESC LIMIT 5");
}

include "../ortak/baslik.php";
include "menu.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <h1>Merhaba <?php echo temiz_yaz($_SESSION["ad"]); ?></h1>
        <p class="giris-metni">Burada kendi tamir taleplerinin özetini görürsün.</p>

        <div class="ozet-alani">
            <article class="ozet">
                <span>Taleplerim</span>
                <strong><?php echo (int) $adet; ?></strong>
            </article>
        </div>

        <p>
            <a class="buton" href="../talep-olustur.php">Yeni talep</a>
            <a class="buton-ikincil" href="taleplerim.php">Tüm talepler</a>
        </p>

        <h2>Son talepler</h2>
        <div class="tablo-kutu">
            <table>
                <tr>
                    <th>Kod</th>
                    <th>Cihaz</th>
                    <th>Durum</th>
                </tr>
                <?php if ($talepler && mysqli_num_rows($talepler) > 0) { ?>
                    <?php while ($satir = mysqli_fetch_assoc($talepler)) { ?>
                        <tr>
                            <td><?php echo temiz_yaz($satir["takip_kodu"]); ?></td>
                            <td><?php echo temiz_yaz($satir["marka"] . " " . $satir["model"]); ?></td>
                            <td><?php echo temiz_yaz(durum_adi($satir["durum"])); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="3">Henüz talep yok.</td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</section>

<?php include "../ortak/altlik.php"; ?>
