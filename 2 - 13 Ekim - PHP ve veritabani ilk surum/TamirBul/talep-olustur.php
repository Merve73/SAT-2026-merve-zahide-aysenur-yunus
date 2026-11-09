<?php
/*
SAYFA: Talep olustur
DOSYA: talep-olustur.php
KIM: Kisi 3

BU SAYFADA BUNLAR OLACAK:
- Sadece giris yapmis musteri formu gorur
- Cihaz turu, hizmet (istege bagli), marka, model, ariza
- Kayit talepler tablosuna yazilir
- Ekranda takip kodu gosterilir. Ornek: TB1003
*/

$kok = "";
$sayfa_basligi = "Talep oluştur";

include "ortak/baglanti.php";
include "ortak/guvenlik.php";

$hata = "";
$tamam = "";
$takip_kodu = "";

// Hizmet detayindan gelindiyse kutuda o hizmet secili baslasin.
$secili_hizmet = 0;
if (isset($_GET["hizmet_id"])) {
    $secili_hizmet = (int) $_GET["hizmet_id"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!giris_yapildi_mi() || $_SESSION["rol"] != "musteri") {
        $hata = "Talep açmak için müşteri hesabıyla giriş yapın.";
    } else if (!$veritabani_hazir) {
        $hata = "Veritabanı kapalı.";
    } else {
        $cihaz_turu = "";
        $marka = "";
        $model = "";
        $ariza = "";
        $hizmet_id = 0;

        if (isset($_POST["cihaz_turu"])) {
            $cihaz_turu = $_POST["cihaz_turu"];
        }
        if (isset($_POST["marka"])) {
            $marka = trim($_POST["marka"]);
        }
        if (isset($_POST["model"])) {
            $model = trim($_POST["model"]);
        }
        if (isset($_POST["ariza"])) {
            $ariza = trim($_POST["ariza"]);
        }
        if (isset($_POST["hizmet_id"])) {
            $hizmet_id = (int) $_POST["hizmet_id"];
        }

        if ($cihaz_turu != "bilgisayar" && $cihaz_turu != "telefon" && $cihaz_turu != "tablet") {
            $hata = "Cihaz türü seçin.";
        } else if ($marka == "" || $model == "" || $ariza == "") {
            $hata = "Marka, model ve arıza boş bırakılamaz.";
        } else {
            // Hizmet secilmediyse SQL icinde NULL yazacagiz. Secildiyse sayiyi yazacagiz.
            $hizmet_sql = "NULL";
            if ($hizmet_id > 0) {
                $hizmet_sql = (string) $hizmet_id;
            }

            // Id'yi formdan almiyoruz. Oturumdaki musterinin kendi id'si.
            $musteri_id = (int) $_SESSION["kullanici_id"];
            $marka_temiz = mysqli_real_escape_string($baglanti, $marka);
            $model_temiz = mysqli_real_escape_string($baglanti, $model);
            $ariza_temiz = mysqli_real_escape_string($baglanti, $ariza);
            $simdi = date("Y-m-d H:i:s");

            // Once gecici kodla ekleriz. Asil kodu id belli olunca yazariz.
            $sorgu = "INSERT INTO talepler (musteri_id, hizmet_id, tamirci_id, cihaz_turu, marka, model, ariza, durum, takip_kodu, olusturma_tarihi) VALUES ($musteri_id, $hizmet_sql, NULL, '$cihaz_turu', '$marka_temiz', '$model_temiz', '$ariza_temiz', 'beklemede', '', '$simdi')";
            $calisti = mysqli_query($baglanti, $sorgu);

            if ($calisti) {
                $yeni_id = mysqli_insert_id($baglanti);
                // 1000 ekliyoruz ki kod cok kisa kalmasin. id 3 ise kod TB1003 olur.
                $takip_kodu = "TB" . (1000 + $yeni_id);
                $kod_temiz = mysqli_real_escape_string($baglanti, $takip_kodu);
                mysqli_query($baglanti, "UPDATE talepler SET takip_kodu = '$kod_temiz' WHERE id = $yeni_id");
                $tamam = "Talebiniz alındı. Takip kodunuz: " . $takip_kodu;
            } else {
                $hata = "Kayıt olmadı: " . mysqli_error($baglanti);
            }
        }
    }
}

// Formdaki hizmet listesi.
$hizmetler = false;
if ($veritabani_hazir) {
    $hizmetler = mysqli_query($baglanti, "SELECT id, ad, cihaz_turu FROM hizmetler WHERE aktif = 1 ORDER BY ad");
}

include "ortak/baslik.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <h1>Tamir talebi oluştur</h1>
        <p class="giris-metni">Arızayı yazın. Onaylı tamirci bu talebi panelinde görür ve randevu kurar.</p>

        <?php if ($hata != "") { ?>
            <p class="hata"><?php echo temiz_yaz($hata); ?></p>
        <?php } ?>
        <?php if ($tamam != "") { ?>
            <p class="tamam"><?php echo temiz_yaz($tamam); ?></p>
            <p><a href="talep-takip.php">Takip sayfasına git</a></p>
        <?php } ?>

        <?php if (!giris_yapildi_mi()) { ?>
            <p>Devam etmek için <a href="giris.php">giriş yapın</a> veya <a href="kayit.php">kayıt olun</a>.</p>
        <?php } else if ($_SESSION["rol"] != "musteri") { ?>
            <p>Bu formu müşteri hesabı doldurur. Tamirci iseniz gelen talepler sayfasına bakın.</p>
        <?php } else if ($tamam == "") { ?>
            <form class="form-kutu" method="post" action="talep-olustur.php">
                <label for="cihaz_turu">Cihaz türü</label>
                <select id="cihaz_turu" name="cihaz_turu">
                    <option value="">Seçin</option>
                    <option value="bilgisayar">Bilgisayar</option>
                    <option value="telefon">Telefon</option>
                    <option value="tablet">Tablet</option>
                </select>

                <label for="hizmet_id">Hizmet (zorunlu değil)</label>
                <select id="hizmet_id" name="hizmet_id">
                    <option value="0">Seçmek istemiyorum</option>
                    <?php if ($hizmetler) { ?>
                        <?php while ($satir = mysqli_fetch_assoc($hizmetler)) { ?>
                            <option value="<?php echo (int) $satir["id"]; ?>" <?php if ($secili_hizmet == (int) $satir["id"]) { echo "selected"; } ?>>
                                <?php echo temiz_yaz(cihaz_adi($satir["cihaz_turu"]) . " - " . $satir["ad"]); ?>
                            </option>
                        <?php } ?>
                    <?php } ?>
                </select>

                <label for="marka">Marka</label>
                <input id="marka" name="marka" type="text">

                <label for="model">Model</label>
                <input id="model" name="model" type="text">

                <label for="ariza">Arıza</label>
                <textarea id="ariza" name="ariza"></textarea>

                <p><button class="buton" type="submit">Talebi kaydet</button></p>
            </form>
        <?php } ?>
    </div>
</section>

<?php include "ortak/altlik.php"; ?>
