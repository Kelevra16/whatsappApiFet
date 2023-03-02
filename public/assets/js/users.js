let currentPage = 1;

document.addEventListener("DOMContentLoaded", function (event) {
    getListUsers();
});

function getListUsers(cuPage = 1) {
  const bodyTableUsuario = document.getElementById("bodyTableUsuario");

  var requestOptions = {
    method: "POST",
    redirect: "follow",
  };

  fetch(`/usuarios/list/${cuPage}`, requestOptions)
    .then((response) => response.json())
    .then((result) => {
      console.log(result);
      if (result.susses && result.status == "200") {
        bodyTableUsuario.innerHTML = "";
        result.data.forEach((element) => {
          html = `<tr scope="row" >
                    <td colspan="1">
                        ${element.nombreCompleto}
                    </td>
                    <td colspan="1">
                        ${element.correo}
                    </td>
                    <td colspan="1">
                        ${element.role}
                    </td>
                    <td colspan="1">
                        ${element.empresa}
                    </td>
                    <td>
                        <button class="btn btt-red-cancel-circle" onclick="deleteUser(${element.id})">Eliminar</button>
                    </td>
                </tr>`;

            bodyTableUsuario.innerHTML += html;
        });

        currentPage = cuPage;
        let maxPage = result.pager;
        paginate(maxPage, ".pagination");
      } else {
        console.log("error", result);
        bodyTableUsuario.innerHTML = "";
        const tr = document.createElement("tr");
        const td = document.createElement("td");
        td.classList.add("align-middle", "text-center");
        td.innerHTML = "No hay Usuarios";
        td.setAttribute("colspan", "5");
        tr.appendChild(td);
        bodyTableUsuario.appendChild(tr);
        currentPage = 1;
        document.querySelector(".pagination").innerHTML = "";
      }
    })
    .catch((error) => {
      bodyTableUsuario.innerHTML = "";
      bodyTableUsuario.innerHTML = "";
      const tr = document.createElement("tr");
      const td = document.createElement("td");
      td.classList.add("align-middle", "text-center");
      td.innerHTML = "No hay Usuarios";
      td.setAttribute("colspan", "5");
      tr.appendChild(td);
      bodyTableUsuario.appendChild(tr);
      currentPage = 1;
      document.querySelector(".pagination").innerHTML = "";
    });
}

function changePage(page) {
  currentPage = page;
  loadingTableView();
  getListUsers(page);
}

function loadingTableView() {
  const bodyTableUsuario = document.getElementById("bodyTableUsuario");
  bodyTableUsuario.innerHTML = "";

  for (let i = 0; i < 4; i++) {
    html = `<tr scope="row" >
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
  </tr>`;

    bodyTableUsuario.innerHTML += html;
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
            ${
              currentPageLocal == 1
                ? `
                <li class="page-item disabled">
                    <span class="page-link">‹</span>
                </li>
            `
                : `
                <li class="page-item">
                    <a href="#" class="page-link" onclick="changePage(${
                      currentPageLocal - 1
                    })" >‹</a>
                </li>
            `
            }
            ${pages
              .map(
                (item, index) => `
                <li${
                  item.current
                    ? ' class="page-item active"'
                    : ' class="page-item"'
                }>
                    ${
                      item.current
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
            ${
              currentPageLocal == pagesMax
                ? `
                <li class="page-item disabled">
                    <span class="page-link">›</span>
                </li>
            `
                : `
                <li class="page-item">
                    <a href="#" class="page-link" onclick="changePage(${
                      currentPageLocal + 1
                    })">›</a>
                </li>
            `
            }
        </ul>
        </nav>
    `;

  document.querySelector(selector).innerHTML = html;
}

const deleteUser = (id) => {
    Swal.fire({
        icon: 'warning',
        title: '¿Estas seguro de eliminar este usuario?',
        showCancelButton: true,
        confirmButtonText: `Eliminar`,
        cancelButtonText: `Cancelar`,
    }).then((result) => {

        if (result.isConfirmed) {

            Swal.fire({
                title: 'Eliminando empresa...',
                text: '',
                timerProgressBar: true,
                heightAuto: false,
                didOpen: () => {
                  Swal.showLoading()
                },
            });

            var formdata = new FormData();
            formdata.append("idUser", id);
        
            var requestOptions = {
              method: 'POST',
              body: formdata,
              redirect: 'follow'
            };

            fetch("/usuarios/delete", requestOptions)
            .then(response => response.json())
            .then(result => {
                if(result.status == 200 && result.susses){
                    Swal.fire({
                        icon: 'success',
                        title: 'Usuario eliminado',
                        showConfirmButton: true,
                        timer: 1500
                    });
                    getListUsers(currentPage);
                }else{
                    let message = (result.message) ? result.message : 'No se pudo eliminar el usuario';
                    Swal.fire({
                        icon: 'error',
                        title: message,
                        showConfirmButton: true,
                        timer: 1500
                    });
                }
            })
            .catch(error => {
                console.log('error', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error al eliminar usuario',
                    showConfirmButton: true,
                    timer: 1500
                });
            });

        }
    })
}


