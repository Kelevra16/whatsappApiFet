let currentPage = 1;
let maxPageG = 0;

document.addEventListener("DOMContentLoaded", function(event) {
    getListDifucionInfo();
});


function getListDifucionInfo(cuPage = 1){
    const idDifucion = document.getElementById('idDifucion');
    const bodyTableDifucion = document.getElementById('bodyTableDifucion');

    currentPage = cuPage;
    paginate(maxPageG, '.pagination');
    var myHeaders = new Headers();
    myHeaders.append("Content-Type", "application/x-www-form-urlencoded");

    var urlencoded = new URLSearchParams();
    urlencoded.append("idDifucion", idDifucion.value);

    var requestOptions = {
        method: 'POST',
        headers: myHeaders,
        redirect: 'follow',
        body: urlencoded
    };

    fetch(`/difusion/edit/list/${cuPage}`, requestOptions)
    .then(response => response.json())
    .then(result => {
        console.log(result)
        if (result.susses && result.status == 200) {
            currentPage = cuPage;
            maxPageG = result.page;
            paginate(maxPageG, '.pagination');

            bodyTableDifucion.innerHTML = '';
            const totalContactos = document.getElementById('totalContactos');
            totalContactos.value = result.data.totalContactos;
            result.data.listContactos.forEach(element => {
                const tr = document.createElement('tr');
                tr.scope = "row";
                const td1 = document.createElement('td');
                td1.colSpan = "1";
                td1.classList.add('align-middle');
                const h5 = document.createElement('h5');
                h5.innerHTML = element.lada;
                const td2 = document.createElement('td');
                td2.colSpan = "1";
                td2.classList.add('align-middle');
                const h5_2 = document.createElement('h5');
                h5_2.innerHTML = element.telefono;
                const td3 = document.createElement('td');
                td3.colSpan = "1";
                td3.classList.add('align-middle');
                const h5_3 = document.createElement('h5');
                h5_3.innerHTML = element.nombre;
                const td4 = document.createElement('td');
                td4.colSpan = "1";
                const div = document.createElement('div');
                div.classList.add('row', 'gx-3');
                const button = document.createElement('button');
                button.type = "button";
                button.classList.add('btn', 'btt-red-cancel');
                button.setAttribute('onclick', `deletContacto(${element.id});`);
                button.setAttribute('style', 'width: auto;')
                const i = document.createElement('i');
                i.classList.add('bi', 'bi-trash-fill');

                button.appendChild(i);
                div.appendChild(button);
                td4.appendChild(div);

                tr.appendChild(td1);
                tr.appendChild(td2);
                tr.appendChild(td3);
                tr.appendChild(td4);

                td1.appendChild(h5);
                td2.appendChild(h5_2);
                td3.appendChild(h5_3);

                bodyTableDifucion.appendChild(tr);
            });
        }else{
            bodyTableDifucion.innerHTML = '';
            const tr = document.createElement('tr');
            tr.scope = "row";
            const td1 = document.createElement('td');
            td1.colSpan = "4";
            td1.classList.add('align-middle');
            const h5 = document.createElement('h5');
            h5.innerHTML = 'No hay contactos';
            td1.appendChild(h5);
            tr.appendChild(td1);
            bodyTableDifucion.appendChild(tr);

            paginate(0, '.pagination');
        }
    })
    .catch(error => {
        bodyTableDifucion.innerHTML = '';
        const tr = document.createElement('tr');
        tr.scope = "row";
        const td1 = document.createElement('td');
        td1.colSpan = "4";
        td1.classList.add('align-middle');
        const h5 = document.createElement('h5');
        h5.innerHTML = 'No hay contactos';
        td1.appendChild(h5);
        tr.appendChild(td1);
        bodyTableDifucion.appendChild(tr);

        paginate(0, '.pagination');
    });


}

function changePage(page){
    currentPage = page;
    loadingTableView();
    getListDifucionInfo(page);
}

