<?php
/*
SAYFA: Yonetim ozeti
DOSYA: admin/panel.php
KIM: Kisi 4

BU SAYFADA BUNLAR OLACAK:
- Kullanici, talep, hizmet ve okunmamis mesaj sayilari
- Son 5 talep
- Son 5 mesaj
*/

$kok = "../";
$sayfa_basligi = "Yönetim";

include "../ortak/baglanti.php";
include "../ortak/guvenlik.php";
sadece_admin();

$kullanici_sayisi = 0;
$talep_sayisi = 0;
$hizmet_sayisi = 0;
$okunmamis = 0;
$talepler = false;
$mesajlar = false;

if ($veritabani_hazir) {
    $s1 = mysqli_query($baglanti, "SELECT COUNT(*) AS adet FROM kullanicilar");
    if ($s1) {
        $satir1 = mysqli_fetch_assoc($s1);
        $kullanici_sayisi = $satir1["adet"];
    }
    $s2 = mysqli_query($baglanti, "SELECT COUNT(*) AS adet FROM talepler");
    if ($s2) {
        $satir2 = mysqli_fetch_assoc($s2);
        $talep_sayisi = $satir2["adet"];
    }
    $s3 = mysqli_query($baglanti, "SELECT COUNT(*) AS adet FROM hizmetler");
    if ($s3) {
        $satir3 = mysqli_fetch_assoc($s3);
        $hizmet_sayisi = $satir3["adet"];
    }
    $s4 = mysqli_query($baglanti, "SELECT COUNT(*) AS adet FROM mesajlar WHERE okundu = 0");
    if ($s4) {
        $satir4 = mysqli_fetch_assoc($s4);
        $okunmamis = $satir4["adet"];
    }

    $talepler = mysqli_query($baglanti, "SELECT talepler.*, kullanicilar.ad, kullanicilar.soyad
        FROM talepler
        INNER JOIN kullanicilar ON kullanicilar.id = talepler.musteri_id
        ORDER BY talepler.id DESC LIMIT 5");
    $mesajlar = mysqli_query($baglanti, "SELECT * FROM mesajlar ORDER BY id DESC LIMIT 5");
}

include "../ortak/baslik.php";
include "menu.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <h1>Yönetim özeti</h1>
        <p class="giris-metni">Sayılar veritabanındaki kayıtlardan hesaplanır.</p>

        <div class="ozet-alani">
            <article class="ozet">
                <span>Kullanıcı</span>
                <strong><?php echo (int) $kullanici_sayisi; ?></strong>
            </article>
            <article class="ozet">
                <span>Talep</span>
                <strong><?php echo (int) $talep_sayisi; ?></strong>
            </article>
            <article class="ozet">
                <span>Hizmet</span>
                <strong><?php echo (int) $hizmet_sayisi; ?></strong>
            </article>
            <article class="ozet">
                <span>Okunmamış mesaj</span>
                <strong><?php echo (int) $okunmamis; ?></strong>
            </article>
        </div>

        <h2>Son talepler</h2>
        <div class="tablo-kutu">
            <table>
                <tr>
                    <th>Kod</th>
                    <th>Müşteri</th>
                    <th>Durum</th>
                </tr>
                <?php if ($talepler && mysqli_num_rows($talepler) > 0) { ?>
                    <?php while ($satir = mysqli_fetch_assoc($talepler)) { ?>
                        <tr>
                            <td><?php echo temiz_yaz($satir["takip_kodu"]); ?></td>
                            <td><?php echo temiz_yaz($satir["ad"] . " " . $satir["soyad"]); ?></td>
                            <td><?php echo temiz_yaz(durum_adi($satir["durum"])); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="3">Talep yok.</td></tr>
                <?php } ?>
            </table>
        </div>

        <h2>Son mesajlar</h2>
        <div class="tablo-kutu">
            <table>
                <tr>
                    <th>Kim</th>
                    <th>Konu</th>
                </tr>
                <?php if ($mesajlar && mysqli_num_rows($mesajlar) > 0) { ?>
                    <?php while ($satir = mysqli_fetch_assoc($mesajlar)) { ?>
                        <tr>
                            <td><?php echo temiz_yaz($satir["ad"]); ?></td>
                            <td><?php echo temiz_yaz($satir["konu"]); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="2">Mesaj yok.</td></tr>
                <?php } ?>
            </table>
        </div>
    </div>
</section>

<?php include "../ortak/altlik.php"; ?>
