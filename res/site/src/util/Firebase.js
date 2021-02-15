class Firebase {

    constructor()
    {
        this._config = {
            apiKey: "AIzaSyARdlKaWK2MpmO_d7jMmEW3ycrGXnDwMh0",
            authDomain: "php-chat-610c7.firebaseapp.com",
            projectId: "php-chat-610c7",
            storageBucket: "php-chat-610c7.appspot.com",
            messagingSenderId: "627711530938",
            appId: "1:627711530938:web:3952a875d50108c7d36f83",
            measurementId: "G-Y9PZV9CY9X"
        }
        this.init();
    }

    init()
    {

          // Initialize Firebase

          if (!window._initializedFirebase)
          {
              firebase.initializeApp(this._config);
              firebase.analytics();
              window._initializedFirebase = true;
          }
          
    }

    static db()
    {
        return firebase.firestore(); // Real Time DB
    }
    static hd()
    {
        return firebase.storage(); // Arquivos na nuvem
    }

    createUser(email, password)
    {
        return new Promise((s, f)=>{

            firebase.auth().createUserWithEmailAndPassword(email, password)
            .then((userCredential) => {
                // Signed in 

                var user = firebase.auth().currentUser;

                user.sendEmailVerification().then(function() {
                // Email sent.
                    console.log("email enviado");
                    s({
                        userCredential
                    });

                }).catch(function(error) {
                    f(error)
                });


                
                // ...
            })
            .catch((error) => {
                var errorCode = error.code;
                var errorMessage = error.message;
                f({
                    errorMessage
                });
        });
     });

    }

    testLogin(email, password)
    {
        return new Promise((s, f)=>{


        firebase.auth().signInWithEmailAndPassword(email, password)
                .then((userCredential) => {
            
                    let user = userCredential.user;

                    s({
                        user
                    });
                    // ...
                })
                .catch((error) => {
                    var errorCode = error.code;
                    var errorMessage = error.message;
                    f({
                        errorMessage
                    });
                });

        });
    }

    initAuth()
    {
        return new Promise((s, f)=>{



            if (sessionStorage.pwd && sessionStorage.eml)
            {
                
                firebase.auth().signInWithEmailAndPassword(sessionStorage.eml, sessionStorage.pwd)
                .then((userCredential) => {
                    // Signed in
                    // sessionStorage.clear();
                    // sessionStorage.login = JSON.stringify(userCredential);
                    //let token = userCredential.user.stsTokenManager.accessToken
                    // let token = userCredential.credential.accessToken;
                    let user = userCredential.user;
    
                    s({
                        user
                    });
                    // ...
                })
                .catch((error) => {
                    var errorCode = error.code;
                    var errorMessage = error.message;
                    f({
                        errorMessage
                    });
                });

            }
            else 
            {
                window.location.href = "/login";
            }


        });
    }
    static updateUserName(name)
    {
        var user = firebase.auth().currentUser;

        user.updateProfile({
        displayName: name
        }).then(function() {
            console.log("dados atualizados");
        // Update successful.
        }).catch(function(error) {
            console.log(error);
        });
    }
    static updateUserPhoto(photo)
    {
        var user = firebase.auth().currentUser;

        user.updateProfile({
            photoURL: photo
        }).then(function() {
            console.log("dados atualizados");
        // Update successful.
        }).catch(function(error) {
            console.log(error);
        });
    }


}