<?
    require_once("include/ConcertoClient.php");
    
    $client = new ConcertoClient();
    $production = array(
        'server' => "http://signage.union.rpi.edu",
        'suffix' => "/content/render"
    );
    $devbox = array(
        'server' => 'http://senatedev.union.rpi.edu',
        'suffix' => '/bam/trunk/content/render/'
    );
    
    ///// The next line are a test of the ability to have connection abstraction //////
    // testable at: http://localhost/wisp/packages/Concerto.pkg/sandbox.php?action=viewfeed&id=1
    // $client->OpenConnection("ConcertoXMLFileConnection",$args);
    /////////////////////////////////////DONE////////////////////////////////////////////////
    
    // open a connection to the concerto server
    $client->OpenConnection("ConcertoHTTPConnection",$devbox);
    
    if ($_GET['action'] == "viewcontent") {
        $content = $client->GetContent($_GET['id']);
        echo $content;
    }
    else if ($_GET['action'] == "viewfeed") {
        $content = $client->GetFeed($_GET['id']);
        echo "<ul>";
        if ($content['items']) {
            foreach ($content['items'] as $item) {
                echo "<li><a href='sandbox.php?action=viewcontent&id=$item[guid]'>" . $item['title'] . "</a></li>";
            }
        }
        echo "</ul>";
        
        
        echo "<br><br><a href='sandbox.php'>back</a>";
    }
    else {
        $feeds = $client->GetFeeds();
        echo "<ul>";
        foreach ($feeds as $id => $feedname) {
            echo "<li><a href='sandbox.php?action=viewfeed&id=$id'>$feedname</a></li>\n";
        }
        echo "</ul>";
    }
?>