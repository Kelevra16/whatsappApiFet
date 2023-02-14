document.addEventListener("DOMContentLoaded", function(event) {
    getState();
});


function getState(){
    var requestOptions = {
        method: 'POST',
        redirect: 'follow'
      };
      
      fetch("/settings/state", requestOptions)
        .then(response => response.json())
        .then(result =>{
            if(result.susses && result.status == 200){
                const estatus = document.getElementById('status');
                estatus.innerHTML = "";
                if(result.data.estatus.logueado == true){
                    let messageAlert = "Cuenta activa";
                    estatusAlert = `<div class="alert alert-success" role="alert">
                        <strong>Atención!</strong> ${messageAlert}
                    </div>`;
                }else{
                    let messageAlert = "Cuenta inactiva";
                    estatusAlert = `<div class="alert alert-danger" role="alert">
                        <strong>Atención!</strong> ${messageAlert}
                    </div>`;

                    try {
                        getQR();
                    } catch (error) {
                        console.log(error);
                    }
                }

                estatus.innerHTML = estatusAlert;
            }else{
                const estatus = document.getElementById('status');
                estatus.innerHTML = "";
                let messageAlert = (result.message)? result.message : "No se ha podido obtener el estado de la cuenta.";
                estatusAlert = `<div class="alert alert-danger" role="alert">
                    <strong>Atención!</strong> ${messageAlert}
                </div>`;
                estatus.innerHTML = estatusAlert;
            }
        })
        .catch(error => {
            console.log('error', error);
            const estatus = document.getElementById('status');
            estatus.innerHTML = "";
            let messageAlert = "Ocurrió un error al obtener el estado de la cuenta";
            estatusAlert = `<div class="alert alert-danger" role="alert">
                <strong>Atención!</strong> ${messageAlert}
            </div>`;
            estatus.innerHTML = estatusAlert;
        });
}


function getQR(){
    var requestOptions = {
        method: 'POST',
        redirect: 'follow'
      };

      const cardqr = document.getElementById('cardqr');
      const loadingQR = document.getElementById('loadingQR');
      cardqr.classList.remove('d-none');
      
      
      fetch("/settings/qr", requestOptions)
        .then(response => response.blob())
        .then(result =>{
            if(result.type == "image/png" && result.size > 500){
                console.log(result);
                loadingQR.classList.add('d-none');
                const qr = document.getElementById('qr');
                qr.innerHTML = "";
                let qrAlert = `<img src="${URL.createObjectURL(result)}" class="img-fluid" alt="Responsive image">`;
                qr.innerHTML = qrAlert;
            }else{
                getQR()
            }
        })
        .catch(error => {
            getQR()
        });
}

function saveChangesUser(){
    Swal.fire({
        title: 'Actualizando Datos...',
        text: '',
        timerProgressBar: true,
        heightAuto: false,
        didOpen: () => {
          Swal.showLoading()
        },
      })

      var myHeaders = new Headers();
      myHeaders.append("Content-Type", "application/x-www-form-urlencoded");

        const nameUser = document.getElementById('nameUser').value;
        const aPaterno = document.getElementById('apePaterno').value;
        const aMaterno = document.getElementById('apeMaterno').value;
        const email = document.getElementById('email').value;

      var urlencoded = new URLSearchParams();
      urlencoded.append("name", nameUser);
      urlencoded.append("aPaterno", aPaterno);
      urlencoded.append("aMaterno", aMaterno);
      urlencoded.append("email", email);

      var requestOptions = {
        method: 'POST',
        headers: myHeaders,
        body: urlencoded,
        redirect: 'follow'
      };

      fetch("/user/update", requestOptions)
      .then(response => response.json())
      .then(result => {
        console.log(result);
        if (result.status === 200) {
          if (result.susses) {
            Swal.fire({
                icon: 'success',
                title: 'Datos actualizados',
                text: result.message,
                heightAuto: false,
            })
          }else{
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: result.message,
                heightAuto: false,
            })
          }
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Ocurrió un error',
            heightAuto: false,
            });
        }
      })
      .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Ocurrió un error',
            heightAuto: false,
        });
      });
}

function changePassword(){
    Swal.fire({
        title: 'Actualizando contraseña...',
        text: '',
        timerProgressBar: true,
        heightAuto: false,
        didOpen: () => {
          Swal.showLoading()
        },
      })

      var myHeaders = new Headers();
      myHeaders.append("Content-Type", "application/x-www-form-urlencoded");

        const nameUser = document.getElementById('passOrig');
        const aPaterno = document.getElementById('newPass');
        const aMaterno = document.getElementById('confirPass');

      var urlencoded = new URLSearchParams();
      urlencoded.append("password", nameUser.value);
      urlencoded.append("newPassword", aPaterno.value);
      urlencoded.append("confirmPassword", aMaterno.value);

      var requestOptions = {
        method: 'POST',
        headers: myHeaders,
        body: urlencoded,
        redirect: 'follow'
      };

      fetch("/user/update/password", requestOptions)
      .then(response => response.json())
      .then(result => {
        console.log(result);
        if (result.status === 200) {
          if (result.susses) {
            nameUser.value="";
            aPaterno.value="";
            aMaterno.value="";
            Swal.fire({
                icon: 'success',
                title: 'Contraseña actualizada',
                text: result.message,
                heightAuto: false,
            }).then((_) => {
                const truck_modal = document.querySelector('#modalPass');
                const modal = bootstrap.Modal.getInstance(truck_modal);    
                modal.hide();
            })
          }else{
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: result.message,
                heightAuto: false,
            })
          }
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Ocurrió un error',
            heightAuto: false,
            });
        }
      })
      .catch(error => {
        console.log('error', error)
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Ocurrió un error',
            heightAuto: false,
        });
      });
}