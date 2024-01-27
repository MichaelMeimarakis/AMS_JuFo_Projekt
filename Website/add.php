<?php
$_POST = array();
include ('./main.php');

$stmt = $conn->prepare("SELECT MedName FROM ams.medikamenteliste WHERE MedID NOT IN (SELECT MedID FROM ams.medikamente WHERE BenutzerID=?);");
$stmt->bind_param("i",$benutzerID); $stmt->execute();
$unused_meds = $stmt->get_result()->fetch_all();

?>

<html lang="DE">
    <head>
        <meta charset="UTF-8"/> 
        <link rel="stylesheet" type="text/css" href="./style.css" />
    </head>
    <body>
        <h2>AMS - Automatischer Medikamenten Spender (Hinzufügen)</h2><br/>
        <main>
            <?=var_dump($unused_meds)?>
            <div>
            <label for="select_med">Medikament: </label>
            <input list="unused_meds" placeholder="Medikament" id="select_med">
            <br/><br/>
            <datalist id="unused_meds">
                <?php foreach($unused_meds as $med)echo "<option>".$med[0]."</option>";?>
            </datalist>
            </div>

            <table id="med_table">
                <thead>
                    <tr>
                        <th colspan="3">Mo</th>
                        <th colspan="3">Di</th>
                        <th colspan="3">Mi</th>
                        <th colspan="3">Do</th>
                        <th colspan="3">Fr</th>
                        <th colspan="3">Sa</th>
                        <th colspan="3">So</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="number" value="0"></td><td><input type="number" value="0"></td><td><input type="number" value="0"></td>
                        <td><input type="number" value="0"></td><td><input type="number" value="0"></td><td><input type="number" value="0"></td>
                        <td><input type="number" value="0"></td><td><input type="number" value="0"></td><td><input type="number" value="0"></td>
                        <td><input type="number" value="0"></td><td><input type="number" value="0"></td><td><input type="number" value="0"></td>
                        <td><input type="number" value="0"></td><td><input type="number" value="0"></td><td><input type="number" value="0"></td>
                        <td><input type="number" value="0"></td><td><input type="number" value="0"></td><td><input type="number" value="0"></td>
                        <td><input type="number" value="0"></td><td><input type="number" value="0"></td><td><input type="number" value="0"></td>
                    </tr>
                </tbody>
            </table>
            <br/>
            <button onclick="window.location.href='./index.php'">Abbrechen</button>
            <button onclick="med_hinz()">Hinzufügen</button>
        </main>
        <script src="./main.js"></script>
    </body>
</html>