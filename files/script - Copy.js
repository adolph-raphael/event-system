function togglePassword(inputId, button){
    var input = document.getElementById(inputId);
    var icon = button.querySelector("i");

    if(input.type === "password"){
        input.type = "text";
        icon.className = "bx bx-hide";
    } else {
        input.type = "password";
        icon.className = "bx bx-show";
    }
}

document.addEventListener("DOMContentLoaded", function(){
    var notification = document.querySelector(".notification");

    if(notification){
        setTimeout(function(){
            notification.classList.add("hide");
        }, 3000);

        setTimeout(function(){
            notification.remove();
        }, 3400);
    }
});
