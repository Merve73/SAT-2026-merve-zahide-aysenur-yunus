<?php
/*
SAYFA: Giris
DOSYA: giris.php
KIM: Kisi 3

BU SAYFADA BUNLAR OLACAK:
- E-posta kutusu
- Sifre kutusu
- Giris dugmesi
- Kayit sayfasina link
- Bilgiler dogruysa role gore panele gider: admin, tamirci, musteri
*/

$kok = "";
$sayfa_basligi = "Giriş";

include "ortak/baglanti.php";
include "ortak/guvenlik.php";

$hata = "";
$tamam = "";

// Kayit sayfasi basarili olunca buraya ?kayit=tamam ile doner.
if (isset($_GET["kayit"]) && $_GET["kayit"] == "tamam") {
    $tamam = "Kayıt tamam. Giriş yapabilirsiniz. Tamirci hesabı, yönetici onayından sonra talep alabilir.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eposta = "";
    $sifre = "";

    if (isset($_POST["eposta"])) {
        $eposta = trim($_POST["eposta"]);
    }
    if (isset($_POST["sifre"])) {
        $sifre = $_POST["sifre"];
    }

    if (!$veritabani_hazir) {
        $hata = "Veritabanına bağlanılamadı. ortak/baglanti.php dosyasındaki bilgileri kontrol edin.";
    } else if ($eposta == "" || $sifre == "") {
        $hata = "E-posta ve şifre boş bırakılamaz.";
    } else {
        $eposta_temiz = mysqli_real_escape_string($baglanti, $eposta);
        $sorgu = "SELECT * FROM kullanicilar WHERE eposta = '$eposta_temiz' LIMIT 1";
        $sonuc = mysqli_query($baglanti, $sorgu);

        if ($sonuc && mysqli_num_rows($sonuc) == 1) {
            $kullanici = mysqli_fetch_assoc($sonuc);

            // password_verify: yazilan sifre ile veritabanindaki hash ayni mi diye bakar.
            // Veritabaninda duz sifre tutmuyoruz.
            if (password_verify($sifre, $kullanici["sifre"])) {
                // Giris basarili. Bundan sonraki sayfalar bu dort bilgiyi okur.
                $_SESSION["kullanici_id"] = $kullanici["id"];
                $_SESSION["ad"] = $kullanici["ad"];
                $_SESSION["soyad"] = $kullanici["soyad"];
                $_SESSION["rol"] = $kullanici["rol"];

                if ($kullanici["rol"] == "admin") {
                    header("Location: admin/panel.php");
                    exit;
                }
                if ($kullanici["rol"] == "tamirci") {
                    header("Location: tamirci/panel.php");
                    exit;
                }

                header("Location: musteri/panel.php");
                exit;
            } else {
                $hata = "Şifre yanlış.";
            }
        } else {
            $hata = "Bu e-posta ile kayıtlı kullanıcı yok.";
        }
    }
}

include "ortak/baslik.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <h1>Giriş</h1>
        <p class="giris-metni">Müşteri, tamirci ve yönetici aynı formdan girer. Gideceği panel role göre değişir.</p>

        <?php if ($hata != "") { ?>
            <p class="hata"><?php echo temiz_yaz($hata); ?></p>
        <?php } ?>
        <?php if ($tamam != "") { ?>
            <p class="tamam"><?php echo temiz_yaz($tamam); ?></p>
        <?php } ?>

        <?php if (giris_yapildi_mi()) { ?>
            <p class="tamam">Zaten giriş yaptınız: <?php echo temiz_yaz($_SESSION["ad"]); ?></p>
        <?php } else { ?>
            <form class="form-kutu" method="post" action="giris.php">
                <label for="eposta">E-posta</label>
                <input id="eposta" name="eposta" type="text">

                <label for="sifre">Şifre</label>
                <input id="sifre" name="sifre" type="password">

                <p><button class="buton" type="submit">Giriş yap</button></p>
                <p>Hesabınız yoksa <a href="kayit.php">kayıt olun</a>.</p>
            </form>
        <?php } ?>
    </div>
</section>

<?php include "ortak/altlik.php"; ?>
