<?php
/*
SAYFA: Musteri profili
DOSYA: musteri/profil.php
KIM: Kisi 2 gorunum, kayit kodu da bu dosyada.

BU SAYFADA BUNLAR OLACAK:
- Ad, soyad, telefon, sehir formu
- Sifre degistirme formu
- E-posta kutusu sadece okunur. E-posta giris adidir, buradan degistirmiyoruz.
- Guncellenecek id oturumdan gelir, formdan degil.
*/

$kok = "../";
$sayfa_basligi = "Profil";

include "../ortak/baglanti.php";
include "../ortak/guvenlik.php";
sadece_musteri();

$kullanici_id = (int) $_SESSION["kullanici_id"];
$hata = "";
$tamam = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && $veritabani_hazir) {
    $form_turu = "";
    if (isset($_POST["form_turu"])) {
        $form_turu = $_POST["form_turu"];
    }

    if ($form_turu == "bilgi") {
        $ad = "";
        $soyad = "";
        $telefon = "";
        $sehir = "";

        if (isset($_POST["ad"])) {
            $ad = trim($_POST["ad"]);
        }
        if (isset($_POST["soyad"])) {
            $soyad = trim($_POST["soyad"]);
        }
        if (isset($_POST["telefon"])) {
            $telefon = trim($_POST["telefon"]);
        }
        if (isset($_POST["sehir"])) {
            $sehir = trim($_POST["sehir"]);
        }

        if ($ad == "" || $soyad == "" || $telefon == "" || $sehir == "") {
            $hata = "Ad, soyad, telefon ve şehir boş bırakılamaz.";
        } else {
            $ad_temiz = mysqli_real_escape_string($baglanti, $ad);
            $soyad_temiz = mysqli_real_escape_string($baglanti, $soyad);
            $telefon_temiz = mysqli_real_escape_string($baglanti, $telefon);
            $sehir_temiz = mysqli_real_escape_string($baglanti, $sehir);

            $sorgu = "UPDATE kullanicilar SET ad = '$ad_temiz', soyad = '$soyad_temiz', telefon = '$telefon_temiz', sehir = '$sehir_temiz' WHERE id = $kullanici_id AND rol = 'musteri'";
            $calisti = mysqli_query($baglanti, $sorgu);

            if ($calisti) {
                $_SESSION["ad"] = $ad;
                $_SESSION["soyad"] = $soyad;
                $tamam = "Bilgiler kaydedildi.";
            } else {
                $hata = "Kayıt olmadı: " . mysqli_error($baglanti);
            }
        }
    }

    if ($form_turu == "sifre") {
        $eski = "";
        $yeni = "";
        $yeni2 = "";

        if (isset($_POST["eski"])) {
            $eski = $_POST["eski"];
        }
        if (isset($_POST["yeni"])) {
            $yeni = $_POST["yeni"];
        }
        if (isset($_POST["yeni2"])) {
            $yeni2 = $_POST["yeni2"];
        }

        $ben = mysqli_query($baglanti, "SELECT sifre FROM kullanicilar WHERE id = $kullanici_id LIMIT 1");
        $kayit = null;
        if ($ben && mysqli_num_rows($ben) == 1) {
            $kayit = mysqli_fetch_assoc($ben);
        }

        if (!$kayit) {
            $hata = "Kullanıcı bulunamadı.";
        } else if (!password_verify($eski, $kayit["sifre"])) {
            $hata = "Eski şifre yanlış.";
        } else if (strlen($yeni) < 6) {
            $hata = "Yeni şifre en az 6 karakter olsun.";
        } else if ($yeni != $yeni2) {
            $hata = "Yeni şifre tekrarı aynı değil.";
        } else {
            $hash = password_hash($yeni, PASSWORD_DEFAULT);
            $hash_temiz = mysqli_real_escape_string($baglanti, $hash);
            $calisti = mysqli_query($baglanti, "UPDATE kullanicilar SET sifre = '$hash_temiz' WHERE id = $kullanici_id");
            if ($calisti) {
                $tamam = "Şifre değişti.";
            } else {
                $hata = "Şifre değişmedi: " . mysqli_error($baglanti);
            }
        }
    }
}

$kullanici = null;
if ($veritabani_hazir) {
    $sonuc = mysqli_query($baglanti, "SELECT * FROM kullanicilar WHERE id = $kullanici_id LIMIT 1");
    if ($sonuc && mysqli_num_rows($sonuc) == 1) {
        $kullanici = mysqli_fetch_assoc($sonuc);
    }
}

include "../ortak/baslik.php";
include "menu.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <h1>Profil</h1>
        <p class="giris-metni">İletişim bilgileriniz ve şifreniz. E-posta, giriş adınız olduğu için burada değişmez.</p>

        <?php if ($hata != "") { ?>
            <p class="hata"><?php echo temiz_yaz($hata); ?></p>
        <?php } ?>
        <?php if ($tamam != "") { ?>
            <p class="tamam"><?php echo temiz_yaz($tamam); ?></p>
        <?php } ?>

        <?php if ($kullanici) { ?>
            <form class="form-kutu" method="post" action="profil.php">
                <input type="hidden" name="form_turu" value="bilgi">
                <label for="ad">Ad</label>
                <input id="ad" name="ad" type="text" value="<?php echo temiz_yaz($kullanici["ad"]); ?>">

                <label for="soyad">Soyad</label>
                <input id="soyad" name="soyad" type="text" value="<?php echo temiz_yaz($kullanici["soyad"]); ?>">

                <label for="eposta">E-posta</label>
                <input id="eposta" type="text" value="<?php echo temiz_yaz($kullanici["eposta"]); ?>" disabled>

                <label for="telefon">Telefon</label>
                <input id="telefon" name="telefon" type="text" value="<?php echo temiz_yaz($kullanici["telefon"]); ?>">

                <label for="sehir">Şehir</label>
                <input id="sehir" name="sehir" type="text" value="<?php echo temiz_yaz($kullanici["sehir"]); ?>">

                <p><button class="buton" type="submit">Bilgileri kaydet</button></p>
            </form>

            <form class="form-kutu" method="post" action="profil.php">
                <input type="hidden" name="form_turu" value="sifre">
                <h2>Şifre değiştir</h2>
                <label for="eski">Eski şifre</label>
                <input id="eski" name="eski" type="password">

                <label for="yeni">Yeni şifre</label>
                <input id="yeni" name="yeni" type="password">

                <label for="yeni2">Yeni şifre tekrar</label>
                <input id="yeni2" name="yeni2" type="password">

                <p><button class="buton-ikincil" type="submit">Şifreyi kaydet</button></p>
            </form>
        <?php } ?>
    </div>
</section>

<?php include "../ortak/altlik.php"; ?>