function loadingTableView(){
    const bodyTableDifucion = document.getElementById('bodyTableDifucion');
    bodyTableDifucion.innerHTML = '';
    const content = `                    <tr scope="row" >
    <td colspan="1">
        <div class="row gx-3">
            <div class="col rounded-3 shine me-1"  style="height: 40px; width: 45px;"></div>
        </div>
    </td>
    <td colspan="1">
        <div class="row gx-3">
            <div class="rounded-3 col shine me-1"  style="height: 40px; width: 150px;"></div>
        </div>
    </td>
    <td colspan="1">
        <div class="row gx-3">
            <div class="rounded-3 col shine me-1"  style="height: 40px; width: 150px;"></div>
        </div>
    </td>
    <td colspan="1">
        <div class="row gx-3">
            <div class="rounded-3 col shine me-1"  style="height: 40px; width: 50px;"></div>
        </div>
    </td>
</tr>`;

    for (let i = 0; i < 5; i++) {
        bodyTableDifucion.innerHTML += content;
    }
}

const paginate = (pagesMax, selector) => {
    const currentPageLocal = currentPage;
  
    if (pagesMax <= 1) {
        document.querySelector(selector).innerHTML = '';
        return;
    }
  
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

  function deletContacto(id){

    Swal.fire({
        title: '¿Estas seguro?',
        text: "¡No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '¡Si, bórralo!'
    }).then((result) => {
        if (result.isConfirmed) {
            const url = `/difusion/edit/delte/contacto`;
            var myHeaders = new Headers();
            myHeaders.append("Content-Type", "application/x-www-form-urlencoded");
        
            var urlencoded = new URLSearchParams();
            urlencoded.append("idContacto", id);
        
            const options = {
                method: 'POST',
                headers:myHeaders,
                body: urlencoded
            }
            fetch(url, options)
            .then(response => response.json())
            .then(data => {
                if (data.status == 200 && data.susses == true) {
                    Swal.fire(
                        '¡Eliminado!',
                        'El contacto ha sido eliminado.',
                        'success'
                    );
                    getListDifucionInfo(currentPage);
                }else{
                    Swal.fire(
                        '¡Error!',
                        'Ha ocurrido un error al eliminar el contacto.',
                        'error'
                    );
                }
            })
            .catch(error => {
                console.log(error);
                Swal.fire(
                    '¡Error!',
                    'Ha ocurrido un error al eliminar el contacto.',
                    'error'
                );
            });
        }
    })



  }

const saveContacto = () => {
    Swal.fire({
        title: 'Creando nuevo contacto...',
        text: '',
        timerProgressBar: true,
        heightAuto: false,
        didOpen: () => {
          Swal.showLoading()
        },
    });

    const nombre = document.getElementById('nombre');
    const lada = document.getElementById('lada');
    const telefono = document.getElementById('telefono');
    const empresa = document.getElementById('empresa');
    const puesto = document.getElementById('puesto');
    const email = document.getElementById('email');
    const idDifucion = document.getElementById('idDifucion');
    // const id = document.getElementById('id');

    if (nombre.value == '' || telefono.value == '' || lada.value == '') {
        Swal.fire({
            icon: 'error',
            title: '¡Error!',
            text: '¡Los campos nombre,teléfono y lada son requeridos!',
        });
        return false;
    }

    var myHeaders = new Headers();
    myHeaders.append("Content-Type", "application/x-www-form-urlencoded");

    var urlencoded = new URLSearchParams();
    urlencoded.append("nombre", nombre.value);
    urlencoded.append("lada", lada.value);
    urlencoded.append("telefono", telefono.value);
    urlencoded.append("empresa", empresa.value);
    urlencoded.append("puesto", puesto.value);
    urlencoded.append("email", email.value);
    urlencoded.append("idDifucion", idDifucion.value);

    var requestOptions = {
        method: 'POST',
        headers: myHeaders,
        redirect: 'follow',
        body: urlencoded
    };

    fetch("/difusion/edit/add/contacto", requestOptions)
        .then(response => response.json())
        .then(result => {
            if (result.status == 200 && result.susses == true) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: '¡El contacto se ha agregado correctamente!',
                });
                getListDifucionInfo(currentPage);
                nombre.value = '';
                lada.value = '';
                telefono.value = '';
                empresa.value = '';
                puesto.value = '';
                email.value = '';
            } else {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: '¡Ha ocurrido un error al agregar el contacto!',
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: '¡Ha ocurrido un error al agregar el contacto!',
            });
        });
}