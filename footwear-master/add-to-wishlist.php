<!DOCTYPE HTML>
<html>
<head>
    <title>Footwear - Free Bootstrap 4 Template by Colorlib</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Rokkitt:100,300,400,700" rel="stylesheet">
    
    <!-- CSS Bestanden -->
    <link rel="stylesheet" href="css/animate.css"> <!-- Animatie effecten -->
    <link rel="stylesheet" href="css/icomoon.css"> <!-- Iconen -->
    <link rel="stylesheet" href="css/ionicons.min.css"> <!-- Ioniconen -->
    <link rel="stylesheet" href="css/bootstrap.min.css"> <!-- Bootstrap basis stijlen -->

    <link rel="stylesheet" href="css/magnific-popup.css"> <!-- Voor pop-up vensters -->
    <link rel="stylesheet" href="css/flexslider.css"> <!-- Voor flexibele sliders -->
    <link rel="stylesheet" href="css/owl.carousel.min.css"> <!-- Voor carousel effecten -->
    <link rel="stylesheet" href="css/owl.theme.default.min.css"> <!-- Themapakket voor de carousel -->
    
    <link rel="stylesheet" href="css/bootstrap-datepicker.css"> <!-- Voor datumpickers -->
    <link rel="stylesheet" href="fonts/flaticon/font/flaticon.css"> <!-- Voor font iconen -->

    <link rel="stylesheet" href="css/style.css"> <!-- Eigen stijlen -->
</head>
<body>
    
<div class="colorlib-loader"></div> <!-- Loader voor de pagina -->

