<?php
/*
SAYFA: Taleplerim
DOSYA: musteri/taleplerim.php
KIM: Kisi 2

BU SAYFADA BUNLAR OLACAK:
- Sadece bu musterinin talepleri
- Kod, cihaz, ariza, durum, tarih
- Baska musterinin talebi bu sorguda gelmez cunku musteri_id oturumdan okunur

SIRADAKI IS (Kisi 2 ekran, Kisi 3 kayit):
- Beklemedeki talebe "Iptal et" butonu ekleyin.
- Buton ayri bir dosyaya POST etsin. O dosya su sorguyu calistirsin:
  UPDATE talepler SET durum = 'iptal' WHERE id = ... AND musteri_id = ... AND durum = 'beklemede'
- id formdan, musteri_id oturumdan gelsin. Boylece baskasinin talebi iptal olmaz.
*/

$kok = "../";
$sayfa_basligi = "Taleplerim";

include "../ortak/baglanti.php";
include "../ortak/guvenlik.php";
sadece_musteri();

$musteri_id = (int) $_SESSION["kullanici_id"];
$talepler = false;

if ($veritabani_hazir) {
    $talepler = mysqli_query($baglanti, "SELECT * FROM talepler WHERE musteri_id = $musteri_id ORDER BY id DESC");
}

include "../ortak/baslik.php";
include "menu.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <h1>Taleplerim</h1>
        <p class="giris-metni">Açtığınız tamir kayıtları. Takip kodunu tamirciye söylemeniz gerekmez, sorgulama sayfası herkese açıktır.</p>

        <div class="tablo-kutu">
            <table>
                <tr>
                    <th>Kod</th>
                    <th>Cihaz</th>
                    <th>Arıza</th>
                    <th>Durum</th>
                    <th>Tarih</th>
                </tr>
                <?php if ($talepler && mysqli_num_rows($talepler) > 0) { ?>
                    <?php while ($satir = mysqli_fetch_assoc($talepler)) { ?>
                        <tr>
                            <td><?php echo temiz_yaz($satir["takip_kodu"]); ?></td>
                            <td><?php echo temiz_yaz(cihaz_adi($satir["cihaz_turu"]) . " / " . $satir["marka"] . " " . $satir["model"]); ?></td>
                            <td><?php echo temiz_yaz($satir["ariza"]); ?></td>
                            <td><?php echo temiz_yaz(durum_adi($satir["durum"])); ?></td>
                            <td><?php echo temiz_yaz($satir["olusturma_tarihi"]); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5">Henüz talep yok. <a href="../talep-olustur.php">Yeni talep açın</a>.</td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</section>

<?php include "../ortak/altlik.php"; ?>
