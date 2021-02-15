var firebaseRegister = new Firebase();


    var finish = document.getElementById("finish");

    finish.addEventListener("click", e=>{
        
        let formData = new FormData(document.getElementById("form"));
    
        let email = formData.get("desemail");

        let password = formData.get("despassword");

        let name = formData.get("desperson");

        password = md5(password);

        console.log(password);

        firebaseRegister.createUser(email, password).then(user=>{

            Firebase.updateUserName(name);
            sessionStorage.pwd = password;
            sessionStorage.eml = email;
            window.location.href = "/chat";


        }).catch(err=>{console.log(err)});
    });



    // $('#form').unbind('submit').submit();
      
    //   grecaptcha.ready(function() {
    //       grecaptcha.execute('6LeGZtQUAAAAAJscBKcE_t8l__0eVniemBuqFLMB', {action: '/admin/login'}).then(function(token) {
    //           $('#form').prepend('<input type="hidden" name="token" value="' + token + '">');
    //           $('#form').prepend('<input type="hidden" name="action" value="/admin/login">');
              
    //       });
    //   });
