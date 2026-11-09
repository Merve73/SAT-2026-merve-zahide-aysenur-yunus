// DOSYA: js/site.js
// KİM: Kişi 1
// NE YAPAR: Sitenin tek javascript dosyası. Dört iş yapar:
// 1) Telefonda Menü düğmesine basınca linkleri açar
// 2) İl seçilince o ilin ilçelerini, kategori seçilince o kategorinin cihazlarını gösterir
// 3) Sohbet ekranında en yeni mesaja kaydırır
// 4) Ana sayfadaki büyük resimleri sırayla değiştirir

// ----- 1) Telefon menüsü -----

// HARİTA: Telefon menüsü (JS)
// Menü düğmesini id'si ile buluruz.
var menuDugmesi = document.getElementById("menu-dugmesi");
// Linklerin durduğu kutuyu buluruz.
var menu = document.getElementById("menu");

// İkisi de sayfada varsa:
if (menuDugmesi && menu) {
    // Düğmeye tıklanınca bu fonksiyon çalışır.
    menuDugmesi.onclick = function () {
        // Menü açıksa:
        if (menu.className == "menu acik") {
            // Kapatırız (css gizler).
            menu.className = "menu";
        } else {
            // Kapalıysa açarız.
            menu.className = "menu acik";
        }
    };
}

// ----- 2) Bağlı seçim kutuları -----

// HARİTA: İl-ilçe ve kategori-cihaz bağlı listeleri (JS)
// ustKutu: il veya kategori kutusu. altKutu: ilçe veya cihaz kutusu.
// PHP alt kutuya bütün seçenekleri yazar. Her seçeneğin data-üst değeri hangi üst değere ait olduğunu söyler.
function bagliListeKur(ustKutu, altKutu) {
    // Alt kutudaki bütün seçenekleri bu diziye kopyalarız.
    var tumSecenekler = [];
    // Her seçeneği sırayla geziyoruz.
    for (var i = 0; i < altKutu.options.length; i++) {
        // Diziye ekleriz.
        tumSecenekler.push(altKutu.options[i]);
    }

    // Alt kutuyu üst kutunun değerine göre yeniden dolduran fonksiyon.
    function listeyiDoldur() {
        // Üst kutuda seçilen değer. Örnek: "Yozgat"
        var secilen = ustKutu.value;
        // Alt kutuda önceden seçili olan değer. Kaybolmasın diye hatırlarız.
        var eskiDeger = altKutu.value;
        // Alt kutuyu boşaltırız.
        altKutu.innerHTML = "";
        // Kopyaladığımız seçenekleri geziyoruz.
        for (var j = 0; j < tumSecenekler.length; j++) {
            // Sıradaki seçenek.
            var secenek = tumSecenekler[j];
            // "Seçiniz" seçeneği her zaman, diğerleri sadece seçilen ile eşleşirse eklenir.
            if (secenek.value == "" || secenek.getAttribute("data-ust") == secilen) {
                // Seçeneği kutuya ekleriz.
                altKutu.appendChild(secenek);
            }
        }
        // Eski seçimi geri seçmeyi deneriz.
        altKutu.value = eskiDeger;
        // Eski seçim yeni listede yoksa:
        if (altKutu.value != eskiDeger) {
            // İlk satıra (Seçiniz) döneriz.
            altKutu.selectedIndex = 0;
        }
        // Üst kutu boşsa alt kutu kilitli, doluysa açık.
        altKutu.disabled = (secilen == "");
    }

    // Üst kutu her değiştiğinde listeyi yeniden kurarız.
    ustKutu.onchange = listeyiDoldur;
    // Sayfa ilk açıldığında da bir kez çalıştırırız.
    listeyiDoldur();
}

// data-alt yazısı olan bütün seçim kutularını buluruz. Örnek: <select id="cmbIl" data-alt="cmbIlce">
var ustKutular = document.querySelectorAll("select[data-alt]");
// Her biri için:
for (var k = 0; k < ustKutular.length; k++) {
    // data-alt içindeki id ile alt kutuyu buluruz.
    var altKutu = document.getElementById(ustKutular[k].getAttribute("data-alt"));
    // Alt kutu varsa:
    if (altKutu) {
        // İki kutuyu birbirine bağlarız.
        bagliListeKur(ustKutular[k], altKutu);
    }
}

// ----- 3) Sohbet ekranı -----

// Mesajların durduğu kutu.
var sohbetKutusu = document.getElementById("sohbet-mesajlari");
// Sohbet sayfasındaysak:
if (sohbetKutusu) {
    // Kutuyu en aşağı kaydırırız, en yeni mesaj görünür.
    sohbetKutusu.scrollTop = sohbetKutusu.scrollHeight;
}

// ----- 4) Ana sayfa slaytları -----

// HARİTA: Ana sayfa slayt değiştirme (JS, setInterval)
// Slaytların durduğu kutu.
var slaytAlani = document.getElementById("slayt-alani");
// Ana sayfadaysak:
if (slaytAlani) {
    // Kutudaki bütün resimler.
    var slaytlar = slaytAlani.getElementsByTagName("img");
    // Kaç saniyede bir değişeceği. PHP data-süre içine yazar.
    var saniye = parseInt(slaytAlani.getAttribute("data-sure"));
    // Sayı değilse veya 1'den küçükse:
    if (isNaN(saniye) || saniye < 1) {
        // 5 saniye kabul ederiz.
        saniye = 5;
    }
    // Şu an görünen resmin sırası. İlk resim 0.
    var gorunenSlayt = 0;
    // Birden fazla resim varsa:
    if (slaytlar.length > 1) {
        // setInterval: fonksiyonu her "saniye * 1000" milisaniyede bir çalıştırır.
        setInterval(function () {
            // Görünen resmi gizleriz.
            slaytlar[gorunenSlayt].className = "slayt";
            // Sıradaki resme geçeriz.
            gorunenSlayt = gorunenSlayt + 1;
            // Son resimden sonra:
            if (gorunenSlayt >= slaytlar.length) {
                // Başa döneriz.
                gorunenSlayt = 0;
            }
            // Yeni resmi gösteririz.
            slaytlar[gorunenSlayt].className = "slayt aktif";
        }, saniye * 1000); // saniyeyi milisaniyeye çeviririz
    }
}
