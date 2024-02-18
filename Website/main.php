<?php

function werte_von_string($str_anzahl) {
    $anzahl_arr = array();
    foreach(explode(".",$str_anzahl) as $key => $tag_str)$anzahl_arr[] = strval((ord(explode(" ",$tag_str)[0]) & 3) << 4)
    + strval((ord(explode(" ",$tag_str)[1]) & 3) << 2) + strval(ord(explode(" ",$tag_str)[2]) & 3);
    return $anzahl_arr;
}

// Gegebene Benutzerdate
$benutzerName = "Benutzer_A";
$benutzerPasswort = "Passwort_A";

// Benutzerdaten testen (bzw. "einloggen")
$conn = new mysqli("localhost","ams_benutzer","ams_passwort");
if ($conn->connect_error)die("Connection failed: " . $conn->connect_error);

// Mit Benutzerdaten zusammenhängende BenutzerID abrufen
$stmt = $conn->prepare("SELECT BenutzerID FROM ams.benutzer WHERE Name=? AND Passwort=?;");
$stmt->bind_param("ss",$benutzerName,$benutzerPasswort); $stmt->execute();
$res = $stmt->get_result();
if($res->num_rows >= 2 || $res->num_rows == 0)die("Benutzername oder Passwort falsch");
else $benutzerID = $res->fetch_row()[0];

// Liste alle Medikamente abrufen
$med_liste = $conn->query("SELECT MedID,MedName FROM ams.medikamenteliste;")->fetch_all();

/*

if(isset($_POST["aktion"])) {
    switch ($_POST["aktion"]) {
        case 'update_zeiten':
            $stmt = $conn->prepare("UPDATE ams.benutzer SET MorgenZeit=?, MittagZeit=?, AbendZeit=? WHERE BenutzerID=?");
            $stmt->bind_param("sssi",$_POST["morgen_zeit"],$_POST["mittag_zeit"],$_POST["abend_zeit"],$benutzerID);
            if(!$stmt->execute())die("Invalide zeiten (?)");
            break;
        case 'update_med':
            for($i = 0; $i < count(explode(";",$_POST["med_name"])); $i++){
                $exists = false;
                foreach($med_liste as $m)if((explode(";",$_POST["med_name"])[$i] == $m[1]))$exists=true;
                if(!$exists)die("???");
            }

            $stmt = $conn->prepare("SELECT medikamenteliste.MedName, medikamente.Anzahl FROM ams.medikamente 
            INNER JOIN ams.medikamenteliste ON ams.medikamenteliste.MedID=ams.medikamente.MedID 
            WHERE BenutzerID=?;");
            $stmt->bind_param("i",$benutzerID);
            $stmt->execute();
            $medikamente = $stmt->get_result()->fetch_all();
            for($i = 0; $i < count(explode(";",$_POST["med_name"]));$i++){
                $inboth = false;
                for($m = 0; $m < count($medikamente);$m++)
                if((explode(";",$_POST["med_name"]))[$i] == $medikamente[$m][0])$inboth = true;

                $in = 0;
                for($in = 0; $in < count($med_liste); $in++)if($med_liste[$in][1] == explode(";",$_POST["med_name"])[$i])break;
                $medID = $med_liste[$in][0];

                $arr = array();
                for($j = 0; $j < 7; $j++) {
                    array_push($arr,
                    strval((ord(explode(" ",explode(".",explode(";",$_POST["medn"])[$i])[$j])[0]) & 3) << 4) 
                    + strval((ord(explode(" ",explode(".",explode(";",$_POST["medn"])[$i])[$j])[1]) & 3) << 2) 
                    + strval(ord(explode(" ",explode(".",explode(";",$_POST["medn"])[$i])[$j])[2]) & 3));
                }

                if($inboth) {
                    //change from post/query
                    $stmt = $conn->prepare("UPDATE ams.medikamente 
                    SET medikamente.Anzahl = CHAR(?,?,?,?,?,?,?)
                    WHERE MedID=? AND BenutzerID=?;");
                    $stmt->bind_param("iiiiiiiii",$arr[0],$arr[1],$arr[2],$arr[3],$arr[4],$arr[5],$arr[6],$medID,$benutzerID);
                    $stmt->execute();
                } else {
                    //create from post
                    $stmt = $conn->prepare("INSERT INTO ams.medikamente 
                    (`BenutzerID`, `MedID`, `Anzahl`)
                    VALUES (CHAR(?,?,?,?,?,?,?)
                    WHERE MedID=? AND BenutzerID=?;");
                    $stmt->bind_param("iiiiiiiii",$arr[0],$arr[1],$arr[2],$arr[3],$arr[4],$arr[5],$arr[6],$medID,$benutzerID);
                    $stmt->execute();
                }
            }
            for($m = 0; $m < count($medikamente);$m++) {
                $inboth = false;
                for($i = 0; $i < count(explode(";",$_POST["med_name"]));$i++)
                if((explode(";",$_POST["med_name"]))[$i] == $medikamente[$m][0])$inboth = true;

                if(!$inboth) {
                    //delete from query
                    $in = 0;
                    for($in = 0; $in < count($med_liste); $in++)if($med_liste[$in][1] == explode(";",$_POST["med_name"])[$i])break;
                    $medID = $med_liste[$in][0];

                    $stmt = $conn->prepare("DELETE FROM ams.medikamente
                    WHERE MedID=? AND BenutzerID=?;");
                    $stmt->bind_param("ii",$medID,$benutzerID);
                }
            }
            break;
    }
}
*/
//

