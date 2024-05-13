<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan Ekle</title>
    <!-- İsteğe bağlı olarak stilleri dahil edin -->
    <style>
        body {
            margin: 0;
        }

        .sidebar {
            background-color: #3f51b5;
            padding: 30px;
            width: 200px;
            height: 100vh;
            float: left;
            margin-left: -10px;
        }

        .sidebar .logo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: inline-block;
            vertical-align: middle;
        }

        .sidebar .logo-text {
            display: inline-block;
            margin-left: 10px; /* Logo ile metin arasında bir boşluk ekledik */
            
        }

        .sidebar .logo-text a {
            color: #ffffff;
            font-weight: bold;
            font-size: 20px;
            margin-bottom: 50px;
            margin-top: 0;
            text-decoration: none;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
            margin-top: 20px;
        }

        .sidebar ul li {
            margin-bottom: 20px;
            margin-top: 0;
        }

        .sidebar ul li a {
            color: #ffffff;
            text-decoration: none;
            font-size: 24px; /* 36px'ten 24px'ye değiştirildi */
        }

        .sidebar ul li a:hover {
            text-decoration: underline;
        }

        /* Yeni stil ekledik */
        .sidebar .logo-container {
            margin-bottom: 50px; /* Logo ve metin ile listeler arasındaki boşluğu ayarlar */
        }


        .main-content {
            float: left;
            margin-left: 20px;
            padding: 20px; /* Alanın etrafına bir iç boşluk ekleyelim */
        }

        .main-content h2 {
            margin-bottom: 20px; /* Başlık ile içerik arasında boşluk bırakalım */
        }

        .main-content form {
            margin-bottom: 20px; /* Form ile diğer içerik arasında boşluk bırakalım */
        }

        .main-content label {
            display: block;
            margin-bottom: 10px; /* Her bir etiketin altında boşluk bırakalım */
        }

        .main-content input[type="text"],
        .main-content textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px; /* Giriş alanları arasında boşluk bırakalım */
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .main-content button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        .main-content button[type="submit"]:hover {
            background-color: #45a049;
        }



    </style>

</head>
<body>

<div class="sidebar">
    <div class="logo-container"> <!-- Logo ve metin gruplandırıldı -->
        <div class="logo">
            <img src="img/planner-icon.png" alt="">
        </div>
        <div class="logo-text">
            <a href="homepage.php">APPSIS</a>
        </div>
    </div>
    <ul>
        <li><a href="#" onclick="filtrelePlanlar('daily')">Günlük</a></li>
        <li><a href="#" onclick="filtrelePlanlar('weekly')">Haftalık</a></li>
        <li><a href="#" onclick="filtrelePlanlar('monthly')">Aylık</a></li>
        <li><a href="#" onclick="filtrelePlanlar('all')">Hepsi</a></li>
    </ul>
