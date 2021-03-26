<?php

include("config.php");
include("Classes/SiteResultsProvider.php");
include("Classes/ImagesResultsProvider.php");

    if(isset($_GET["term"])) {
        $term = $_GET["term"];
    } else {
        exit("You must enter a search term!");
    }

    $type = isset($_GET['type']) ? $_GET['type'] : "sites";
    $page = isset($_GET['page']) ? $_GET['page'] : 1;

?>

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
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
    <link rel="stylesheet" type="text/css" href="Assets/CSS/style.css">
    
</head>

<body>
    
    <div class="wrapper">

        <div class="header">

            <div class="headerContent">

                <!-- Logo Section -->
                <div class="logoContainer">

                    <a href="index.php"><img src="Assets/Images/DoodleLogo.png" alt="Doodle search Page Logo"></a>

                </div>

                <!-- Search Bar Section -->
                <div class="searchContainer">

                    <form action="search.php" method="GET">
                    
                        <div class="searchBarContainer">
                        
                            <input type="hidden" name="type" value="<?php echo $type; ?>">

                            <input type="text" class="searchBox" name="term" value="<?php echo $term ?>">

                            <button> <i class="fas fa-search searchIcon"></i> </button>
                        
                        </div>
                
                    </form>
                
                </div>

            </div>

            <!-- Tabs Section -->
            <div class="tabsContainer">

                <ul class="tabList">

                    <!-- Sets "active" CSS element to which ever tab is currently pressed -->
                    
                    <li class="<?php echo $type == 'sites' ? 'active' : ''; ?>"> 

                        <a href='<?php echo "search.php?term=$term&type=sites"; ?>'>  Sites </a> 

                    </li>

                    <li class="<?php echo $type == 'images' ? 'active' : ''; ?>"> 

                        <a href='<?php echo "search.php?term=$term&type=images"; ?>'>  Images </a> 

                    </li>

                </ul>

            </div>

        </div>

        <!-- Results section -->

        <div class="mainResultSection">
        
            <?php 
            
                if($type == "sites") {

                    $resultsProvider = new SiteResultsProvider($con); 
                    $pageSize = 20;
    
                }   else    {
                
                    $resultsProvider = new ImageResultsProvider($con); 
                    $pageSize = 30;

                }

                $numResults = $resultsProvider->getNumResults($term);

                echo " <p class='resultsCount'> $numResults results found </p>";
                echo $resultsProvider->getResultsHTML($page, $pageSize, $term);
            
            ?>

        </div>

        <!-- Pagination section -->

        <div class="paginationContainer">
            <div class="pageButtons">

                <div class="pageNumberContainer">
                    <img src="Assets/Images/pageStart.png">
                </div>

                <?php                   // Adds the "o"s to the pagination system

                    $pagesToShow = 10;
                    $numPages = ceil($numResults / $pageSize);              // Finds number of pages by search results (rounds up)
                    $pagesLeft = min($pagesToShow, $numPages);              // Take min of either value - eg 20 results = 2 pages

                    $currentPage = $page - floor($pagesToShow / 2);         // Calculates the number of pages to show either side of current page

                    if($currentPage < 1) {                                  // If there are less than 10 results display 1 page

                        $currentPage = 1;

                    }

                    if($currentPage + $pagesLeft > $numPages + 1) {         // Edge case - Where end of page system creates 9 results per page (Fix)

                        $currentPage = $numPages - $pagesLeft;

                    }

                    while($pagesLeft != 0 && $currentPage <= $numPages) {

                        if($currentPage == $page) {

                            echo "<div class='pageNumberContainer'>
                        
                                    <img src='Assets/Images/pageSelected.png'>
                                    <span class='pageNumber'> $currentPage </span>
                    
                                </div>";

                        } else {

                            echo "<div class='pageNumberContainer'>

                                <a href='search.php?term=$term&type=$type&page=$currentPage'>
                                    <img src='Assets/Images/page.png'>
                                    <span class='pageNumber'> $currentPage </span>
                                </a>
                        
                            </div>";

                        }

                            $currentPage++;
                            $pagesLeft--;

                    }

                ?>

                <div class="pageNumberContainer">
                    <img src="Assets/Images/pageEnd.png">
                </div>

            </div>
        </div>

    </div>

    <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
    <script type="text/javascript" src="Assets/JavaScript/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>

</body>

</html>