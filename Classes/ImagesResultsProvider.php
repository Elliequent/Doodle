<?php

// Create the search results when user enters serch term in the index.php and displays results on search.php

class ImageResultsProvider {

    private $con;

    public function __construct($con) {

        $this->con = $con;

    }

    public function getNumResults($term) {                                          // Displays number of search results for term searched

        $query = $this->con->prepare("SELECT COUNT(*) as total FROM images WHERE (title LIKE :term OR alt LIKE :term) AND broken = 0");

        $searchTerm = "%" . $term . "%";
        $query->bindParam(":term", $searchTerm);
        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC);

        return $row["total"];

    }   // End of getNumResults function


    public function getResultsHTML($page, $pageSize, $term) {                       // Outputs results from user search terms

        $fromLimit = ($page - 1) * $pageSize;
        // Page 1 : (1 - 1) *  20 = 0   - LIMIT 0, 20
        // Page 2 : (2 - 1) * 20 = 20   - LIMIT 20, 40
        // Page 3 : (3 - 1) * 20 = 40   - LIMIT 40, 60

        $query = $this->con->prepare("SELECT * FROM images WHERE (title LIKE :term OR alt LIKE :term) AND broken = 0
                                    ORDER BY clicks DESC   
                                    LIMIT :fromLimit, :pageSize");

        $searchTerm = "%" . $term . "%";                                            
        $query->bindParam(":term", $searchTerm);
        $query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
        $query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);
        $query->execute();

        // HTML Output per result

        $resultsHTML = "<div class='imageResults'>";

        $count = 0;

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $count++;                                                               // Counts while loop iterations

            $id = $row['id'];
            $imageUrl = $row['imageUrl'];
            $siteUrl = $row['siteUrl'];
            $title = $row['title'];
            $alt = $row['alt'];

            if($title) {

                $displayText = $title;

            }   else if($alt)  {

                $displayText = $alt;

            }   else    {

                $displayText = $imageUrl;

            }                                                                   // BELOW - image$count allows unique classes to each img

            $resultsHTML .= " <div class='gridItem image$count'>
    
                                <a href='$imageUrl' data-fancybox 
                                                    data-caption='$displayText' 
                                                    data-siteurl='$siteUrl'>

                                <script>

                                $(document).ready(function() {

                                    loadImage(\"$imageUrl\", \"image$count\");

                                });

                                </script>

                                    <span class='details'> $displayText </span>

                                </a>
                
                            </div>";

        }

        $resultsHTML .= "</div>";

        return $resultsHTML;

    }   // End of getResultsHTML function

}

?>