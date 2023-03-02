document.addEventListener("DOMContentLoaded", function (event) {
});

const saveUser = () => {

    Swal.fire({
        title: 'Guardando usurario...',
        text: '',
        timerProgressBar: true,
        heightAuto: false,
        didOpen: () => {
          Swal.showLoading()
        },
    });


    const nombre = document.getElementById("nombre");
    const aPaterno = document.getElementById("aPaterno");
    const aMaterno = document.getElementById("aMaterno");
    const email = document.getElementById("email");
    const password = document.getElementById("password");
    const confpassword = document.getElementById("confpassword");
    const idEmpresa = document.getElementById("idEmpresa");
    const idRole = document.getElementById("idRole");

    var formdata = new FormData();
    formdata.append("nombre", nombre.value);
    formdata.append("aPaterno", aPaterno.value);
    formdata.append("aMaterno", aMaterno.value);
    formdata.append("email", email.value);
    formdata.append("password", password.value);
    formdata.append("confpassword", confpassword.value);
    formdata.append("idEmpresa", idEmpresa.value);
    formdata.append("idRole", idRole.value);

    var requestOptions = {
        method: "POST",
        body: formdata,
        redirect: "follow"
    };

    fetch("/usuarios/save", requestOptions)
        .then((res) => res.json())
        .then((data) => {
            if (data.susses && data.status == 200) {
                Swal.fire({
                    icon: "success",
                    title: "Usuario guardado",
                    showConfirmButton: true,
                });
                nombre.value = "";
                aPaterno.value = "";
                aMaterno.value = "";
                email.value = "";
                password.value = "";
                confpassword.value = "";
                idEmpresa.value = "";
                idRole.value = "";
            } else {
                let mssg = data.message ? data.message : "Sucedió un error, Inténtalo mas tarde";
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: mssg,
                });
                
            }
        })
        .catch((error) => {
            console.log("error", error);
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Sucedió un error, Inténtalo mas tarde"
            });
        });
    
};