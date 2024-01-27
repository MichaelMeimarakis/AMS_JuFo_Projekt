let med_inputs = () => document.querySelectorAll("#med_table tbody tr td input");
med_inputs().forEach(z => { test_med(); oninput = () => { test_med(); }; });
let zeiten = [document.getElementById("morgen_zeit"),document.getElementById("mittag_zeit"),document.getElementById("abend_zeit")];
zeiten.forEach(z => {z.vor_value = z.valueAsDate; z.oninput = () => { test_zeit(); }; });


function test_zeit() {
    console.log("Here")
    if((zeiten[0].valueAsDate < zeiten[1].valueAsDate && zeiten[1].valueAsDate < zeiten[2].valueAsDate)) {
        zeiten.forEach(element => {element.vor_value = element.valueAsDate}); return true;
    }
    else {
        zeiten.forEach(element => {element.valueAsDate = element.vor_value;});return false
    }
}
function test_med() {
    let r = true;
    med_inputs().forEach(element => {
        if(element.value == null || element.value == "" || element.value < 0) {
            element.value = 0; r = false;
        }
        else if(element.value > 3) {
            element.value = 3; r = false;
        }
    });
    return r;
}

function zeit_aendern() {
    if(!test_zeit()) {
        alert("Zeiten falsch"); return;
    }
    let _form = Object.assign(document.createElement("form"), {
        "method":"post",
        "action":"./index.php",
        "hidden":"true"
    });
    let _aktion = Object.assign(document.createElement("input"), {
        "type":"text",
        "name":"Aktion",
        "value":"Zeiten"
    })
    let _werte = Object.assign(document.createElement("input"), {
        "type":"text",
        "name":"Werte"
    });
    _werte.setAttribute("value",((zeiten[0].valueAsDate.getUTCHours() < 10 ? "0" : "") + zeiten[0].valueAsDate.getUTCHours().toString() + ":" 
    + (zeiten[0].valueAsDate.getUTCMinutes() < 10 ? "0" : "") + zeiten[0].valueAsDate.getUTCMinutes().toString() + ":00")
    + ";" + ((zeiten[1].valueAsDate.getUTCHours() < 10 ? "0" : "") + zeiten[1].valueAsDate.getUTCHours().toString() + ":" 
    + (zeiten[1].valueAsDate.getUTCMinutes() < 10 ? "0" : "") + zeiten[1].valueAsDate.getUTCMinutes().toString() + ":00")
    + ";" + ((zeiten[2].valueAsDate.getUTCHours() < 10 ? "0" : "") + zeiten[2].valueAsDate.getUTCHours().toString() + ":" 
    + (zeiten[2].valueAsDate.getUTCMinutes() < 10 ? "0" : "") + zeiten[2].valueAsDate.getUTCMinutes().toString() + ":00"));

    _form.appendChild(_aktion);
    _form.appendChild(_werte);
    document.querySelector("body").appendChild(_form);
    _form.submit();
}

function med_aendern() 
{
    if(!test_med()) {
        alert("Invalide Med");
        return;
    }

    let _form = Object.assign(document.createElement("form"), {
        "method":"post",
        "action":"./index.php",
        "hidden":"true"
    });
    let _aktion = Object.assign(document.createElement("input"), {
        "type":"text",
        "name":"Aktion",
        "value":"Aendern"
    });

    let _namen = Object.assign(document.createElement("input"), {
    "type":"text",
    "name":"Med_Name"
    });
    {
        let n = "";
        let rows = document.querySelectorAll("#med_table tbody tr");
        for(let i = 0; i < rows.length; i++) {
            n += rows[i].children[0].innerHTML + ";";
        };
        n = n.substring(0,n.length-1);
        _namen.setAttribute("value",n);
    }
 
    let _werte = Object.assign(document.createElement("input"),{
    "type":"text",
    "name":"Werte"
    })
    {
        let n = "";
        let rows = document.querySelectorAll("#med_table tbody tr");
        for(let i = 0; i < rows.length; i++) {
            for(let j = 1; j <= 19; j += 3) {
                n += rows[i].children[j].children[0].value.toString() + " "
                + rows[i].children[j + 1].children[0].value.toString() + " "
                + rows[i].children[j + 2].children[0].value.toString();
                n += (function() {if(j == 19)return ";";else return ".";})();
            }
        }
        n = n.substring(0,n.length-1);
        _werte.setAttribute("value",n);
    }
    _form.appendChild(_aktion);
    _form.appendChild(_namen);
    _form.appendChild(_werte);
    document.querySelector("body").appendChild(_form);
    _form.submit();
}

function med_entf(p) {
    let _form = Object.assign(document.createElement("form"), {
        "method":"post",
        "action":"./index.php",
        "hidden":"true"
    });
    let _aktion = Object.assign(document.createElement("input"), {
        "type":"text",
        "name":"Aktion",
        "value":"Entf"
    });

    let _name = Object.assign(document.createElement("input"), {
        "type":"text",
        "name":"Med_Name",
        "value":document.querySelector("#med_table tbody").children[p].children[0].innerText
    });
    _form.appendChild(_aktion);
    _form.appendChild(_name)
    document.querySelector("body").appendChild(_form);
    _form.submit();
}

function med_hinz() {
    let _form = Object.assign(document.createElement("form"), {
        "method":"post",
        "action":"./index.php",
        "hidden":"true"
    });
    let _aktion = Object.assign(document.createElement("input"), {
        "type":"text",
        "name":"Aktion",
        "value":"Hinz"
    });
    let _namen = Object.assign(document.createElement("input"), {
    "type":"text",
    "name":"Med_Name",
    "value":document.getElementById("select_med").value
    });
    let _werte = Object.assign(document.createElement("input"),{
        "type":"text",
        "name":"Werte"
        })
        {
            let row = document.querySelector("#med_table tbody tr");
            let n = "";
            for(let j = 0; j < 19; j += 3) {
                n += row.children[j].children[0].value.toString() + " " 
                + row.children[j + 1].children[0].value.toString() + " " 
                + row.children[j + 2].children[0].value.toString() + ".";
            }
            _werte.setAttribute("value",n.substring(0,n.length-1));
        }
        _form.appendChild(_aktion);
        _form.appendChild(_namen);
        _form.appendChild(_werte);
        document.querySelector("body").appendChild(_form);
        _form.submit();

}