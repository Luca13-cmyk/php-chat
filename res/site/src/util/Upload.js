
class Upload
{
    static send(file, from)
    {
        return new Promise((s, f) => {

        let fileRef = Firebase.hd().ref(from).child(Date.now() + "_" + file.name);
        let task = fileRef.put(file);

        task.on("state_changed", e=>{
    
            console.log('upload', e); // Dados do upload
    
            }, err => {
    
                f(err);
    
            }, ()=>{ // terminou o upload
    
                fileRef.getDownloadURL().then(url => {
                    console.log("upload concluido **********************", url);
                    s(url);
                    
                });
            
            });
        });
    }
}