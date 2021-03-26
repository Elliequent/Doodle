<?php

class DomDocumentParser {

    private $doc;

    public function __construct($url) {                                         // Constructor
        
        $options = array( 
            'http' => array('method' => "GET", 'header' => "User-Agent: doodleBot/0.1\n")
            // Websites require a user agent to know who visited = doodleBot v1.0
        );

        $context = stream_context_create($options);

        $this->doc = new DomDocument();
        @$this->doc->loadHTML(file_get_contents($url, false, $context));        // @ at start supresses error messages

    }

    public function getLink() {

        return $this->doc->getElementsByTagName("a");                           // return elements HTML "a" tag elements

    }

    public function getTitleTags() {

        return $this->doc->getElementsByTagName("title");                       // return elements HTML "title" tag elements

    }

    public function getMetaTags() {

        return $this->doc->getElementsByTagName("meta");                        // return elements HTML "meta" tag elements

    }

    public function getImages() {

        return $this->doc->getElementsByTagName("img");                        // return elements HTML "img" tag elements

    }

} // End of Class

?>