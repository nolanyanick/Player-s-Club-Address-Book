<?php
    $xmlList = simplexml_load_file("members.xml") or die("Error: Cannot create object");
    $display_block = "";

    foreach($xmlList->name as $member) {
        $id = $member->id;
        $first = $member->first;
        $last = $member->last;

        $display_block .="<div style='width:50%; margin-left:auto; margin-right:auto; padding-bottom:5px; padding-top:5px;'>
        <p style='color:gray;border-bottom:2px darkgreen solid;font-weight:900;'>ID: " .$id. "<br>".
            "<span style='color:darkgreen;'>Name: " .$first. " " .$last. "</span></p></div>";
    };
    $display_block .= "<a href='addressBookMenu.html'><button style='margin-bottom:50px;'>return to main menu</button></a>";
?>
<!DOCTYPE html>
<html lang="en">
<link href="css/styles.css" type="text/css" rel="stylesheet" />
<head>    
    <title>View Members</title>
</head>
<body>
    <article class='col-6 col-s-12'>
        <h1>Members</h1>
    <?php
    echo $display_block;
    ?>
    </article>
    <footer class='col-12 col-s-12' >Copyright Player's Club &copy; 2018-2019</footer>
</body>
</html>