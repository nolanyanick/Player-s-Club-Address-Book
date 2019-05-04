<?php
    //production server
    $mysqli = mysqli_connect("localhost", "lisabalbach_yanickn", "CIT190102", "lisabalbach_Yanick");
    
    //test
    //$mysqli = mysqli_connect("localhost", "root", "", "players");

    if(mysqli_connect_errno()) {
        printf("Connect failed: %n", mysqli_connect_error());
        exit();
    }
    $get_master_name = "SELECT * FROM master_name";
    $get_master_res = mysqli_query($mysqli, $get_master_name) or die(mysqli_error($mysqli));

    if(mysqli_num_rowS($get_master_res) < 1) {
        $display_block = "<h1>View Members</h1>
                            <p class=\"subHeader\" style=\"margin-bottom:0px;padding-bottom:30px; font-variant:small-caps;\"><strong><em>Sorry, no records to select!</em></strong></br>
                            <a href='addressBookMenu.html'><button style='margin-top:15px;'>return to main menu</button></a></p>";
    }
    else {
        $xml = "<memberList>";
        while ($r = mysqli_fetch_array($get_master_res)) {
            $xml .= "<name>";
            $xml .= "<id>".$r['id']."</id>";
            $xml .= "<first>".$r['first_name']."</first>";
            $xml .= "<last>".$r['last_name']."</last>";
            $xml .= "</name>";

        }
        $display_block = "<h1>a members list has been created</h1>
                <p style='text-align:center; padding-bottom:25px; padding-top:8px; margin-bottom:0;'>
                <a href='viewMembers.php'><button>view member list</button></a>
                <a href='addressBookMenu.html'><button>return to main menu</button></a>";

                $xml .= "</memberList>";
                $sxe = new SimpleXMLElement($xml);
                $sxe ->asXML("members.xml");
        }
?>
<!DOCTYPE html>
<html lang="en">
<link href="css/styles.css" type="text/css" rel="stylesheet" />
<head>    
    <title>View Members</title>
</head>
<body>
    <article class=' content col-6 col-s-12'>
    <?php
    echo $display_block;
    ?>
</article>
    <footer class="col-12 col-s-12">Copyright Player's Club &copy; 2018-2019</footer>
</body>
</html>