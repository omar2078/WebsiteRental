<?php
      session_start();

      if (isset($_SESSION['user']) && $_SESSION['user'] === "is logged in" ){
        
      }
      else {
        header("Location: auth/login.blade.php");
        exit;
      }

      
      if($_SESSION['type'] == "client"){
            $client_id= $_SESSION['client_id'] ;
            $name = $_SESSION['name']  ;
            $email = $_SESSION['email']  ;
            $property_id = $_GET['property_id'];
      }
      else{
        header("Location: hostChat.blade.php");
        exit;
      }
     
     
    

     //Getting property info

     // Connect to the database
     $conn = mysqli_connect("localhost", "root", "", "website-rental");
     // Check connection
     if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
     }

    $sql = "SELECT * FROM properties WHERE property_id = '$property_id'";
    $property_data = $conn->query($sql);


     //Getting property info
    if (mysqli_num_rows($property_data) > 0) {
        $row = mysqli_fetch_assoc($property_data);
        $image_link = $conn->query("SELECT photo FROM properties WHERE property_id = $property_id")->fetch_object()->photo;
        $host_id = $row["host_id"] ;
        $title = $row["title"] ;
        $price = $row["price"] ;
        $location = $row["location"] ;
        $type = $row["type"] ;
        if( $row["last_minute"] == "0") {
            $last_minute = "no" ;
           }  
         else { 
           $last_minute = "yes" ;
         }
        $rating = $row["rating"] ;
        $description = $row["description"] ;
    }
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">
    <style> @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap'); </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="icon" href="../media/pfe logo.png">
    <title>Chat with host</title>
</head>
<style>
    body{
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background: #383636;
        }
        #navbar {
          position: fixed ;
          background-color: #333;
          overflow: hidden;
          
  }
  
  #navbar ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
    background-color: #333;
  }
  
  #navbar li {
    float: left;
  }
  
  #navbar a {
    display: block;
    color: white;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
  }
  
  #navbar a:hover {
    background-color: #111;
  }
    #contacts{
      height: 100vh;
      width: 30%;
      margin: 0;
      padding: 0;
      background: #383636;
      overflow-y: scroll;
    }
    #chat-area{
      height: 665px;
      width: 70%;
      margin: 0;
      padding: 0;
      
    }
    #chat-area #messages-area{
      height: 645px;
      width: 100%;
      bottom: 11%;
      background-image: url("../media/chatbackground.jpg");
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
      overflow-y: scroll;
    }
    #chat-area #messages-area #items{
      width: 100%;
      bottom: 0%;
    }
    
    #chat-area #messages-area #items *{
      width: fit-content;
      display: flex;
      flex-wrap: wrap;
      word-wrap: break-word;
      display: block;
      border-radius: 10px;
      padding: 1%;
      color: white;
    }

    .left{
      max-width: 50%;
      margin-left: 1%;
      background: #2b2929;
    }
    .right{
      max-width: 50%;
      background: #f33c42;
    }
    
    #chat-area h3{
     position: relative;

    }
    #chat-area textarea{
      position: absolute;
      bottom: 0%;
      height: 10%;
      width: 64.5%;
      border: solid 2px #f33c42;
    }

    form input{
      position: absolute;
      right: 0%;
      bottom: 0%;
      height: 11%;
      width: 5%;
    }
    .contacts{
      width: 90%;
      margin: 20px auto;
      padding: 10px;
      color: white;
      background: #212529;
      border-radius: 10px;
      transition: all 0.1s ;
       box-shadow: 0px 0px 20px black;
    }
    .contacts:hover{
      border: solid 5px white;
     
    }
    .contacts:active{
      width: 90%;
    }

</style>
<body>
<div id="navbar">
  <ul>
    <li><a href="index.php">Home</a></li>
    <li><a href="Listings.blade.php">Properties</a></li>
    <li><a href="Myaccount.blade.php">Myaccount</a></li>
  </ul>
</div>
        <div id="contacts">
              <div style="height: 7vh;">

              </div>
              <?php

                    if (mysqli_num_rows($conn->query("SELECT DISTINCT host_id FROM conversation WHERE client_id = '$client_id' AND host_id = '$host_id'")) == 0) {
                      $new_contact = $conn->query("INSERT INTO conversation (client_id, host_id,property_id, sender, message) VALUES ('$client_id', '$host_id','$property_id' , 'client','')");
                      
                    }

                    $contacts_query = "SELECT DISTINCT host_id FROM conversation WHERE client_id = '$client_id' " ;
                    $contacts_result = $conn->query($contacts_query);

                    if (mysqli_num_rows($contacts_result) == 0) {
                          echo '<p style="color: white; text-align: center;">Your contacs will appear here</p>';
                    }
                    else{
                      while($row = mysqli_fetch_assoc($contacts_result)){
                            $host_info_query = "SELECT * FROM hosts WHERE host_id =".$row["host_id"];
                            $host_info_result = $conn->query($host_info_query);
                            $row2 = mysqli_fetch_assoc($host_info_result) ;
                            $host = $row2["host_id"] ;
                            //property id
                            $property_query = "SELECT DISTINCT property_id FROM conversation WHERE host_id = '$host' AND client_id = '$client_id'" ;
                            $property_result = $conn->query($property_query);
                            $row3 = mysqli_fetch_assoc($property_result) ;
                            echo '
                                <a href=Chat.blade.php?property_id='.$row3["property_id"].' style="text-decoration: none;">
                                    <div id="'.$row2["host_id"].'" class="contacts">
                                          '.$row2["name"].'
                                            <br>
                                          '.$row2["email"].'
                                    </div>
                                </a>
                            ';
                      }
                    }
              ?>
        </div>
        <div id="chat-area">
        
              <div id="messages-area">
                <div id="items">
                    
                  
                    
                </div>
                
               
              </div>
              <form onsubmit="clearTextarea(this.elements['messageInput']);" action="../php/conversation.php" method="post" >
                  <textarea name="messageInput" id="messageInput" placeholder="Write something to the host" required cols="30" rows="5" ></textarea>
                  <?php

                    echo '
                          <input type="hidden" id="host_id" name="host_id" value="'.$host_id.'">
                          <input type="hidden" id="client_id" name="client_id" value="'.$client_id.'">
                          <input type="hidden" id="property_id" name="property_id" value="'.$_GET['property_id'].'">
                          <input type="hidden" id="sender" name="sender" value="client" >
                    ';
                  ?>
                  <input type="submit" value="send" style="border: solid 1px #f33c42 ; background: #f33c42; color: white;" >
              </form>
              
        </div>
        
</body>

<script>
  




// Fetch new data from the server and update the chat container
function fetchData() {
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "../php/verifyMessages.php?client_id=<?php echo $client_id; ?>&host_id=<?php echo $host_id; ?>", true);
  xhr.onreadystatechange = function() {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
      document.getElementById("items").innerHTML = xhr.responseText;
      positionRightMessages();
      var messages = document.getElementById("messages-area");
      messages.scrollTop = messages.scrollHeight;
    }
  };
  xhr.send();



}

// Call fetchData every 0.5 seconds to update the chat
setInterval(fetchData, 500);

// Call fetchData once when the page loads
fetchData();


function clearTextarea(textarea) {
  if (!textarea.value.trim()) {
    textarea.value = "";
  }
}

function positionRightMessages() {
  $(".right").each(function() {
    var width = $(this).width();
    $(this).css("left", (1000 - width) + "px");
  });
}




</script>

</html>