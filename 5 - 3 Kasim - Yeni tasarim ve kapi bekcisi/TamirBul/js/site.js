// DOSYA: js/site.js
// KİM: Kişi 1
// NE YAPAR: Sitenin tek javascript dosyası. On bir iş yapar:
// 1) Telefonda Menü düğmesine basınca linkleri açar
// 2) İl seçilince o ilin ilçelerini, kategori seçilince o kategorinin cihazlarını gösterir
// 3) Sohbet ekranında en yeni mesaja kaydırır
// 4) Ana sayfadaki büyük resimleri sırayla değiştirir
// 5) Yönetimde Word gibi araç çubuğu: seçilen stili kaydetmeden önce gösterir (canlı önizleme)
// 6) Koyu mod / açık mod düğmesi
// 7) Talep formunda seçilen kategori ve cihaz türünün hizmet kartlarını gösterir
// 8) Üst menüdeki açılır menüleri açar ve kapatır
// 9) Pop-up pencereler: onay sorusu, bilgi penceresi, resim büyütme, bildirim
// 10) Form gönderilirken düğmede yükleniyor animasyonu
// 11) Yönetim hizmet formunda sadece seçili kategorinin cihaz kutularını gösterir

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

// ----- 5) Yazı stili araç çubuğu ve canlı önizleme -----

// HARİTA: Yazı stili canlı önizleme (JS)
// Bir yazının alanını (class="stil-alani") kurar. Alanın içinde üç şey var:
// araç çubuğu (.kelime-araclari), yazı kutusu (.stil-hedef) ve önizleme (.onizleme-yazisi).
// Her seçeneğin data-css yazısı PHP'den gelir. Örnek: data-css="color: #dc2626;"
function stilAlaniKur(alan) {
    // Araç çubuğundaki bütün seçim kutuları ve düğmeler (select, onay kutusu, seçim yuvarlağı).
    var araclar = alan.querySelectorAll(".kelime-araclari select, .kelime-araclari input");
    // İki önizleme yazısı: koyu zemin ve açık zemin.
    var onizlemeYazilari = alan.querySelectorAll(".onizleme-yazisi");
    // Yazının kutusu (input veya textarea).
    var yaziKutusu = alan.querySelector(".stil-hedef");
    // Temizle düğmesi.
    var temizleDugmesi = alan.querySelector(".arac-temizle");

    // Önizlemeyi yeniden çizen fonksiyon.
    function onizlemeyiGuncelle() {
        // Seçilen bütün CSS parçalarını bu yazıya toplarız.
        var css = "";
        // Her araç için:
        for (var i = 0; i < araclar.length; i++) {
            // Sıradaki araç.
            var arac = araclar[i];
            // Seçili olanın CSS'i burada olacak.
            var parca = null;
            // Seçim kutusuysa (select):
            if (arac.tagName == "SELECT") {
                // Seçili seçeneğin data-css'i. "Yazı tipi" gibi boş seçenekte null gelir.
                parca = arac.options[arac.selectedIndex].getAttribute("data-css");
            } else if (arac.checked) {
                // Basılı düğme veya seçili renk: kendi data-css'i.
                parca = arac.getAttribute("data-css");
            }
            // CSS varsa:
            if (parca) {
                // Ekleriz.
                css = css + parca + " ";
            }
        }
        // İki önizlemeyi de güncelleriz.
        for (var j = 0; j < onizlemeYazilari.length; j++) {
            // cssText: etiketin style yazısının tamamı. Eski stil silinir, yenisi yazılır.
            onizlemeYazilari[j].style.cssText = css;
            // Yazı kutusu doluysa önizlemede onun yazısı görünür. textContent HTML çalıştırmaz.
            if (yaziKutusu && yaziKutusu.value != "") {
                // Yazıyı koyarız.
                onizlemeYazilari[j].textContent = yaziKutusu.value;
            }
        }
    }

    // Her araç değişince önizlemeyi güncelleriz.
    for (var k = 0; k < araclar.length; k++) {
        // onchange: seçim değişince veya düğmeye basılınca çalışır.
        araclar[k].onchange = onizlemeyiGuncelle;
    }
    // Yazı kutusu varsa:
    if (yaziKutusu) {
        // oninput: her harf yazıldığında çalışır.
        yaziKutusu.oninput = onizlemeyiGuncelle;
    }
    // Temizle düğmesi varsa:
    if (temizleDugmesi) {
        // Basınca bütün araçları varsayılana döndürürüz.
        temizleDugmesi.onclick = function () {
            // Her araç için:
            for (var m = 0; m < araclar.length; m++) {
                // Seçim kutusuysa ilk seçeneğe (boş) döner.
                if (araclar[m].tagName == "SELECT") {
                    // İlk seçenek.
                    araclar[m].selectedIndex = 0;
                } else if (araclar[m].type == "checkbox") {
                    // K ve T düğmeleri bırakılır.
                    araclar[m].checked = false;
                } else {
                    // Seçim yuvarlaklarında değeri boş olan (Otomatik, varsayılan hizalama) seçilir.
                    araclar[m].checked = (araclar[m].value == "");
                }
            }
            // Önizlemeyi güncelleriz.
            onizlemeyiGuncelle();
        };
    }
}

