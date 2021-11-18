<?php
if ($link) {
    $insertViews = mysqli_query($link, "INSERT IGNORE INTO wyswietlenia(SELECT NULL, id, 0 FROM artykuly) ");
    $insertTodayViews = mysqli_query($link, "INSERT IGNORE INTO wyswietleniadzis(SELECT NULL, id, 0, CURRENT_DATE FROM artykuly) ");
    $countViews = mysqli_query($link, "SELECT COUNT(*) FROM wyswietleniadzis WHERE data=CURRENT_DATE");
    $row = mysqli_fetch_assoc($countViews);
    $views = $row['COUNT(*)'];
    if($views==0){
        $deleteViews = mysqli_query($link, "DELETE FROM wyswietleniadzis WHERE true");
        mysqli_query($link, "INSERT INTO wyswietleniadzis (SELECT NULL, id, 0, CURRENT_DATE FROM artykuly)");
    }
//bez close
}
