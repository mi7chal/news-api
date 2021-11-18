<?php
function isLogged($ssid)
{
    try {
        if (!isset($link)) {
            require_once 'connect.php';
        }

        $resultLogged = mysqli_query($link, "SELECT id, ssid, userId, expiration from sessionid WHERE ssid='$ssid'");
        if ($resultLogged) {
            if (mysqli_num_rows($resultLogged) > 1) {
                mysqli_query($link, "DELETE from sessionId WHERE ssid='$ssid'");
                return false;
            } else {
                $row = mysqli_fetch_array($resultLogged);
                $date1 = new DateTime("now");

                if ($row['expiration'] > $date1) {
                    unset($resultLogged);
                    unset($date1);
                    unset($row);
                    return true;
                } else {
                    unset($resultLogged);
                    unset($date1);
                    unset($row);
                    return false;
                }
            }
        } else {
            unset($resultLogged);
            return false;
        }
    } catch (Exception $e) {
        echo 'Błąd: ' . $e->getMessage();
    } finally {
        echo 'Nieznany błąd.';
    }
}
