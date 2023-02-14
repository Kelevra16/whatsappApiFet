let tempCheckedGroups = [];
let CheckedGroups = [];
let tempNameElementChecked = [];
let ElementChecked = [];
document.addEventListener("DOMContentLoaded", function(event) {
    getListDifucion();
});

function checkGroup(element){
    
    if(element.checked){
        tempCheckedGroups.push(element.value)
        dataName = element.getAttribute('data-name');
        tempNameElementChecked.push(dataName);
    }else{
        tempCheckedGroups = tempCheckedGroups.filter(item => item !== element.value);
        dataName = element.getAttribute('data-name');
        tempNameElementChecked = tempNameElementChecked.filter(item => item !== dataName);
    }
}

function saveGroupSelect(){
    CheckedGroups = [];
    ElementChecked = [];
    CheckedGroups.push(...tempCheckedGroups);
    ElementChecked.push(...tempNameElementChecked);
    const dropdownMenu = document.querySelector('.dropdown-menu');
    dropdownMenu.classList.remove("show");
    const drowMenuInput = document.getElementById('dropdownMenuButton');
    ElementCheckedString = ElementChecked.join(', ');
    drowMenuInput.value = ElementCheckedString;
}

function deselectTempGroup(){
    const groupDifucion = document.querySelectorAll('.form-check-input');
    groupDifucion.forEach(element => {
        if (CheckedGroups.includes(element.value)) {
            element.checked = true;
        }else{
            element.checked = false;
        }
    });
}

function cancelGroupSelect(){
    deselectTempGroup();
    const dropdownMenuButton = document.querySelector('.dropdown-menu');
    dropdownMenuButton.classList.remove("show");
    tempCheckedGroups = [];
    tempNameElementChecked = [];
    tempCheckedGroups.push(...CheckedGroups);
    tempNameElementChecked.push(...ElementChecked);
}

function selectFileImage(){
    const inputFileImage = document.getElementById('imgFile');
    const nameArchiveSelect = document.getElementById('nameArchiveSelect');
    const resetImg = document.getElementById('resetImg');
    const lbArchive = document.getElementById('lbImgArchive');
    lbArchive.classList.add('disabled');
    resetImg.classList.remove('d-none');
    let nameArchive = inputFileImage.files[0].name;
    nameArchiveSelect.innerHTML = nameArchive;
    nameArchiveSelect.classList.remove('d-none');
}

function selectFileArchive(){
    const archiveFile = document.getElementById('archiveFile');
    const nameArchiveSelect = document.getElementById('nameArchiveSelect');
    const resetImg = document.getElementById('resetImg');
    const lbImgFile = document.getElementById('lbImgFile');
    lbImgFile.classList.add('disabled');
    resetImg.classList.remove('d-none');
    let nameArchive = archiveFile.files[0].name;
    nameArchiveSelect.innerHTML = nameArchive;
    nameArchiveSelect.classList.remove('d-none');
}

function resetArchive(){
    const archiveFile = document.getElementById('archiveFile');
    const inputFileImage = document.getElementById('imgFile');
    const nameArchiveSelect = document.getElementById('nameArchiveSelect');
    const lbImgFile = document.getElementById('lbImgFile');
    lbImgFile.classList.remove('disabled');
    const lbArchive = document.getElementById('lbImgArchive');
    lbArchive.classList.remove('disabled');
    archiveFile.value = '';
    inputFileImage.value = '';
    nameArchiveSelect.innerHTML = '';
    nameArchiveSelect.classList.add('d-none');
    const resetImg = document.getElementById('resetImg');
    resetImg.classList.add('d-none');
}

function sendCanpaing(){

    Swal.fire({
        title: 'Enviando mensajes',
        text: '',
        imageUrl: '/assets/img/campaign/sendmessage.png',
        imageWidth: 193,
        imageHeight: 193,
        imageAlt: 'send message',
        timerProgressBar: true,
        heightAuto: false,
        didOpen: () => {
          Swal.showLoading()
        },
    })

    let message = document.getElementById('textAreaMessage').value;
    let groups = CheckedGroups.join(',');
    let adjuntoFile = document.getElementById('archiveFile').files[0];
    let adjuntoImg = document.getElementById('imgFile').files[0];
    let titulo = document.getElementById('titulo').value;

    var formdata = new FormData();
    formdata.append("message", message);
    formdata.append("groups", groups);
    formdata.append("adjuntoFile", adjuntoFile);
    formdata.append("adjuntoImg", adjuntoImg);
    formdata.append("titulo", titulo);

    var requestOptions = {
      method: 'POST',
      body: formdata,
      redirect: 'follow'
    };

    fetch("/campaign/save", requestOptions)
      .then(response => response.json())
      .then(result => {
        console.log(result)
            if(result.susses){
                Swal.fire({
                    icon: 'success',
                    title: 'Campaña enviada',
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
        })
      });
    
}


function getListDifucion(){
    const groupListDifu = document.getElementById('groupListDifu');
    groupListDifu.innerHTML = '';

    var myHeaders = new Headers();

    var requestOptions = {
      method: 'POST',
      headers: myHeaders,
      redirect: 'follow'
    };

    fetch("/difusion/listDifusion", requestOptions)
      .then(response => response.json())
      .then(result => {
        console.log(result)
        if (result.susses) {
            result.data.forEach(element => {
                const li = document.createElement('li');
                li.classList.add('list-group-item', 'py-3');
                li.style.border = 'none';
                const input = document.createElement('input');
                input.classList.add('form-check-input', 'me-1');
                input.type = 'checkbox';
                input.value = element.id;
                input.dataset.name = element.nombre;
                input.id = element.id;
                input.onchange = function(){checkGroup(this)};
                const label = document.createElement('label');
                label.classList.add('form-check-label');
                label.htmlFor = element.id;
                label.innerHTML = element.nombre;
                li.appendChild(input);
                li.appendChild(label);
                groupListDifu.appendChild(li);
                tempCheckedGroups = [];
                CheckedGroups = [];
                tempNameElementChecked = [];
                ElementChecked = [];
            });
        }else{
            console.log('error');
        }
      })
      .catch(error => {
        console.log('error', error)
    });
}