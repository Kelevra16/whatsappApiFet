
let currentPage = 1;

document.addEventListener("DOMContentLoaded", function(event) {
    getListCampaign()
});


function getListCampaign(cuPage = 1){
    const bodyTableCampaign = document.getElementById('bodyTableCampaign');

    var requestOptions = {
        method: 'POST',
        redirect: 'follow'
      };
      
      fetch(`/campaign/list/${cuPage}`, requestOptions)
        .then(response => response.json())
        .then(result => {
            console.log(result)
            if(result.susses && result.status == "200"){
                bodyTableCampaign.innerHTML = '';
                result.data.forEach(element => {

                    bodyTableCampaign.innerHTML += `
                    <tr>
                        <td class="align-middle">
                            <div class="d-flex align-items-center">
                                <img width="45px" height="45px" class="img-fluid imgCamping" src="/assets/img/dashboard/noimage.png">
                                <span class="ms-2 me-3 fw-normal col">${element.titulo}</span>
                                <button class="btn btt-blue-send buttonBlueMax disabled" disabled >Mandar de nuevo</button>
                            </div>
                        </td>
                        <td class="align-middle">
                            <div class="d-flex flex-wrap flex-column">
                                <span class="fw-normal ms-2 me-3">${element.dateSend}</span>
                            </div>
                        </td>
                        <td class="align-middle">
                            <div class="d-flex flex-wrap flex-column">
                                <span class="fw-normal">${element.totalMensajes}</span>
                                <span class="fw-normal">Contactos</span>
                            </div>
                        </td>
                        <td class="align-middle text-center">
                            <button class="btn btt-green-circle mx-1">${element.status}</button>
                        </td>
                        <td class="align-middle">
                        <button class="btn btt-red-cancel" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Eliminar campaña" onclick="deleteCampaign(${element.id })"><i class="bi bi-trash"></i></button>
                    </td>
                    </tr>
                    `
                });

                currentPage = cuPage;
                let maxPage = result.pager;
                paginate(maxPage, '.pagination');
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
                const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

            }else{
                console.log('error', result)
                bodyTableCampaign.innerHTML = '';
                bodyTableCampaign.innerHTML = `<tr><td class="align-middle text-center" colspan="4">No hay campañas</td></tr>`;
                currentPage = 1;
                document.querySelector('.pagination').innerHTML = '';
            }
        })
        .catch(error => {
            bodyTableCampaign.innerHTML = '';
            bodyTableCampaign.innerHTML = `<tr><td class="align-middle text-center" colspan="4">No hay campañas</td></tr>`;
            currentPage = 1;
            document.querySelector('.pagination').innerHTML = '';
        });

}


function changePage(page){
    currentPage = page;
    loadingTableView();
    getListCampaign(page);
}


function loadingTableView() {
    const bodyTableCampaign = document.getElementById('bodyTableCampaign');
    bodyTableCampaign.innerHTML = '';
  
    for (let i = 0; i < 4; i++) {
      bodyTableCampaign.innerHTML += `
            <tr scope="row">
                <td colspan="1">
                    <div class="row gx-3">
                        <div class="rounded-3 shine me-1" style="height: 45px; width: 45px;"></div>
                        <div class="rounded-3 shine me-1 col" style="height: 30px; width: 80px;"></div>
                        <div class="rounded-3 shine me-1 col" style="height: 38px; width: 150px;"></div>
                    </div>
                </td>
                <td colspan="1">
                    <div class="row gx-3">
                    <div class="rounded-3 col shine me-1" style="height: 30px; width: 150px;"></div>
                    </div>
                </td>
                <td colspan="1">
                    <div class="row gx-3">
                        <div class="rounded-3 col shine me-1" style="height: 20px; width: 100px;"></div>
                    </div>
                </td>
                <td colspan="1">
                    <div class="row gx-3">
                    <div class="rounded-3 col shine me-1" style="height: 30px; width: 50px;"></div>
                    </div>
                </td>
            </tr>
      `;
    }
  }


const paginate = (pagesMax, selector) => {
    const currentPageLocal = currentPage;

    const pages = []

    if (pagesMax <= 6){
        for (let i = 1; i <= pagesMax; i++) {
            pages.push({
                page: i,
                current: currentPageLocal == i
            })
        }
    }else{
        if (currentPageLocal == 1){
            for (let i = 1; i <= 5; i++) {
                pages.push({
                    page: i,
                    current: currentPageLocal == i
                })
            }
        }else if (currentPageLocal == pagesMax){
            pagemin = ((pagesMax - 4) < 1) ? 1 : (pagesMax - 4);
            for (let i = pagemin; i <= pagesMax; i++) {
                pages.push({
                    page: i,
                    current: currentPageLocal == i
                })
            }
        }else{
            pageMint = (currentPageLocal - 2 < 1) ? 1 : (currentPageLocal - 2);
            pagePlus = ((currentPageLocal+3) > pagesMax) ? pagesMax : (currentPageLocal+3);
            for (let i = pageMint; i <= pagePlus; i++) {
                pages.push({
                    page: i,
                    current: currentPageLocal == i
                })
            }
        }
    }


    const html = `
    <nav aria-label="Page navigation">
        <ul class="pagination">
            ${currentPageLocal == 1 ? `
                <li class="page-item disabled">
                    <span class="page-link">‹</span>
                </li>
            ` : `
                <li class="page-item">
                    <a href="#" class="page-link" onclick="changePage(${currentPageLocal -1})" >‹</a>
                </li>
            `}
            ${pages.map((item, index) => `
                <li${item.current ? ' class="page-item active"' : ' class="page-item"'}>
                    ${item.current ? `
                        <span class="page-link">${item.page}</span>
                    ` : `
                        <a href="#" class="page-link" onclick="changePage(${item.page})">${item.page}</a>
                    `}
                </li>
            `).join('')}
            ${currentPageLocal == pagesMax ? `
                <li class="page-item disabled">
                    <span class="page-link">›</span>
                </li>
            ` : `
                <li class="page-item">
                    <a href="#" class="page-link" onclick="changePage(${currentPageLocal +1})">›</a>
                </li>
            `}
        </ul>
        </nav>
    `

    document.querySelector(selector).innerHTML = html
}


function deleteCampaign(idCampaign){
    Swal.fire({
        title: '¿Estás seguro?',
        text: "No podrás revertir esta acción",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#27A11A',
        cancelButtonColor: '#F4516C',
        confirmButtonText: 'Si, eliminar'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Eliminando...',
                text: 'Espere un momento',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            var myHeaders = new Headers();
            myHeaders.append("Content-Type", "application/x-www-form-urlencoded");

            var urlencoded = new URLSearchParams();
            urlencoded.append("idCampaign", idCampaign);
      
            var requestOptions = {
              method: 'POST',
              headers: myHeaders,
              body: urlencoded,
              redirect: 'follow'
            };
      
            fetch("/campaign/deleteCampaign", requestOptions)
              .then(response => response.json())
              .then(result => {
                if (result.status == 200 && result.susses) {
                  Swal.fire(
                    'Eliminado!',
                    'La campaña ha sido eliminada.',
                    'success'
                  );
                  getListCampaign(currentPage);
                }else{
                  Swal.fire(
                    'Error!',
                    'Ha ocurrido un error.',
                    'error'
                  );
                }
              })
                .catch(error => {
                    console.log('error', error);
                    Swal.fire(
                        'Error!',
                        'Ha ocurrido un error.',
                        'error'
                    )
                });
        }
    })
}