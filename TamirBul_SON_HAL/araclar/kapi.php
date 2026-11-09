<?php
/*
DOSYA: araclar/kapi.php
KİM: Kişi 3
NE YAPAR: Sitenin kapı bekçisi. Tarayıcıdan gelen her istek önce buraya uğrar.
araclar/sunucu.bat sunucuyu bu dosyayla başlatır: php -S localhost:8000 araclar/kapi.php
Sadece site sayfalarına, stil, kod ve resim dosyalarına izin verir.
Veritabanı dosyası (.sql), .git klasörü, notlar (.txt) ve parça dosyalar dışarıdan açılamaz.
İzin yoksa "Sayfa bulunamadı" (404) sayfası gösterilir.
*/

// HARİTA: Kapı bekçisi (dışarıya kapalı dosyalar)
// İstenen adresin yol kısmı. Örnek: /admin/panel.php (soru işaretinden sonrası alınmaz).
$yol = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
// Adres boş gelirse ana sayfa sayarız.
if (!is_string($yol)) {
    // Ana sayfa.
    $yol = "/";
}
// %20 gibi kodları normal harfe çeviririz. Böylece kodlanmış adresle kandırılamaz.
$yol = rawurldecode($yol);
// Windows ters eğik çizgiyi de klasör ayracı sayar. Hepsini düz eğik çizgi yaparız.
$yol = str_replace("\\", "/", $yol);
// Küçük harfe çeviririz. Windows ORTAK ile ortak'ı aynı klasör sayar.
$yol = strtolower($yol);

// Bu istek yasak mı? Başta değil. Aşağıdaki kontrollerden biri tutarsa true olur.
$yasak = false;

// Dışarıdan hiç açılmayan klasörler.
$kapali_klasorler = array("/ortak/", "/veritabani/", "/araclar/", "/_ileride/", "/.git/", "/.cursor/", "/.vscode/");
// Her kapalı klasör için:
foreach ($kapali_klasorler as $klasor) {
    // Adresin herhangi bir yerinde bu klasör geçiyorsa:
    if (strpos($yol, $klasor) !== false) {
        // Yasak.
        $yasak = true;
    }
}

// Sadece başka sayfaların içine eklenen parça dosyalar. Tek başına açılınca hata verirlerdi.
$parca_dosyalar = array("/musteri/is-durumu.php", "/musteri/teklif-kartlari.php", "/tamirci/onay-durumu.php", "/tamirci/uygun-talepler-sorgu.php");
// Adres bu listede varsa:
if (in_array($yol, $parca_dosyalar)) {
    // Yasak.
    $yasak = true;
}

// Adres eğik çizgiyle bitmiyorsa bir dosya istenmiştir. Uzantısına bakarız.
if (substr($yol, -1) != "/") {
    // Dosyanın uzantısı. Örnek: php, css, sql. pathinfo: dosya adını parçalar.
    $uzanti = pathinfo($yol, PATHINFO_EXTENSION);
    // İzin verilen uzantılar: sayfalar, stil, kod, resimler.
    $izinli_uzantilar = array("php", "css", "js", "png", "jpg", "jpeg", "gif", "webp", "svg", "ico");
    // Uzantı listede yoksa (sql, txt, bat, md, uzantısız .gitignore gibi):
    if (!in_array($uzanti, $izinli_uzantilar)) {
        // Yasak.
        $yasak = true;
    }
    // Yüklenen dosyalar klasöründe PHP asla çalışmaz. Resim diye kod yüklenemesin.
    if (strpos($yol, "/yuklemeler/") !== false && $uzanti == "php") {
        // Yasak.
        $yasak = true;
    }
}

// Yasaksa:
if ($yasak) {
    // Tarayıcıya "bulunamadı" kodunu göndeririz. Dosyanın var olduğunu bile söylemeyiz.
    http_response_code(404);
    // Türkçe harfler doğru görünsün.
    header("Content-Type: text/html; charset=UTF-8");
    // Basit bir sayfa yazarız.
    echo '<!DOCTYPE html><html lang="tr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Sayfa bulunamadı | TamirBul</title>';
    // Sayfanın görünüşü: koyu zemin, ortada yazı.
    echo '<style>body{margin:0;min-height:100vh;display:grid;place-items:center;background:#0b1120;color:#e2e8f0;font-family:system-ui,Segoe UI,sans-serif;text-align:center;padding:24px}h1{font-size:64px;margin:0;color:#38bdf8}a{display:inline-block;margin-top:20px;padding:12px 22px;border-radius:10px;background:#2563eb;color:#fff;text-decoration:none;font-weight:600}</style></head>';
    // Yazı ve ana sayfa düğmesi.
    echo '<body><div><h1>404</h1><p>Aradığınız sayfa bulunamadı.</p><a href="/index.php">Ana sayfaya dön</a></div></body></html>';
    // Burada dururuz. return true: PHP sunucusu dosyayı göndermez.
    return true;
}

// İzin var. return false: PHP sunucusu istenen dosyayı her zamanki gibi açar.
return false;
