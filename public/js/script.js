console.log("JS connected successfully");
var xhr = new XMLHttpRequest();
var userMessage = "";

function showSearchInput(str)
{
    let prefix = "";
    if(str.length > 0)
        prefix = "Search for ";
    document.getElementById("searchInput").innerHTML = prefix + str;
}

function searchUsers(str, teamName){
    if(str.length <= 0) return;


    xhr.onreadystatechange = function () {
        if (xhr.status === 200) {
            if(xhr.responseText != null){
                // console.log(xhr.responseText);
                let matchingUsers = JSON.parse(xhr.responseText);


                const userList = document.getElementById("addUserList");
                userList.innerHTML = "";
                matchingUsers.forEach((obj) => {
                    console.log("id: " + obj.id);
                    var display = "<li class='list-group-item text-info'>" + obj.username +" <a class='ml-2' href='/teamView?addUserId="+ obj.id +"&teamName="+ teamName + "'" + "><i class='fa fa-plus text-info'></i></a></li>";

                    userList.innerHTML += display;
                });

            }
        }
    };

    xhr.open('GET', '/teamView?liveSearchForUser=' + str);
    xhr.send();
}

function getLiveMessages(){
    xhr.onreadystatechange = function () {
        if (xhr.status === 200) {
            if(xhr.responseText != null){
                // console.log("res" + xhr.responseText);
                let messages = JSON.parse(xhr.responseText);


                const messagesList = document.getElementById("messagesContainer");
                messagesList.innerHTML = "";
                messages.forEach((obj) => {
                    console.log("id: " + obj.username + "" +obj.message);
                    var display =
                        "<div class='container mb-3'>" +
                            "<div class='row'>" +
                                "<div class='col-md-12'>" +
                                    "<div class='card'>" +
                                        "<div class='card-body'>" +
                                            "<div class='message'>" +
                                                "<strong class='text-info'>"+ obj.username + ": " +"</strong>"+ obj.message +
                                            "</div>" +
                                        "</div>" +
                                    "</div>" +
                                "</div>" +
                            "</div>" +
                        "</div>";

                    messagesList.innerHTML += display;
                });

            }
        }
    };

    xhr.open('GET', '/teamView?liveMessages=true');
    xhr.send();
}


function sendMessageLive(){
    if(userMessage.length < 0) return;

    xhr.open('GET', '/teamView?sendLiveMessage='+userMessage, true);

    xhr.onreadystatechange = function () {
        if (xhr.status === 200) {
            console.log("sent successfully");
            console.log(userMessage);
            if(xhr.responseText != null){
                console.log(xhr.responseText);
            }
        }else{
            console.log("error");
        }
    };

    xhr.send();
}

function setUserMessage(str){
    userMessage = str;
}




// EVENT LISTENERS
document.addEventListener('DOMContentLoaded', function (){
    // perform the interval operations every 2 seconds
    const interval = setInterval(getLiveMessages, 2000);

});