</div>

    <div class="main-content">
    <h2>Yeni Plan Ekle</h2>
    <form action="plan_ekle.php" method="POST">
        <div>
            <label for="baslik">Başlık:</label>
            <input type="text" id="baslik" name="baslik" required>
        </div>
        <div>
            <label for="aciklama">Açıklama:</label>
            <textarea id="aciklama" name="aciklama" required></textarea>
        </div>
        <button type="submit" name="ekle">Plan Ekle</button>
    </form>
    

    
    <div class="planlar">
        <h2>Tüm Planlar</h2>
        <ul id="planListesi">
        <?php
        // Veritabanı bağlantısını içe aktar
        include("baglanti.php");

        $period = isset($_GET['period']) ? $_GET['period'] : '';

        switch ($period) {
            case 'daily':
                $sql = "SELECT plan_id, baslik, aciklama FROM planlar WHERE DATE(kayit_tarihi) = CURDATE()";
                break;
            case 'weekly':
                $sql = "SELECT plan_id, baslik, aciklama FROM planlar WHERE WEEK(kayit_tarihi) = WEEK(NOW())";
                break;
            case 'monthly':
                $sql = "SELECT plan_id, baslik, aciklama FROM planlar WHERE MONTH(kayit_tarihi) = MONTH(NOW())";
                break;
            case 'all':
                $sql = "SELECT plan_id, baslik, aciklama FROM planlar";
                break;
            default:
                $sql = "SELECT plan_id, baslik, aciklama FROM planlar";
                break;
        }


        // Sorguyu çalıştır ve sonuçları al
        $result = mysqli_query($baglanti, $sql);

        if (!$result) {
            echo "Sorgu hatası: " . mysqli_error($baglanti);
        } else {
            // Veritabanından gelen planları döngüyle listeleme
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<li>";
                    echo "<input type='checkbox' id='tamamla_" . $row["plan_id"] . "' onchange='tamamlaPlan(" . $row["plan_id"] . ")'>";
                    echo "<label for='tamamla_" . $row["plan_id"] . "'>Tamamlandı</label>";
                    echo "<strong>Başlık:</strong> " . $row["baslik"] . " - <strong>Açıklama:</strong> " . $row["aciklama"] . " ";
                    echo "<button class='silButon' onclick='silPlan(" . $row["plan_id"] . ")'>Sil</button> ";
                    echo "<button class='duzenleButon' onclick='duzenlePlan(" . $row["plan_id"] . ", \"" . $row["baslik"] . "\", \"" . $row["aciklama"] . "\")'>Düzenle</button>";
                    echo "</li>";
                }
            } else {
                echo "<li>Hiç plan bulunamadı.</li>";
            }
        }
        
        
        
        // Veritabanı bağlantısını kapat
        mysqli_close($baglanti);
    ?>

        </ul>
    </div>

    </div>
    <script>
        // Plan silme işlemini gerçekleştiren JavaScript fonksiyonu
        function silPlan(plan_id) {
            if (confirm('Bu planı silmek istediğinizden emin misiniz?')) {
                fetch('plan_sil.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'plan_id=' + plan_id
                })
                .then(response => response.text()) // Yanıtı metin olarak işle
                .then(data => {
                    alert(data.trim());
                    updatePlanList();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(error.message); // Hata mesajı göster
                });
            }
        }

        // Plan düzenleme işlemini gerçekleştiren JavaScript fonksiyonu
        function duzenlePlan(plan_id, baslik, aciklama) {
            // Düzenleme formunu oluştur
            var yeni_baslik = prompt("Yeni başlık:", baslik);
            var yeni_aciklama = prompt("Yeni açıklama:", aciklama);

            // Eğer kullanıcı iptal ettiyse veya herhangi bir değer girmediyse işlemi iptal et
            if (yeni_baslik === null || yeni_aciklama === null || yeni_baslik.trim() === "" || yeni_aciklama.trim() === "") {
                return;
            }

            // Düzenleme isteğini gönder
            fetch('plan_duzenle.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'plan_id=' + plan_id + '&baslik=' + encodeURIComponent(yeni_baslik) + '&aciklama=' + encodeURIComponent(yeni_aciklama)
            })
            .then(response => response.text()) // Yanıtı metin olarak işle
            .then(data => {
                alert(data.trim());
                updatePlanList();
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message); // Hata mesajı göster
            });
        }

        function filtrelePlanlar(secilenFiltre) {
    fetch('plan_listele.php?period=' + secilenFiltre, {
        method: 'GET'
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('planListesi').innerHTML = data;
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function tamamlaPlan(plan_id) {
    var checkbox = document.getElementById('tamamla_' + plan_id);
    var tamamlaDurumu = checkbox.checked ? 1 : 0;

    fetch('plan_tamamla.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'plan_id=' + plan_id + '&tamamla_durumu=' + tamamlaDurumu
    })
    .then(response => response.text())
    .then(data => {
        alert(data.trim());
        updatePlanList();
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message);
    });
}




        // Planlar listesini güncelleyen fonksiyon
        function updatePlanList() {
            // Planlar listesini yenilemek için AJAX isteği gönder
            fetch('plan_listele.php', {
                method: 'GET'
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('planListesi').innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // Sayfa yüklendiğinde plan listesini güncelle
        window.onload = function() {
            updatePlanList();
        };
    </script>
</body>
</html>
