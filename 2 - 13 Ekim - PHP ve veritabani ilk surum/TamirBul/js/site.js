// Bu dosya sade bir is yapar: telefonda Menu dugmesine basinca linkleri acar.
// Bilgisayarda menu zaten aciktir. O isi css/stil.css halleder.

// Dugmeyi id'si ile buluruz. HTML'deki id="menu-dugmesi" ile ayni olmali.
var menuDugmesi = document.getElementById("menu-dugmesi");

// Linklerin durdugu nav kutusunu buluruz.
var menu = document.getElementById("menu");

// Dugme sayfada varsa tiklamayi dinleriz. Yoksa hata vermesin diye if var.
if (menuDugmesi && menu) {
    // onclick: bu dugmeye tiklaninca icindeki fonksiyon calisir.
    menuDugmesi.onclick = function () {
        // className tum sinif yazisidir. "menu acik" ise menu gorunur demektir.
        if (menu.className == "menu acik") {
            // Acikken tiklaninca sinifi tekrar "menu" yapariz, css onu gizler.
            menu.className = "menu";
        } else {
            // Kapaliysa "acik" sinifini ekleriz.
            menu.className = "menu acik";
        }
    };
}