// Sayfadaki bütün stil alanları.
var stilAlanlari = document.querySelectorAll(".stil-alani");
// Her biri için:
for (var stilSirasi = 0; stilSirasi < stilAlanlari.length; stilSirasi++) {
    // Alanı kurarız.
    stilAlaniKur(stilAlanlari[stilSirasi]);
}

// ----- 6) Koyu mod / açık mod -----

// HARİTA: Koyu mod / açık mod düğmesi (JS)
// Sitenin varsayılanı koyu mod. PHP (ortak/baslik.php) "tema" çerezine bakıp <html> etiketine sınıf yazar.
// Düğmeye basınca sınıfı değiştiririz ve seçimi çereze yazarız. Sonraki sayfalar da aynı modda açılır.
var temaDugmesi = document.getElementById("tema-dugmesi");
// Düğme sayfada varsa:
if (temaDugmesi) {
    // Düğmeye tıklanınca:
    temaDugmesi.onclick = function () {
        // <html> etiketi. Bütün sayfanın en dış etiketi.
        var htmlEtiketi = document.documentElement;
        // Yeni mod. Şu an koyuysa açık, açıksa koyu olacak.
        var yeniTema = "koyu";
        // Şu an koyu modda mıyız?
        if (htmlEtiketi.className == "koyu") {
            // Açık moda geçeriz.
            yeniTema = "acik";
        }
        // Sınıfı değiştiririz. CSS renkleri ve düğmenin yazısı ("Açık mod" / "Koyu mod") hemen değişir.
        htmlEtiketi.className = yeniTema;
        // Seçimi çereze yazarız. max-age: 1 yıl (saniye). path=/: bütün sayfalarda geçerli.
        document.cookie = "tema=" + yeniTema + "; max-age=31536000; path=/";
    };
}

// ----- 7) Talep formunda cihaza özel hizmetler -----

