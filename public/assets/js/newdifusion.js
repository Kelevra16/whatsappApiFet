document.addEventListener("DOMContentLoaded", function(event) {
   
});

function selectFileArchive(){
    const archiveFile = document.getElementById('excel');
    const nameArchiveSelect = document.getElementById('nameArchiveSelect');
    const resetImg = document.getElementById('resetImg');
    resetImg.classList.remove('d-none');
    let nameArchive = archiveFile.files[0].name;
    nameArchiveSelect.innerHTML = nameArchive;
    nameArchiveSelect.classList.remove('d-none');
}

function resetArchive(){
    const archiveFile = document.getElementById('excel');
    const nameArchiveSelect = document.getElementById('nameArchiveSelect');
    const lbArchive = document.getElementById('lbImgArchive');
    lbArchive.classList.remove('disabled');
    archiveFile.value = '';
    nameArchiveSelect.innerHTML = '';
    nameArchiveSelect.classList.add('d-none');
    const resetImg = document.getElementById('resetImg');
    resetImg.classList.add('d-none');
}

function saveDifusion(){
    Swal.fire({
        title: 'Creando nueva lista de difusión...',
        text: '',
        timerProgressBar: true,
        heightAuto: false,
        didOpen: () => {
          Swal.showLoading()
        },
    });

    let titulo = document.getElementById('titleDifusion').value;
    let descripcion = document.getElementById('description').value;
    let location = document.getElementById('location').value;
    let excel = document.getElementById('excel').files[0];


    var formdata = new FormData();
    formdata.append("titulo", titulo);
    formdata.append("descripcion",descripcion);
    formdata.append("excel", excel);
    formdata.append("location",location);

    var requestOptions = {
      method: 'POST',
      body: formdata,
      redirect: 'follow'
    };

    fetch("/difusion/createdXml", requestOptions)
      .then(response => response.json())
      .then(result => {
        console.log(result);
        if(result.susses){
            Swal.fire({
                icon: 'success',
                title: 'Lista de difusión creada',
                showConfirmButton: true,
              })
        }else{
            let mssg = (result.message)? result.message : 'Sucedió un error, Inténtalo mas tarde';
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