<div id="page">
    <nav class="colorlib-nav" role="navigation">
        <div class="top-menu">
            <div class="container">
                <div class="row">
                    <div class="col-sm-7 col-md-9">
                        <!-- Logo link naar de homepage -->
                        <div id="colorlib-logo"><a href="index.php">Footwear</a></div> <!-- Link naar index.php -->
                    </div>
                    <div class="col-sm-5 col-md-3">
                        <!-- Zoekfunctie -->
                        <form action="#" class="search-wrap">
                            <div class="form-group">
                                <input type="search" class="form-control search" placeholder="Search"> <!-- Zoekveld -->
                                <button class="btn btn-primary submit-search text-center" type="submit"><i class="icon-search"></i></button> <!-- Zoekknop -->
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 text-left menu-1">
                        <!-- Navigatiemenu -->
                        <ul>
                            <li><a href="index.php">Home</a></li> <!-- Link naar index.php -->
                            <li class="has-dropdown active">
                                <a href="men.php">Men</a> <!-- Link naar de mannen sectie -->
                                <ul class="dropdown">
                                    <li><a href="product-detail.php">Product Detail</a></li> <!-- Link naar productdetails -->
                                    <li><a href="cart.php">Shopping Cart</a></li> <!-- Link naar winkelwagentje -->
                                    <li><a href="checkout.php">Checkout</a></li> <!-- Link naar afrekenpagina -->
                                    <li><a href="order-complete.php">Order Complete</a></li> <!-- Link naar bevestiging na bestelling -->
                                    <li><a href="add-to-wishlist.php">Wishlist</a></li> <!-- Link naar verlanglijst -->
                                </ul>
                            </li>
                            <li><a href="women.php">Women</a></li> <!-- Link naar de vrouwen sectie -->
                            <li><a href="about.php">About</a></li> <!-- Link naar over ons -->
                            <li><a href="contact.php">Contact</a></li> <!-- Link naar contactpagina -->
                            <li class="cart"><a href="cart.php"><i class="icon-shopping-cart"></i> Cart [0]</a></li> <!-- Link naar winkelwagentje met aantal artikelen -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="sale">
            <div class="container">
                <div class="row">
                    <div class="col-sm-8 offset-sm-2 text-center">
                        <div class="row">
                            <div class="owl-carousel2"> <!-- Carousel voor aanbiedingen -->
                                <div class="item">
                                    <div class="col">
                                        <h3><a href="#">25% off (Almost) Everything! Use Code: Summer Sale</a></h3> <!-- Aanbiedingstekst -->
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="col">
                                        <h3><a href="#">Our biggest sale yet 50% off all summer shoes</a></h3> <!-- Tweede aanbieding -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Breadcrumbs voor navigatie -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col">
                    <p class="bread"><span><a href="index.php">Home</a></span> / <span>My Wishlist</span></p> <!-- Link naar index.php -->
                </div>
            </div>
        </div>
    </div>

    <div class="colorlib-product">
        <div class="container">
            <div class="row row-pb-lg">
                <div class="col-md-10 offset-md-1">
                    <div class="process-wrap"> <!-- Stappen in het bestelproces -->
                        <div class="process text-center active">
                            <p><span>01</span></p>
                            <h3>Shopping Cart</h3>
                        </div>
                        <div class="process text-center">
                            <p><span>02</span></p>
                            <h3>Checkout</h3>
                        </div>
                        <div class="process text-center">
                            <p><span>03</span></p>
                            <h3>Order Complete</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-pb-lg">
                <div class="col-md-12">
                    <div class="product-name d-flex"> <!-- Productnamen sectie -->
                        <div class="one-forth text-left px-4">
                            <span>Product Details</span>
                        </div>
                        <div class="one-eight text-center">
                            <span>Price</span> <!-- Prijs -->
                        </div>
                        <div class="one-eight text-center">
                            <span>Quantity</span> <!-- Aantal -->
                        </div>
                        <div class="one-eight text-center">
                            <span>Total</span> <!-- Totaalprijs -->
                        </div>
                        <div class="one-eight text-center px-4">
                            <span>Remove</span> <!-- Verwijderknop -->
                        </div>
                    </div>
                    <div class="product-cart d-flex"> <!-- Specifieke productinformatie in het winkelwagentje -->
                        <div class="one-forth">
                            <div class="product-img" style="background-image: url(images/item-6.jpg);">
                            </div>
                            <div class="display-tc">
                                <h3>Product Name</h3> <!-- Naam van het product -->
                            </div>
                        </div>
                        <div class="one-eight text-center">
                            <div class="display-tc">
                                <span class="price">$68.00</span> <!-- Prijs van het product -->
                            </div>
                        </div>
                        <div class="one-eight text-center">
                            <div class="display-tc">
                                <input type="text" id="quantity" name="quantity" class="form-control input-number text-center" value="1" min="1" max="100"> <!-- Hoeveelheid invoer -->
                            </div>
                        </div>
                        <div class="one-eight text-center">
                            <div class="display-tc">
                                <span class="price">$120.00</span> <!-- Totaalprijs -->
                            </div>
                        </div>
                        <div class="one-eight text-center">
                            <div class="display-tc">
                                <a href="#" class="closed"></a> <!-- Verwijderknop voor het product -->
                            </div>
                        </div>
                    </div>
                    <!-- Hier kunnen meer producten aan het winkelwagentje worden toegevoegd -->
                </div>
            </div>
            <div class="row">
                <div class="col-sm-8 offset-sm-2 text-center colorlib-heading colorlib-heading-sm">
                    <h2>Shop more</h2> <!-- Sectie om meer producten te winkelen -->
                </div>
            </div>
            <div class="row">
                <!-- Voorbeeld van producten -->
                <div class="col-md-3 col-lg-3 mb-4 text-center">
                    <div class="product-entry border">
                        <a href="#" class="prod-img">
                            <img src="images/item-1.jpg" class="img-fluid" alt="Product afbeelding"> <!-- Productafbeelding -->
                        </a>
                        <div class="desc">
                            <h2><a href="#">Men's Sneakers</a></h2> <!-- Productnaam -->
                            <span class="price">$139.00</span> <!-- Productprijs -->
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-lg-3 mb-4 text-center">
                    <div class="product-entry border">
                        <a href="#" class="prod-img">
                            <img src="images/item-2.jpg" class="img-fluid" alt="Product afbeelding"> <!-- Productafbeelding -->
                        </a>
                        <div class="desc">
                            <h2><a href="#">Women's Sneakers</a></h2> <!-- Productnaam -->
                            <span class="price">$139.00</span> <!-- Productprijs -->
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-lg-3 mb-4 text-center">
                    <div class="product-entry border">
                        <a href="#" class="prod-img">
                            <img src="images/item-3.jpg" class="img-fluid" alt="Product afbeelding"> <!-- Productafbeelding -->
                        </a>
                        <div class="desc">
                            <h2><a href="#">Men's Taja Commissioner</a></h2> <!-- Productnaam -->
                            <span class="price">$139.00</span> <!-- Productprijs -->
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-lg-3 mb-4 text-center">
                    <div class="product-entry border">
                        <a href="#" class="prod-img">
                            <img src="images/item-4.jpg" class="img-fluid" alt="Product afbeelding"> <!-- Productafbeelding -->
                        </a>
                        <div class="desc">
                            <h2><a href="#">Russ Men's Sneakers</a></h2> <!-- Productnaam -->
                            <span class="price">$139.00</span> <!-- Productprijs -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer id="colorlib-footer" role="contentinfo"> <!-- Voettekst -->
        <div class="container">
            <div class="row row-pb-md">
                <div class="col footer-col colorlib-widget">
                    <h4>About Footwear</h4>
                    <p>Even the all-powerful Pointing has no control about the blind texts it is an almost unorthographic life</p> <!-- Informatie over het merk -->
                    <p>
                        <ul class="colorlib-social-icons">
                            <li><a href="#"><i class="icon-twitter"></i></a></li> <!-- Twitter link -->
                            <li><a href="#"><i class="icon-facebook"></i></a></li> <!-- Facebook link -->
                            <li><a href="#"><i class="icon-linkedin"></i></a></li> <!-- LinkedIn link -->
                            <li><a href="#"><i class="icon-dribbble"></i></a></li> <!-- Dribbble link -->
                        </ul>
                    </p>
                </div>
                <div class="col footer-col colorlib-widget">
                    <h4>Customer Care</h4>
                    <p>
                        <ul class="colorlib-footer-links">
                            <li><a href="#">Contact</a></li> <!-- Link naar contact -->
                            <li><a href="#">Returns/Exchange</a></li> <!-- Link naar retouren -->
                            <li><a href="#">Gift Voucher</a></li> <!-- Link naar cadeaubonnen -->
                            <li><a href="#">Wishlist</a></li> <!-- Link naar verlanglijst -->
                            <li><a href="#">Special</a></li> <!-- Link naar speciale aanbiedingen -->
                            <li><a href="#">Customer Services</a></li> <!-- Link naar klantenservice -->
                            <li><a href="#">Site maps</a></li> <!-- Link naar sitemap -->
                        </ul>
                    </p>
                </div>
                <div class="col footer-col colorlib-widget">
                    <h4>Information</h4>
                    <p>
                        <ul class="colorlib-footer-links">
                            <li><a href="#">About us</a></li> <!-- Informatie over ons -->
                            <li><a href="#">Delivery Information</a></li> <!-- Leveringsinformatie -->
                            <li><a href="#">Privacy Policy</a></li> <!-- Privacybeleid -->
                            <li><a href="#">Support</a></li> <!-- Ondersteuning -->
                            <li><a href="#">Order Tracking</a></li> <!-- Bestelling volgen -->
                        </ul>
                    </p>
                </div>

                <div class="col footer-col">
                    <h4>News</h4>
                    <ul class="colorlib-footer-links">
                        <li><a href="blog.php">Blog</a></li> <!-- Link naar blog -->
                        <li><a href="#">Press</a></li> <!-- Link naar pers -->
                        <li><a href="#">Exhibitions</a></li> <!-- Link naar tentoonstellingen -->
                    </ul>
                </div>

                <div class="col footer-col">
                    <h4>Contact Information</h4>
                    <ul class="colorlib-footer-links">
                        <li>291 South 21th Street, <br> Suite 721 New York NY 10016</li> <!-- Adres -->
                        <li><a href="tel://1234567920">+ 1235 2355 98</a></li> <!-- Telefoonnummer -->
                        <li><a href="mailto:info@yoursite.com">info@yoursite.com</a></li> <!-- E-mailadres -->
                        <li><a href="#">yoursite.com</a></li> <!-- Website link -->
                    </ul>
                </div>
            </div>
        </div>
        <div class="copy">
            <div class="row">
                <div class="col-sm-12 text-center">
                    <p>
                        <span><!-- Link terug naar Colorlib kan niet worden verwijderd. Template is gelicentieerd onder CC BY 3.0. -->
                        Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="icon-heart" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
                        <!-- Link terug naar Colorlib kan niet worden verwijderd. Template is gelicentieerd onder CC BY 3.0. --></span> 
                        <span class="block">Demo Images: <a href="http://unsplash.co/" target="_blank">Unsplash</a> , <a href="http://pexels.com/" target="_blank">Pexels.com</a></span>
                    </p>
                </div>
            </div>
        </div>
    </footer>