// HARİTA: Kategori ve cihaz türüne göre hizmet kartlarını süzme (JS)
// Talep formundaki kategori kutusu, cihaz türü kutusu ve hizmet kartlarının alanı.
var hizmetKategoriKutusu = document.getElementById("cmbAnaKategori");
// Cihaz türü kutusu. Seçeneklerin data-id değeri cihaz türünün numarasıdır.
var hizmetCihazKutusu = document.getElementById("cmbCihazTuru");
// Hizmet kartlarının alanı (ortak/hizmet-secimi.php).
var hizmetSecimi = document.getElementById("hizmet-secimi");
// Üçü de sayfadaysa:
if (hizmetKategoriKutusu && hizmetCihazKutusu && hizmetSecimi) {
    // Kategori veya cihaz seçilince kartları süzen fonksiyon.
    function hizmetleriSuz() {
        // Seçilen kategorinin numarası. Örnek: "1". Seçilmediyse "".
        var kategori = hizmetKategoriKutusu.value;
        // Seçilen cihaz türünün numarası. Seçili seçeneğin data-id değeri. "Seçiniz" seçiliyse boş.
        var cihaz = "";
        // Bir seçenek seçiliyse:
        if (hizmetCihazKutusu.selectedIndex >= 0) {
            // data-id değerini okuruz. Yoksa (Seçiniz satırı) null gelir, boş yazı yaparız.
            cihaz = hizmetCihazKutusu.options[hizmetCihazKutusu.selectedIndex].getAttribute("data-id") || "";
        }
        // data-kategori değeri olan bütün kartlar ("Emin değilim" kartında yoktur, hep görünür).
        var kartlar = hizmetSecimi.querySelectorAll(".hizmet-karti[data-kategori]");
        // Kaç kart görünüyor?
        var gorunenSayi = 0;
        // Her kart için:
        for (var i = 0; i < kartlar.length; i++) {
            // Kartın cihaz numaraları. Örnek: "2,4". split(",") virgülden bölüp diziye çevirir: ["2", "4"]
            var kartCihazlari = kartlar[i].getAttribute("data-cihazlar");
            // Kart bu kategoride mi?
            var uygun = (kartlar[i].getAttribute("data-kategori") == kategori);
            // Cihaz seçildiyse ve kartın cihaz listesi doluysa, seçilen cihaz listede olmalı.
            if (uygun && cihaz != "" && kartCihazlari != "") {
                // indexOf: dizide arar, bulamazsa -1 verir.
                uygun = (kartCihazlari.split(",").indexOf(cihaz) != -1);
            }
            // Uygunsa:
            if (uygun) {
                // Kartı gösteririz.
                kartlar[i].style.display = "";
                // Sayarız.
                gorunenSayi = gorunenSayi + 1;
            } else {
                // Kartı gizleriz.
                kartlar[i].style.display = "none";
                // Gizli kart seçiliyse seçimini kaldırırız (uygun olmayan hizmet gitmesin).
                kartlar[i].querySelector("input").checked = false;
            }
        }
        // Hiçbir hizmet seçili kalmadıysa "Emin değilim" seçilir.
        if (!hizmetSecimi.querySelector("input:checked")) {
            // İlk kart "Emin değilim".
            hizmetSecimi.querySelector(".hizmet-yok input").checked = true;
        }
        // "Önce kategori seçin" yazısı sadece kategori seçilmemişken görünür.
        document.getElementById("hizmet-kategori-bekle").style.display = (kategori == "") ? "" : "none";
        // "Bu cihaz için hizmet yok" yazısı: kategori seçili ama kart yoksa görünür.
        document.getElementById("hizmet-yok-bilgisi").style.display = (kategori != "" && gorunenSayi == 0) ? "block" : "none";
    }
    // addEventListener: kutuya ikinci bir iş ekler. Kategori kutusunun onchange'ini (cihaz listesi) bozmaz.
    hizmetKategoriKutusu.addEventListener("change", hizmetleriSuz);
    // Cihaz türü değişince de süzeriz.
    hizmetCihazKutusu.addEventListener("change", hizmetleriSuz);
    // Sayfa açılınca da bir kez çalıştırırız.
    hizmetleriSuz();
}

// ----- 8) Açılır menü -----

// HARİTA: Açılır menü (JS)
// Üst menüdeki bütün açılır menü düğmeleri.
var acilirDugmeler = document.querySelectorAll(".acilir-dugme");

