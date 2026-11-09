<?php
/*
SAYFA: Kayit
DOSYA: kayit.php
KIM: Kisi 3

BU SAYFADA BUNLAR OLACAK:
- Ad, soyad, e-posta, telefon, sehir
- Rol secimi: musteri veya tamirci (yonetici bu formdan acilamaz)
- Tamirci ise uzmanlik secimi
- Sifre ve sifre tekrar
- Kayit olunca giris sayfasina doner
*/

$kok = "";
$sayfa_basligi = "Kayıt ol";

include "ortak/baglanti.php";
include "ortak/guvenlik.php";

$hata = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad = "";
    $soyad = "";
    $eposta = "";
    $telefon = "";
    $sehir = "";
    $sifre = "";
    $sifre_tekrar = "";
    $uzmanlik = "";

    if (isset($_POST["ad"])) {
        $ad = trim($_POST["ad"]);
    }
    if (isset($_POST["soyad"])) {
        $soyad = trim($_POST["soyad"]);
    }
    if (isset($_POST["eposta"])) {
        $eposta = trim($_POST["eposta"]);
    }
    if (isset($_POST["telefon"])) {
        $telefon = trim($_POST["telefon"]);
    }
    if (isset($_POST["sehir"])) {
        $sehir = trim($_POST["sehir"]);
    }
    if (isset($_POST["sifre"])) {
        $sifre = $_POST["sifre"];
    }
    if (isset($_POST["sifre_tekrar"])) {
        $sifre_tekrar = $_POST["sifre_tekrar"];
    }

    // Formdan admin secilemesin. Sadece bu iki rolden biri kabul edilir.
    $rol = "musteri";
    if (isset($_POST["rol"]) && $_POST["rol"] == "tamirci") {
        $rol = "tamirci";
    }

    if (isset($_POST["uzmanlik"])) {
        if ($_POST["uzmanlik"] == "bilgisayar" || $_POST["uzmanlik"] == "telefon" || $_POST["uzmanlik"] == "tablet") {
            $uzmanlik = $_POST["uzmanlik"];
        }
    }

    if (!$veritabani_hazir) {
        $hata = "Veritabanı kapalı, kayıt olunamadı.";
    } else if ($ad == "" || $soyad == "" || $eposta == "" || $telefon == "" || $sehir == "" || $sifre == "") {
        $hata = "Yıldızlı alanlar boş bırakılamaz.";
    } else if (strpos($eposta, "@") === false) {
        $hata = "E-posta adresinde @ olmalı.";
    } else if ($sifre != $sifre_tekrar) {
        $hata = "Şifre tekrarı aynı değil.";
    } else if (strlen($sifre) < 6) {
        $hata = "Şifre en az 6 karakter olsun.";
    } else if ($rol == "tamirci" && $uzmanlik == "") {
        $hata = "Tamirci kaydında uzmanlık seçin.";
    } else {
        $eposta_temiz = mysqli_real_escape_string($baglanti, $eposta);
        $var_mi = mysqli_query($baglanti, "SELECT id FROM kullanicilar WHERE eposta = '$eposta_temiz' LIMIT 1");

        if ($var_mi && mysqli_num_rows($var_mi) > 0) {
            $hata = "Bu e-posta ile kayıt var. Giriş yapın.";
        } else {
            // Musteri hemen girebilsin. Tamirciyi yonetici onaylar.
            $onayli = 1;
            if ($rol == "tamirci") {
                $onayli = 0;
            }
            if ($rol != "tamirci") {
                $uzmanlik = "";
            }

            $ad_temiz = mysqli_real_escape_string($baglanti, $ad);
            $soyad_temiz = mysqli_real_escape_string($baglanti, $soyad);
            $telefon_temiz = mysqli_real_escape_string($baglanti, $telefon);
            $sehir_temiz = mysqli_real_escape_string($baglanti, $sehir);
            $uzmanlik_temiz = mysqli_real_escape_string($baglanti, $uzmanlik);

            // password_hash sifreyi geri cozulmeyen bir metne cevirir.
            $sifre_hash = password_hash($sifre, PASSWORD_DEFAULT);
            $sifre_hash_temiz = mysqli_real_escape_string($baglanti, $sifre_hash);
            $simdi = date("Y-m-d H:i:s");

            $sorgu = "INSERT INTO kullanicilar (ad, soyad, eposta, sifre, telefon, rol, sehir, uzmanlik, hakkinda, onayli, kayit_tarihi) VALUES ('$ad_temiz', '$soyad_temiz', '$eposta_temiz', '$sifre_hash_temiz', '$telefon_temiz', '$rol', '$sehir_temiz', '$uzmanlik_temiz', '', $onayli, '$simdi')";
            $calisti = mysqli_query($baglanti, $sorgu);

            if ($calisti) {
                header("Location: giris.php?kayit=tamam");
                exit;
            } else {
                $hata = "Kayıt olmadı: " . mysqli_error($baglanti);
            }
        }
    }
}

include "ortak/baslik.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <h1>Kayıt ol</h1>
        <p class="giris-metni">Müşteri arıza kaydı açar. Tamirci, yönetici onayından sonra talep alabilir.</p>

        <?php if ($hata != "") { ?>
            <p class="hata"><?php echo temiz_yaz($hata); ?></p>
        <?php } ?>

        <form class="form-kutu" method="post" action="kayit.php">
            <label for="ad">Ad</label>
            <input id="ad" name="ad" type="text">

            <label for="soyad">Soyad</label>
            <input id="soyad" name="soyad" type="text">

            <label for="eposta">E-posta</label>
            <input id="eposta" name="eposta" type="text">

            <label for="telefon">Telefon</label>
            <input id="telefon" name="telefon" type="text">

            <label for="sehir">Şehir</label>
            <input id="sehir" name="sehir" type="text">

            <label>Hesap türü</label>
            <label><input type="radio" name="rol" value="musteri" checked> Müşteri</label>
            <label><input type="radio" name="rol" value="tamirci"> Tamirci</label>

            <label for="uzmanlik">Uzmanlık (tamirciyseniz)</label>
            <select id="uzmanlik" name="uzmanlik">
                <option value="">Seçin</option>
                <option value="bilgisayar">Bilgisayar</option>
                <option value="telefon">Telefon</option>
                <option value="tablet">Tablet</option>
            </select>

            <label for="sifre">Şifre</label>
            <input id="sifre" name="sifre" type="password">

            <label for="sifre_tekrar">Şifre tekrar</label>
            <input id="sifre_tekrar" name="sifre_tekrar" type="password">

            <p><button class="buton" type="submit">Kayıt ol</button></p>
            <p>Hesabınız varsa <a href="giris.php">giriş yapın</a>.</p>
        </form>
    </div>
</section>

<?php include "ortak/altlik.php"; ?>
