var firebaseLogin = new Firebase();


    var finish = document.getElementById("submit-login");

    finish.addEventListener("click", e=>{
        
        let formData = new FormData(document.getElementById("form"));
    
        let email = formData.get("desemail");

        let password = formData.get("despassword");

        password = md5(password);

        console.log(password);

        
        firebaseLogin.testLogin(email, password).then(result=>{

            if (result)
            {
                sessionStorage.pwd = password;
                sessionStorage.eml = email;
                window.location.href = "/chat";

            }


        }).catch(err=>{console.log(err)});
    });