// Bütün açık menüleri kapatır. "haric": kapatılmayacak menü (yoksa null).
function acilirMenuleriKapat(haric) {
    // Sayfadaki bütün açılır menüler.
    var acikMenuler = document.querySelectorAll(".acilir.acik");
    // Her biri için:
    for (var i = 0; i < acikMenuler.length; i++) {
        // Hariç tutulan değilse:
        if (acikMenuler[i] != haric) {
            // "acik" sınıfını sileriz. CSS listeyi gizler.
            acikMenuler[i].classList.remove("acik");
            // Ekran okuyucuya "kapalı" deriz.
            acikMenuler[i].querySelector(".acilir-dugme").setAttribute("aria-expanded", "false");
        }
    }
}

// Her düğme için:
for (var dugmeSirasi = 0; dugmeSirasi < acilirDugmeler.length; dugmeSirasi++) {
    // Düğmeye basılınca:
    acilirDugmeler[dugmeSirasi].onclick = function (olay) {
        // this: basılan düğme. parentNode: düğmenin kutusu (.acilir).
        var kutu = this.parentNode;
        // Diğer açık menüleri kapatırız.
        acilirMenuleriKapat(kutu);
        // toggle: sınıf varsa siler, yoksa ekler. Sonuç: açık mı?
        var acikMi = kutu.classList.toggle("acik");
        // Ekran okuyucuya durumu söyleriz.
        this.setAttribute("aria-expanded", acikMi ? "true" : "false");
        // Tıklama sayfaya ulaşmasın, yoksa aşağıdaki kod menüyü hemen kapatır.
        olay.stopPropagation();
    };
}
// Sayfada başka bir yere tıklanınca menüler kapanır.
document.addEventListener("click", function (olay) {
    // Tıklanan yer bir açılır menünün içinde değilse:
    if (!olay.target.closest(".acilir")) {
        // Hepsini kapatırız.
        acilirMenuleriKapat(null);
    }
});

// ----- 9) Pop-up (açılır pencere) -----

// HARİTA: Pop-up pencereler (dialog)
// Sayfanın altındaki ortak pencere (ortak/altlik.php). <dialog> HTML'in hazır pencere etiketidir.
var ortakPencere = document.getElementById("ortak-pencere");

// Ortak pencereyi açar. baslik: üstteki yazı. icerik: HTML parçası. evetYazisi: onay düğmesi (yoksa "").
// evetIsi: onay düğmesine basılınca çalışacak fonksiyon (yoksa null).
function pencereAc(ikon, baslik, icerik, evetYazisi, evetIsi) {
    // Pencere sayfada yoksa bir şey yapmayız.
    if (!ortakPencere) {
        // Fonksiyondan çıkarız.
        return;
    }
    // İkonu yazarız.
    ortakPencere.querySelector(".pencere-ikonu").textContent = ikon;
    // Başlığı yazarız. textContent HTML çalıştırmaz.
    ortakPencere.querySelector(".pencere-baslik").textContent = baslik;
    // İçeriği koyarız. İçerik bizim hazırladığımız HTML'dir (kullanıcı yazısı değil).
    ortakPencere.querySelector(".pencere-icerik").innerHTML = icerik;
    // Onay düğmesi.
    var evetDugmesi = ortakPencere.querySelector(".pencere-evet");
    // Onay düğmesi isteniyorsa:
    if (evetYazisi != "") {
        // Yazısını koyarız.
        evetDugmesi.textContent = evetYazisi;
        // Gösteririz.
        evetDugmesi.style.display = "";
        // Basınca: pencereyi kapatıp işi yaparız.
        evetDugmesi.onclick = function () {
            // Pencereyi kapatırız.
            ortakPencere.close();
            // İşi yaparız.
            evetIsi();
        };
    } else {
        // Onay düğmesi gizli kalır (sadece Kapat düğmesi görünür).
        evetDugmesi.style.display = "none";
    }
    // showModal: pencereyi sayfanın ortasında açar, arkayı karartır.
    ortakPencere.showModal();
}

