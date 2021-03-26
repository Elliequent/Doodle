
<!DOCTYPE html>

<html lang="en">

<head>

    <!-- Title and Favicon -->
    <title>Doodle</title>
    <link rel="icon" type="image/png" href="Assets/Images/pageStart.png">

    <!-- meta -->
    <meta charset="UTF-8">
    <meta name="description" content="Doodle | The worlds search engine">
    <meta name="keywords" content="Search engine, web crawler, websites">
    <meta name="author" content="Ian Fraser">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- JavaScript -->
    <script src="https://kit.fontawesome.com/34d9021f0e.js"></script>

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="Assets/CSS/style.css">
    

</head>

<body>
    
    <div class="wrapper indexPage">

        <div class="mainSection">
        
            <!-- Logo -->
            <div class="logoContainer">

                <img src="Assets/Images/DoodleLogo.png" alt="Doodle Home Page">

            </div>

            <!-- Search Bar -->
            <div class="searchContainer">

                <form action="search.php" method="GET">
                    
                    <input type="text" class="searchBox" name="term" require>
                    <input type="submit" class="searchButton" value="Search">

                </form>
            </div>

        </div>

    </div>

</body>

</html>