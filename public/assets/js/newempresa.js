document.addEventListener("DOMContentLoaded", function(event) {
});

function saveEmpresa(){

    Swal.fire({
        title: 'Guardando empresa...',
        text: '',
        timerProgressBar: true,
        heightAuto: false,
        didOpen: () => {
          Swal.showLoading()
        },
    });

    const nombre = document.getElementById('tittleEmpresa');
    const direccion = document.getElementById('direccion');
    const descripcion = document.getElementById('descripcion');
    const telefono = document.getElementById('telefono');
    const apikey = document.getElementById('apikey');

    var formdata = new FormData();
    formdata.append("nombre", nombre.value);
    formdata.append("direccion",direccion.value);
    formdata.append("descripcion", descripcion.value);
    formdata.append("telefono",telefono.value);
    formdata.append("apikey",apikey.value);

    var requestOptions = {
      method: 'POST',
      body: formdata,
      redirect: 'follow'
    };

    fetch('/empresas/save',requestOptions)
    .then(res => res.json())
    .then(data => {
        console.log(data);
        if(data.susses){
            Swal.fire({
                icon: 'success',
                title: 'Empresa guardada',
                showConfirmButton: true,
              })
            nombre.value = '';
            direccion.value = '';
            descripcion.value = '';
            telefono.value = '';
            apikey.value = '';
        }else{
            let mssg = (data.message)? data.message : 'Sucedió un error, Inténtalo mas tarde';
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: mssg,
            })
        }
    })
    .catch(error => {
        console.log('error', error)
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Sucedió un error inesperado, Inténtalo mas tarde',
        });
      });
}