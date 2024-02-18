<!DOCTYPE html>

<?php

include ('./main.php');

?>


<html lang="DE">
    <head>
        <meta charset="UTF-8"/>
        <link rel="stylesheet" type="text/css" href="./style.css" />
    </head>
    <body>
        <h2>AMS - Automatischer Medikamenten Spender</h2>
        <br/>
        <main>
            <article id="zeiten_article">
                <h3>Zeitanzeige</h3>
                <input type="time" id="morgen_zeit" value="<?=substr($benutzerDaten[0],0,5)?>"/>
                <input type="time" id="mittag_zeit" value="<?=substr($benutzerDaten[1],0,5)?>"/>
                <input type="time" id="abend_zeit" value="<?=substr($benutzerDaten[2],0,5)?>"/>
                <br/><br/>
                <button onclick="zeit_aendern();">Zeiten ändern</button>
            </article>
            <br/><br/>
            <article id="medikamente">
                <h3>Medikamentenanzeige</h3>
                <table id="med_table">
                    <thead>
                        <tr>
                            <th>Medikamente</th>
                            <th colspan="3">Mo</th>
                            <th colspan="3">Di</th>
                            <th colspan="3">Mi</th>
                            <th colspan="3">Do</th>
                            <th colspan="3">Fr</th>
                            <th colspan="3">Sa</th>
                            <th colspan="3">So</th>
                            <th colspan="1">Entf</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        for($i = 0; $i < count($medikamente); $i++) {
                            $med_row = $medikamente[$i];
                            echo "<tr><td>".$med_row[0]."</td>";
                            for($tag = 0; $tag < 7; $tag++) {
                                echo "<td><input type='number' value='".strval((ord($med_row[1][$tag])&(3<<4))>>4)."'/></td>"
                                ."<td><input type='number' value='".strval((ord($med_row[1][$tag])&(3<<2))>>2)."'/></td>"
                                ."<td><input type='number' value='".strval(ord($med_row[1][$tag])&3)."'/></td>";
                            }
                            echo "<td><button class=\"del_button\" onclick='med_entf(".strval($i).")'>&#x2718</button></td>"
                            ."</tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <br>
                <button onclick="med_aendern();">Medikamente ändern</button>
                <button onclick="window.location.href='./add.php'">Medikament hinzufügen</button>
            </article>
            <br/><br/>
            <button onclick="window.location.href = window.location.href;">Refresh</button>
        </main>
        <script src="./main.js"></script>
    </body>
</html>