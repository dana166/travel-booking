<?php
include 'db.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = connect_db();

// معالجة حجز جديد
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'book') {
    $user_id = $_SESSION['user_id'];
    $destination_id = $_POST['destination_id'];
    $travel_date = $_POST['travel_date'];
    $num_people = $_POST['num_people'];

    $sql = "INSERT INTO bookings (user_id, destination_id, travel_date, num_people) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisi", $user_id, $destination_id, $travel_date, $num_people);

    if ($stmt->execute() === TRUE) {
        $message = "Booking successful!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>العربية للسياحة</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <style>
       
    </style>
</head>
<body>
    <!-- العنوان الرئيسي -->
    <header class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#"><b>العربية للسياحة</b></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="#home"><b>الرئيسية</b></a></li>
                <li class="nav-item"><a class="nav-link" href="#destinations"><b>الوجهات السياحية</b></a></li>
                <li class="nav-item"><a class="nav-link" href="#services"><b>الخدمات</b></a></li>
                <li class="nav-item"><a class="nav-link" href="#gallery"><b>معرض الصور</b></a></li>
                <li class="nav-item"><a class="nav-link" href="#contact"><b>اتصل بنا</b></a></li>
            </ul>
        </div>
    </header>

    <!-- شريط الترويج -->
    <section id="home" class="hero-section text-center text-white" style="background-image: url('travel.jpg'); background-size:cover; padding: 100px 0;">
        <div class="container">
            <h1 class="display-4"> استمتع بأجمل الوجهات السياحية</h1>
            <!-- زر الحجز مع صورة خلفية -->
            <a href="#booking-section" class="btn btn-primary btn-lg custom-button">احجز الآن</a>
        </div>
    </section>

    <div class="container">
        <!-- قسم الوجهات السياحية -->
        <section id="destinations" class="my-5">
    <h2 class="text-center">الوجهات السياحية</h2>
    <br>
    <div class="row">
        <?php
        $sql = "SELECT id, name, description, image FROM destinations";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            echo "<div class='col-md-4'>
                    <div class='card mb-4'>
                        <img src='".$row['image']."' class='card-img-top destination-img' alt='".$row['name']."'>
                        <div class='card-body'>
                            <h5 class='card-title'>".$row['name']."</h5>
                            <p class='card-text'>".$row['description']."</p>
                        </div>
                    </div>
                  </div>";
        }
        ?>
    </div>
</section>

<style>
    /* تخصيص التأثير على الصور */
    .destination-img {
        transition: transform 0.3s ease-in-out; /* لتحديد مدة تأثير التكبير */
    }

    .destination-img:hover {
        transform: scale(1.1); /* تكبير الصورة عند تمرير الماوس */
    }
</style>


        <!-- قسم الخدمات -->
        <section id="services" class="my-5">
            <h2 class="text-center">الخدمات</h2>
            <div class="row">
                <div class="col-md-3">
                    <div class="card mb-4 text-center">
                        <div class="card-body">
                            <h5 class="card-title">حجز الفنادق</h5>
                            <p class="card-text">نوفر أفضل العروض لحجز الفنادق</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mb-4 text-center">
                        <div class="card-body">
                            <h5 class="card-title">تنظيم الجولات السياحية</h5>
                            <p class="card-text">نقوم بتنظيم جولات ممتعة ومميزة</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mb-4 text-center">
                        <div class="card-body">
                            <h5 class="card-title">خدمات النقل</h5>
                            <p class="card-text">  VIP   <BR>نوفر خدمات نقل مريحة وآمنة</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mb-4 text-center">
                        <div class="card-body">
                            <h5 class="card-title">استشارات السفر</h5>
                            <p class="card-text">نقدم أفضل النصائح لاستمتاع برحلتك</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

       <section id="gallery" class="my-5">
    <h2 class="text-center">اكتشف خيارات التنقل لرحلتك القادمة </h2>
    <br>
    <div class="row">
        <div class="col-md-4 mb-4">
            <img src="img1.jpg" class="img-fluid gallery-img" alt="Gallery Image 1">
        </div>
        <div class="col-md-4 mb-4">
            <img src="img2.jpg" class="img-fluid gallery-img" alt="Gallery Image 2">
        </div>
        <div class="col-md-4 mb-4">
            <img src="img3.jpg" class="img-fluid gallery-img" alt="Gallery Image 3">
        </div>
        <div class="col-md-4 mb-4">
            <img src="img4.jpg" class="img-fluid gallery-img" alt="Gallery Image 4">
        </div>
        <div class="col-md-4 mb-4">
            <img src="img5.jpg" class="img-fluid gallery-img" alt="Gallery Image 5">
        </div>
        <div class="col-md-4 mb-4">
            <img src="img6.jpg" class="img-fluid gallery-img" alt="Gallery Image 6">
        </div>
    </div>
