let currentPage = 1;

document.addEventListener("DOMContentLoaded", function (event) {
    getListLog();
});


const getListLog = async (cuPage = 1) => {
    const bodyTableLog = document.getElementById("bodyTableLog");
    const inputDate = document.getElementById("inputDate").value;

    var myHeaders = new Headers();
    myHeaders.append("Content-Type", "application/x-www-form-urlencoded");

    var urlencoded = new URLSearchParams();
    urlencoded.append("fecha", inputDate);

    var requestOptions = {
        method: "POST",
        headers: myHeaders,
        body: urlencoded,
        redirect: "follow",
    };

    fetch(`/logError/list/${cuPage}`, requestOptions)
        .then((response) => response.json())
        .then((result) => {
            console.log(result);
            if (result.susses && result.status == "200") {
                bodyTableLog.innerHTML = "";
                result.data.forEach((element) => {
                    html = `<tr scope="row" >
                      <td colspan="1">
                          ${element.id}
                      </td>
                      <td colspan="1">
                          ${element.fecha}
                      </td>
                      <td colspan="1">
                          ${element.tipoError}
                      </td>
                      <td colspan="1">
                          ${element.mensaje.substring(0, 50)}...
                      </td>
                      <td>
                          <button class="btn btt-blue-edit" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Ver registro" onclick="viewLog(${element.id})"><i class="bi bi-eye"></i></button>
                          <!-- <button class="btn btt-red-cancel" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Eliminar registro" onclick="deleteLog(${element.id})"><i class="bi bi-trash"></i></button> -->
                      </td>
                  </tr>`;

                    bodyTableLog.innerHTML += html;
                });

                currentPage = cuPage;
                let maxPage = result.pager;
                paginate(maxPage, currentPage, ".pagination");
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
                const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
            } else {
                console.log("error", result);
                bodyTableLog.innerHTML = "";
                bodyTableLog.innerHTML = `<tr><td class="align-middle text-center" colspan="5">No hay Registros</td></tr>`;
                currentPage = 1;
                document.querySelector(".pagination").innerHTML = "";
            }
        })
        .catch((error) => {
            console.log("error", error);
            bodyTableLog.innerHTML = "";
            bodyTableLog.innerHTML = `<tr><td class="align-middle text-center" colspan="5">No hay Registros</td></tr>`;
            currentPage = 1;
            document.querySelector(".pagination").innerHTML = "";
        });
};

const filter = () => {
    getListLog(1);
};

function loadingTableView() {
    const bodyTableLog = document.getElementById("bodyTableLog");
    bodyTableLog.innerHTML = "";

    for (let i = 0; i < 4; i++) {
        html = `<tr scope="row" >
      <td colspan="1">
          <div class="row gx-3">
              <div class="rounded-3 col shine me-1"  style="height: 30px; width: 40px;"></div>
          </div>
      </td>
      <td colspan="1">
          <div class="row gx-3">
              <div class="rounded-3 col shine me-1"  style="height: 30px; width: 70px;"></div>
          </div>
      </td>
      <td colspan="1">
          <div class="row gx-3">
              <div class="rounded-3 col shine me-1"  style="height: 30px; width: 50px;"></div>
          </div>
      </td>
      <td colspan="1">
          <div class="row gx-3">
              <div class="rounded-3 col shine me-1"  style="height: 30px; width: 150px;"></div>
          </div>
      </td>
      <td colspan="1">
          <div class="row gx-3">
              <div class="rounded-3 col shine me-1"  style="height: 30px; width: 50px;"></div>
          </div>
      </td>
  </tr>`;

        bodyTableLog.innerHTML += html;
    }
}

const viewLog = (id) => {
    const modalContentLog = document.getElementById("modalContentLog");
    const myModal = new bootstrap.Modal(document.getElementById('modalLog'))
    myModal.show();

    fetch(`/logError/view/${id}`)
        .then((response) => response.json())
        .then((result) => {
            console.log(result);
            if (result.status == 200) {
                modalContentLog.innerHTML = "";
                let html = `            <div class="modal-header">
                <h5 class="modal-title">Ver registro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <div class="row">
                 <div class="col-12">
                     <div class="row">
                         <div class="col-12">
                             <label for="inputOrigen" class="form-label">Origen</label>
                             <input type="text" class="form-control" id="inputOrigen" value="${result.data.origen}" disabled>
                         </div>
                     </div>

                     <div class="row">
                         <div class="col-12">
                             <label for="inputFecha" class="form-label">Fecha</label>
                             <input type="text" class="form-control" id="inputFecha" value="${result.data.fecha}" disabled>
                         </div>
                     </div>

                     <div class="row">
                         <div class="col-12">
                             <label for="inputTipoError" class="form-label">Tipo</label>
                             <input type="text" class="form-control" id="inputTipoError" value="${result.data.tipoError}" disabled>
                         </div>
                     </div>

                     <div class="row">
                         <div class="col-12">
                             <label for="inputMessage" class="form-label">Mensaje</label>
                             <textarea class="form-control" id="inputMessage" rows="3" disabled>${result.data.mensaje}</textarea>
                         </div>
                     </div>
                 </div>
             </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ok</button>
            </div>`;
                modalContentLog.innerHTML += html;
                myModal.show();
            } else {
                modalContentLog.innerHTML = "";
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Ocurrio un error al cargar el registro',
                });
            }

        })
        .catch((error) => {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Ocurrio un error al cargar el registro',
            });
        });
}

function changePage(page){
    currentPage = page;
    loadingTableView();
    getListLog(page);
}
