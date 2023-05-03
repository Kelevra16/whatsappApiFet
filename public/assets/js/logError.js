let currentPage = 1;

document.addEventListener("DOMContentLoaded", function (event) {
    getListLog();
});


const getListLog = async (cuPage = 1) => {
    const bodyTableLog = document.getElementById("bodyTableLog");

    var requestOptions = {
        method: "POST",
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
                paginate(maxPage, ".pagination");
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
            bodyTableLog.innerHTML = "";
            bodyTableLog.innerHTML = `<tr><td class="align-middle text-center" colspan="5">No hay Registros</td></tr>`;
            currentPage = 1;
            document.querySelector(".pagination").innerHTML = "";
        });
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

const paginate = (pagesMax, selector) => {
    const currentPageLocal = currentPage;

    const pages = [];

    if (pagesMax <= 6) {
        for (let i = 1; i <= pagesMax; i++) {
            pages.push({
                page: i,
                current: currentPageLocal == i,
            });
        }
    } else {
        if (currentPageLocal == 1) {
            for (let i = 1; i <= 5; i++) {
                pages.push({
                    page: i,
                    current: currentPageLocal == i,
                });
            }
        } else if (currentPageLocal == pagesMax) {
            pagemin = pagesMax - 4 < 1 ? 1 : pagesMax - 4;
            for (let i = pagemin; i <= pagesMax; i++) {
                pages.push({
                    page: i,
                    current: currentPageLocal == i,
                });
            }
        } else {
            pageMint = currentPageLocal - 2 < 1 ? 1 : currentPageLocal - 2;
            pagePlus =
                currentPageLocal + 3 > pagesMax ? pagesMax : currentPageLocal + 3;
            for (let i = pageMint; i <= pagePlus; i++) {
                pages.push({
                    page: i,
                    current: currentPageLocal == i,
                });
            }
        }
    }

    const html = `
      <nav aria-label="Page navigation">
          <ul class="pagination">
              ${currentPageLocal == 1
            ? `
                  <li class="page-item disabled">
                      <span class="page-link">‹</span>
                  </li>
              `
            : `
                  <li class="page-item">
                      <a href="#" class="page-link" onclick="changePage(${currentPageLocal - 1
            })" >‹</a>
                  </li>
              `
        }
              ${pages
            .map(
                (item, index) => `
                  <li${item.current
                        ? ' class="page-item active"'
                        : ' class="page-item"'
                    }>
                      ${item.current
                        ? `
                          <span class="page-link">${item.page}</span>
                      `
                        : `
                          <a href="#" class="page-link" onclick="changePage(${item.page})">${item.page}</a>
                      `
                    }
                  </li>
              `
            )
            .join("")}
              ${currentPageLocal == pagesMax
            ? `
                  <li class="page-item disabled">
                      <span class="page-link">›</span>
                  </li>
              `
            : `
                  <li class="page-item">
                      <a href="#" class="page-link" onclick="changePage(${currentPageLocal + 1
            })">›</a>
                  </li>
              `
        }
          </ul>
          </nav>
      `;

    document.querySelector(selector).innerHTML = html;
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
