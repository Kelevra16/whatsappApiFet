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

    let titulo = document.getElementById('titleDifusion');
    let descripcion = document.getElementById('description');
    let location = document.getElementById('location');
    let excel = document.getElementById('excel');
    const noarchiveCheckboxes = document.getElementById('noarchive');


    var formdata = new FormData();
    formdata.append("titulo", titulo.value);
    formdata.append("descripcion",descripcion.value);
    formdata.append("excel", excel.files[0]);
    formdata.append("location",location.value);
    const url = (noarchiveCheckboxes.checked)? '/difusion/createdlist' : "/difusion/createdXml";

    var requestOptions = {
      method: 'POST',
      body: formdata,
      redirect: 'follow'
    };

    fetch(url, requestOptions)
      .then(response => response.json())
      .then(result => {
        console.log(result);
        if(result.susses){
            Swal.fire({
                icon: 'success',
                title: 'Lista de difusión creada',
                showConfirmButton: true,
              }).then((result) => {
                window.location.href = '/difusion/created';
              });
            
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


const noArchive = () => {
    const noarchiveCheckboxes = document.getElementById('noarchive');

    if(noarchiveCheckboxes.checked){
        document.getElementById('excel').disabled = true;
        document.getElementById('lbImgArchive').classList.add('disabled');
        document.getElementById('nameArchiveSelect').classList.add('d-none');
        document.getElementById('resetImg').classList.add('d-none');
    }else{
        document.getElementById('excel').disabled = false;
        document.getElementById('lbImgArchive').classList.remove('disabled');
    }

}