</div>

<div class="gototop js-top"> <!-- Terug naar boven knop -->
    <a href="#" class="js-gotop"><i class="ion-ios-arrow-up"></i></a>
</div>

<!-- jQuery en andere JavaScript bestanden -->
<script src="js/jquery.min.js"></script> <!-- jQuery bibliotheek -->
<script src="js/popper.min.js"></script> <!-- Popper.js voor pop-ups -->
<script src="js/bootstrap.min.js"></script> <!-- Bootstrap basis JavaScript -->
<script src="js/jquery.easing.1.3.js"></script> <!-- Easing effecten -->
<script src="js/jquery.waypoints.min.js"></script> <!-- Scroll effecten -->
<script src="js/jquery.flexslider-min.js"></script> <!-- Flexslider -->
<script src="js/owl.carousel.min.js"></script> <!-- Carousel -->
<script src="js/jquery.magnific-popup.min.js"></script> <!-- Magnific Popup -->
<script src="js/magnific-popup-options.js"></script> <!-- Opties voor Magnific Popup -->
<script src="js/bootstrap-datepicker.js"></script> <!-- Date Picker -->
<script src="js/jquery.stellar.min.js"></script> <!-- Parallax effecten -->
<script src="js/main.js"></script> <!-- Eigen JavaScript -->

</body>
</html>
