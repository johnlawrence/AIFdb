var arrayUnique = function(a) {
    return a.reduce(function(p, c) {
        if (p.indexOf(c) < 0) p.push(c);
        return p;
    }, []);
};

function checkPeople() {
    getPeople(function (a) {
        var ppl = [];
        for (i = 0; i < a.length; i++) {
            ld = JSON.parse(a[i]);
            for ( var j = 0; j < ld.locutions.length; j++) {
                ppl.push(ld.locutions[j].personID);
            }
        }
        var uppl = arrayUnique(ppl);
        getNames(uppl);
    });
}

function getPeople(callback) {
    var nsIDs = $("input#nsm").val().split(",");
    var requests = [];
    for (i = 0; i < nsIDs.length; i++) {
        requests.push($.get("../locutions/nodeset/" + nsIDs[i]));
    }

    $.when.apply($, requests).then(function () {
        var array = $.map(arguments, function (arg) {
            return arg[0];
        });
        callback(array);
    });
}

function getNames(pIDs) {
    var na = [];
    getN(pIDs, function (a) {
        for (i = 0; i < a.length; i++) {
            p = JSON.parse(a[i]); 
            na.push(p.firstName + "%2B" + p.surname);
        }
        getSocial(na);
    });
}

function getN(pIDs, callback) {
    var requests = [];
    for (i = 0; i < pIDs.length; i++) {
        requests.push($.get("../people/" + pIDs[i]));
    }

    $.when.apply($, requests).then(function () {
        var array = $.map(arguments, function (arg) {
            return arg[0];
        });
        callback(array);
    });
}
    
function getSocial(na) {
    getS(na, function (a) {
        for (i = 0; i < a.length; i++) {
            try{
                var json = JSON.parse(a[i]);
                pid = json.personID;
                person = json;
                avatarURL = "";
                for (j=0;j<person.info.length;j++){
                    if(person.info[j].name == "Avatar"){
                        avatarURL = person.info[j].value;
                    }
                }
                $("#participantlist").append('<div class="participant" id="participant-'+person['personID']+'"><img src="' + avatarURL + '" class="avatar" />'+person['firstname']+' '+person['surname']+'</div>');
            }catch(e){
            }
        }
    });
}

function getS(names, callback) {
    var requests = [];
    for (i = 0; i < names.length; i++) {
        requests.push($.get("helpers/getSocial.php?name=" + names[i]));
    }

    $.when.apply($, requests).then(function () {
        var array = $.map(arguments, function (arg) {
            return arg[0];
        });
        callback(array);
    });
}

function doMerge() {
    $("#nsmform").hide();
    $("#progressbar").show();
    $.ajax({
        type: "POST",
        url: "../nodesets/merge",
        data: '{"nodeSets":['+$("input#nsm").val()+']}',
        beforeSend: function(xhr){
            xhr.setRequestHeader("Authorization", "Basic dGVzdDpwYXNz");
        },
        success: function(data){
            $("#progressbar").hide();
            $("#rescont").show();
            ns = JSON.parse(data);
            $("p#results").html("<a href='../argview/"+ns.nodeSetID+"' style='margin-top:15px;'>Click here to view the combined nodeset</a>");
        },
        error    : function(){ console.log('error'); }
    });
}