if(count($_POST) > 0 && isset($_POST["Aktion"])) {
    if(isset($_POST["Werte"]) && $_POST["Aktion"] == "Hinz") {
        $med_name = $_POST["Med_Name"];
        
        $MedID = -1;
        foreach ($med_liste as $med) {
            if($med_name == $med[1]) {
                $MedID = $med[0];
                break;
            }
        }
        if($MedID != -1) {
            $stmt = $conn->prepare("SELECT medikamente.MedID FROM ams.medikamente WHERE BenutzerID=?;");
            $stmt->bind_param("i",$benutzerID); $stmt->execute();
            $medikamente = $stmt->get_result()->fetch_all();

            $exists = false;
            foreach ($medikamente as $med) {
                if($MedID == $med[0]) {
                    $exists = true;
                    break;
                }
            }
            if(!$exists) {
                $anzahl = werte_von_string($_POST["Werte"]);

                $stmt = $conn->prepare("INSERT INTO ams.medikamente (BenutzerID,MedID,Anzahl) VALUES (?,?,CHAR(?,?,?,?,?,?,?));");
                $stmt->bind_param("iiiiiiiii",$benutzerID,$MedID,$anzahl[0],$anzahl[1],$anzahl[2],$anzahl[3],$anzahl[4],$anzahl[5],$anzahl[6]); 
                $stmt->execute();
            }
        }
    } else if($_POST["Aktion"] == "Entf") {
        $med_name = $_POST["Med_Name"];
        
        $MedID = -1;
        foreach ($med_liste as $med) {
            if($med_name == $med[1]) {
                $MedID = $med[0];
                break;
            }
        }
        if($MedID != -1) {
            $stmt = $conn->prepare("SELECT medikamente.MedID FROM ams.medikamente WHERE BenutzerID=?;");
            $stmt->bind_param("i",$benutzerID); $stmt->execute();
            $medikamente = $stmt->get_result()->fetch_all();

            $exists = false;
            foreach ($medikamente as $med) {
                if($MedID == $med[0]) {
                    $exists = true;
                    break;
                }
            }
            if($exists) {
                $stmt = $conn->prepare("DELETE FROM ams.medikamente WHERE MedID=? AND BenutzerID=?");
                $stmt->bind_param("ii", $MedID, $benutzerID); $stmt->execute();
            }
        }
    } else if(isset($_POST["Werte"]) && $_POST["Aktion"] == "Aendern") {
        foreach(explode(";",$_POST["Med_Name"]) as $key => $med_name) {
            $MedID = -1;
            foreach ($med_liste as $med) {
                if($med_name == $med[1]) {
                    $MedID = $med[0];
                    break;
                }
            }
            if($MedID != -1) {
                $stmt = $conn->prepare("SELECT medikamente.MedID FROM ams.medikamente WHERE BenutzerID=?;");
                $stmt->bind_param("i",$benutzerID); $stmt->execute();
                $medikamente = $stmt->get_result()->fetch_all();
    
                $exists = false;
                foreach ($medikamente as $med) {
                    if($MedID == $med[0]) {
                        $exists = true;
                        break;
                    }
                }
                if($exists) {
                    $anzahl = werte_von_string(explode(";",$_POST["Werte"])[$key]);

                    $stmt = $conn->prepare("UPDATE ams.medikamente SET Anzahl=CHAR(?,?,?,?,?,?,?) WHERE BenutzerID=? AND MedID=?;");
                    $stmt->bind_param("iiiiiiiii",$anzahl[0],$anzahl[1],$anzahl[2],$anzahl[3],$anzahl[4],$anzahl[5],$anzahl[6],$benutzerID,$MedID); 
                    $stmt->execute();
                }
            }  
        }
    } else if(isset($_POST["Werte"]) && $_POST["Aktion"] == "Zeiten") {
        $zeiten = explode(";",$_POST["Werte"]);
        $stmt = $conn->prepare("UPDATE ams.benutzer SET MorgenZeit=?, MittagZeit=?, AbendZeit=? WHERE BenutzerID=?");
        $stmt->bind_param("sssi",explode(";",$_POST["Werte"])[0],explode(";",$_POST["Werte"])[1],explode(";",$_POST["Werte"])[2],$benutzerID);
        $stmt->execute();
    }
}


// Mit BenutzerID zusammenhängende Daten wie Zeiten abrufen
$stmt = $conn->prepare("SELECT MorgenZeit, MittagZeit, AbendZeit FROM ams.benutzer WHERE BenutzerID=?;");
$stmt->bind_param("i",$benutzerID); $stmt->execute();
$benutzerDaten = $stmt->get_result()->fetch_row();

// Alle mit BenutzerID zusammenhängende Medikamente abrufen
$stmt = $conn->prepare("SELECT medikamenteliste.MedName, medikamente.Anzahl FROM ams.medikamente 
INNER JOIN ams.medikamenteliste ON ams.medikamenteliste.MedID=ams.medikamente.MedID 
WHERE BenutzerID=? ORDER BY medikamenteliste.MedName ASC;");
$stmt->bind_param("i",$benutzerID); $stmt->execute();
$medikamente = $stmt->get_result()->fetch_all();