</section>

<style>
    .gallery-img {
        transition: transform 0.3s ease;
    }

    .gallery-img:hover {
        transform: scale(1.1);
    }
</style>


        <!-- قسم الشهادات -->
        <section id="testimonials" class="my-5">
            <h2 class="text-center">آراء العملاء</h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-body">
                        <p class="text-right">- أحمد</p>
                            <p class="card-text">"تجربة رائعة، كانت الرحلة منظمة بشكل جيد والخدمات ممتازة"</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-body">
                        <p class="text-right">- سارة</p>
                            <p class="card-text">"أفضل رحلة قمت بها، كل شيء كان مثالياً."</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- قسم الحجز -->
        <section id="booking-section" class="my-5">
            <h2 class="text-center">احجز رحلتك</h2>
            <div class="card">
                <div class="card-body">
                    <form method="post" action="index.php">
                        <input type="hidden" name="action" value="book">
                        <div class="form-group">
                            <label for="destination_id">الوجهة</label>
                            <select id="destination_id" name="destination_id" class="form-control" required>
                                <?php
                                $sql = "SELECT id, name FROM destinations";
                                $result = $conn->query($sql);
                                while($row = $result->fetch_assoc()) {
                                    echo "<option value='".$row['id']."'>".$row['name']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="travel_date">تاريخ السفر</label>
                            <input type="date" id="travel_date" name="travel_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="num_people">عدد الأشخاص</label>
                            <input type="number" id="num_people" name="num_people" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">احجز الآن</button>
                    </form>
                </div>
            </div>
        </section>

        <!-- قسم المعلومات العامة -->
        <section id="info" class="my-5">
            <h2 class="text-center">نصائح وإرشادات للسفر</h2>
            <p class="text-center">تأكد من حمل جميع المستندات اللازمة مثل جواز السفر والتأشيرة.</p>
            <p class="text-center">احرص على معرفة الطقس في الوجهة التي ستسافر إليها وتحضير الملابس المناسبة.</p>
            <p class="text-center">تعلم بعض العبارات الأساسية بلغة البلد الذي تزوره لتسهيل التواصل.</p>
        </section>

        <!-- قسم التواصل -->
        <section id="contact" class="my-5">
            <h2 class="text-center">اتصل بنا</h2>
            <div class="card">
                <div class="card-body">
                    <form method="post" action="contact.php">
                        <div class="form-group">
                            <label for="name">الاسم</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email">البريد الإلكتروني</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="message">رسالة</label>
                            <textarea id="message" name="message" class="form-control" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">إرسال</button>
                    </form>
                    <div class="mt-4">
                        <p class="text-center"><strong>رقم الهاتف:</strong> 133-476-7850</p>
                        <p class="text-center"> arab@travelco.com <strong>: البريد الإلكتروني</strong></p>
                        <p class="text-center"><strong> العنوان: </strong>  الأردن , عمان , العبدلي  </p>
                        <div class="text-center">
                            <a href="#" class="btn btn-primary">Facebook</a>
                            <a href="#" class="btn btn-primary">Instagram</a>
                            <a href="#" class="btn btn-primary">Twitter</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- التذييل -->
    <footer class="bg-light text-center py-4">
        <div class="container">
            <p>&copy; 2024 العربية للسياحة. جميع الحقوق محفوظة.</p>
        </div>
    </footer>

    <!-- السكربتات -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // تفعيل التمرير السلس عند الضغط على الروابط
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault();
            var target = this.hash;
            var $target = $(target);
            $('html, body').stop().animate({
                'scrollTop': $target.offset().top
            }, 900, 'swing', function() {
                window.location.hash = target;
            });
        });
    </script>
</body>
</html>