// Pencere varsa Kapat düğmesini ve dışarı tıklamayı ayarlarız.
if (ortakPencere) {
    // Kapat düğmesi pencereyi kapatır.
    ortakPencere.querySelector(".pencere-kapat").onclick = function () {
        // Pencereyi kapatırız.
        ortakPencere.close();
    };
    // Karartılmış arka plana tıklanınca da kapanır. Tıklanan yer pencerenin kendisiyse dışarısıdır.
    ortakPencere.onclick = function (olay) {
        // Tıklanan pencere kutusunun kendisiyse (içindeki bir şey değil):
        if (olay.target == ortakPencere) {
            // Kapatırız.
            ortakPencere.close();
        }
    };
}

// 9a) Onay penceresi: data-onay yazısı olan formlar gönderilmeden önce sorulur.
// Örnek: <form data-onay="Bu hizmet silinsin mi?"> (Eskiden tarayıcının gri confirm kutusu vardı.)
var onayFormlari = document.querySelectorAll("form[data-onay]");
// Her form için:
for (var formSirasi = 0; formSirasi < onayFormlari.length; formSirasi++) {
    // Form gönderilmeye çalışılınca:
    onayFormlari[formSirasi].onsubmit = function (olay) {
        // this: gönderilen form.
        var form = this;
        // Form daha önce onaylandıysa gönderilsin.
        if (form.getAttribute("data-onaylandi") == "1") {
            // true: gönderime izin.
            return true;
        }
        // Şimdilik gönderimi durdururuz.
        olay.preventDefault();
        // Sorulacak yazı. Ekrana textContent ile değil, aşağıda güvenli şekilde koyarız.
        var soru = document.createElement("p");
        // Yazı (HTML çalışmaz).
        soru.textContent = form.getAttribute("data-onay");
        // Pencereyi açarız. Evet'e basılınca form gönderilir.
        pencereAc("?", "Emin misiniz?", soru.outerHTML, form.getAttribute("data-onay-dugme") || "Evet, devam et", function () {
            // İşaretleriz ki bir daha sorulmasın.
            form.setAttribute("data-onaylandi", "1");
            // requestSubmit: formu normal şekilde gönderir (yükleniyor animasyonu da çalışır).
            form.requestSubmit();
        });
    };
}

// 9b) Bilgi pencereleri: data-pencere="kutu-id" olan düğmeye basınca o kutunun içeriği pencerede açılır.
// Örnek: <button data-pencere="takip-bilgi">i</button> ve <template id="takip-bilgi">...</template>
var bilgiDugmeleri = document.querySelectorAll("[data-pencere]");
// Her düğme için:
for (var bilgiSirasi = 0; bilgiSirasi < bilgiDugmeleri.length; bilgiSirasi++) {
    // Basılınca:
    bilgiDugmeleri[bilgiSirasi].onclick = function () {
        // İçeriği tutan <template> etiketi.
        var sablon = document.getElementById(this.getAttribute("data-pencere"));
        // Şablon varsa:
        if (sablon) {
            // Pencereyi açarız. Başlık ve ikon şablonun data değerlerinden gelir.
            pencereAc(sablon.getAttribute("data-ikon") || "i", sablon.getAttribute("data-baslik") || "Bilgi", sablon.innerHTML, "", null);
        }
    };
}

// 9c) Resim büyütme: class="buyutulebilir" olan resme tıklanınca büyük hali pencerede açılır.
var buyukResimler = document.querySelectorAll("img.buyutulebilir");
// Her resim için:
for (var resimSirasi = 0; resimSirasi < buyukResimler.length; resimSirasi++) {
    // Tıklanınca:
    buyukResimler[resimSirasi].onclick = function () {
        // Yeni bir resim etiketi oluştururuz.
        var buyuk = document.createElement("img");
        // Aynı adres.
        buyuk.src = this.src;
        // Aynı açıklama.
        buyuk.alt = this.alt;
        // Büyük resim sınıfı.
        buyuk.className = "pencere-resmi";
        // Pencerede gösteririz.
        pencereAc("+", this.alt || "Fotoğraf", buyuk.outerHTML, "", null);
    };
}

