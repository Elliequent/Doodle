<?php

// Create the search results when user enters serch term in the index.php and displays results on search.php

class SiteResultsProvider {

    private $con;

    public function __construct($con) {

        $this->con = $con;

    }

    public function getNumResults($term) {                                  // Displays number of search results for term searched

        $query = $this->con->prepare("SELECT COUNT(*) as total FROM sites WHERE title LIKE :term
                                        OR url LIKE :term
                                        OR keywords LIKE :term
                                        OR description LIKE :term");

        $searchTerm = "%" . $term . "%";
        $query->bindParam(":term", $searchTerm);
        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC);

        return $row["total"];

    }   // End of getNumResults function


    public function getResultsHTML($page, $pageSize, $term) {               // Outputs results from user search terms

        $fromLimit = ($page - 1) * $pageSize;
        // Page 1 : (1 - 1) *  20 = 0   - LIMIT 0, 20
        // Page 2 : (2 - 1) * 20 = 20   - LIMIT 20, 40
        // Page 3 : (3 - 1) * 20 = 40   - LIMIT 40, 60

        $query = $this->con->prepare("SELECT * FROM sites WHERE title LIKE :term
                                        OR url LIKE :term
                                        OR keywords LIKE :term
                                        OR description LIKE :term
                                        ORDER BY clicks DESC   
                                        LIMIT :fromLimit, :pageSize");

        $searchTerm = "%" . $term . "%";                                            
        $query->bindParam(":term", $searchTerm);
        $query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
        $query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);
        $query->execute();

        // HTML Output per result

        $resultsHTML = "<div class='siteResults'>";

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $id = $row['id'];
            $url = $row['url'];
            $title = $row['title'];
            $description = $row['description'];

            if($description == "" || $description == NULL) {                        // [+] This section for no description

                $resultsHTML .= "<div class='resultContainer'>
    
                                    <h3 class='title'>
                                        <a class='result' href='$url' data-linkId='$id'> $title </a>
                                    </h3>

                                    <span class='url'> $url </span>
                                    <span class='description'> No description supplied </span>

                                </div>";

            } else {

                $titleLimit = 55;                                               // Title character limit
                $urlLimit = 100;                                                // Url character limit
                $descriptionLimit = 200;                                        // Description character limit
                
    
                $title = $this->trimField($title, $titleLimit);
                $url = $this->trimField($url, $urlLimit);
                $description = $this->trimField($description, $descriptionLimit);
    
                $resultsHTML .= " <div class='resultContainer'>
    
                                    <h3 class='title'>
                                        <a class='result' href='$url' data-linkId='$id'> $title </a>
                                    </h3>
    
                                    <span class='url'> $url </span>
                                    <span class='description'> $description </span>
                
                                </div>";

            }

        }

        $resultsHTML .= "</div>";

        return $resultsHTML;

    }   // End of getResultsHTML function


    private function trimField($string, $characterLimit) {          // Trims url and description to defined character limit

        $dots = strlen($string) > $characterLimit ? "..." : "";
        
        return substr($string, 0, $characterLimit) . $dots;

    }   // End of trimField function

    
}

?>