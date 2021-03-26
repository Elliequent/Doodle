<?php

include("config.php");
include("Classes/DomDocumentParser.php");

$alreadyCrawled = array();                                          // Links crawled
$crawling = array();                                                // Links to be crawled
$alreadyFoundImages = array();                                      // Images crawled


function insertLink($url, $title, $description, $keywords) {        // Adds links to database

    global $con;

    // Note: This is a more secure version of SQL as it prevents SQL injection into databases
    $query = $con->prepare("INSERT INTO sites(url, title, description, keywords)
							VALUES(:url, :title, :description, :keywords)");

	$query->bindParam(":url", $url);
	$query->bindParam(":title", $title);
	$query->bindParam(":description", $description);
	$query->bindParam(":keywords", $keywords);

    return $query->execute();                                       // Returns if SQL sucessful

}   // End of insertLink function


function insertImage($url, $src, $alt, $title) {        // Adds images to database

    global $con;

    $query = $con->prepare("INSERT INTO images(siteUrl, imageUrl, alt, title)
							VALUES(:siteUrl, :imageUrl, :alt, :title)");

	$query->bindParam(":siteUrl", $url);
	$query->bindParam(":imageUrl", $src);
	$query->bindParam(":alt", $alt);
	$query->bindParam(":title", $title);

    $query->execute();

}   // End of insertImage function


function linkExists($url) {                                         // Checks if link already exits in the database

    global $con;

    $query = $con->prepare("SELECT * FROM sites WHERE url = :url");
    $query->bindParam(":url", $url);
    $query->execute();

    return $query->rowCount() != 0;                                      // Returns value if url exits in database   

}   // End of insertLink function


function createLink($src, $url)   {                     // Creates the URL link from the crawled data

    $scheme = parse_url($url)['scheme'];                // adds http / https
    $host = parse_url($url)['host'];                    // adds wwww.websitename.com

    if(substr($src, 0, 2) == "//") {                    // If link starts with // add scheme

        $src = $scheme . ":" . $src;                    

    } elseif(substr($src, 0, 1) == "/") {               // If link starts with / add scheme and host

        $src = $scheme . "://" . $host . $src;          

    } elseif(substr($src, 0, 2) == "./") {              // If the link starts with ./ add scheme and host and directory path

        $src = $scheme . "://" . $host . dirname(parse_url($url)['path']) . substr($src, 1);

    } elseif(substr($src, 0, 3) == "../") {              // If the link starts with ./ add scheme and host and a /

        $src = $scheme . "://" . $host . "/" . $src;

    } elseif(substr($src, 0, 5) != "https" && substr($src, 0, 4) != "http") {    // If the link does not cover any of the above

        $src = $scheme . "://" . $host . "/" . $src;

    }

    return $src;

}   // End of createLink function


function getDetails($url) {                                         // Extracts the title tags and meta data for each link crawled
                                                                    // Then adds extracted data to database
    // Get Link Titles

    $parser = new DomDocumentParser($url);                          // Creating new DomDocumentParser.php object
    $titleArray = $parser->getTitleTags();                          // Calling function from DomDocumentParser.php

    if(sizeof($titleArray) == 0 || $titleArray->item(0) == NULL) {

        return;                                                     // Removes errors with NULLs or empty arrays

    }

    $title = $titleArray->item(0)->nodeValue;                       // Getting value of each title added to array
    $title = str_replace("\n", "", $title);                         // Removes \n new lines from title

    if($title == "") {

        return;

    }

    // Get Link Meta Data

    $description = "";
    $keywords = "";

    $metasArray = $parser->getMetaTags();                           // Calling function from DomDocumentParser.php

    foreach($metasArray as $meta) {                                 // For each piece of meta data

        if($meta->getAttribute("name") == "description") {          // Collects website description

            $description = $meta->getAttribute("content");

        }

        if($meta->getAttribute("name") == "keywords") {             // Collects website keywords

            $keywords = $meta->getAttribute("content");

        }

    }

    $description = str_replace("\n", "", $description);             // Removes new lines (returns) in meta data
    $keywords = str_replace("\n", "", $keywords);

    if(linkExists($url)) {

        echo "[-] $url - Already exits in database. <br>";

    } elseif (insertLink($url, $title, $description, $keywords)) {

        echo "[+] $url - Added to database successfully. <br>";

    } else {

        echo "[?] $url - Unknown error has occured. <br>";

    }

    // Get Image Links

    $imageArray = $parser->getImages();                             // Calling function from DomDocumentParser.php

    foreach($imageArray as $image) {

        $src = $image->getAttribute("src");  
        $alt = $image->getAttribute("alt");
        $title = $image->getAttribute("title");

        if(!$title && !$alt) {

            continue;                                               // If not title or alt ignore this image

        }

        global $alreadyFoundImages;

        $src = createLink($src, $url);                              // Converts link to absolute link

        if(!in_array($src, $alreadyFoundImages)) {

            $alreadyFoundImages[] = $src;

            insertImage($url, $src, $alt, $title);

        }

    }
    
}   // End of getDetails Function


function followLinks($url) {                                        // Takes URLs from crawler and follows them to add to database

    global $alreadyCrawled;                                         // By placing global here this means anything applied here
    global $crawling;                                               // applies to every function with this variable
    
    $parser = new DomDocumentParser($url);                          // Creating new DomDocumentParser.php object
    $linkList = $parser->getLink();                                 // Calling function from DomDocumentParser.php

    foreach($linkList as $link) {                                   // Foreach link found

        $href = $link->getAttribute("href");                        // look for href in each link

        if(strpos($href, "#") !== false) {                          // If webcrawl finds deadlinks
           
            continue;

        }   elseif(substr($href, 0, 11) == "javascript:")   {       // If Webcrawl finds javascript link
            
            continue;

        }

        $href = createLink($href, $url);

        if(!in_array($href, $alreadyCrawled)) {                     // If the link is not in alreadyCrawled array

            $alreadyCrawled[] = $href;                              // Add to alreadyCrawled
            $crawling[] = $href;

            getDetails($href);                                      // Call to getDetails above

        }   //else return;  (add to limit cycle to one page)

    }

    array_shift($crawling);                                         // Removes last item in the array

    foreach($crawling as $site) {

        followLinks($site);                                         // recursively calls funtion to follow all links found

    }

}   // End of followLinks Function

$startUrl = "https://www.amazon.co.uk/";
followLinks($startUrl);

?>