// 9d) Bildirim (sağ üstte kayan yazı): kapat düğmesine basınca kapanır.
var bildirimler = document.querySelectorAll(".bildirim");
// Her bildirim için:
for (var bildirimSirasi = 0; bildirimSirasi < bildirimler.length; bildirimSirasi++) {
    // Kapat düğmesine basılınca bildirimi sileriz.
    bildirimler[bildirimSirasi].querySelector(".bildirim-kapat").onclick = function () {
        // parentNode: düğmenin bildirimi. remove: sayfadan siler.
        this.parentNode.remove();
    };
}

// ----- 10) Form gönderilirken yükleniyor animasyonu -----

// HARİTA: Düğmede yükleniyor animasyonu
// Form gönderilince gönder düğmesi dönen bir halkaya dönüşür ve kilitlenir.
// Böylece iki kez basılıp aynı talep iki kez açılmaz.
var butunFormlar = document.querySelectorAll("form");
// Her form için:
for (var gonderSirasi = 0; gonderSirasi < butunFormlar.length; gonderSirasi++) {
    // addEventListener: onay penceresinin onsubmit'ini bozmaz.
    butunFormlar[gonderSirasi].addEventListener("submit", function (olay) {
        // Onay penceresi gönderimi durdurduysa bir şey yapmayız.
        if (olay.defaultPrevented) {
            // Fonksiyondan çıkarız.
            return;
        }
        // Formdaki gönder düğmesi.
        var dugme = this.querySelector("button[type=submit]");
        // Düğme varsa:
        if (dugme) {
            // Dönen halka sınıfını ekleriz (css: .yukleniyor).
            dugme.classList.add("yukleniyor");
            // setTimeout: 10 milisaniye sonra kilitleriz. Hemen kilitlersek tarayıcı formu göndermeyebilir.
            setTimeout(function () {
                // Düğme kilitlenir.
                dugme.disabled = true;
            }, 10);
        }
    });
}

// ----- 11) Yönetimde hizmetin cihaz türleri -----

// HARİTA: Yönetim hizmet formunda cihaz kutularını süzme (JS)
// Yönetim hizmet formundaki kategori kutusu ve cihaz onay kutularının alanı (admin/hizmetler.php).
var yonetimKategoriKutusu = document.getElementById("kategori_id");
// Cihaz onay kutularının alanı.
var yonetimCihazAlani = document.getElementById("hizmet-cihaz-kutulari");
// İkisi de sayfadaysa:
if (yonetimKategoriKutusu && yonetimCihazAlani) {
    // Sadece seçilen kategorinin cihaz kutularını gösteren fonksiyon.
    function cihazKutulariniSuz() {
        // Seçilen kategori numarası.
        var kategori = yonetimKategoriKutusu.value;
        // Bütün cihaz kutuları.
        var kutular = yonetimCihazAlani.querySelectorAll("[data-kategori]");
        // Her kutu için:
        for (var i = 0; i < kutular.length; i++) {
            // Kutu bu kategorideyse:
            if (kutular[i].getAttribute("data-kategori") == kategori) {
                // Gösteririz.
                kutular[i].style.display = "";
            } else {
                // Gizleriz.
                kutular[i].style.display = "none";
                // İşaretini kaldırırız. Başka kategorinin cihazı gitmesin.
                kutular[i].querySelector("input").checked = false;
            }
        }
    }
    // Kategori değişince süzeriz.
    yonetimKategoriKutusu.addEventListener("change", cihazKutulariniSuz);
    // Sayfa açılınca da bir kez çalıştırırız.
    cihazKutulariniSuz();
}
