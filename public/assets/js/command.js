let currentPage = 1;

document.addEventListener("DOMContentLoaded", function(event) {
    getListCommand()
});

function changePage(page){
    currentPage = page;
    loadingTableView();
    getListCommand(page);
}


function getListCommand(cuPage = 1){
    const bodyTableCommand = document.getElementById('bodyTableCommand');

    var requestOptions = {
        method: 'POST',
        redirect: 'follow'
      };
      
      fetch(`/comandos/list/${cuPage}`, requestOptions)
        .then(response => response.json())
        .then(result => {
            console.log(result)
            if(result.susses && result.status == "200"){
                bodyTableCommand.innerHTML = '';

                result.data.forEach(element => {
                    let datestr = new Date(element.created_at.date).toLocaleDateString();
                    // let day = objectDate.getDate();3
                    // let month = objectDate.getMonth();                            
                    // let year = objectDate.getFullYear();

                    const html = `<tr scope="row" >
                        <td colspan="1">
                        ${element.command}
                        </td>
                        <td colspan="1">
                        ${element.typeCommand}
                        </td>
                        <td colspan="1">
                            ${element.created_by}
                        </td>
                        <td colspan="1">
                            ${datestr}
                        </td>
                        <td colspan="1">
                            <button class="btn btt-red-cancel-circle" onclick="deleteCommand(${element.id})">Eliminar</button>
                        </td>
                    </tr>`

                    bodyTableCommand.innerHTML += html;
                });

                currentPage = cuPage;
                let maxPage = result.pager;
                paginate(maxPage, '.pagination');

            }else{
                console.log('error', result)
                bodyTableCommand.innerHTML = '';
                const tr = document.createElement('tr');
                const td = document.createElement('td');
                td.classList.add('align-middle','text-center');
                td.innerHTML = 'No hay comandos creados';
                td.setAttribute('colspan', '4');
                tr.appendChild(td);
                bodyTableCommand.appendChild(tr);
                currentPage = 1;
                document.querySelector('.pagination').innerHTML = '';
            }
        })
        .catch(error => {
            console.log('error', error)
            bodyTableCommand.innerHTML = '';
            bodyTableCommand.innerHTML = '';
            const tr = document.createElement('tr');
            const td = document.createElement('td');
            td.classList.add('align-middle','text-center');
            td.innerHTML = 'No hay comandos creados';
            td.setAttribute('colspan', '4');
            tr.appendChild(td);
            bodyTableCommand.appendChild(tr);
            currentPage = 1;
            document.querySelector('.pagination').innerHTML = '';
        });

}


function loadingTableView() {
    const bodyTableCommand = document.getElementById('bodyTableCommand');
    bodyTableCommand.innerHTML = '';
  
    for (let i = 0; i < 4; i++) {
        const html = `<tr scope="row" >
                <td colspan="1">
                    <div class="row gx-3">
                        <div class="rounded-3 col shine me-1"  style="height: 30px; width: 150px;"></div>
                    </div>
                </td>
                <td colspan="1">
                    <div class="row gx-3">
                        <div class="rounded-3 col shine me-1"  style="height: 30px; width: 150px;"></div>
                    </div>
                </td>
                <td colspan="1">
                    <div class="row gx-3">
                        <div class="rounded-3 col shine me-1"  style="height: 30px; width: 150px;"></div>
                    </div>
                </td>
                <td colspan="1">
                    <div class="row gx-3">
                        <div class="rounded-3 col shine me-1"  style="height: 30px; width: 150px;"></div>
                    </div>
                </td>
                <td colspan="1">
                    <div class="row gx-3">
                        <div class="rounded-3 col shine me-1"  style="height: 30px; width: 150px;"></div>
                    </div>
                </td>
            </tr>`
        bodyTableCommand.innerHTML += html;
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


function deleteCommand(idCommand){
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
            urlencoded.append("idCommand", idCommand);
      
            var requestOptions = {
              method: 'POST',
              headers: myHeaders,
              body: urlencoded,
              redirect: 'follow'
            };
      
            fetch("/comandos/delete", requestOptions)
              .then(response => response.json())
              .then(result => {
                if (result.status == 200 && result.susses) {
                  Swal.fire(
                    'Eliminado!',
                    'La campaña ha sido eliminada.',
                    'success'
                  );
                  getListCommand(currentPage);
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