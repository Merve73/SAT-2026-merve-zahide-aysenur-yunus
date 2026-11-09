<?php
/*
SAYFA: Gelen talepler
DOSYA: tamirci/gelen-talepler.php
KIM: Kisi 2

BU SAYFADA BUNLAR OLACAK:
- Durumu beklemede olan talepler
- Musterinin adi, telefonu, sehri
- Cihaz ve ariza
- Randevu kur linki

INNER JOIN: talepler ile kullanicilar tablosunu musteri_id uzerinden yan yana getirir.
Eslesen satiri olmayan talep listede gorunmez.
*/

$kok = "../";
$sayfa_basligi = "Gelen talepler";

include "../ortak/baglanti.php";
include "../ortak/guvenlik.php";
sadece_tamirci();

$talepler = false;
if ($veritabani_hazir) {
    $sorgu = "SELECT talepler.*, kullanicilar.ad, kullanicilar.soyad, kullanicilar.telefon, kullanicilar.sehir
              FROM talepler
              INNER JOIN kullanicilar ON kullanicilar.id = talepler.musteri_id
              WHERE talepler.durum = 'beklemede'
              ORDER BY talepler.id DESC";
    $talepler = mysqli_query($baglanti, $sorgu);
}

include "../ortak/baslik.php";
include "menu.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <h1>Gelen talepler</h1>
        <p class="giris-metni">Henüz kimsenin almadığı kayıtlar. Randevu kurunca talep bu listeden düşer.</p>

        <div class="tablo-kutu">
            <table>
                <tr>
                    <th>Kod</th>
                    <th>Müşteri</th>
                    <th>Cihaz</th>
                    <th>Arıza</th>
                    <th></th>
                </tr>
                <?php if ($talepler && mysqli_num_rows($talepler) > 0) { ?>
                    <?php while ($satir = mysqli_fetch_assoc($talepler)) { ?>
                        <tr>
                            <td><?php echo temiz_yaz($satir["takip_kodu"]); ?></td>
                            <td>
                                <?php echo temiz_yaz($satir["ad"] . " " . $satir["soyad"]); ?><br>
                                <?php echo temiz_yaz($satir["telefon"]); ?><br>
                                <?php echo temiz_yaz($satir["sehir"]); ?>
                            </td>
                            <td><?php echo temiz_yaz(cihaz_adi($satir["cihaz_turu"]) . " / " . $satir["marka"] . " " . $satir["model"]); ?></td>
                            <td><?php echo temiz_yaz($satir["ariza"]); ?></td>
                            <td><a class="buton buton-kucuk" href="randevu-ekle.php?talep_id=<?php echo (int) $satir["id"]; ?>">Randevu kur</a></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5">Bekleyen talep yok.</td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</section>

<?php include "../ortak/altlik.php"; ?>
